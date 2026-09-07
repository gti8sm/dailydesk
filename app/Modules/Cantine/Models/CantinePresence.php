<?php

namespace App\Modules\Cantine\Models;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CantinePresence extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'date',
        'meal_type',
        'is_present',
        'recorded_by',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_present' => 'boolean',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)
                     ->whereMonth('date', $month);
    }

    public function scopeLunch($query)
    {
        return $query->where('meal_type', 'lunch');
    }

    public function scopeSnack($query)
    {
        return $query->where('meal_type', 'snack');
    }
}
