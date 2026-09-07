@extends('layouts.app')

@section('title', 'Invitation - ' . $appName)

<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #3b82f6;">{{ $appName }}</h2>
    
    <p>Bonjour,</p>
    
    <p>Vous êtes invité(e) à rejoindre la plateforme <strong>{{ $appName }}</strong> pour gérer la famille <strong>{{ $family->family_name }}</strong>.</p>
    
    <p>Vous pourrez consulter les présences de vos enfants, signaler des absences, et mettre à jour les informations (allergies, régime alimentaire, inscriptions garderie/cantine).</p>
    
    <p style="margin: 30px 0;">
        <a href="{{ $url }}" 
           style="background: #3b82f6; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">
            Créer mon compte
        </a>
    </p>
    
    <p style="color: #666; font-size: 12px;">
        Ce lien expirera après une certaine durée. Si vous n'avez pas demandé cette invitation, ignorez cet email.
    </p>
</div>
