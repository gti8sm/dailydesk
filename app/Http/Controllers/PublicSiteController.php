<?php

namespace App\Http\Controllers;

use App\Models\PublicSitePage;
use App\Models\PublicSiteNews;
use App\Models\PublicSiteBlock;
use App\Models\PublicSiteEvent;
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

        // Blocs de la page d'accueil (page_id = null), filtrés par modules activés
        $blocks = PublicSiteBlock::whereNull('page_id')->published()->ordered()->get()
            ->filter(fn($block) => $block->isModuleEnabled());

        // Fallback : si aucun bloc, on garde l'ancien rendu
        $hasBlocks = $blocks->count() > 0;

        if (!$hasBlocks) {
            $news = PublicSiteNews::published()->latestFirst()->take(4)->get();
            $schools = School::active()->orderBy('name')->get();

            $now = now();
            $menus = CantineMenu::published()
                ->withoutGlobalScope('tenant')
                ->whereYear('menu_date', $now->year)
                ->whereMonth('menu_date', $now->month)
                ->orderBy('menu_date')
                ->get()
                ->groupBy(fn($m) => $m->menu_date->format('Y-m-d'));

            $homePage = $pages->firstWhere('slug', 'accueil') ?? $pages->first();

            return view('public-site.index', compact('tenant', 'pages', 'news', 'schools', 'menus', 'homePage'));
        }

        // Rendu par blocs
        $renderedBlocks = $blocks->map(fn($block) => [
            'html' => $block->render(),
            'width_class' => $block->width_class,
        ]);

        return view('public-site.index-blocks', compact('tenant', 'pages', 'renderedBlocks'));
    }

    public function page(Request $request, string $pageSlug)
    {
        $tenant = tenant();
        if (!$tenant) {
            abort(404);
        }

        $page = PublicSitePage::published()->where('slug', $pageSlug)->firstOrFail();
        $pages = PublicSitePage::published()->ordered()->get();

        // Blocs de cette page, filtrés par modules activés
        $blocks = PublicSiteBlock::forPage($page->id)->published()->ordered()->get()
            ->filter(fn($block) => $block->isModuleEnabled());

        if ($blocks->count() > 0) {
            $renderedBlocks = $blocks->map(fn($block) => [
                'html' => $block->render(),
                'width_class' => $block->width_class,
            ]);
            return view('public-site.page-blocks', compact('tenant', 'page', 'pages', 'renderedBlocks'));
        }

        // Fallback : ancien rendu avec content TinyMCE
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

    public function events(Request $request)
    {
        $tenant = tenant();
        if (!$tenant) {
            abort(404);
        }

        $today = now()->startOfDay();

        $events = PublicSiteEvent::published()
            ->where(function ($q) use ($today) {
                // Upcoming single events or first occurrence of recurring events
                $q->where('starts_at', '>=', $today);
                // Or recurring events that still have future occurrences
                $q->orWhere(function ($q2) use ($today) {
                    $q2->where('recurrence_type', '!=', 'none')
                       ->where(function ($q3) use ($today) {
                           $q3->whereNull('recurrence_end_date')
                              ->orWhere('recurrence_end_date', '>=', $today);
                       });
                });
            })
            ->orderBy('starts_at')
            ->paginate(9);

        $pages = PublicSitePage::published()->ordered()->get();

        return view('public-site.events', compact('tenant', 'events', 'pages'));
    }

    public function eventShow(Request $request, string $eventSlug)
    {
        $tenant = tenant();
        if (!$tenant) {
            abort(404);
        }

        $event = PublicSiteEvent::published()->where('slug', $eventSlug)->firstOrFail();
        $pages = PublicSitePage::published()->ordered()->get();

        return view('public-site.event-show', compact('tenant', 'event', 'pages'));
    }
}
