<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Intercommunality extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'siren',
        'type',
        'address',
        'city',
        'postal_code',
        'phone',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'tenant_intercommunality')
            ->withPivot('joined_at')
            ->withTimestamps();
    }

    public function schools()
    {
        return $this->hasMany(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'communaute_communes' => 'Communauté de communes',
            'communaute_agglomeration' => "Communauté d'agglomération",
            'syndicat' => 'Syndicat intercommunal',
            'epci' => 'EPCI',
            default => ucfirst($this->type),
        };
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }
}
