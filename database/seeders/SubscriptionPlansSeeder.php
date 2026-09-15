<?php

namespace Database\Seeders;

use App\Models\Central\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Village',
                'slug' => 'village',
                'price_monthly' => 39.00,
                'price_yearly' => 390.00,
                'max_children' => null,
                'population_min' => 0,
                'population_max' => 999,
                'modules' => null,
                'features' => [
                    'Tous les modules inclus',
                    'Enfants illimités',
                    'Utilisateurs illimités',
                    'Support par email (48h)',
                    'Personnalisation logo + couleurs',
                    'Exports CSV',
                    'Tablette + mobile',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Petite commune',
                'slug' => 'petite-commune',
                'price_monthly' => 79.00,
                'price_yearly' => 790.00,
                'max_children' => null,
                'population_min' => 1000,
                'population_max' => 4999,
                'modules' => null,
                'features' => [
                    'Tous les modules inclus',
                    'Enfants illimités',
                    'Utilisateurs illimités',
                    'Support email + téléphone (24h)',
                    'Domaine personnalisé',
                    'Exports Excel/PDF',
                    'Tablette + mobile',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Commune moyenne',
                'slug' => 'commune-moyenne',
                'price_monthly' => 149.00,
                'price_yearly' => 1490.00,
                'max_children' => null,
                'population_min' => 5000,
                'population_max' => 19999,
                'modules' => null,
                'features' => [
                    'Tous les modules inclus',
                    'Enfants illimités',
                    'Utilisateurs illimités',
                    'Support prioritaire (jour J)',
                    'Domaine personnalisé',
                    'Multi-sites',
                    'Exports Excel/PDF avancés',
                    'API d\'intégration',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Grande commune',
                'slug' => 'grande-commune',
                'price_monthly' => 299.00,
                'price_yearly' => 2990.00,
                'max_children' => null,
                'population_min' => 20000,
                'population_max' => 99999,
                'modules' => null,
                'features' => [
                    'Tous les modules inclus',
                    'Enfants illimités',
                    'Utilisateurs illimités',
                    'Support dédié 7j/7 (4h)',
                    'Domaine personnalisé',
                    'Multi-sites',
                    'Formation sur mesure incluse',
                    'Rapports personnalisés',
                    'API prioritaire',
                ],
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Agglomération',
                'slug' => 'agglomeration',
                'price_monthly' => 0.00,
                'price_yearly' => 0.00,
                'max_children' => null,
                'population_min' => 100000,
                'population_max' => null,
                'modules' => null,
                'features' => [
                    'Tous les modules inclus',
                    'Enfants illimités',
                    'Utilisateurs illimités',
                    'Support sur mesure',
                    'Domaine personnalisé',
                    'Multi-sites illimités',
                    'Formation illimitée',
                    'Accompagnement dédié',
                    'Sur devis',
                ],
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }

        // Désactiver les anciens plans obsolètes
        SubscriptionPlan::whereIn('slug', ['starter', 'pro', 'premium'])->update(['is_active' => false]);
    }
}
