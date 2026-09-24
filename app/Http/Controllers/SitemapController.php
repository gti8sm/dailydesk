<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            [
                'loc' => url('/accueil'),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => url('/accueil/merci'),
                'changefreq' => 'monthly',
                'priority' => '0.3',
            ],
            [
                'loc' => url('/cgv'),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'loc' => url('/mentions-legales'),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
            [
                'loc' => url('/rgpd'),
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
        ];

        // Sites publics des tenants actifs
        $tenants = \App\Models\Tenant::where('status', 'active')->get();
        foreach ($tenants as $tenant) {
            $urls[] = [
                'loc' => url('/' . $tenant->slug),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
            $urls[] = [
                'loc' => url('/' . $tenant->slug . '/actualites'),
                'changefreq' => 'daily',
                'priority' => '0.6',
            ];
            $urls[] = [
                'loc' => url('/' . $tenant->slug . '/menus'),
                'changefreq' => 'weekly',
                'priority' => '0.6',
            ];
            $urls[] = [
                'loc' => url('/' . $tenant->slug . '/ecoles'),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ];
        }

        $content = view('sitemap.xml', compact('urls'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
