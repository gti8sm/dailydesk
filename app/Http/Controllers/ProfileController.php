<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile.index');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }

    public function updatePin(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'current_pin' => 'nullable|string',
            'pin' => 'required|string|digits_between:4,6',
        ]);

        if ($user->confidential_code && !empty($validated['current_pin'])) {
            if ($user->confidential_code !== $validated['current_pin']) {
                return back()->withErrors(['current_pin' => 'Le code PIN actuel est incorrect.']);
            }
        } elseif ($user->confidential_code && empty($validated['current_pin'])) {
            return back()->withErrors(['current_pin' => 'Veuillez saisir votre code PIN actuel.']);
        }

        $user->update([
            'confidential_code' => $validated['pin'],
        ]);

        return back()->with('success', 'Code PIN configuré avec succès.');
    }

    public function updateIpWhitelist(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'ip_whitelist_enabled' => 'boolean',
            'whitelisted_ips' => 'nullable|string',
        ]);

        $ips = [];
        if (!empty($validated['whitelisted_ips'])) {
            $rawIps = array_filter(array_map('trim', explode("\n", $validated['whitelisted_ips'])));
            foreach ($rawIps as $ip) {
                if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                    return back()->withErrors(['whitelisted_ips' => "L'adresse IP « {$ip} » n'est pas valide."]);
                }
                $ips[] = $ip;
            }
        }

        $user->update([
            'ip_whitelist_enabled' => $request->has('ip_whitelist_enabled'),
            'whitelisted_ips' => $ips,
        ]);

        return back()->with('success', 'Whitelist IP mise à jour avec succès.');
    }
}
