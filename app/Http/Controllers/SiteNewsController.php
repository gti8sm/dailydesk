<?php

namespace App\Http\Controllers;

use App\Models\PublicSiteNews;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SiteNewsController extends Controller
{
    public function index()
    {
        $this->authorizeAccess();

        $news = PublicSiteNews::latestFirst()->paginate(15);

        return view('site.news.index', compact('news'));
    }

    public function create()
    {
        $this->authorizeAccess();

        return view('site.news.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        $validated['published_at'] = $validated['published_at'] ?? ($validated['is_published'] ? now() : null);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('news', 'public');
        }

        PublicSiteNews::create($validated);

        return redirect()->route('site.news.index')
            ->with('success', "Actualité '{$validated['title']}' créée avec succès.");
    }

    public function edit(PublicSiteNews $news)
    {
        $this->authorizeAccess();

        return view('site.news.edit', compact('news'));
    }

    public function update(Request $request, PublicSiteNews $news)
    {
        $this->authorizeAccess();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
            'published_at' => 'nullable|date',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');
        $validated['published_at'] = $validated['published_at'] ?? ($validated['is_published'] ? now() : null);

        if ($request->hasFile('image')) {
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('news', 'public');
        }

        $news->update($validated);

        return redirect()->route('site.news.index')
            ->with('success', "Actualité '{$news->title}' mise à jour avec succès.");
    }

    public function destroy(PublicSiteNews $news)
    {
        $this->authorizeAccess();

        $title = $news->title;
        if ($news->image_path) {
            Storage::disk('public')->delete($news->image_path);
        }
        $news->delete();

        return redirect()->route('site.news.index')
            ->with('success', "Actualité '{$title}' supprimée.");
    }

    private function authorizeAccess(): void
    {
        if (!auth()->user()->can('manage_public_site')) {
            abort(403, 'Accès non autorisé');
        }
    }
}
