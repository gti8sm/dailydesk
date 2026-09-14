<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\SupportTicketComment;
use App\Mail\NewSupportTicketMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TenantSupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        return view('support.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'category' => 'required|in:general,technical,billing,feature_request,bug',
            'priority' => 'required|in:low,normal,high,urgent',
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

        $ticket = SupportTicket::create([
            'tenant_id' => auth()->user()->tenant_id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'attachments' => $attachments,
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        // Notify super admins
        $superAdmins = \App\Models\User::role('super_admin')->get();
        foreach ($superAdmins as $admin) {
            Mail::to($admin->email)->send(new NewSupportTicketMail($ticket));
        }

        return redirect()->route('support.show', $ticket)->with('success', 'Ticket créé. Notre équipe vous répondra dans les plus brefs délais.');
    }

    public function show(SupportTicket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['comments.user']);

        return view('support.show', compact('ticket'));
    }

    public function comment(Request $request, SupportTicket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:5000',
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

        SupportTicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'is_staff' => false,
            'content' => $validated['content'],
            'attachments' => $attachments,
        ]);

        if ($ticket->status === 'resolved' || $ticket->status === 'closed') {
            $ticket->update(['status' => 'in_progress', 'resolved_at' => null]);
        }

        // Notify super admins of new comment
        $superAdmins = \App\Models\User::role('super_admin')->get();
        foreach ($superAdmins as $admin) {
            Mail::to($admin->email)->send(new NewSupportTicketMail($ticket));
        }

        return redirect()->route('support.show', $ticket)->with('success', 'Message ajouté.');
    }
}
