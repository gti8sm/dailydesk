<?php

/**
 * Available modules registry.
 * Add a new module here and it will automatically appear in:
 * - Central module management (super admin)
 * - Tenant module management (admin)
 * - Dashboard cards
 * - Navigation
 */
return [
    'garderie' => [
        'label' => 'Garderie',
        'icon' => 'fas fa-child',
        'color' => 'blue',
        'description' => 'Gestion de la garderie matin et soir',
        'route' => 'garderie.index',
        'permission' => 'view_garderie',
    ],
    'cantine' => [
        'label' => 'Cantine',
        'icon' => 'fas fa-utensils',
        'color' => 'orange',
        'description' => 'Gestion des présences cantine et repas',
        'route' => 'cantine.index',
        'permission' => 'view_cantine',
    ],
    'stock' => [
        'label' => 'Stock',
        'icon' => 'fas fa-boxes-stacked',
        'color' => 'indigo',
        'description' => 'Gestion des stocks, lieux et mouvements',
        'route' => 'stock.items.index',
        'permission' => 'view_stock',
    ],
    'public_site' => [
        'label' => 'Site Public',
        'icon' => 'fas fa-globe',
        'color' => 'emerald',
        'description' => 'Site web public de la commune avec pages éditables et actualités',
        'route' => 'site.pages.index',
        'permission' => 'manage_public_site',
    ],
];
