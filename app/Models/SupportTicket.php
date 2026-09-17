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
        'is_archived',
        'archived_at',
        'staff_read_at',
        'user_read_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'archived_at' => 'datetime',
        'staff_read_at' => 'datetime',
        'user_read_at' => 'datetime',
        'attachments' => 'array',
        'is_archived' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
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

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeNotArchived($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Tickets non lus par le staff (super admin).
     * Un ticket est non lu si staff_read_at est null OU si le ticket
     * a été modifié (nouveau commentaire) après staff_read_at.
     */
    public function scopeUnreadByStaff($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('staff_read_at')
              ->orWhere('updated_at', '>', \DB::raw('staff_read_at'));
        });
    }

    /**
     * Tickets non lus par l'utilisateur (tenant).
     * Un ticket est non lu si un commentaire du staff existe après user_read_at.
     */
    public function scopeUnreadByUser($query)
    {
        return $query->whereHas('comments', function ($q) {
            $q->where('is_staff', true)
              ->where(function ($sq) {
                  $sq->whereColumn('support_ticket_comments.created_at', '>', 'support_tickets.user_read_at')
                    ->orWhereNull('support_tickets.user_read_at');
              });
        });
    }

    public function getIsUnreadByStaffAttribute(): bool
    {
        return $this->staff_read_at === null
            || $this->updated_at > $this->staff_read_at;
    }

    public function getIsUnreadByUserAttribute(): bool
    {
        if ($this->user_read_at === null) {
            return $this->comments()->where('is_staff', true)->exists();
        }
        return $this->comments()
            ->where('is_staff', true)
            ->where('created_at', '>', $this->user_read_at)
            ->exists();
    }

    public function markAsReadByStaff(): void
    {
        $this->update(['staff_read_at' => now()]);
    }

    public function markAsReadByUser(): void
    {
        $this->update(['user_read_at' => now()]);
    }

    public function archive(?string $reason = null): void
    {
        $this->update([
            'is_archived' => true,
            'archived_at' => now(),
            'status' => 'closed',
        ]);
    }

    public function unarchive(): void
    {
        $this->update([
            'is_archived' => false,
            'archived_at' => null,
            'status' => 'open',
        ]);
    }
}
