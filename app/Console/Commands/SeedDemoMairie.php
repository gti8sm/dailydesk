<?php

namespace App\Console\Commands;

use App\Models\Child;
use App\Models\Family;
use App\Models\ParentModel;
use App\Models\PublicSiteBlock;
use App\Models\PublicSiteNews;
use App\Models\PublicSiteEvent;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use App\Modules\Cantine\Models\CantineDish;
use App\Modules\Cantine\Models\CantineMenu;
use App\Modules\Cantine\Models\CantinePresence;
use App\Modules\Garderie\Models\GarderiePresence;
use App\Modules\Stock\Models\StockItem;
use App\Modules\Stock\Models\StockLocation;
use App\Modules\Stock\Models\StockMovement;
use App\Services\PublicSiteProvisioner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SeedDemoMairie extends Command
{
    protected $signature = 'demo:mairie {--slug=beauville} {--refresh : Supprime et recrée la mairie de démo}';

    protected $description = 'Crée une mairie de démonstration complète : agents, écoles, classes, familles, enfants, menus, présences, stock et site public';

    public function handle(): int
    {
        $slug = $this->option('slug');

        if (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $this->error('Le slug ne doit contenir que des minuscules, chiffres et tirets.');
            return 1;
        }

        $existing = Tenant::where('slug', $slug)->first();

        if ($existing && !$this->option('refresh')) {
            $this->warn("Le tenant '{$slug}' existe déjà. Utilisez --refresh pour le recréer.");
            return 0;
        }

        if ($existing) {
            $this->purgeTenant($existing);
            $this->info("Tenant '{$slug}' existant supprimé (--refresh).");
        }

        $this->info('Création de la mairie de démonstration...');

        $tenant = $this->createTenant($slug);
        tenancy()->initialize($tenant);

        // Marque l'onboarding comme terminé pour éviter la redirection
        // de l'assistant à chaque accès au dashboard
        $settings = $tenant->settings ?? [];
        $settings['onboarding_completed'] = true;
        $tenant->update(['settings' => $settings]);

        $this->createSettings($tenant);
        $users = $this->createUsers($tenant);
        [$schoolMat, $schoolElem] = $this->createSchools($tenant);
        $classes = $this->createClasses($schoolMat, $schoolElem);
        $children = $this->createFamiliesAndChildren($classes);
        $this->seedDishes($users);
        $this->createMenus($users);
        $this->createPresences($children, $users);
        $this->createStock($users);
        $this->createPublicSite($tenant, $users);

        $this->newLine(2);
        $this->info('════════════════════════════════════════════════════════');
        $this->info("  Mairie de démo créée : {$tenant->name}");
        $this->info('════════════════════════════════════════════════════════');
        $this->line("  URL admin    : /{$tenant->slug}/dashboard");
        $this->line("  Site public  : /{$tenant->slug}");
        $this->newLine();
        $this->line('  Comptes (mot de passe : <fg=yellow>Demo-2026!</>)');
        $this->table(
            ['Rôle', 'Email', 'Usage'],
            $users['table']
        );

        return 0;
    }

    private function createTenant(string $slug): Tenant
    {
        $tenant = Tenant::create([
            'name' => 'Mairie de Beauville',
            'slug' => $slug,
            'email' => 'mairie@beauville.fr',
            'phone' => '05 56 00 00 00',
            'address' => '1 Place de la Mairie',
            'city' => 'Beauville',
            'postal_code' => '33350',
            'insee_code' => '33033',
            'population' => 2500,
            'status' => 'active',
            'subscription_plan' => 'petite-commune',
            'subscription_starts_at' => now(),
            'subscription_expires_at' => now()->addYear(),
            'primary_color' => '#3B82F6',
            'secondary_color' => '#6366F1',
            'modules_enabled' => ['garderie', 'cantine', 'stock', 'public_site', 'public_site_news', 'public_site_events'],
            'max_children' => null,
        ]);

        $baseDomain = config('app.tenant_domain', env('TENANT_DOMAIN', 'dailydesk.fr'));
        $tenant->domains()->create(['domain' => $slug . '.' . $baseDomain]);

        return $tenant;
    }

    private function purgeTenant(Tenant $tenant): void
    {
        $tenantId = $tenant->id;

        User::where('tenant_id', $tenantId)->delete();
        foreach ([
            Family::class, Child::class, ParentModel::class, SchoolClass::class, Setting::class,
            \App\Models\FamilyInvitation::class,
            \App\Modules\Garderie\Models\GarderiePresence::class,
            \App\Modules\Garderie\Models\GarderieEvent::class,
            \App\Modules\Cantine\Models\CantinePresence::class,
            \App\Modules\Cantine\Models\CantineEvent::class,
            CantineMenu::class, CantineDish::class,
            StockMovement::class, StockItem::class, StockLocation::class,
            \App\Models\PublicSitePage::class, PublicSiteBlock::class, PublicSiteNews::class, PublicSiteEvent::class,
            \App\Models\PublicSiteContactMessage::class,
        ] as $model) {
            $model::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->delete();
        }
        School::where('owner_tenant_id', $tenantId)->delete();

        $tenant->delete();
    }

    private function createSettings(Tenant $tenant): void
    {
        $settings = [
            ['app_name', $tenant->name, 'string', 'general'],
            ['school_year', '2026-2027', 'string', 'general'],
            ['garderie_morning_start', '07:00', 'string', 'garderie'],
            ['garderie_morning_end', '08:40', 'string', 'garderie'],
            ['garderie_evening_start', '16:30', 'string', 'garderie'],
            ['garderie_evening_end', '18:30', 'string', 'garderie'],
        ];

        foreach ($settings as [$key, $value, $type, $group]) {
            Setting::set($key, $value, $type, $group);
        }
    }

    private function createUsers(Tenant $tenant): array
    {
        $password = Hash::make('Demo-2026!');
        $table = [];

        $specs = [
            ['Marie Dupont', 'marie.dupont@beauville.fr', 'admin', 'Admin mairie — accès complet'],
            ['Sophie Martin', 'sophie.martin@beauville.fr', 'personnel_mairie', 'Agent garderie + saisie cantine'],
            ['Karim Benali', 'karim.benali@beauville.fr', 'alsh', 'Animateur ALSH — garderie'],
            ['Jean Rocher', 'jean.rocher@beauville.fr', 'cantine', 'Cuisinier — menus et plats'],
            ['Claire Moreau', 'claire.moreau@beauville.fr', 'enseignant', 'Enseignante — événements'],
        ];

        $users = [];
        foreach ($specs as [$name, $email, $role, $usage]) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'is_active' => true,
                'tenant_id' => $tenant->id,
            ]);
            $user->assignRole($role);
            $users[$role] = $user;
            $table[] = [$role, $email, $usage];
        }

        return ['users' => $users, 'table' => $table];
    }

    private function createSchools(Tenant $tenant): array
    {
        $maternelle = School::create([
            'name' => 'École Maternelle Les Petits Loups',
            'type' => 'maternelle',
            'address' => '2 rue des Écoles, Beauville',
            'is_active' => true,
            'owner_tenant_id' => $tenant->id,
        ]);

        $elementaire = School::create([
            'name' => 'École Élémentaire Jean Moulin',
            'type' => 'elementaire',
            'address' => '4 rue des Écoles, Beauville',
            'is_active' => true,
            'owner_tenant_id' => $tenant->id,
        ]);

        return [$maternelle, $elementaire];
    }

    private function createClasses(School $maternelle, School $elementaire): array
    {
        $specs = [
            [$maternelle, 'Petite Section', 'Mme Lavigne'],
            [$maternelle, 'Moyenne Section', 'Mme Petit'],
            [$maternelle, 'Grande Section', 'M. Girard'],
            [$elementaire, 'CP', 'Mme Moreau'],
            [$elementaire, 'CE1', 'M. Bernard'],
            [$elementaire, 'CE2', 'Mme Faure'],
            [$elementaire, 'CM1', 'M. Dubois'],
            [$elementaire, 'CM2', 'Mme Roux'],
        ];

        $classes = [];
        foreach ($specs as [$school, $name, $teacher]) {
            $classes[$name] = SchoolClass::create([
                'name' => $name,
                'teacher_name' => $teacher,
                'school_year' => '2026-2027',
                'school_id' => $school->id,
                'is_active' => true,
            ]);
        }

        return $classes;
    }

    private function createFamiliesAndChildren(array $classes): array
    {
        // [famille, parent {prénom, email, tél, relation}, enfants [prénom, classe, sexe, naissance, cantine, garderie, allergies]]
        $families = [
            ['Famille Dupont', ['Nathalie', 'nathalie.dupont@famille.fr', '06 12 34 56 78', 'mother'], [
                ['Léa', 'Grande Section', 'F', '2020-05-12', true, true, null],
                ['Hugo', 'CE1', 'M', '2017-09-03', true, false, null],
            ]],
            ['Famille Bernard', ['Pierre', 'pierre.bernard@famille.fr', '06 23 45 67 89', 'father'], [
                ['Chloé', 'CM1', 'F', '2016-03-25', true, true, 'arachides'],
            ]],
            ['Famille Lefèvre', ['Marion', 'marion.lefevre@famille.fr', '06 34 56 78 90', 'mother'], [
                ['Noah', 'Moyenne Section', 'M', '2021-01-18', true, true, null],
                ['Jade', 'CP', 'F', '2019-11-07', true, true, null],
            ]],
            ['Famille Moreau', ['Julien', 'julien.moreau@famille.fr', '06 45 67 89 01', 'father'], [
                ['Emma', 'Grande Section', 'F', '2020-08-30', true, false, 'lait'],
            ]],
            ['Famille Faure', ['Sophie', 'sophie.faure@famille.fr', '06 56 78 90 12', 'mother'], [
                ['Lucas', 'CM2', 'M', '2015-06-14', true, true, null],
                ['Zoé', 'CE2', 'F', '2017-12-02', true, true, null],
            ]],
            ['Famille Petit', ['Vincent', 'vincent.petit@famille.fr', '06 67 89 01 23', 'father'], [
                ['Théo', 'Petite Section', 'M', '2022-04-09', false, true, null],
            ]],
        ];

        $children = [];
        $password = Hash::make('Demo-2026!');

        foreach ($families as [$familyName, $parentSpec, $childSpecs]) {
            [$parentFirst, $parentEmail, $parentPhone, $relationship] = $parentSpec;
            $lastName = str_replace('Famille ', '', $familyName);

            $family = Family::create([
                'family_name' => $familyName,
                'address' => 'Beauville',
                'postal_code' => '33350',
                'city' => 'Beauville',
                'phone' => $parentPhone,
                'email' => $parentEmail,
                'is_active' => true,
            ]);

            // Compte parent pour le portail
            $parentUser = User::create([
                'name' => $parentFirst . ' ' . $lastName,
                'email' => $parentEmail,
                'password' => $password,
                'is_active' => true,
            ]);
            $parentUser->assignRole('parent');

            ParentModel::create([
                'family_id' => $family->id,
                'user_id' => $parentUser->id,
                'first_name' => $parentFirst,
                'last_name' => $lastName,
                'email' => $parentEmail,
                'phone' => $parentPhone,
                'mobile' => $parentPhone,
                'relationship' => $relationship,
                'is_primary_contact' => true,
                'can_pickup' => true,
                'is_legal_guardian' => true,
            ]);

            foreach ($childSpecs as [$firstName, $className, $gender, $birthDate, $cantine, $garderie, $allergies]) {
                $class = $classes[$className];
                $children[] = Child::create([
                    'family_id' => $family->id,
                    'school_id' => $class->school_id,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'birth_date' => $birthDate,
                    'gender' => $gender,
                    'class_id' => $class->id,
                    'allergies' => $allergies,
                    'is_active' => true,
                    'garderie_subscribed' => $garderie,
                    'cantine_subscribed' => $cantine,
                ]);
            }
        }

        return $children;
    }

    private function seedDishes(array $users): void
    {
        $dishes = (new SeedCantineDishes())->getDishes();
        $createdBy = $users['users']['cantine']->id ?? 1;

        foreach ($dishes as $dish) {
            CantineDish::firstOrCreate(
                ['name' => $dish['name'], 'category' => $dish['category']],
                [
                    'allergens' => $dish['allergens'] ?? null,
                    'vegetarian' => $dish['vegetarian'] ?? false,
                    'notes' => $dish['notes'] ?? null,
                    'created_by' => $createdBy,
                ]
            );
        }
    }

    private function createMenus(array $users): void
    {
        $createdBy = $users['users']['cantine']->id ?? 1;

        $compositions = [
            ['Radis beurre', 'Rôti de bœuf au jus', 'Pommes de terre rôties au laurier', 'Yaourt bio à la vanille'],
            ['Salade de haricots verts', 'Omelette au fromage bio', 'Fondue de poireaux', 'Fruit de saison bio'],
            ['Potiron rôti aux herbes de Provence', 'Haut de cuisse poulet grillé', 'Lentilles bio', 'Flan au chocolat'],
            ['Batavia bio aux croûtons', 'Filet de colin d\'Alaska', 'Riz créole', 'Compote de pommes bio'],
            ['Riz maïs bio au surimi', 'Purée de pommes de terre', 'Saucisse de Toulouse', 'Yaourt bio à la vanille'],
        ];

        $count = 0;
        $week = 0;

        while ($week < 4) {
            $monday = now()->startOfWeek()->addWeeks($week);

            foreach (range(0, 4) as $dayOffset) {
                $date = $monday->copy()->addDays($dayOffset);

                if ($date->isPast() && !$date->isToday()) {
                    continue; // pas de menus dans le passé
                }

                [$starter, $main, $side, $dessert] = $compositions[($dayOffset + $week) % 5];

                CantineMenu::firstOrCreate(
                    ['menu_date' => $date->format('Y-m-d'), 'meal_type' => 'lunch'],
                    [
                        'starter' => $starter,
                        'main_course' => $main,
                        'side_dish' => $side,
                        'dessert' => $dessert,
                        'is_published' => true,
                        'created_by' => $createdBy,
                    ]
                );
                $count++;
            }
            $week++;
        }

        $this->line("  ✓ {$count} menus cantine publiés (4 semaines)");
    }

    private function createPresences(array $children, array $users): void
    {
        $today = today()->format('Y-m-d');
        $garderieAgent = $users['users']['personnel_mairie']->id ?? 1;
        $garderie = 0;
        $cantine = 0;

        foreach ($children as $child) {
            if ($child->garderie_subscribed) {
                GarderiePresence::firstOrCreate(
                    ['child_id' => $child->id, 'date' => $today],
                    [
                        'arrival_time' => '07:15:00',
                        'recorded_by_arrival' => $garderieAgent,
                    ]
                );
                $garderie++;
            }

            if ($child->cantine_subscribed) {
                CantinePresence::firstOrCreate(
                    ['child_id' => $child->id, 'date' => $today, 'meal_type' => 'lunch'],
                    ['is_present' => true, 'recorded_by' => $garderieAgent]
                );
                $cantine++;
            }
        }

        $this->line("  ✓ {$garderie} présences garderie + {$cantine} repas cantine (aujourd'hui)");
    }

    private function createStock(array $users): void
    {
        $createdBy = $users['users']['cantine']->id ?? 1;

        $cuisine = StockLocation::firstOrCreate(
            ['name' => 'Cuisine centrale'],
            ['description' => 'Réserve principale de la cantine', 'is_active' => true, 'created_by' => $createdBy]
        );
        $reserve = StockLocation::firstOrCreate(
            ['name' => 'Réserve écoles'],
            ['description' => 'Fournitures et entretien', 'is_active' => true, 'created_by' => $createdBy]
        );

        $items = [
            [$cuisine->id, 'Farine T55 (kg)', 'FAR-001', 'Épicerie', 'kg', 40, 15],
            [$cuisine->id, 'Pâtes complètes (kg)', 'PAT-002', 'Épicerie', 'kg', 30, 10],
            [$cuisine->id, 'Lait frais (L)', 'LAI-003', 'Frais', 'L', 12, 20],
            [$cuisine->id, 'Œufs (boîte de 6)', 'OEU-004', 'Frais', 'boîte', 24, 12],
            [$cuisine->id, 'Riz basmati (kg)', 'RIZ-005', 'Épicerie', 'kg', 18, 8],
            [$reserve->id, 'Papier toilette (rouleau)', 'PPT-006', 'Entretien', 'rouleau', 45, 20],
            [$reserve->id, 'Liquide vaisselle (L)', 'LVD-007', 'Entretien', 'L', 6, 4],
            [$reserve->id, 'Gants jetables (boîte)', 'GJT-008', 'Entretien', 'boîte', 5, 6],
        ];

        foreach ($items as [$locationId, $name, $ref, $category, $unit, $qty, $min]) {
            StockItem::firstOrCreate(
                ['reference' => $ref],
                [
                    'location_id' => $locationId,
                    'name' => $name,
                    'category' => $category,
                    'unit' => $unit,
                    'quantity' => $qty,
                    'min_quantity' => $min,
                    'is_active' => true,
                    'created_by' => $createdBy,
                ]
            );
        }

        $this->line('  ✓ Stock : 2 lieux, 8 articles (dont 3 sous le seuil pour la démo alertes)');
    }

    private function createPublicSite(Tenant $tenant, array $users): void
    {
        // Pages par défaut (accueil, démarches, conseil, mentions légales...)
        $created = app(PublicSiteProvisioner::class)->provisionDefaultPages($tenant);

        // Publier les pages utiles à la démo
        \App\Models\PublicSitePage::whereIn('slug', ['la-commune', 'conseil-municipal', 'demarches', 'contact', 'accessibilite'])
            ->update(['is_published' => true]);

        // Blocs de la page d'accueil
        $blocks = [
            ['hero', 'Bannière d\'accueil', [
                'title' => 'Bienvenue à Beauville',
                'subtitle' => 'Vivre ensemble au cœur de notre commune',
                'image_url' => '', 'cta_label' => 'Découvrir la commune', 'cta_url' => '',
            ], 1],
            ['news-list', 'Dernières actualités', ['limit' => 3, 'layout' => 'grid', 'show_image' => true], 2],
            ['events-upcoming', 'Prochains événements', ['limit' => 3, 'show_image' => true], 3],
            ['cantine-menus', 'Menus de la cantine', ['month_offset' => 0, 'limit' => 5, 'show_image' => false], 4],
            ['contact', 'Contact', ['address_override' => '', 'phone_override' => '', 'email_override' => '', 'show_form' => true, 'map_url' => ''], 5],
        ];

        foreach ($blocks as [$type, $title, $config, $order]) {
            PublicSiteBlock::firstOrCreate(
                ['block_type' => $type, 'page_id' => null],
                [
                    'title' => $title,
                    'config' => $config,
                    'width' => in_array($type, ['news-list', 'events-upcoming']) ? 'half' : 'full',
                    'is_published' => true,
                    'sort_order' => $order,
                ]
            );
        }

        // Actualités
        $news = [
            ['Forum des associations', 'Le forum des associations se tiendra le samedi 12 octobre de 10h à 17h dans la salle des fêtes. Venez découvrir les 25 associations de la commune !'],
            ['Travaux voirie rue des Écoles', 'Les travaux de réfection de la voirie débutent lundi 6 octobre. Merci de votre patience pendant la durée du chantier (3 semaines).'],
            ['Inscriptions cantine 2026-2027', 'Les inscriptions à la cantine scolaire sont ouvertes jusqu\'au 15 octobre. Retrouvez les menus de la semaine sur le site.'],
        ];

        foreach ($news as [$title, $content]) {
            PublicSiteNews::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($title)],
                [
                    'title' => $title,
                    'excerpt' => mb_substr($content, 0, 120) . '...',
                    'content' => "<p>{$content}</p>",
                    'is_published' => true,
                    'published_at' => now()->subDays(rand(1, 7)),
                ]
            );
        }

        // Événements (dont un marché hebdomadaire récurrent)
        PublicSiteEvent::firstOrCreate(
            ['slug' => 'marche-hebdomadaire'],
            [
                'title' => 'Marché hebdomadaire',
                'description' => 'Marché en plein air sur la place de la Mairie : producteurs locaux, fruits et légumes, fromages.',
                'location' => 'Place de la Mairie',
                'starts_at' => now()->next('Wednesday')->format('Y-m-d'),
                'is_published' => true,
                'recurrence_type' => 'weekly',
                'recurrence_interval' => 1,
                'recurrence_end_date' => now()->addMonths(6)->format('Y-m-d'),
            ]
        );
        PublicSiteEvent::firstOrCreate(
            ['slug' => 'conseil-municipal-octobre'],
            [
                'title' => 'Conseil municipal',
                'description' => 'Séance publique du conseil municipal. Ordre du jour disponible en mairie.',
                'location' => 'Salle du conseil',
                'starts_at' => now()->addDays(8)->format('Y-m-d'),
                'is_published' => true,
                'recurrence_type' => 'none',
            ]
        );
        PublicSiteEvent::firstOrCreate(
            ['slug' => 'the-dansant'],
            [
                'title' => 'Thé dansant du CCAS',
                'description' => 'Thé dansant organisé par le CCAS au profit des actions sociales. Entrée 5 €.',
                'location' => 'Salle des fêtes',
                'starts_at' => now()->addDays(15)->format('Y-m-d'),
                'is_published' => true,
                'recurrence_type' => 'none',
            ]
        );

        $this->line('  ✓ Site public : ' . count($created) . ' pages, 5 blocs accueil, 3 actualités, 3 événements');
    }
}
