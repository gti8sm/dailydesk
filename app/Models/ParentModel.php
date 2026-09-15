<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentModel extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, LogsActivity;

    protected $table = 'parents';

    protected $fillable = [
        'family_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'address',
        'postal_code',
        'city',
        'relationship',
        'is_primary_contact',
        'can_pickup',
        'is_legal_guardian',
    ];

    protected $casts = [
        'is_primary_contact' => 'boolean',
        'can_pickup' => 'boolean',
        'is_legal_guardian' => 'boolean',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getRelationshipLabelAttribute()
    {
        return match ($this->relationship) {
            'mother' => 'Mère',
            'father' => 'Père',
            'guardian' => 'Tuteur légal',
            'other' => 'Autre',
            default => 'Autre',
        };
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([$this->address, $this->postal_code, $this->city]);
        return implode(', ', $parts) ?: null;
    }
}
