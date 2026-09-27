<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PublicSiteEvent extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, LogsActivity;

    protected $table = 'public_site_events';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'location',
        'starts_at',
        'ends_at',
        'image_path',
        'is_published',
        'recurrence_type',
        'recurrence_interval',
        'recurrence_end_date',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'recurrence_end_date' => 'date',
        'is_published' => 'boolean',
        'recurrence_interval' => 'integer',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now()->startOfDay())->orderBy('starts_at');
    }

    public function scopePast($query)
    {
        return $query->where('starts_at', '<', now()->startOfDay())->orderByDesc('starts_at');
    }

    /**
     * Check if this event recurs.
     */
    public function isRecurring(): bool
    {
        return $this->recurrence_type !== 'none';
    }

    /**
     * Get all occurrence dates for this event (including the original).
     * Returns a collection of Carbon dates.
     */
    public function getOccurrences(\Carbon\CarbonInterface $from = null, \Carbon\CarbonInterface $until = null): \Illuminate\Support\Collection
    {
        $dates = collect();
        $start = $this->starts_at->copy();
        $end = $this->recurrence_end_date ?? $until ?? $start->copy()->addYear();
        $from = $from ?? now()->startOfYear();

        if (!$this->isRecurring()) {
            if ($start <= $end && $start >= $from) {
                $dates->push($start);
            }
            return $dates;
        }

        $current = $start->copy();
        $maxIterations = 366; // safety limit
        while ($current <= $end && $maxIterations-- > 0) {
            if ($current >= $from) {
                $dates->push($current->copy());
            }
            $current = $this->advanceDate($current);
        }

        return $dates;
    }

    protected function advanceDate(\Carbon\CarbonInterface $date): \Carbon\CarbonInterface
    {
        $interval = max(1, (int) $this->recurrence_interval);
        return match ($this->recurrence_type) {
            'daily' => $date->addDays($interval),
            'weekly' => $date->addWeeks($interval),
            'monthly' => $date->addMonths($interval),
            'yearly' => $date->addYears($interval),
            default => $date->addDays($interval),
        };
    }

    /**
     * Get a human-readable recurrence label.
     */
    public function getRecurrenceLabelAttribute(): string
    {
        if (!$this->isRecurring()) {
            return '';
        }

        $interval = $this->recurrence_interval > 1 ? "tous les {$this->recurrence_interval} " : 'tous les ';

        return match ($this->recurrence_type) {
            'daily' => $this->recurrence_interval > 1 ? "Tous les {$this->recurrence_interval} jours" : 'Tous les jours',
            'weekly' => $this->recurrence_interval > 1 ? "Toutes les {$this->recurrence_interval} semaines" : 'Toutes les semaines',
            'monthly' => $this->recurrence_interval > 1 ? "Tous les {$this->recurrence_interval} mois" : 'Tous les mois',
            'yearly' => $this->recurrence_interval > 1 ? "Tous les {$this->recurrence_interval} ans" : 'Tous les ans',
            default => '',
        };
    }

    protected static function bootPublicSiteEvent(): void
    {
        static::saving(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }
}
