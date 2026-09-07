<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'slug',
            'email',
            'phone',
            'address',
            'city',
            'postal_code',
            'logo_path',
            'primary_color',
            'secondary_color',
            'status',
            'subscription_plan',
            'subscription_starts_at',
            'subscription_expires_at',
            'trial_ends_at',
            'max_children',
            'modules_enabled',
            'settings',
        ];
    }

    protected $fillable = [
        'id',
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'logo_path',
        'primary_color',
        'secondary_color',
        'status',
        'subscription_plan',
        'subscription_starts_at',
        'subscription_expires_at',
        'trial_ends_at',
        'max_children',
        'modules_enabled',
        'settings',
    ];

    protected $casts = [
        'subscription_starts_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'modules_enabled' => 'array',
        'settings' => 'array',
    ];

    /**
     * Check if tenant is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if tenant subscription is expired
     */
    public function isExpired(): bool
    {
        if ($this->subscription_expires_at === null) {
            return false;
        }

        return now()->isAfter($this->subscription_expires_at);
    }

    /**
     * Check if tenant is in trial period
     */
    public function isInTrial(): bool
    {
        if ($this->trial_ends_at === null) {
            return false;
        }

        return now()->isBefore($this->trial_ends_at);
    }

    /**
     * Check if tenant has a specific module enabled
     */
    public function hasModule(string $module): bool
    {
        if ($this->modules_enabled === null) {
            return false;
        }

        return in_array($module, $this->modules_enabled);
    }

    /**
     * Check if tenant can add more children
     */
    public function canAddChild(): bool
    {
        if ($this->max_children === null) {
            return true; // Unlimited
        }

        $currentCount = $this->run(function () {
            return \App\Models\Child::count();
        });

        return $currentCount < $this->max_children;
    }

    /**
     * Get remaining children slots
     */
    public function getRemainingChildrenSlots(): ?int
    {
        if ($this->max_children === null) {
            return null; // Unlimited
        }

        $currentCount = $this->run(function () {
            return \App\Models\Child::count();
        });

        return max(0, $this->max_children - $currentCount);
    }

    /**
     * Get primary domain
     */
    public function getPrimaryDomain(): ?string
    {
        return $this->domains()->first()?->domain;
    }
}
