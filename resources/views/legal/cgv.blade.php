@extends('layouts.app')

@section('title', 'Conditions Générales de Vente')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Conditions Générales de Vente</h1>

    <div class="prose prose-gray max-w-none space-y-6 text-gray-700">
        <p class="text-sm text-gray-500">Dernière mise à jour : {{ date('d/m/Y') }}</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 1 — Objet</h2>
        <p>Les présentes Conditions Générales de Vente (CGV) régissent les relations contractuelles entre la société <strong>SmallWebConcept</strong> (ci-après « l'Éditeur ») et toute personne morale ou physique (ci-après « le Client ») souscrivant à l'un des services proposés sur la plateforme <strong>DailyDesk</strong> (ci-après « la Plateforme »).</p>
        <p>DailyDesk est une plateforme SaaS (Software as a Service) destinée aux mairies et collectivités pour la gestion de la garderie et de la cantine scolaire.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 2 — Description des services</h2>
        <p>L'Éditeur met à disposition du Client une application web permettant :</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>La gestion des présences en garderie et cantine scolaire</li>
            <li>La gestion des enfants et des familles</li>
            <li>La communication avec les parents via un portail dédié</li>
            <li>La génération de rapports et d'exports de données</li>
        </ul>
        <p>Les services sont proposés sous forme d'abonnements mensuels ou annuels selon les plans suivants :</p>
        <ul class="list-disc pl-6 space-y-1">
            @foreach($plans as $plan)
            <li>
                <strong>{{ $plan->name }}</strong> — {{ number_format($plan->price_monthly, 0, ',', ' ') }} €/mois
                ({{ number_format($plan->price_yearly, 0, ',', ' ') }} €/an) :
                @if($plan->max_children)
                    jusqu'à {{ $plan->max_children }} enfants
                @else
                    enfants illimités
                @endif
                — Modules : {{ implode(', ', array_map('ucfirst', $plan->modules ?? [])) }}
            </li>
            @endforeach
        </ul>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 3 — Période d'essai</h2>
        <p>Une période d'essai gratuite peut être accordée au Client pour une durée maximale de 90 jours. À l'issue de cette période, le Client doit choisir un plan d'abonnement pour continuer à utiliser la Plateforme. En l'absence de souscription, l'accès au service sera suspendu.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 4 — Tarifs et facturation</h2>
        <p>Les tarifs sont exprimés en euros hors taxes. La TVA applicable est ajoutée au tarif indiqué. Le paiement s'effectue par prélèvement bancaire ou virement, mensuellement ou annuellement selon le choix du Client.</p>
        <p>Une facture est émise pour chaque période de facturation. En cas de retard de paiement, l'Éditeur se réserve le droit de suspendre l'accès au service après une mise en demeure restée sans effet pendant 15 jours.</p>
        <p>L'Éditeur se réserve le droit de modifier ses tarifs à tout moment, avec un préavis minimum de 30 jours avant la date d'effet. Les modifications prendront effet à la prochaine échéance de facturation.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 5 — Durée et résiliation</h2>
        <p>L'abonnement est conclu pour une durée d'un an, renouvelable par tacite reconduction. Le Client peut résilier son abonnement à tout moment, avec un préavis de 30 jours, par courrier ou email adressé à l'Éditeur. La résiliation prend effet à la fin de la période de prépayée.</p>
        <p>En cas de manquement grave du Client à ses obligations, l'Éditeur peut résilier le contrat de plein droit, après mise en demeure restée sans effet pendant 8 jours.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 6 — Obligations du Client</h2>
        <p>Le Client s'engage à :</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Fournir des informations exactes et complètes lors de l'inscription</li>
            <li>Utiliser la Plateforme conformément à sa destination et aux présentes CGV</li>
            <li>Respecter les lois en vigueur, notamment en matière de protection des données personnelles (RGPD)</li>
            <li>Ne pas porter atteinte à l'intégrité ou au bon fonctionnement de la Plateforme</li>
            <li>Assurer la sécurité de ses identifiants de connexion</li>
        </ul>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 7 — Obligations de l'Éditeur</h2>
        <p>L'Éditeur s'engage à :</p>
        <ul class="list-disc pl-6 space-y-1">
            <li>Fournir un accès à la Plateforme 24h/24, 7j/7, sous réserve de maintenance</li>
            <li>Assurer la sécurité et la confidentialité des données du Client</li>
            <li>Effectuer les mises à jour et améliorations de la Plateforme</li>
            <li>Fournir un support technique par email et/ou via le système de tickets intégré</li>
        </ul>
        <p>L'Éditeur ne pourra être tenu responsable des interruptions de service dues à des cas de force majeure ou à des opérations de maintenance planifiées.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 8 — Données personnelles</h2>
        <p>Le traitement des données personnelles est conforme au Règlement Général sur la Protection des Données (RGPD). Pour plus d'informations, se référer à notre <a href="{{ route('legal.rgpd') }}" class="text-blue-600 hover:text-blue-800">politique de confidentialité</a>.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 9 — Propriété intellectuelle</h2>
        <p>La Plateforme et l'ensemble de ses composants (logiciel, design, contenus) sont la propriété exclusive de l'Éditeur. Le Client dispose d'un droit d'usage non exclusif pour la durée de son abonnement.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 10 — Responsabilité</h2>
        <p>L'Éditeur met tout en œuvre pour assurer la disponibilité et la sécurité de la Plateforme. Sa responsabilité ne peut être engagée pour les dommages indirects résultant de l'utilisation du service. L'Éditeur n'est pas responsable du contenu des données saisies par le Client.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 11 — Droit applicable et litiges</h2>
        <p>Les présentes CGV sont soumises au droit français. En cas de litige, les parties s'efforceront de trouver une solution amiable. À défaut, le litige sera soumis aux tribunaux français compétents.</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">Article 12 — Contact</h2>
        <p>Pour toute question relative aux présentes CGV, le Client peut contacter l'Éditeur :</p>
        <p>
            <strong>SmallWebConcept</strong><br>
            Email : contact@smallwebconcept.fr<br>
            Site web : <a href="https://smallwebconcept.fr" class="text-blue-600 hover:text-blue-800">www.smallwebconcept.fr</a>
        </p>
    </div>
</div>
@endsection
