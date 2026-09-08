<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Family extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, LogsActivity;

    protected $fillable = [
        'family_name',
        'address',
        'postal_code',
        'city',
        'phone',
        'email',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parents()
    {
        return $this->hasMany(ParentModel::class);
    }

    public function children()
    {
        return $this->hasMany(Child::class);
    }

    public function primaryContact()
    {
        return $this->hasOne(ParentModel::class)->where('is_primary_contact', true);
    }
}
