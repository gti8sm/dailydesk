<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class SupportTicket extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'user_name',
        'subject',
        'description',
        'attachments',
        'status',
        'priority',
        'category',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'attachments' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(SupportTicketComment::class, 'ticket_id');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }
}
