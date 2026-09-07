<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'parents';

    protected $fillable = [
        'family_id',
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'relationship',
        'is_primary_contact',
        'can_pickup',
    ];

    protected $casts = [
        'is_primary_contact' => 'boolean',
        'can_pickup' => 'boolean',
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
}
