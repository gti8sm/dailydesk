<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Stancl\Tenancy\Tenancy;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $tenancy = app(Tenancy::class);
            if ($tenancy->initialized && $tenancy->tenant) {
                $table = $builder->getModel()->getTable();
                $builder->where($table . '.tenant_id', $tenancy->tenant->getTenantKey());
            }
        });

        static::creating(function ($model) {
            $tenancy = app(Tenancy::class);
            if ($tenancy->initialized && $tenancy->tenant && !isset($model->tenant_id)) {
                $model->tenant_id = $tenancy->tenant->getTenantKey();
            }
        });
    }

    public function initializeBelongsToTenant(): void
    {
        $this->fillable[] = 'tenant_id';
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class);
    }
}
