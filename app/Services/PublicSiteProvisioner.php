<?php

namespace App\Services;

use App\Models\PublicSitePage;
use App\Models\Tenant;

class PublicSiteProvisioner
{
    /**
     * Pages publiques créées à l'activation du module public_site.
     * Idempotent : ne crée que les pages dont le slug n'existe pas encore.
     */
    public static function defaultPages(Tenant $tenant): array
    {
        return [
            'accueil' => [
                'title' => 'Accueil',
                'is_published' => true,
                'sort_order' => 0,
                'meta_description' => 'Site officiel de la commune de ' . $tenant->name,
                'content' => '<p>Bienvenue sur le site officiel de la commune de ' . $tenant->name . '.</p>'
                    . '<p>Retrouvez ici toutes les informations pratiques de la commune : actualités, agenda, démarches, conseil municipal et services communaux.</p>',
            ],
            'la-commune' => [
                'title' => 'La commune',
                'is_published' => false,
                'sort_order' => 1,
                'meta_description' => 'Découvrez ' . $tenant->name . ' : histoire, patrimoine et présentation',
                'content' => '<h2>Découvrez ' . $tenant->name . '</h2>'
                    . '<p>Présentez ici votre commune : son histoire, son patrimoine, ses atouts touristiques et sa vie locale.</p>'
                    . '<p>Vous pouvez modifier cette page depuis l\'administration : <em>Modules → Site Public → Pages</em>, ou la composer avec le constructeur de blocs.</p>',
            ],
            'conseil-municipal' => [
                'title' => 'Conseil municipal',
                'is_published' => false,
                'sort_order' => 2,
                'meta_description' => 'Composition du conseil municipal de ' . $tenant->name,
                'content' => '<h2>Le conseil municipal</h2>'
                    . '<p>Présentez ici les élus de la commune et leurs délégations. Le bloc « Conseil municipal » du constructeur de pages permet d\'afficher la composition sous forme de grille.</p>'
                    . '<h3>Délibérations</h3>'
                    . '<p>Conformément à l\'article L.2121-25 du CGCT, les délibérations du conseil municipal sont communicables à toute personne qui en fait la demande et publiées dans les huit jours.</p>',
            ],
            'demarches' => [
                'title' => 'Démarches administratives',
                'is_published' => false,
                'sort_order' => 3,
                'meta_description' => 'Démarches administratives : état civil, CNI, passeport, recensement, élections',
                'content' => '<h2>Vos démarches</h2>'
                    . '<p>Retrouvez les démarches administratives les plus courantes. Le bloc « Démarches » du constructeur de pages permet d\'afficher des liens pré-formatés vers service-public.fr.</p>'
                    . '<p>Pour les démarches non dématérialisées, rendez-vous en mairie pendant les horaires d\'ouverture.</p>',
            ],
            'urbanisme' => [
                'title' => 'Urbanisme',
                'is_published' => false,
                'sort_order' => 4,
                'meta_description' => 'Urbanisme : PLU, permis de construire, déclarations préalables',
                'content' => '<h2>Urbanisme</h2>'
                    . '<p>Informez ici sur le Plan Local d\'Urbanisme (PLU), les permis de construire et les déclarations préalables de travaux.</p>'
                    . '<p>Le dépôt de demande d\'urbanisme peut se faire en ligne sur le guichet numérique national : <a href="https://www.service-public.fr/particuliers/vosdroits/N319" rel="noopener" target="_blank">service-public.fr</a>.</p>',
            ],
            'contact' => [
                'title' => 'Contact',
                'is_published' => false,
                'sort_order' => 5,
                'meta_description' => 'Contactez la mairie de ' . $tenant->name,
                'content' => '<h2>Nous contacter</h2>'
                    . '<p>Utilisez le bloc « Contact » du constructeur de pages pour afficher vos coordonnées et un formulaire de contact.</p>',
            ],
            'accessibilite' => [
                'title' => 'Accessibilité',
                'is_published' => false,
                'sort_order' => 98,
                'meta_description' => 'Déclaration d\'accessibilité du site de ' . $tenant->name,
                'content' => self::accessibilityContent($tenant),
            ],
            'mentions-legales' => [
                'title' => 'Mentions légales',
                'is_published' => true,
                'sort_order' => 99,
                'meta_description' => 'Mentions légales du site de ' . $tenant->name,
                'content' => self::legalContent($tenant),
            ],
        ];
    }

