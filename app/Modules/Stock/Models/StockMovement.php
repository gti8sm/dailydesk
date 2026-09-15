<?php

namespace App\Modules\Stock\Models;

use App\Models\User;
use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory, BelongsToTenant, LogsActivity;

    protected $table = 'stock_movements';

    protected $fillable = [
        'stock_item_id',
        'type',
        'quantity',
        'reason',
        'new_quantity',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'new_quantity' => 'decimal:2',
    ];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'in' => 'Entrée',
            'out' => 'Sortie',
            'adjust' => 'Ajustement',
            default => $this->type,
        };
    }
}
