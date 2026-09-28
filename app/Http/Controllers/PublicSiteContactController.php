<?php

namespace App\Http\Controllers;

use App\Models\PublicSiteContactMessage;
use Illuminate\Http\Request;

class PublicSiteContactController extends Controller
{
    public function submit(Request $request)
    {
        $tenant = tenant();

        if (!$tenant || !in_array('public_site', $tenant->modules_enabled ?? [])) {
            abort(404);
        }

        // Honeypot anti-spam : si rempli, on simule un succès sans rien stocker
        if ($request->filled('website')) {
            return back()->with('contact_success', 'Votre message a bien été envoyé. Nous vous répondrons rapidement.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:150',
            'message' => 'required|string|max:5000',
        ], [
            'name.required' => 'Merci d\'indiquer votre nom.',
            'email.required' => 'Merci d\'indiquer votre email.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'message.required' => 'Merci d\'écrire votre message.',
        ]);

        PublicSiteContactMessage::create([
            ...$validated,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('contact_success', 'Votre message a bien été envoyé. Nous vous répondrons rapidement.');
    }
}
