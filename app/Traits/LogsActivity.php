<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            self::logActivity($model, 'created');
        });

        static::updated(function ($model) {
            self::logActivity($model, 'updated', $model->getOriginal(), $model->getAttributes());
        });

        static::deleted(function ($model) {
            self::logActivity($model, 'deleted');
        });
    }

    protected static function logActivity($model, string $action, $oldValues = null, $newValues = null): void
    {
        $user = Auth::user();

        $properties = null;
        if ($action === 'updated' && $oldValues) {
            $changes = [];
            foreach ($model->getChanges() as $key => $value) {
                if (in_array($key, ['updated_at', 'created_at'])) {
                    continue;
                }
                $changes[$key] = [
                    'old' => $oldValues[$key] ?? null,
                    'new' => $value,
                ];
            }
            if (!empty($changes)) {
                $properties = $changes;
            }
        }

        ActivityLog::create([
            'tenant_id' => $model->tenant_id ?? null,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => (string) $model->getKey(),
            'description' => self::buildDescription($model, $action),
            'properties' => $properties,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    protected static function buildDescription($model, string $action): string
    {
        $modelName = class_basename($model);
        $identifier = $model->name ?? $model->title ?? $model->email ?? $model->family_name ?? ('#' . $model->getKey());

        return match ($action) {
            'created' => "Création de {$modelName} : {$identifier}",
            'updated' => "Modification de {$modelName} : {$identifier}",
            'deleted' => "Suppression de {$modelName} : {$identifier}",
            default => "{$action} sur {$modelName} : {$identifier}",
        };
    }
}
