<?php

namespace App\Http\Controllers;

use App\Models\Family;
use App\Models\FamilyInvitation;
use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class FamilyInvitationController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $families = Family::with(['children', 'parents'])
            ->orderBy('family_name')
            ->get();

        $invitations = FamilyInvitation::with('family')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('invitations.index', compact('families', 'invitations'));
    }

    public function send(Request $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'family_ids' => 'required|array',
            'family_ids.*' => 'exists:families,id',
            'expiry_days' => 'nullable|integer|min:1|max:90',
        ]);

        $expiryDays = (int) ($validated['expiry_days'] ?? 30);
        $sent = 0;

        foreach ($validated['family_ids'] as $familyId) {
            $family = Family::find($familyId);
            if (!$family || !$family->email) {
                continue;
            }

            $existingPending = FamilyInvitation::where('family_id', $familyId)
                ->where('status', 'pending')
                ->where('expires_at', '>', now())
                ->first();

            if ($existingPending) {
                continue;
            }

            $invitation = FamilyInvitation::create([
                'family_id' => $familyId,
                'email' => $family->email,
                'token' => FamilyInvitation::generateToken(),
                'status' => 'pending',
                'expires_at' => now()->addDays($expiryDays),
            ]);

            try {
                Mail::to($invitation->email)->send(new \App\Mail\FamilyInvitationMail($invitation));
            } catch (\Exception $e) {
                // Mail may fail in dev, continue anyway
            }

            $sent++;
        }

        return redirect()->route('invitations.index')
            ->with('success', "{$sent} invitation(s) envoyée(s).");
    }

    public function qrCodes()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $invitations = FamilyInvitation::with('family')
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('invitations.qrcodes', compact('invitations'));
    }

    public function accept($token)
    {
        $invitation = FamilyInvitation::where('token', $token)->first();

        if (!$invitation || !$invitation->isPending()) {
            return view('invitations.expired');
        }

        $family = $invitation->family;

        return view('invitations.register', compact('invitation', 'family'));
    }

    public function register(Request $request, $token)
    {
        $invitation = FamilyInvitation::where('token', $token)->first();

        if (!$invitation || !$invitation->isPending()) {
            return view('invitations.expired');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'relationship' => 'required|in:mother,father,guardian,other',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_active' => true,
        ]);

        $user->assignRole('parent');

        ParentModel::create([
            'family_id' => $invitation->family_id,
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'mobile' => $validated['mobile'] ?? null,
            'relationship' => $validated['relationship'],
            'is_primary_contact' => !$invitation->family->parents()->exists(),
            'can_pickup' => true,
        ]);

        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        auth()->login($user);

        return redirect()->route('parent.dashboard')
            ->with('success', 'Bienvenue ! Votre compte a été créé avec succès.');
    }
}
