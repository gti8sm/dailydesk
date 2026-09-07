<?php

namespace App\Http\Controllers;

use App\Models\Child;
use App\Models\Family;
use App\Models\ParentModel;
use App\Modules\Garderie\Models\GarderieEvent;
use App\Modules\Cantine\Models\CantineEvent;
use Illuminate\Http\Request;

class ParentPortalController extends Controller
{
    private function getParent(): ParentModel
    {
        $parent = auth()->user()->parent;
        if (!$parent) {
            abort(403, 'Aucun profil parent associé.');
        }
        return $parent;
    }

    public function dashboard()
    {
        $parent = $this->getParent();
        $family = $parent->family;
        $children = $family->children()->active()->with('schoolClass')->get();

        $childIds = $children->pluck('id');

        $garderieEvents = GarderieEvent::whereIn('child_id', $childIds)
            ->orderBy('event_date', 'desc')
            ->limit(10)
            ->get();

        $cantineEvents = CantineEvent::whereIn('child_id', $childIds)
            ->orderBy('event_date', 'desc')
            ->limit(10)
            ->get();

        return view('parent.dashboard', compact('parent', 'family', 'children', 'garderieEvents', 'cantineEvents'));
    }

    public function editProfile()
    {
        $parent = $this->getParent();
        $family = $parent->family;

        return view('parent.profile', compact('parent', 'family'));
    }

    public function updateProfile(Request $request)
    {
        $parent = $this->getParent();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'family_address' => 'nullable|string|max:255',
            'family_postal_code' => 'nullable|string|max:10',
            'family_city' => 'nullable|string|max:100',
            'family_phone' => 'nullable|string|max:20',
            'family_email' => 'nullable|email|max:255',
        ]);

        $parent->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'mobile' => $validated['mobile'],
        ]);

        $parent->user->update([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
        ]);

        $parent->family->update([
            'address' => $validated['family_address'],
            'postal_code' => $validated['family_postal_code'],
            'city' => $validated['family_city'],
            'phone' => $validated['family_phone'],
            'email' => $validated['family_email'],
        ]);

        return redirect()->route('parent.profile')
            ->with('success', 'Informations mises à jour.');
    }

    public function editChild(Child $child)
    {
        $parent = $this->getParent();
        if ($child->family_id !== $parent->family_id) {
            abort(403);
        }

        $classes = \App\Models\SchoolClass::active()->orderBy('name')->get();

        return view('parent.child-edit', compact('child', 'classes'));
    }

    public function updateChild(Request $request, Child $child)
    {
        $parent = $this->getParent();
        if ($child->family_id !== $parent->family_id) {
            abort(403);
        }

        $validated = $request->validate([
            'allergies' => 'nullable|string',
            'dietary_restrictions' => 'nullable|string',
            'medical_notes' => 'nullable|string',
            'garderie_subscribed' => 'boolean',
            'cantine_subscribed' => 'boolean',
        ]);

        $validated['garderie_subscribed'] = $request->has('garderie_subscribed');
        $validated['cantine_subscribed'] = $request->has('cantine_subscribed');

        $child->update($validated);

        return redirect()->route('parent.dashboard')
            ->with('success', "Informations de {$child->full_name} mises à jour.");
    }

    public function events()
    {
        $parent = $this->getParent();
        $childIds = $parent->family->children()->active()->pluck('id');

        $garderieEvents = GarderieEvent::whereIn('child_id', $childIds)
            ->with(['child', 'createdBy'])
            ->orderBy('event_date', 'desc')
            ->paginate(10, ['*'], 'garderie_page');

        $cantineEvents = CantineEvent::whereIn('child_id', $childIds)
            ->with(['child', 'createdBy'])
            ->orderBy('event_date', 'desc')
            ->paginate(10, ['*'], 'cantine_page');

        return view('parent.events', compact('garderieEvents', 'cantineEvents'));
    }

    public function notifications()
    {
        $parent = $this->getParent();
        $prefs = $parent->user->notification_preferences ?? [];

        return view('parent.notifications', compact('parent', 'prefs'));
    }

    public function updateNotifications(Request $request)
    {
        $parent = $this->getParent();

        $validated = $request->validate([
            'email_presence_summary' => 'boolean',
            'email_absence_reminder' => 'boolean',
        ]);

        $prefs = [
            'email_signalement' => true,
            'email_presence_summary' => $request->has('email_presence_summary'),
            'email_absence_reminder' => $request->has('email_absence_reminder'),
        ];

        $parent->user->update(['notification_preferences' => $prefs]);

        return redirect()->route('parent.notifications')
            ->with('success', 'Préférences de notification mises à jour.');
    }
}