    /**
     * Crée les pages par défaut si elles n'existent pas.
     * Retourne la liste des slugs créés.
     */
    public function provisionDefaultPages(Tenant $tenant): array
    {
        $created = [];

        foreach (self::defaultPages($tenant) as $slug => $definition) {
            // withTrashed : une page supprimée (soft delete) bloque l'insertion
            // via la contrainte unique — on ne recrée jamais par-dessus
            $exists = PublicSitePage::withTrashed()->where('slug', $slug)->exists();

            if ($exists) {
                continue;
            }

            PublicSitePage::create([
                'title' => $definition['title'],
                'slug' => $slug,
                'content' => $definition['content'],
                'meta_description' => $definition['meta_description'],
                'is_published' => $definition['is_published'],
                'sort_order' => $definition['sort_order'],
            ]);

            $created[] = $slug;
        }

        return $created;
    }

    /**
     * Contenu des mentions légales, généré depuis les données du tenant.
     */
    public static function legalContent(Tenant $tenant): string
    {
        $address = trim($tenant->address . ($tenant->address ? "\n" : '') . $tenant->postal_code . ' ' . $tenant->city);
        $hosting = $tenant->settings['hosting_provider'] ?? 'o2switch, 222-224 Boulevard Gustave Flaubert, 63000 Clermont-Ferrand — www.o2switch.fr';

        return '<h2>Éditeur du site</h2>'
            . '<p>Le présent site est édité par la commune de <strong>' . e($tenant->name) . '</strong>.</p>'
            . '<ul>'
            . '<li>Adresse : ' . nl2br(e($address)) . '</li>'
            . ($tenant->phone ? '<li>Téléphone : ' . e($tenant->phone) . '</li>' : '')
            . ($tenant->email ? '<li>Email : ' . e($tenant->email) . '</li>' : '')
            . ($tenant->insee_code ? '<li>Code INSEE : ' . e($tenant->insee_code) . '</li>' : '')
            . '</ul>'
            . '<p>Directeur de la publication : Monsieur/Madame le Maire.</p>'
            . '<h2>Hébergement</h2>'
            . '<p>' . e($hosting) . '</p>'
            . '<h2>Propriété intellectuelle</h2>'
            . '<p>L\'ensemble des contenus présents sur ce site (textes, images, logos) est protégé par le droit de la propriété intellectuelle. Toute reproduction sans autorisation préalable est interdite.</p>'
            . '<h2>Données personnelles (RGPD)</h2>'
            . '<p>Les informations recueillies via les formulaires de ce site font l\'objet d\'un traitement destiné uniquement à la réponse aux demandes. Elles ne sont ni cédées ni transmises à des tiers.</p>'
            . '<p>Conformément à la loi « Informatique et Libertés » et au RGPD, vous disposez d\'un droit d\'accès, de rectification, d\'effacement et d\'opposition sur vos données. Pour l\'exercer, contactez la mairie' . ($tenant->email ? ' à l\'adresse ' . e($tenant->email) : '') . '.</p>'
            . '<p>Ce site ne dépose pas de cookies de suivi. Aucune donnée de navigation n\'est transmise à des tiers.</p>';
    }

    /**
     * Contenu de la déclaration d'accessibilité (RGAA), à compléter par la commune.
     */
    public static function accessibilityContent(Tenant $tenant): string
    {
        return '<h2>Déclaration d\'accessibilité</h2>'
            . '<p>' . e($tenant->name) . ' s\'engage à rendre son site accessible conformément à l\'article 47 de la loi n° 2005-102 du 11 février 2005.</p>'
            . '<p><em>Cette déclaration d\'accessibilité est un modèle à compléter par la commune. Indiquez ici votre état de conformité au RGAA (référentiel général d\'amélioration de l\'accessibilité).</em></p>'
            . '<h3>État de conformité</h3>'
            . '<p>À définir par la commune (conforme, partiellement conforme ou non conforme), avec la date d\'audit et le référentiel utilisé (ex : RGAA 4.1).</p>'
            . '<h3>Amélioration et contact</h3>'
            . '<p>Si vous n\'arrivez pas à accéder à un contenu ou à un service, contactez la mairie pour obtenir une alternative.</p>'
            . '<h3>Voie de recours</h3>'
            . '<p>Cette procédure est à utiliser dans le cas suivant : vous avez signalé au responsable du site internet un défaut d\'accessibilité qui vous empêche d\'accéder à un contenu et vous n\'avez pas obtenu de réponse satisfaisante.</p>'
            . '<p>Vous pouvez :</p>'
            . '<ul>'
            . '<li>Écrire un message au Défenseur des droits (gratuit) : Défenseur des droits, 7 rue Saint-Florentin, 75409 Paris Cedex 08</li>'
            . '<li>Contacter le délégué du Défenseur des droits dans votre région</li>'
            . '<li>Envoyer un formulaire en ligne sur <a href="https://www.defenseurdesdroits.fr" rel="noopener" target="_blank">www.defenseurdesdroits.fr</a></li>'
            . '</ul>';
    }
}
