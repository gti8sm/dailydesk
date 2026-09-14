@extends('layouts.app')

@section('title', 'Mentions légales')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Mentions légales</h1>

    <div class="prose prose-gray max-w-none space-y-6 text-gray-700">
        <p class="text-sm text-gray-500">Dernière mise à jour : {{ date('d/m/Y') }}</p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">1. Éditeur du site</h2>
        <p>
            Le site <strong>DailyDesk</strong> est édité par :
        </p>
        <p>
            <strong>SmallWebConcept</strong><br>
            [Forme juridique : auto-entrepreneur]<br>
            [SIRET : À REMPLIR]<br>
            [Adresse : À REMPLIR]<br>
            Email : contact@smallwebconcept.fr<br>
            Site web : <a href="https://smallwebconcept.fr" class="text-blue-600 hover:text-blue-800">www.smallwebconcept.fr</a><br>
            [Téléphone : À REMPLIR]
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">2. Directeur de la publication</h2>
        <p>
            Le directeur de la publication est [Nom du responsable : À REMPLIR], en qualité de [fonction : À REMPLIR].
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">3. Hébergement</h2>
        <p>
            Le site est hébergé par :
        </p>
        <p>
            <strong>o2switch</strong><br>
            SAS au capital de 100 000 €<br>
            222-224 Boulevard Gustave Flaubert<br>
            63000 Clermont-Ferrand, France<br>
            SIRET : 509 665 526 00018<br>
            Téléphone : 04 44 44 60 40<br>
            Site web : <a href="https://www.o2switch.fr" class="text-blue-600 hover:text-blue-800" target="_blank">www.o2switch.fr</a>
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">4. Propriété intellectuelle</h2>
        <p>
            L'ensemble des éléments présents sur ce site (textes, logos, images, design, code source) est la propriété exclusive de SmallWebConcept, sauf mention contraire. Toute reproduction, représentation, modification ou adaptation, totale ou partielle, sans autorisation écrite préalable est interdite.
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">5. Marques</h2>
        <p>
            La marque « DailyDesk » ainsi que les logos associés sont la propriété de SmallWebConcept. Toute utilisation non autorisée est interdite.
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">6. Liens hypertextes</h2>
        <p>
            Le site peut contenir des liens vers d'autres sites. SmallWebConcept n'exerce aucun contrôle sur ces sites et décline toute responsabilité quant à leur contenu.
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">7. Données personnelles</h2>
        <p>
            Le traitement des données personnelles est conforme au Règlement Général sur la Protection des Données (RGPD - Règlement UE 2016/679). Pour plus d'informations, consultez notre <a href="{{ route('legal.rgpd') }}" class="text-blue-600 hover:text-blue-800">politique de confidentialité</a>.
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">8. Cookies</h2>
        <p>
            Le site utilise des cookies nécessaires à son fonctionnement (session d'authentification, préférences). Aucun cookie de tracking publicitaire n'est utilisé.
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">9. Droit applicable</h2>
        <p>
            Les présentes mentions légales sont soumises au droit français. En cas de litige, les tribunaux français sont seuls compétents.
        </p>

        <h2 class="text-xl font-semibold text-gray-900 mt-8">10. Crédits</h2>
        <p>
            Site développé par <a href="https://smallwebconcept.fr" target="_blank" class="text-blue-600 hover:text-blue-800">SmallWebConcept</a>.
        </p>
    </div>
</div>
@endsection
