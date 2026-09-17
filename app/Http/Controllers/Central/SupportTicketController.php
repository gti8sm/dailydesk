<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketComment;
use App\Mail\SupportTicketReplyMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportTicketController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $statusFilter = request()->get('status', 'all');
        $archivedFilter = request()->boolean('archived');

        $query = SupportTicket::with(['user', 'comments', 'tenant']);

        // Filtre archive
        $query->where('is_archived', $archivedFilter);

        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $tickets = $query->latest()->paginate(15);

        $stats = [
            'total' => SupportTicket::notArchived()->count(),
            'open' => SupportTicket::notArchived()->where('status', 'open')->count(),
            'in_progress' => SupportTicket::notArchived()->where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::notArchived()->whereIn('status', ['resolved', 'closed'])->count(),
            'archived' => SupportTicket::archived()->count(),
            'unread' => SupportTicket::notArchived()->unreadByStaff()->count(),
        ];

        return view('central.support.index', compact('tickets', 'stats', 'statusFilter', 'archivedFilter'));
    }

    public function show(SupportTicket $ticket)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $ticket->load(['user', 'comments.user', 'tenant']);
        $ticket->markAsReadByStaff();

        return view('central.support.show', compact('ticket'));
    }

    public function comment(Request $request, SupportTicket $ticket)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
            'status' => 'nullable|in:open,in_progress,resolved,closed',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx|max:5120',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-tickets', 'public');
                $attachments[] = [
                    'path' => $path,
                    'name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                ];
            }
        }

        $comment = SupportTicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'is_staff' => true,
            'content' => $validated['content'],
            'attachments' => $attachments,
        ]);

        if (!empty($validated['status'])) {
            $ticket->update([
                'status' => $validated['status'],
                'resolved_at' => in_array($validated['status'], ['resolved', 'closed']) ? now() : null,
            ]);
        }

        // Notify the ticket creator
        if ($ticket->user && $ticket->user->email) {
            Mail::to($ticket->user->email)->send(new SupportTicketReplyMail($ticket, $comment));
        }

        return redirect()->route('central.support.show', $ticket)->with('success', 'Réponse ajoutée.');
    }

    public function archive(SupportTicket $ticket)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $ticket->archive();

        return redirect()->route('central.support.index', ['archived' => 1])
            ->with('success', 'Ticket archivé.');
    }

    public function unarchive(SupportTicket $ticket)
    {
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Accès non autorisé');
        }

        $ticket->unarchive();

        return redirect()->route('central.support.show', $ticket)
            ->with('success', 'Ticket désarchivé et rouvert.');
    }
}
