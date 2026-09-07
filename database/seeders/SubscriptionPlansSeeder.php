<?php

namespace Database\Seeders;

use App\Models\Central\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price_monthly' => 49.00,
                'price_yearly' => 490.00,
                'max_children' => 50,
                'modules' => ['garderie'],
                'features' => [
                    'Gestion garderie',
                    'Jusqu\'à 50 enfants',
                    'Support par email',
                    'Rapports basiques',
                    'Stockage 1 Go',
                ],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'price_monthly' => 99.00,
                'price_yearly' => 990.00,
                'max_children' => 150,
                'modules' => ['garderie', 'cantine'],
                'features' => [
                    'Gestion garderie + cantine',
                    'Jusqu\'à 150 enfants',
                    'Support prioritaire (email + téléphone)',
                    'Rapports avancés',
                    'Exports Excel/PDF',
                    'Facturation automatique',
                    'Stockage 5 Go',
                    'API d\'intégration',
                ],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'price_monthly' => 199.00,
                'price_yearly' => 1990.00,
                'max_children' => null, // Illimité
                'modules' => ['garderie', 'cantine', 'communication'],
                'features' => [
                    'Tous les modules',
                    'Enfants illimités',
                    'Support dédié 24/7',
                    'Personnalisation complète (logo, couleurs)',
                    'Domaine personnalisé',
                    'Formation sur mesure',
                    'Rapports personnalisés',
                    'Stockage illimité',
                    'API complète',
                    'Priorité sur les nouvelles fonctionnalités',
                ],
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
