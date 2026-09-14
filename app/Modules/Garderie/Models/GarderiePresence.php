<?php

namespace App\Modules\Garderie\Models;

use App\Models\Child;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarderiePresence extends Model
{
    use HasFactory, BelongsToTenant;

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
        $morningEnd = \Carbon\Carbon::parse(\App\Models\Setting::get('garderie_morning_end', '08:30'));
        $eveningStart = \Carbon\Carbon::parse(\App\Models\Setting::get('garderie_evening_start', '16:30'));

        $arrival = $this->arrival_time ? \Carbon\Carbon::parse($this->arrival_time) : null;
        $departure = $this->departure_time ? \Carbon\Carbon::parse($this->departure_time) : null;

        $totalMinutes = 0;

        // Garderie du matin : si arrivée enregistrée avant la fin de la garderie matinale
        if ($arrival && $arrival->lt($morningEnd)) {
            $morningDeparture = ($departure && $departure->lt($morningEnd)) ? $departure : $morningEnd;
            $totalMinutes += $arrival->diffInMinutes($morningDeparture);
        }

        // Garderie du soir : si départ enregistré après le début de la garderie du soir
        if ($departure && $departure->gt($eveningStart)) {
            $eveningArrival = ($arrival && $arrival->gt($eveningStart)) ? $arrival : $eveningStart;
            $totalMinutes += $eveningArrival->diffInMinutes($departure);
        }

        $this->duration_minutes = $totalMinutes;
        $this->save();
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
