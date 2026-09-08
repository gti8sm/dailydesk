<?php

namespace App\Modules\Garderie\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Modules\Garderie\Models\GarderieEvent;
use Illuminate\Http\Request;

class GarderieEventController extends Controller
{
    public function index(Request $request)
    {
        $query = GarderieEvent::with(['child.family', 'createdBy']);

        if ($request->filled('child_id')) {
            $query->where('child_id', $request->child_id);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('unnotified')) {
            $query->where('parents_notified', false);
        }

        $events = $query->orderBy('event_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('garderie.events.index', compact('events'));
    }

    public function create(Request $request)
    {
        $children = Child::active()->orderBy('last_name')->orderBy('first_name')->get();
        $preselectedChild = $request->get('child_id');

        return view('garderie.events.create', compact('children', 'preselectedChild'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'event_type' => 'required|in:incident,accident,behavior,medical,other',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'severity' => 'required|in:low,medium,high',
        ]);

        $validated['created_by'] = auth()->id();

        GarderieEvent::create($validated);

        return redirect()->route('garderie.events.index')
            ->with('success', 'Événement enregistré avec succès.');
    }

    public function show(GarderieEvent $event)
    {
        $event->load(['child.family', 'createdBy']);

        return view('garderie.events.show', compact('event'));
    }

    public function markNotified(GarderieEvent $event)
    {
        if (!auth()->user()->can('notify_event_parents')) {
            abort(403, 'Seul un administrateur peut notifier les parents.');
        }

        $event->load('child.family.parents.user');
        $parents = $event->child?->family?->parents ?? collect();

        foreach ($parents as $parent) {
            if ($parent->email) {
                \Mail::to($parent->email)->send(
                    new \App\Mail\EventNotificationMail(
                        childName: $event->child->full_name,
                        module: 'Garderie',
                        title: $event->title,
                        description: $event->description,
                        severity: $event->severity,
                        eventDate: $event->event_date->format('d/m/Y'),
                        eventTime: $event->event_time?->format('H:i'),
                    )
                );
            }
        }

        $event->markAsNotified();

        return redirect()->back()
            ->with('success', 'Parents notifiés par email avec succès.');
    }
}
