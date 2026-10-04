<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Modules\Cantine\Models\CantineMenu;
use Illuminate\Console\Command;

class SeedCantineMenus extends Command
{
    protected $signature = 'cantine:seed-menus {tenant : Slug du tenant} {--weeks=4 : Nombre de semaines à générer} {--unpublished : Créer les menus en brouillon au lieu de publiés}';

    protected $description = 'Crée les menus cantine des prochaines semaines (petits déjeuners complets, publiés) pour un tenant';

    public function handle(): int
    {
        $tenant = Tenant::where('slug', $this->argument('tenant'))->first();

        if (!$tenant) {
            $this->error("Tenant introuvable : '{$this->argument('tenant')}'");
            return 1;
        }

        $weeks = max(1, min(12, (int) $this->option('weeks')));

        tenancy()->initialize($tenant);

        $createdBy = \App\Models\User::where('tenant_id', $tenant->id)
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['cantine', 'admin', 'admin_mairie']))
            ->first()?->id;

        $isPublished = !$this->option('unpublished');

        // Compositions équilibrées alternées sur la semaine
        $compositions = [
            ['Radis beurre', 'Rôti de bœuf au jus', 'Pommes de terre rôties au laurier', 'Yaourt bio à la vanille'],
            ['Salade de haricots verts', 'Omelette au fromage bio', 'Fondue de poireaux', 'Fruit de saison bio'],
            ['Potiron rôti aux herbes de Provence', 'Haut de cuisse poulet grillé', 'Lentilles bio', 'Flan au chocolat'],
            ['Batavia bio aux croûtons', 'Filet de colin d\'Alaska', 'Riz créole', 'Compote de pommes bio'],
            ['Riz maïs bio au surimi', 'Purée de pommes de terre', 'Saucisse de Toulouse', 'Yaourt bio à la vanille'],
        ];

        $created = 0;
        $skipped = 0;

        for ($week = 0; $week < $weeks; $week++) {
            $monday = now()->startOfWeek()->addWeeks($week);

            foreach (range(0, 4) as $dayOffset) {
                $date = $monday->copy()->addDays($dayOffset);

                // Pas de menus dans le passé (sauf aujourd'hui)
                if ($date->isPast() && !$date->isToday()) {
                    continue;
                }

                [$starter, $main, $side, $dessert] = $compositions[($dayOffset + $week) % 5];

                $menu = CantineMenu::firstOrCreate(
                    ['menu_date' => $date->format('Y-m-d'), 'meal_type' => 'lunch'],
                    [
                        'starter' => $starter,
                        'main_course' => $main,
                        'side_dish' => $side,
                        'dessert' => $dessert,
                        'is_published' => $isPublished,
                        'created_by' => $createdBy,
                    ]
                );

                if ($menu->wasRecentlyCreated) {
                    $created++;
                } else {
                    $skipped++;
                }
            }
        }

        $this->info("Menus cantine pour '{$tenant->slug}' : {$created} créés, {$skipped} déjà existants (lundi → vendredi, {$weeks} semaine(s)).");
        $this->line($isPublished ? 'Menus publiés — visibles par les parents et sur le site public.' : 'Menus créés en brouillon (option --unpublished).');

        return 0;
    }
}
