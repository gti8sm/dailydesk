<?php

namespace App\Modules\Garderie\Models;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarderiePresence extends Model
{
    use HasFactory;

    protected $fillable = [
        'child_id',
        'date',
        'arrival_time',
        'departure_time',
        'recorded_by_arrival',
        'recorded_by_departure',
        'duration_minutes',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function recordedByArrival()
    {
        return $this->belongsTo(User::class, 'recorded_by_arrival');
    }

    public function recordedByDeparture()
    {
        return $this->belongsTo(User::class, 'recorded_by_departure');
    }

    public function calculateDuration()
    {
        if ($this->arrival_time && $this->departure_time) {
            $arrival = \Carbon\Carbon::parse($this->arrival_time);
            $departure = \Carbon\Carbon::parse($this->departure_time);
            
            // Récupérer les horaires de garderie depuis les paramètres
            $morningEnd = \Carbon\Carbon::parse(\App\Models\Setting::get('garderie_morning_end', '08:30'));
            $eveningStart = \Carbon\Carbon::parse(\App\Models\Setting::get('garderie_evening_start', '16:30'));
            
            $totalMinutes = 0;
            
            // Calculer le temps de garderie du matin (si applicable)
            if ($arrival->lt($morningEnd)) {
                $morningDeparture = $departure->lt($morningEnd) ? $departure : $morningEnd;
                $totalMinutes += $arrival->diffInMinutes($morningDeparture);
            }
            
            // Calculer le temps de garderie du soir (si applicable)
            if ($departure->gt($eveningStart)) {
                $eveningArrival = $arrival->gt($eveningStart) ? $arrival : $eveningStart;
                $totalMinutes += $eveningArrival->diffInMinutes($departure);
            }
            
            $this->duration_minutes = $totalMinutes;
            $this->save();
        }
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
}
