<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Modules\Cantine\Models\CantineDish;
use Illuminate\Console\Command;

class SeedCantineDishes extends Command
{
    protected $signature = 'cantine:seed-dishes';
    protected $description = 'Pré-charge le catalogue de plats pour tous les tenants';

    public function handle(): void
    {
        $dishes = $this->getDishes();
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn('Aucun tenant trouvé.');
            return;
        }

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);
            $userId = \App\Models\User::where('tenant_id', $tenant->id)->first()?->id ?? 1;

            $count = 0;
            foreach ($dishes as $dish) {
                CantineDish::firstOrCreate(
                    [
                        'name' => $dish['name'],
                        'category' => $dish['category'],
                    ],
                    [
                        'allergens' => $dish['allergens'] ?? null,
                        'vegetarian' => $dish['vegetarian'] ?? false,
                        'notes' => $dish['notes'] ?? null,
                        'created_by' => $userId,
                    ]
                );
                $count++;
            }

            $this->info("✓ {$count} plats chargés pour '{$tenant->slug}'.");
        }

        $this->info('Terminé pour tous les tenants.');
    }

    private function getDishes(): array
    {
        return [
            // === ENTRÉES ===
            ['name' => 'Boulgour bio aux crevettes', 'category' => 'starter', 'allergens' => ['crustacés', 'gluten']],
            ['name' => 'Radis beurre', 'category' => 'starter', 'allergens' => ['lait']],
            ['name' => 'Salade de haricots verts', 'category' => 'starter'],
            ['name' => 'Batavia bio aux croûtons', 'category' => 'starter', 'allergens' => ['gluten']],
            ['name' => 'Riz maïs bio au surimi', 'category' => 'starter', 'allergens' => ['poisson', 'œufs']],
            ['name' => 'Potiron rôti aux herbes de Provence', 'category' => 'starter', 'vegetarian' => true],
            ['name' => "Salade d'endives sauce vinaigrette bio", 'category' => 'starter', 'allergens' => ['moutarde']],
            ['name' => 'Carottes râpées aux olives bio', 'category' => 'starter', 'vegetarian' => true],
            ['name' => 'Salade de gésiers', 'category' => 'starter'],
            ['name' => 'Céleri rave sauce mayonnaise', 'category' => 'starter', 'allergens' => ['œufs', 'moutarde', 'céleri']],

            // === PLATS PRINCIPAUX ===
            ['name' => 'Rôti de bœuf au jus', 'category' => 'main_course'],
            ['name' => 'Omelette au fromage bio', 'category' => 'main_course', 'allergens' => ['œufs', 'lait'], 'vegetarian' => true],
            ['name' => 'Haut de cuisse poulet grillé', 'category' => 'main_course'],
            ['name' => 'Cœur filet merlu sauce vierge', 'category' => 'main_course', 'allergens' => ['poisson']],
            ['name' => 'Rôti de veau au jus', 'category' => 'main_course'],
            ['name' => 'Falafel semoule et tajine de légumes', 'category' => 'main_course', 'allergens' => ['gluten', 'sésame'], 'vegetarian' => true],
            ['name' => 'Côte de porc VPF grillée', 'category' => 'main_course'],
            ['name' => 'Curry de légumes à l\'indienne', 'category' => 'main_course', 'vegetarian' => true],
            ['name' => 'Brochette de poisson meunière', 'category' => 'main_course', 'allergens' => ['poisson', 'gluten', 'lait']],
            ['name' => 'Dos de colin lieu aux amandes', 'category' => 'main_course', 'allergens' => ['poisson', 'fruits à coque']],
            ['name' => 'Émincé bœuf mariné Kentucky', 'category' => 'main_course', 'allergens' => ['gluten', 'sésame', 'soja']],

            // === ACCOMPAGNEMENTS ===
            ['name' => 'Fondue de poireaux', 'category' => 'side_dish', 'allergens' => ['lait'], 'vegetarian' => true],
            ['name' => 'Pommes de terre rôties au laurier', 'category' => 'side_dish', 'vegetarian' => true],
            ['name' => 'Lentilles bio', 'category' => 'side_dish', 'vegetarian' => true],
            ['name' => 'Épinards branches à la crème', 'category' => 'side_dish', 'allergens' => ['lait'], 'vegetarian' => true],
            ['name' => 'Carottes Vichy', 'category' => 'side_dish', 'vegetarian' => true],
            ['name' => 'Féculent du jour', 'category' => 'side_dish', 'vegetarian' => true],
            ['name' => 'Salade bio', 'category' => 'side_dish', 'vegetarian' => true],
            ['name' => 'Haricots beurre au beurre', 'category' => 'side_dish', 'allergens' => ['lait'], 'vegetarian' => true],
            ['name' => 'Spaghettis au beurre', 'category' => 'side_dish', 'allergens' => ['gluten', 'lait'], 'vegetarian' => true],
            ['name' => 'Riz créole bio', 'category' => 'side_dish', 'vegetarian' => true],

            // === DESSERTS ===
            ['name' => 'Yaourt bio à la vanille', 'category' => 'dessert', 'allergens' => ['lait'], 'vegetarian' => true],
            ['name' => 'Fruit de saison bio', 'category' => 'dessert', 'vegetarian' => true],
            ['name' => 'Flan au chocolat', 'category' => 'dessert', 'allergens' => ['lait', 'œufs'], 'vegetarian' => true],
            ['name' => 'Yaourt aux fruits mixés bio', 'category' => 'dessert', 'allergens' => ['lait'], 'vegetarian' => true],
            ['name' => 'Crêpe au sucre', 'category' => 'dessert', 'allergens' => ['gluten', 'œufs', 'lait'], 'vegetarian' => true],
            ['name' => 'Cantal AOP', 'category' => 'dessert', 'allergens' => ['lait'], 'vegetarian' => true],
            ['name' => 'Crème au chocolat', 'category' => 'dessert', 'allergens' => ['lait', 'œufs'], 'vegetarian' => true],
            ['name' => 'Yaourt nature Sojasun', 'category' => 'dessert', 'allergens' => ['soja'], 'vegetarian' => true],
            ['name' => 'Pomme pochée aux noix et cannelle', 'category' => 'dessert', 'allergens' => ['fruits à coque'], 'vegetarian' => true],
            ['name' => 'Saint Paulin bio', 'category' => 'dessert', 'allergens' => ['lait'], 'vegetarian' => true],
            ['name' => 'Banane bio', 'category' => 'dessert', 'vegetarian' => true],
            ['name' => 'Brie bio', 'category' => 'dessert', 'allergens' => ['lait'], 'vegetarian' => true],
            ['name' => 'Semoule au lait bio', 'category' => 'dessert', 'allergens' => ['lait', 'gluten'], 'vegetarian' => true],
            ['name' => 'Fromage blanc coulis fruits rouges bio', 'category' => 'dessert', 'allergens' => ['lait'], 'vegetarian' => true],
            ['name' => 'Fourme d\'Ambert AOP', 'category' => 'dessert', 'allergens' => ['lait'], 'vegetarian' => true],
        ];
    }
}
