@extends('layouts.app')

@section('title', 'Aide & Documentation')

@php
    $user = auth()->user();
    $isSuperAdmin = $user && $user->hasRole('super_admin');
    $isAdmin = $user && $user->hasRole('admin_mairie');
    $isParent = $user && $user->hasRole('parent');
@endphp

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="text-center mb-8">
        <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-question text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Aide & Documentation</h1>
        <p class="mt-2 text-sm text-gray-600">Tout ce que vous devez savoir pour utiliser DailyDesk</p>
    </div>

    <div class="space-y-6">
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-connexion')" class="w-full px-6 py-4 flex items-center justify-between bg-blue-50 hover:bg-blue-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-sign-in-alt text-blue-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Connexion & Sécurité</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-connexion"></i>
            </button>
            <div id="section-connexion" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Email ou identifiant :</strong> Utilisez votre adresse email ou votre identifiant pour vous connecter.</p>
                <p class="text-sm text-gray-700"><strong>Mot de passe ou PIN :</strong> Vous pouvez utiliser votre mot de passe complet ou votre code PIN (4-6 chiffres).</p>
                <p class="text-sm text-gray-700"><strong>Rester connecté 30 jours :</strong> Cochez cette option pour éviter de vous reconnecter à chaque visite.</p>
                <p class="text-sm text-gray-700"><strong>Mot de passe oublié :</strong> Cliquez sur le lien en bas du formulaire de connexion. Un email vous sera envoyé pour réinitialiser votre mot de passe.</p>
                @if($isAdmin)
                <p class="text-sm text-gray-700"><strong>Whitelist IP :</strong> Les administrateurs peuvent restreindre les connexions à certaines adresses IP.</p>
                @endif
            </div>
        </div>

        @if($isSuperAdmin)
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border-l-4 border-indigo-500">
            <button onclick="toggleSection('section-superadmin')" class="w-full px-6 py-4 flex items-center justify-between bg-indigo-50 hover:bg-indigo-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt text-indigo-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Super Admin — Gestion globale</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-superadmin"></i>
            </button>
            <div id="section-superadmin" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Tenants :</strong> Créez et gérez les mairies (tenants). Chaque tenant a son propre domaine, ses utilisateurs et ses données.</p>
                <p class="text-sm text-gray-700"><strong>Plans d'abonnement :</strong> Définissez les plans (modules inclus, nombre max d'enfants, prix). Modifiez un plan pour mettre à jour tous les tenants qui l'utilisent.</p>
                <p class="text-sm text-gray-700"><strong>Modules :</strong> Activez ou désactivez les modules (Garderie, Cantine) par tenant. Configurez les paramètres globaux par défaut.</p>
                <p class="text-sm text-gray-700"><strong>Journal d'activité :</strong> Consultez toutes les actions effectuées sur la plateforme. Filtrez par tenant, action, utilisateur ou date.</p>
                <p class="text-sm text-gray-700"><strong>Impersonation :</strong> Connectez-vous temporairement en tant qu'admin d'un tenant pour le dépanner. Utilisez "Retour Super Admin" pour revenir.</p>
                <p class="text-sm text-gray-700"><strong>Statistiques :</strong> Vue d'ensemble des tenants actifs, revenus, croissance mensuelle et répartition par plan.</p>
            </div>
        </div>
        @endif

        @can('view_garderie')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-garderie')" class="w-full px-6 py-4 flex items-center justify-between bg-blue-50 hover:bg-blue-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-child text-blue-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Module Garderie</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-garderie"></i>
            </button>
            <div id="section-garderie" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Enregistrer une présence :</strong> Cliquez sur un enfant puis sur le bouton "Arrivée" ou "Départ". L'heure est enregistrée automatiquement.</p>
                <p class="text-sm text-gray-700"><strong>Filtrer par date :</strong> Utilisez le sélecteur de date en haut pour consulter l'historique des présences.</p>
                <p class="text-sm text-gray-700"><strong>Événements :</strong> Signalez un incident, un accident ou un comportement particulier via l'onglet "Événements".</p>
                @can('manage_settings')
                <p class="text-sm text-gray-700"><strong>Notifications :</strong> Les parents peuvent être notifiés automatiquement (configurable dans Paramètres).</p>
                @endcan
            </div>
        </div>
        @endcan

        @can('view_cantine')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-cantine')" class="w-full px-6 py-4 flex items-center justify-between bg-orange-50 hover:bg-orange-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-utensils text-orange-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Module Cantine</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-cantine"></i>
            </button>
            <div id="section-cantine" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Marquer les présents :</strong> D'un clic, marquez chaque enfant comme présent ou absent pour le repas.</p>
                <p class="text-sm text-gray-700"><strong>Type de repas :</strong> Choisissez entre "Midi" et "Goûter" selon le moment de la journée.</p>
                <p class="text-sm text-gray-700"><strong>Allergies & régimes :</strong> Les informations sur les allergies sont affichées à côté de chaque enfant.</p>
                <p class="text-sm text-gray-700"><strong>Événements :</strong> Signalez un refus alimentaire, une réaction allergique ou un incident via l'onglet dédié.</p>
            </div>
        </div>
        @endcan

        @can('manage_families')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-familles')" class="w-full px-6 py-4 flex items-center justify-between bg-green-50 hover:bg-green-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-users text-green-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Gestion des Familles</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-familles"></i>
            </button>
            <div id="section-familles" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Créer une famille :</strong> Renseignez le nom de famille, les coordonnées et les contacts.</p>
                <p class="text-sm text-gray-700"><strong>Ajouter un enfant :</strong> Depuis la fiche famille, ajoutez les enfants avec leur date de naissance, classe et informations médicales.</p>
                <p class="text-sm text-gray-700"><strong>Importer en masse :</strong> Utilisez un fichier CSV pour importer plusieurs famles à la fois.</p>
                <p class="text-sm text-gray-700"><strong>Invitations :</strong> Envoyez une invitation par email aux parents pour qu'ils créent leur compte et accèdent à leur espace.</p>
            </div>
        </div>
        @endcan

        @hasrole('parent')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-parent')" class="w-full px-6 py-4 flex items-center justify-between bg-purple-50 hover:bg-purple-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-home text-purple-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Espace Parent</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-parent"></i>
            </button>
            <div id="section-parent" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Tableau de bord :</strong> Visualisez les présences de vos enfants à la garderie et la cantine.</p>
                <p class="text-sm text-gray-700"><strong>Signalements :</strong> Signalez un changement (allergie, régime alimentaire, information médicale) via l'onglet dédié.</p>
                <p class="text-sm text-gray-700"><strong>Notifications :</strong> Recevez des notifications lors des arrivées/départs de vos enfants (si activé par la mairie).</p>
            </div>
        </div>
        @endhasrole

        @hasrole('admin_mairie')
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <button onclick="toggleSection('section-admin')" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-cog text-gray-600 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-900">Administration</h2>
                </div>
                <i class="fas fa-chevron-down text-gray-400" id="icon-section-admin"></i>
            </button>
            <div id="section-admin" class="hidden px-6 py-4 space-y-3">
                <p class="text-sm text-gray-700"><strong>Utilisateurs :</strong> Créez et gérez les comptes utilisateurs. Attribuez des rôles (admin, personnel, enseignant, parent).</p>
                <p class="text-sm text-gray-700"><strong>Classes :</strong> Créez les classes scolaires par année. Associez les enfants à leur classe.</p>
                <p class="text-sm text-gray-700"><strong>Paramètres :</strong> Configurez les horaires de garderie, le SMTP pour les emails, et les notifications.</p>
                <p class="text-sm text-gray-700"><strong>Imports/Exports :</strong> Importez des familles en CSV, exportez les présences en Excel/PDF.</p>
            </div>
        </div>
        @endhasrole
    </div>

    <div class="mt-8 text-center">
        <p class="text-sm text-gray-500">
            <i class="fas fa-info-circle mr-1"></i>
            Besoin d'aide supplémentaire ? Contactez votre administrateur.
        </p>
    </div>
</div>

<script>
function toggleSection(id) {
    const section = document.getElementById(id);
    const icon = document.getElementById('icon-' + id);
    section.classList.toggle('hidden');
    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
}
</script>
@endsection
