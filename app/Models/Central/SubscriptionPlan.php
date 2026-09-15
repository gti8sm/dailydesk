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
        'population_min',
        'population_max',
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
        'population_min' => 'integer',
        'population_max' => 'integer',
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
     * Find the plan matching a given population.
     * Returns the first active plan where population falls within [population_min, population_max].
     */
    public static function findByPopulation(int $population): ?self
    {
        return self::active()
            ->where('population_min', '<=', $population)
            ->where(function ($q) use ($population) {
                $q->whereNull('population_max')
                  ->orWhere('population_max', '>=', $population);
            })
            ->orderBy('sort_order')
            ->first();
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
