<?php

namespace App\Http\Controllers;

use App\Models\PublicSiteEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SiteEventController extends Controller
{
    public function index()
    {
        $this->authorizeAccess();

        $events = PublicSiteEvent::orderBy('starts_at', 'desc')->paginate(15);

        return view('site.events.index', compact('events'));
    }

    public function create()
    {
        $this->authorizeAccess();

        return view('site.events.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
            'recurrence_type' => 'nullable|string|in:none,daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1|max:365',
            'recurrence_end_date' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        $validated['recurrence_type'] = $validated['recurrence_type'] ?? 'none';
        $validated['recurrence_interval'] = $validated['recurrence_interval'] ?? 1;
        if ($validated['recurrence_type'] === 'none') {
            $validated['recurrence_end_date'] = null;
        }

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('events', 'public');
        }

        PublicSiteEvent::create($validated);

        return redirect()->route('site.events.index')
            ->with('success', "Événement '{$validated['title']}' créé avec succès.");
    }

    public function edit(PublicSiteEvent $event)
    {
        $this->authorizeAccess();

        return view('site.events.edit', compact('event'));
    }

    public function update(Request $request, PublicSiteEvent $event)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
            'recurrence_type' => 'nullable|string|in:none,daily,weekly,monthly,yearly',
            'recurrence_interval' => 'nullable|integer|min:1|max:365',
            'recurrence_end_date' => 'nullable|date|after_or_equal:starts_at',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        $validated['recurrence_type'] = $validated['recurrence_type'] ?? 'none';
        $validated['recurrence_interval'] = $validated['recurrence_interval'] ?? 1;
        if ($validated['recurrence_type'] === 'none') {
            $validated['recurrence_end_date'] = null;
        }

        if ($request->hasFile('image')) {
            if ($event->image_path) {
                Storage::disk('public')->delete($event->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('events', 'public');
        }

        $event->update($validated);

        return redirect()->route('site.events.index')
            ->with('success', "Événement '{$event->title}' mis à jour avec succès.");
    }

    public function destroy(PublicSiteEvent $event)
    {
        $this->authorizeAccess();

        $title = $event->title;
        if ($event->image_path) {
            Storage::disk('public')->delete($event->image_path);
        }
        $event->delete();

        return redirect()->route('site.events.index')
            ->with('success', "Événement '{$title}' supprimé.");
    }

    private function authorizeAccess(): void
    {
        if (!auth()->user()->can('manage_public_site')) {
            abort(403, 'Accès non autorisé');
        }
    }
}
