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
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_published' => 'boolean',
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

    protected static function bootPublicSiteEvent(): void
    {
        static::saving(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }
}
