<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'name',
        'email',
        'login',
        'password',
        'confidential_code',
        'ip_whitelist_enabled',
        'whitelisted_ips',
        'is_active',
        'last_login_at',
        'notification_preferences',
        'tenant_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'confidential_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
        'whitelisted_ips' => 'array',
        'ip_whitelist_enabled' => 'boolean',
        'is_active' => 'boolean',
        'notification_preferences' => 'array',
    ];

    public function parent()
    {
        return $this->hasOne(ParentModel::class);
    }

    public function isIpAllowed(string $ip): bool
    {
        if (!$this->ip_whitelist_enabled) {
            return true;
        }

        if (empty($this->whitelisted_ips)) {
            return false;
        }

        return in_array($ip, $this->whitelisted_ips);
    }

    public function verifyConfidentialCode(string $code): bool
    {
        return $this->confidential_code === $code;
    }
}
