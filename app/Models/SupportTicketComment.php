<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicketComment extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'user_name',
        'is_staff',
        'content',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
