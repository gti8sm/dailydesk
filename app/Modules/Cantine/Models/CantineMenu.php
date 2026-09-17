<?php

namespace App\Modules\Cantine\Models;

use App\Models\School;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CantineMenu extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'school_id',
        'menu_date',
        'meal_type',
        'title',
        'starter',
        'main_course',
        'side_dish',
        'dessert',
        'allergens',
        'vegetarian',
        'notes',
        'is_published',
        'created_by',
    ];

    protected $casts = [
        'menu_date' => 'date',
        'allergens' => 'array',
        'vegetarian' => 'boolean',
        'is_published' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('menu_date', $date);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('menu_date', $year)->whereMonth('menu_date', $month);
    }

    public function scopeLunch($query)
    {
        return $query->where('meal_type', 'lunch');
    }

    public function scopeSnack($query)
    {
        return $query->where('meal_type', 'snack');
    }

    public function getMealTypeLabelAttribute(): string
    {
        return $this->meal_type === 'lunch' ? 'Déjeuner' : 'Goûter';
    }
}
