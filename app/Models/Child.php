<?php

namespace App\Models;

use App\Modules\Garderie\Models\GarderiePresence;
use App\Modules\Garderie\Models\GarderieEvent;
use App\Modules\Cantine\Models\CantinePresence;
use App\Modules\Cantine\Models\CantineEvent;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Child extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'family_id',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'class',
        'class_id',
        'allergies',
        'medical_notes',
        'dietary_restrictions',
        'photo_path',
        'is_active',
        'garderie_subscribed',
        'cantine_subscribed',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'garderie_subscribed' => 'boolean',
        'cantine_subscribed' => 'boolean',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function garderiePresences()
    {
        return $this->hasMany(GarderiePresence::class);
    }

    public function garderieEvents()
    {
        return $this->hasMany(GarderieEvent::class);
    }

    public function cantinePresences()
    {
        return $this->hasMany(CantinePresence::class);
    }

    public function cantineEvents()
    {
        return $this->hasMany(CantineEvent::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute()
    {
        return $this->birth_date->age;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%");
        });
    }
}
