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

        $content = view('sitemap.xml', compact('urls'))->render();

        return response($content, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
