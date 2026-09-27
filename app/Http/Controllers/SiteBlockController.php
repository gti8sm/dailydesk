<?php

namespace App\Http\Controllers;

use App\Models\PublicSiteBlock;
use App\Models\PublicSitePage;
use Illuminate\Http\Request;

class SiteBlockController extends Controller
{
    public function index(Request $request, $pageId = null)
    {
        $this->authorizeAccess();

        $page = null;
        if ($pageId) {
            $page = PublicSitePage::findOrFail($pageId);
            $blocks = PublicSiteBlock::forPage($page->id)->ordered()->get();
        } else {
            $blocks = PublicSiteBlock::whereNull('page_id')->ordered()->get();
        }

        $blockTypes = config('public-site-blocks', []);

        return view('site.pages.builder', compact('blocks', 'page', 'blockTypes'));
    }

    public function store(Request $request, $pageId = null)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'block_type' => 'required|string',
            'title' => 'nullable|string|max:255',
            'width' => 'nullable|string|in:full,half,third,two-thirds,quarter',
        ]);

        $blockType = $validated['block_type'];
        $blockConfig = config("public-site-blocks.{$blockType}");

        if (!$blockConfig) {
            return back()->with('error', 'Type de bloc inconnu.');
        }

        $maxOrder = PublicSiteBlock::whereNull('page_id')
            ->when($pageId, fn($q) => $q->orWhere('page_id', $pageId))
            ->max('sort_order') ?? 0;

        $block = PublicSiteBlock::create([
            'page_id' => $pageId,
            'block_type' => $blockType,
            'title' => $validated['title'] ?? $blockConfig['label'],
            'config' => $blockConfig['default_config'] ?? [],
            'width' => $validated['width'] ?? 'full',
            'is_published' => true,
            'sort_order' => $maxOrder + 1,
        ]);

        return back()->with('success', "Bloc '{$blockConfig['label']}' ajouté.");
    }

    public function update(Request $request, PublicSiteBlock $block)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'width' => 'nullable|string|in:full,half,third,two-thirds,quarter',
            'is_published' => 'nullable|boolean',
            'config' => 'nullable|array',
        ]);

        if ($request->has('title')) {
            $block->title = $validated['title'];
        }
        if ($request->has('width')) {
            $block->width = $validated['width'];
        }
        if ($request->has('is_published')) {
            $block->is_published = $request->boolean('is_published');
        } elseif ($request->has('toggle_published')) {
            $block->is_published = !$block->is_published;
        }
        if ($request->has('config')) {
            $block->config = $validated['config'];
        }

        $block->save();

        return back()->with('success', 'Bloc mis à jour.');
    }

    public function updateOrder(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:public_site_blocks,id',
        ]);

        foreach ($validated['order'] as $index => $blockId) {
            PublicSiteBlock::where('id', $blockId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    public function saveGrapesjs(Request $request, PublicSiteBlock $block)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'project' => 'nullable|string',
            'html' => 'nullable|string',
            'css' => 'nullable|string',
        ]);

        $block->grapesjs_project = $validated['project'] ?? null;
        $block->content_html = $validated['html'] ?? null;
        $block->content_css = $validated['css'] ?? null;
        $block->save();

        return response()->json(['success' => true]);
    }

    public function loadGrapesjs(PublicSiteBlock $block)
    {
        $this->authorizeAccess();

        return response()->json([
            'project' => $block->grapesjs_project,
            'html' => $block->content_html,
            'css' => $block->content_css,
        ]);
    }

    public function destroy(PublicSiteBlock $block)
    {
        $this->authorizeAccess();

        $block->delete();

        return back()->with('success', 'Bloc supprimé.');
    }

    private function authorizeAccess(): void
    {
        if (!auth()->user()->can('manage_public_site')) {
            abort(403, 'Accès non autorisé');
        }
    }
}
