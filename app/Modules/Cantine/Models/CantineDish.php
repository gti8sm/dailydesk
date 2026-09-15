<?php

namespace App\Modules\Cantine\Models;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CantineDish extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'name',
        'category',
        'allergens',
        'vegetarian',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'allergens' => 'array',
        'vegetarian' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeStarter($query)
    {
        return $query->where('category', 'starter');
    }

    public function scopeMainCourse($query)
    {
        return $query->where('category', 'main_course');
    }

    public function scopeSideDish($query)
    {
        return $query->where('category', 'side_dish');
    }

    public function scopeDessert($query)
    {
        return $query->where('category', 'dessert');
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'starter' => 'Entrée',
            'main_course' => 'Plat principal',
            'side_dish' => 'Accompagnement',
            'dessert' => 'Dessert',
            default => $this->category,
        };
    }
}
