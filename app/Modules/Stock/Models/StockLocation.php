<?php

namespace App\Modules\Stock\Models;

use App\Models\User;
use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLocation extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected $table = 'stock_locations';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(StockItem::class, 'location_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
