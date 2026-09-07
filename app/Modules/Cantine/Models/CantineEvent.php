<?php

namespace App\Modules\Cantine\Models;

use App\Models\Child;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CantineEvent extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'child_id',
        'created_by',
        'event_date',
        'event_time',
        'event_type',
        'title',
        'description',
        'parents_notified',
        'notified_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_time' => 'datetime:H:i',
        'parents_notified' => 'boolean',
        'notified_at' => 'datetime',
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function markAsNotified()
    {
        $this->update([
            'parents_notified' => true,
            'notified_at' => now(),
        ]);
    }

    public function scopeUnnotified($query)
    {
        return $query->where('parents_notified', false);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('event_date', $date);
    }
}
