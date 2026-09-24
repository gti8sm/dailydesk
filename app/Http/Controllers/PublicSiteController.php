<?php

namespace App\Http\Controllers;

use App\Models\PublicSitePage;
use App\Models\PublicSiteNews;
use App\Models\School;
use App\Modules\Cantine\Models\CantineMenu;
use Illuminate\Http\Request;

class PublicSiteController extends Controller
{
    public function index(Request $request)
    {
        $tenant = tenant();

        if (!$tenant) {
            abort(404);
        }

        $pages = PublicSitePage::published()->ordered()->get();
        $news = PublicSiteNews::published()->latestFirst()->take(4)->get();
        $schools = School::active()->orderBy('name')->get();

        // Menus cantine publiés du mois en cours
        $now = now();
        $menus = CantineMenu::published()
            ->withoutGlobalScope('tenant')
            ->whereYear('menu_date', $now->year)
            ->whereMonth('menu_date', $now->month)
            ->orderBy('menu_date')
            ->get()
            ->groupBy(fn($m) => $m->menu_date->format('Y-m-d'));

        // Page d'accueil éditable (slug = accueil ou première page publiée)
        $homePage = $pages->firstWhere('slug', 'accueil') ?? $pages->first();

        return view('public-site.index', compact('tenant', 'pages', 'news', 'schools', 'menus', 'homePage'));
    }

    public function page(Request $request, string $pageSlug)
    {
        $tenant = tenant();
        if (!$tenant) {
            abort(404);
        }

        $page = PublicSitePage::published()->where('slug', $pageSlug)->firstOrFail();
        $pages = PublicSitePage::published()->ordered()->get();

        return view('public-site.page', compact('tenant', 'page', 'pages'));
    }

    public function news(Request $request)
    {
        $tenant = tenant();
        if (!$tenant) {
            abort(404);
        }

        $news = PublicSiteNews::published()->latestFirst()->paginate(9);
        $pages = PublicSitePage::published()->ordered()->get();

        return view('public-site.news', compact('tenant', 'news', 'pages'));
    }

    public function newsShow(Request $request, string $newsSlug)
    {
        $tenant = tenant();
        if (!$tenant) {
            abort(404);
        }

        $item = PublicSiteNews::published()->where('slug', $newsSlug)->firstOrFail();
        $pages = PublicSitePage::published()->ordered()->get();

        return view('public-site.news-show', compact('tenant', 'item', 'pages'));
    }

    public function menus(Request $request)
    {
        $tenant = tenant();
        if (!$tenant) {
            abort(404);
        }

        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $menus = CantineMenu::published()
            ->withoutGlobalScope('tenant')
            ->whereYear('menu_date', $year)
            ->whereMonth('menu_date', $month)
            ->orderBy('menu_date')
            ->get()
            ->groupBy(fn($m) => $m->menu_date->format('Y-m-d'));

        $pages = PublicSitePage::published()->ordered()->get();
        $monthName = ucfirst(now()->create($year, $month, 1)->locale('fr')->monthName);

        return view('public-site.menus', compact('tenant', 'menus', 'year', 'month', 'monthName', 'pages'));
    }

    public function schools(Request $request)
    {
        $tenant = tenant();
        if (!$tenant) {
            abort(404);
        }

        $schools = School::active()->orderBy('name')->get();
        $pages = PublicSitePage::published()->ordered()->get();

        return view('public-site.schools', compact('tenant', 'schools', 'pages'));
    }
}
