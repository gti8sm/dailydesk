<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $connection = 'central';

    protected $fillable = [
        'name',
        'slug',
        'price_monthly',
        'price_yearly',
        'max_children',
        'modules',
        'features',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'modules' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active plans
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Check if plan has a specific module
     */
    public function hasModule(string $module): bool
    {
        return in_array($module, $this->modules ?? []);
    }

    /**
     * Get yearly savings percentage
     */
    public function getYearlySavingsPercentage(): float
    {
        $monthlyYearly = $this->price_monthly * 12;
        if ($monthlyYearly == 0) {
            return 0;
        }

        return round((($monthlyYearly - $this->price_yearly) / $monthlyYearly) * 100);
    }
}
