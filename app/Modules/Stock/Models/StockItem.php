<?php

namespace App\Modules\Stock\Models;

use App\Models\User;
use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected $table = 'stock_items';

    protected $fillable = [
        'location_id',
        'name',
        'reference',
        'category',
        'unit',
        'quantity',
        'min_quantity',
        'description',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'min_quantity' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function location()
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class, 'stock_item_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_quantity')->where('min_quantity', '>', 0);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->min_quantity > 0 && $this->quantity <= $this->min_quantity;
    }

    public function getStockLevelAttribute(): string
    {
        if ($this->min_quantity <= 0) {
            return 'ok';
        }
        if ($this->quantity <= 0) {
            return 'critical';
        }
        if ($this->quantity <= $this->min_quantity) {
            return 'low';
        }
        if ($this->quantity <= $this->min_quantity * 1.5) {
            return 'warning';
        }
        return 'ok';
    }
}
