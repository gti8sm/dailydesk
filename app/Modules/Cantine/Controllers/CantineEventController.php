<?php

namespace App\Modules\Cantine\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Modules\Cantine\Models\CantineEvent;
use Illuminate\Http\Request;

class CantineEventController extends Controller
{
    public function index(Request $request)
    {
        $query = CantineEvent::with(['child.family', 'createdBy']);

        if ($request->filled('child_id')) {
            $query->where('child_id', $request->child_id);
        }

        if ($request->filled('unnotified')) {
            $query->where('parents_notified', false);
        }

        $events = $query->orderBy('event_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('cantine.events.index', compact('events'));
    }

    public function create(Request $request)
    {
        $children = Child::active()->orderBy('last_name')->orderBy('first_name')->get();
        $preselectedChild = $request->get('child_id');

        return view('cantine.events.create', compact('children', 'preselectedChild'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'child_id' => 'required|exists:children,id',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'event_type' => 'required|in:allergy,refusal,incident,other',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $validated['created_by'] = auth()->id();

        CantineEvent::create($validated);

        return redirect()->route('cantine.events.index')
            ->with('success', 'Événement enregistré avec succès.');
    }

    public function show(CantineEvent $event)
    {
        $event->load(['child.family', 'createdBy']);

        return view('cantine.events.show', compact('event'));
    }

    public function markNotified(CantineEvent $event)
    {
        $event->markAsNotified();

        return redirect()->back()
            ->with('success', 'Parents marqués comme notifiés.');
    }
}
