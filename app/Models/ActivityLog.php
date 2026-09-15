<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ActivityLog extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'user_name',
        'action',
        'model_type',
        'model_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForModule(Builder $query, string $module): Builder
    {
        $modelMap = self::getModelModuleMap();

        $modelTypes = collect($modelMap)
            ->filter(fn($m) => $m === $module)
            ->keys()
            ->all();

        if (empty($modelTypes)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('model_type', $modelTypes);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('description', 'like', "%{$term}%")
              ->orWhere('user_name', 'like', "%{$term}%")
              ->orWhere('ip_address', 'like', "%{$term}%")
              ->orWhere('model_type', 'like', "%{$term}%");
        });
    }

    public function getModuleAttribute(): ?string
    {
        return self::getModelModuleMap()[$this->model_type] ?? null;
    }

    public function getModuleLabelAttribute(): ?string
    {
        $labels = [
            'users' => 'Utilisateurs',
            'families' => 'Familles',
            'children' => 'Enfants',
            'garderie' => 'Garderie',
            'cantine' => 'Cantine',
            'invitations' => 'Invitations',
            'tenants' => 'Tenants',
            'auth' => 'Authentification',
            'settings' => 'Configuration',
            'other' => 'Autre',
        ];

        return $labels[$this->module] ?? null;
    }

    public static function getModelModuleMap(): array
    {
        return [
            'App\Models\User' => 'users',
            'App\Models\Family' => 'families',
            'App\Models\Child' => 'children',
            'App\Models\ParentModel' => 'families',
            'App\Models\SchoolClass' => 'children',
            'App\Models\Tenant' => 'tenants',
            'App\Models\Central\SubscriptionPlan' => 'tenants',
            'App\Models\Central\Subscription' => 'tenants',
            'App\Models\FamilyInvitation' => 'invitations',
            'App\Models\Setting' => 'settings',
            'App\Modules\Garderie\Models\GarderiePresence' => 'garderie',
            'App\Modules\Garderie\Models\GarderieEvent' => 'garderie',
            'App\Modules\Cantine\Models\CantinePresence' => 'cantine',
            'App\Modules\Cantine\Models\CantineEvent' => 'cantine',
            'App\Modules\Cantine\Models\CantineMenu' => 'cantine',
            'App\Modules\Cantine\Models\CantineDish' => 'cantine',
        ];
    }
}

