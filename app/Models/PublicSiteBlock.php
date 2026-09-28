<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicSiteBlock extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, LogsActivity;

    protected $table = 'public_site_blocks';

    protected $fillable = [
        'page_id',
        'block_type',
        'title',
        'config',
        'grapesjs_project',
        'content_html',
        'content_css',
        'width',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'config' => 'array',
        'is_published' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(PublicSitePage::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeForPage($query, $pageId)
    {
        return $query->where('page_id', $pageId);
    }

    /**
     * Map la largeur vers une classe Tailwind col-span-*.
     */
    public function getWidthClassAttribute(): string
    {
        return match ($this->width) {
            'half' => 'lg:col-span-6',
            'third' => 'lg:col-span-4',
            'two-thirds' => 'lg:col-span-8',
            'quarter' => 'lg:col-span-3',
            default => 'lg:col-span-12',
        };
    }

    /**
     * Vérifie que le module requis par ce bloc est activé chez le tenant courant.
     */
    public function isModuleEnabled(): bool
    {
        $blockConfig = config("public-site-blocks.{$this->block_type}");

        if (!$blockConfig) {
            return false;
        }

        if (!isset($blockConfig['requires_module'])) {
            return true;
        }

        $tenant = tenant();

        return $tenant && in_array($blockConfig['requires_module'], $tenant->modules_enabled ?? []);
    }

    /**
     * Rend le bloc en appelant le partial Blade correspondant.
     */
    public function render(): string
    {
        if (!$this->isModuleEnabled()) {
            return '';
        }

        $blockType = $this->block_type;

        if ($blockType === 'grapesjs') {
            $css = $this->content_css ? '<style>' . $this->content_css . '</style>' : '';
            return $css . ($this->content_html ?? '');
        }

        $view = 'public-site.blocks._' . $blockType;
        if (!view()->exists($view)) {
            return '';
        }

        return view($view, ['block' => $this, 'config' => $this->config ?? []])->render();
    }
}
