@extends('layouts.app')

@section('title', 'Politique de confidentialité (RGPD)')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Politique de confidentialité (RGPD)</h1>

    <div class="prose prose-gray max-w-none space-y-6 text-gray-700">
        <p class="text-sm text-gray-500">Dernière mise à jour : {{ date('d/m/Y') }}</p>

        <p>La plateforme <strong>DailyDesk</strong>, éditée par <strong>SmallWebConcept</strong>, s'engage à protéger les données personnelles de ses utilisateurs conformément au Règlement Général sur la Protection des Données (RGPD - Règlement UE 2016/679) et à la Loi Informatique et Libertés.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">1. Responsable du traitement</h2>
        <p>
            Le responsable du traitement des données personnelles est :
        </p>
        <p>
            <strong>SmallWebConcept</strong><br>
            Email : contact@smallwebconcept.fr<br>
            [Adresse : À REMPLIR]
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">2. Données collectées</h2>
        <p>La plateforme collecte les catégories de données suivantes :</p>

        <h3 class="font-semibold text-gray-800 mt-4">2.1 Données des administrateurs de tenant</h3>
        <ul class="list-disc pl-6 space-y-1">
            <li>Nom, prénom, email, identifiant de connexion</li>
            <li>Mot de passe (chiffré)</li>
            <li>Adresse IP (à des fins de sécurité)</li>
            <li>Date de dernière connexion</li>
        </ul>

        <h3 class="font-semibold text-gray-800 mt-4">2.2 Données des parents</h3>
        <ul class="list-disc pl-6 space-y-1">
            <li>Nom, prénom, email</li>
            <li>Informations sur les enfants (nom, prénom, classe)</li>
            <li>Données de présence (garderie, cantine)</li>
            <li>Signalements et événements liés aux enfants</li>
        </ul>

        <h3 class="font-semibold text-gray-800 mt-4">2.3 Données des enfants</h3>
        <ul class="list-disc pl-6 space-y-1">
            <li>Nom, prénom, date de naissance</li>
            <li>Classe, école</li>
            <li>Informations de présence (garderie, cantine)</li>
            <li>Informations alimentaires (allergies, régime)</li>
        </ul>

        <h3 class="font-semibold text-gray-800 mt-4">2.4 Données de support</h3>
        <ul class="list-disc pl-6 space-y-1">
            <li>Sujet, description et messages des tickets de support</li>
            <li>Pièces jointes (captures d'écran, documents)</li>
        </ul>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">3. Finalités du traitement</h2>
        <p>Les données sont collectées et traitées pour les finalités suivantes :</p>
        <ul class="list-disc pl-6 space-y-1">
            <li><strong>Gestion du service</strong> : gestion des présences garderie/cantine, communication avec les parents</li>
            <li><strong>Authentification</strong> : accès sécurisé à la plateforme</li>
            <li><strong>Support</strong> : traitement des demandes d'assistance</li>
            <li><strong>Sécurité</strong> : traçabilité des connexions et des actions (journaux d'activité)</li>
            <li><strong>Facturation</strong> : gestion des abonnements et émission des factures</li>
        </ul>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">4. Base légale</h2>
        <p>Le traitement des données repose sur :</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>L'<strong>exécution d'un contrat</strong> (abonnement à la plateforme) pour les données de gestion</li>
            <li>L'<strong>obligation légale</strong> pour la conservation des données de traçabilité</li>
            <li>Le <strong>consentement</strong> pour les données des parents (invitation à rejoindre la plateforme)</li>
            <li>L'<strong>intérêt légitime</strong> pour la sécurité du service</li>
        </ul>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">5. Destinataires des données</h2>
        <p>Les données sont accessibles :</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Aux administrateurs de la mairie/collectivité (tenant) pour les données de leur organisation</li>
            <li>Aux parents pour les données de leurs propres enfants</li>
            <li>Au super admin (SmallWebConcept) pour le support technique et la gestion de la plateforme</li>
            <li>Aux hébergeurs (o2switch) uniquement pour le stockage technique des données</li>
        </ul>
        <p>Les données ne sont <strong>jamais vendues ni partagées avec des tiers à des fins commerciales</strong>.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">6. Durée de conservation</h2>
        <table class="w-full text-sm border border-gray-200">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-200 px-4 py-2 text-left">Type de données</th>
                    <th class="border border-gray-200 px-4 py-2 text-left">Durée de conservation</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-gray-200 px-4 py-2">Comptes utilisateurs actifs</td>
                    <td class="border border-gray-200 px-4 py-2">Durée de l'abonnement + 30 jours</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 px-4 py-2">Données des enfants et présences</td>
                    <td class="border border-gray-200 px-4 py-2">Durée de l'abonnement + 1 an</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 px-4 py-2">Journaux d'activité (logs)</td>
                    <td class="border border-gray-200 px-4 py-2">1 an</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 px-4 py-2">Tickets de support</td>
                    <td class="border border-gray-200 px-4 py-2">2 ans après clôture</td>
                </tr>
                <tr>
                    <td class="border border-gray-200 px-4 py-2">Factures</td>
                    <td class="border border-gray-200 px-4 py-2">10 ans (obligation légale)</td>
                </tr>
            </tbody>
        </table>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">7. Sécurité des données</h2>
        <p>SmallWebConcept met en œuvre les mesures techniques et organisationnelles suivantes :</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Chiffrement des mots de passe (bcrypt)</li>
            <li>Connexion sécurisée (HTTPS/TLS)</li>
            <li>Isolation des données par tenant (base de données séparée ou cloisonnée)</li>
            <li>Sauvegardes régulières des données</li>
            <li>Journalisation des actions sensibles</li>
            <li>Mise à jour régulière des composants logiciels</li>
        </ul>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">8. Droits des utilisateurs</h2>
        <p>Conformément au RGPD, chaque utilisateur dispose des droits suivants :</p>
        <ul class="list-disc pl-6 space-y-1">
            <li><strong>Droit d'accès</strong> : obtenir une copie de ses données personnelles</li>
            <li><strong>Droit de rectification</strong> : corriger des données inexactes</li>
            <li><strong>Droit à l'effacement</strong> : demander la suppression de ses données</li>
            <li><strong>Droit à la limitation du traitement</strong></li>
            <li><strong>Droit à la portabilité</strong> : recevoir ses données dans un format structuré</li>
            <li><strong>Droit d'opposition</strong> : s'opposer au traitement de ses données</li>
            <li><strong>Droit de retirer son consentement</strong> à tout moment</li>
        </ul>
        <p>Pour exercer ces droits, l'utilisateur peut contacter :</p>
        <p>
            <strong>Email</strong> : contact@smallwebconcept.fr<br>
            <strong>Courrier</strong> : SmallWebConcept, [Adresse : À REMPLIR]
        </p>
        <p>L'utilisateur peut également introduire une réclamation auprès de l'autorité de contrôle compétente :</p>
        <p>
            <strong>CNIL</strong><br>
            3 Place de Fontenoy, TSA 80715<br>
            75334 Paris CEDEX 07<br>
            Site web : <a href="https://www.cnil.fr" target="_blank" class="text-blue-600 hover:text-blue-800">www.cnil.fr</a>
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">9. Transferts hors UE</h2>
        <p>Les données sont hébergées en France (o2switch, Clermont-Ferrand). Aucun transfert de données en dehors de l'Union Européenne n'est effectué.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">10. Cookies</h2>
        <p>La plateforme utilise uniquement des cookies techniques nécessaires à son fonctionnement :</p>
        <ul class="list-disc pl-6 space-y-1">
            <li><strong>Cookie de session</strong> : maintien de la session d'authentification</li>
            <li><strong>Token CSRF</strong> : protection contre les attaques CSRF</li>
        </ul>
        <p>Aucun cookie de tracking ou publicitaire n'est utilisé. Aucun consentement n'est donc requis au titre de la loi Informatique et Libertés.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">11. Contact</h2>
        <p>
            Pour toute question relative à la protection des données personnelles :<br>
            <strong>Email</strong> : contact@smallwebconcept.fr
        </p>
    </div>
</div>
@endsection
