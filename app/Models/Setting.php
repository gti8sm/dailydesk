<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use BelongsToTenant, LogsActivity;
    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    /**
     * Clé de cache tenant-aware : setting_{tenant|global}_{key}.
     * Sans ça, la valeur d'un tenant pollue le cache des pages hors
     * tenant (login, landing) et des autres tenants.
     */
    protected static function currentTenantKey(): ?string
    {
        $tenancy = app(\Stancl\Tenancy\Tenancy::class);

        if ($tenancy->initialized && $tenancy->tenant) {
            return (string) $tenancy->tenant->getTenantKey();
        }

        return null;
    }

    protected static function cacheKey(?string $tenantKey, string $key): string
    {
        return 'setting_' . ($tenantKey ?: 'global') . '_' . $key;
    }

    /**
     * Récupère un paramètre avec résolution hiérarchique :
     * 1. paramètre du tenant courant (si contexte tenant)
     * 2. paramètre global (tenant_id NULL)
     * 3. valeur par défaut
     */
    public static function get($key, $default = null)
    {
        $tenantKey = self::currentTenantKey();

        $cached = Cache::remember(self::cacheKey($tenantKey, $key), 3600, function () use ($key, $tenantKey) {
            $setting = self::resolve($key, $tenantKey);

            // Tableau pour distinguer « absent » (null) d'une valeur falsy
            return $setting ? ['v' => self::castValue($setting->value, $setting->type)] : null;
        });

        return $cached === null ? $default : $cached['v'];
    }

    /**
     * Écrit un paramètre : dans le contexte tenant → ligne du tenant,
     * sinon → ligne globale. Invalide le cache du contexte écrit
     * (et les caches de fallback des tenants si écriture globale).
     */
    public static function set($key, $value, $type = 'string', $group = 'general', $description = null)
    {
        $tenantKey = self::currentTenantKey();

        $setting = self::withoutGlobalScope('tenant')
            ->where('key', $key)
            ->when($tenantKey, fn($q) => $q->where('tenant_id', $tenantKey))
            ->when(!$tenantKey, fn($q) => $q->whereNull('tenant_id'))
            ->first();

        if ($setting) {
            $setting->update([
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]);
        } else {
            $setting = self::withoutGlobalScope('tenant')->create([
                'key' => $key,
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
                'tenant_id' => $tenantKey,
            ]);
        }

        self::flush($key, $tenantKey);

        return $setting;
    }

    /**
     * Paramètres d'un groupe avec la même résolution hiérarchique :
     * les valeurs du tenant remplacent celles globales, les clés
     * absentes du tenant tombent sur le global.
     */
    public static function getGroup($group)
    {
        $tenantKey = self::currentTenantKey();

        $rows = self::withoutGlobalScope('tenant')
            ->where('group', $group)
            ->where(function ($q) use ($tenantKey) {
                $q->whereNull('tenant_id');
                if ($tenantKey) {
                    $q->orWhere('tenant_id', $tenantKey);
                }
            })
            ->get();

        $merged = $rows->whereNull('tenant_id')->keyBy('key');

        if ($tenantKey) {
            $merged = $merged->merge($rows->where('tenant_id', $tenantKey)->keyBy('key'));
        }

        return $merged->mapWithKeys(function ($setting) {
            return [$setting->key => self::castValue($setting->value, $setting->type)];
        });
    }

    /**
     * Invalide le cache d'une clé pour un contexte donné.
     * Écriture globale → invalide aussi le fallback de chaque tenant.
     * À appeler après toute écriture directe en base (ex: paramètres
     * globaux du super admin qui écrivent sans passer par set()).
     */
    public static function flush(string $key, ?string $tenantKey = null): void
    {
        Cache::forget(self::cacheKey($tenantKey, $key));

        if (!$tenantKey) {
            Tenant::query()->pluck('id')->each(
                fn($id) => Cache::forget(self::cacheKey((string) $id, $key))
            );
        }
    }

    /**
     * Ligne résolue pour une clé : tenant d'abord, global ensuite.
     */
    protected static function resolve(string $key, ?string $tenantKey): ?object
    {
        $query = self::withoutGlobalScope('tenant')->where('key', $key);

        if ($tenantKey) {
            $tenantRow = (clone $query)->where('tenant_id', $tenantKey)->first();

            if ($tenantRow) {
                return $tenantRow;
            }
        }

        return (clone $query)->whereNull('tenant_id')->first();
    }

    protected static function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'array':
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
}
