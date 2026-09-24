<?php

namespace App\Http\Controllers;

use App\Models\PublicSitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SitePageController extends Controller
{
    public function index()
    {
        $this->authorizeAccess();

        $pages = PublicSitePage::orderBy('sort_order')->orderBy('title')->paginate(15);

        return view('site.pages.index', compact('pages'));
    }

    public function create()
    {
        $this->authorizeAccess();

        return view('site.pages.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'meta_description' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        PublicSitePage::create($validated);

        return redirect()->route('site.pages.index')
            ->with('success', "Page '{$validated['title']}' créée avec succès.");
    }

    public function edit(PublicSitePage $page)
    {
        $this->authorizeAccess();

        return view('site.pages.edit', compact('page'));
    }

    public function update(Request $request, PublicSitePage $page)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'meta_description' => 'nullable|string|max:255',
            'is_published' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $page->update($validated);

        return redirect()->route('site.pages.index')
            ->with('success', "Page '{$page->title}' mise à jour avec succès.");
    }

    public function destroy(PublicSitePage $page)
    {
        $this->authorizeAccess();

        $title = $page->title;
        $page->delete();

        return redirect()->route('site.pages.index')
            ->with('success', "Page '{$title}' supprimée.");
    }

    private function authorizeAccess(): void
    {
        if (!auth()->user()->can('manage_public_site')) {
            abort(403, 'Accès non autorisé');
        }
    }
}
