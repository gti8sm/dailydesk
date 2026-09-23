<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Tenancy;

class School extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'address',
        'type',
        'is_active',
        'owner_tenant_id',
        'intercommunality_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Pas de BelongsToTenant global scope classique :
     * on gère un scope custom qui inclut les écoles de l'intercommunality.
     */
    protected static function bootSchool(): void
    {
        static::addGlobalScope('tenant_or_interco', function (Builder $builder) {
            $tenancy = app(Tenancy::class);
            if ($tenancy->initialized && $tenancy->tenant) {
                $tenantId = $tenancy->tenant->getTenantKey();
                $intercoIds = $tenancy->tenant->getIntercommunalityIds();

                $builder->where(function (Builder $q) use ($tenantId, $intercoIds) {
                    $q->where('schools.owner_tenant_id', $tenantId);
                    if (!empty($intercoIds)) {
                        $q->orWhereIn('schools.intercommunality_id', $intercoIds);
                    }
                });
            }
        });

        static::creating(function ($model) {
            $tenancy = app(Tenancy::class);
            if ($tenancy->initialized && $tenancy->tenant && !isset($model->owner_tenant_id)) {
                $model->owner_tenant_id = $tenancy->tenant->getTenantKey();
            }
        });
    }

    public function initializeSchool(): void
    {
        $this->fillable[] = 'owner_tenant_id';
        $this->fillable[] = 'intercommunality_id';
    }

    public function ownerTenant()
    {
        return $this->belongsTo(Tenant::class, 'owner_tenant_id');
    }

    public function intercommunality()
    {
        return $this->belongsTo(Intercommunality::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Écoles appartenant au tenant courant uniquement (hors interco).
     */
    public function scopeOwnedOnly($query)
    {
        $tenancy = app(Tenancy::class);
        if ($tenancy->initialized && $tenancy->tenant) {
            return $query->where('schools.owner_tenant_id', $tenancy->tenant->getTenantKey());
        }
        return $query;
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'maternelle' => 'Maternelle',
            'elementaire' => 'Élémentaire',
            'primaire' => 'Primaire',
            'college' => 'Collège',
            default => ucfirst($this->type),
        };
    }

    /**
     * Indique si l'école est partagée dans une intercommunalité.
     */
    public function getIsSharedAttribute(): bool
    {
        return !empty($this->intercommunality_id);
    }
}
