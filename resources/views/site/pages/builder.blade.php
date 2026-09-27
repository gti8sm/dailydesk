@extends('layouts.app')

@section('title', 'Builder — ' . ($page ? $page->title : 'Page d\'accueil'))

@section('content')
@php
    $previewUrl = $page
        ? route('public.site.page', ['tenant' => tenant()->slug, 'pageSlug' => $page->slug])
        : route('public.site', ['tenant' => tenant()->slug]);
    $storeUrl = $page
        ? route('site.blocks.store', ['page' => $page->id])
        : route('site.home.blocks.store');
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="blockBuilder()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <div>
            <a href="{{ route('site.pages.index') }}" class="text-emerald-600 hover:text-emerald-800 font-medium text-sm">
                <i class="fas fa-arrow-left mr-2"></i>Retour aux pages
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">
                <i class="fas fa-cubes text-emerald-600 mr-2"></i>Builder — {{ $page ? $page->title : 'Page d\'accueil' }}
            </h1>
            <p class="mt-1 text-sm text-gray-600">Glissez-déposez les blocs pour composer votre page publique.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ $previewUrl }}" target="_blank"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium text-sm">
                <i class="fas fa-eye mr-2"></i>Voir
            </a>
            <button @click="showAddModal = true" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                <i class="fas fa-plus mr-2"></i>Ajouter un bloc
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <p class="text-sm text-green-700">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <p class="text-sm text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Blocs -->
    <div id="blocks-list" class="space-y-3">
        @forelse($blocks as $block)
        <div class="block-card bg-white rounded-xl shadow-md p-4 flex items-start gap-4" data-id="{{ $block->id }}"
             x-data="{ editing: false }">
            <!-- Drag handle -->
            <div class="cursor-move text-gray-400 hover:text-gray-600 pt-2">
                <i class="fas fa-grip-vertical text-lg"></i>
            </div>

            <!-- Block info -->
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <div class="bg-emerald-100 rounded-lg w-10 h-10 flex items-center justify-center">
                        <i class="{{ $blockTypes[$block->block_type]['icon'] ?? 'fas fa-cube' }} text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $block->title }}</h3>
                        <p class="text-xs text-gray-500">{{ $blockTypes[$block->block_type]['label'] ?? $block->block_type }} · {{ $block->width }}</p>
                    </div>
                    @if(!$block->is_published)
                    <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Brouillon</span>
                    @endif
                </div>

                <!-- Edit form (toggled) -->
                <div x-show="editing" x-cloak class="mt-4 border-t border-gray-100 pt-4">
                    @include('site.pages._block-form', ['block' => $block, 'blockConfig' => $blockTypes[$block->block_type] ?? []])
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2">
                <!-- Width selector -->
                <select @change="updateWidth({{ $block->id }}, $event.target.value)"
                        class="text-xs border border-gray-300 rounded-lg px-2 py-1">
                    <option value="full" @if($block->width === 'full') selected @endif>Pleine largeur</option>
                    <option value="half" @if($block->width === 'half') selected @endif>1/2</option>
                    <option value="third" @if($block->width === 'third') selected @endif>1/3</option>
                    <option value="two-thirds" @if($block->width === 'two-thirds') selected @endif>2/3</option>
                    <option value="quarter" @if($block->width === 'quarter') selected @endif>1/4</option>
                </select>

                <!-- Toggle published -->
                <button @click="togglePublished({{ $block->id }})"
                        class="text-gray-400 hover:text-gray-600 p-2" title="{{ $block->is_published ? 'Masquer' : 'Publier' }}">
                    <i class="fas {{ $block->is_published ? 'fa-eye text-emerald-600' : 'fa-eye-slash' }}"></i>
                </button>

                <!-- Edit -->
                <button @click="editing = !editing" class="text-blue-600 hover:text-blue-800 p-2" title="Configurer">
                    <i class="fas fa-cog"></i>
                </button>

                <!-- Delete -->
                <form action="{{ route('site.blocks.destroy', $block) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce bloc ?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 p-2" title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-16 text-gray-400 bg-white rounded-xl shadow-md">
            <i class="fas fa-cubes text-6xl mb-4"></i>
            <p class="text-lg mb-2">Aucun bloc sur cette page.</p>
            <p class="text-sm">Cliquez sur "Ajouter un bloc" pour commencer.</p>
        </div>
        @endforelse
    </div>

    <!-- Add block modal -->
    <div x-show="showAddModal" x-cloak @keydown.escape.window="showAddModal = false"
         class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="fixed inset-0 bg-black/50" @click="showAddModal = false"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[80vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-gray-200 sticky top-0 bg-white rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900"><i class="fas fa-plus-circle text-emerald-600 mr-2"></i>Ajouter un bloc</h2>
                        <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="p-6">
                    @php
                        $categories = ['content' => 'Contenu', 'module' => 'Modules', 'widget' => 'Widgets'];
                        $grouped = collect($blockTypes)->groupBy("category", true);
                    @endphp
                    @foreach($categories as $catKey => $catLabel)
                    @if(isset($grouped[$catKey]))
                    <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3 mt-4 first:mt-0">{{ $catLabel }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 mb-6">
                        @foreach($grouped[$catKey] as $type => $config)
                        <form action="{{ $storeUrl }}" method="POST" class="block">
                            @csrf
                            <input type="hidden" name="block_type" value="{{ $type }}">
                            <button type="submit" class="w-full text-left bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-300 rounded-xl p-4 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <div class="bg-emerald-100 group-hover:bg-emerald-200 rounded-lg w-10 h-10 flex items-center justify-center transition-colors">
                                        <i class="{{ $config['icon'] }} text-emerald-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 text-sm">{{ $config['label'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $config['description'] }}</p>
                                    </div>
                                </div>
                            </button>
                        </form>
                        @endforeach
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- GrapesJS modal -->
    <div x-show="showGrapesjs" x-cloak class="fixed inset-0 z-50 bg-white">
        <div class="h-full flex flex-col">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                <h2 class="font-bold text-gray-900"><i class="fas fa-pen-fancy text-emerald-600 mr-2"></i>Éditeur de contenu — <span x-text="editingBlockTitle"></span></h2>
                <div class="flex gap-2">
                    <button @click="saveGrapesjs()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                    <button @click="closeGrapesjs()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium text-sm">
                        <i class="fas fa-times mr-2"></i>Fermer
                    </button>
                </div>
            </div>
            <div id="gjs-editor" class="flex-1"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
function blockBuilder() {
    return {
        showAddModal: false,
        showGrapesjs: false,
        editingBlockId: null,
        editingBlockTitle: '',
        grapesjsEditor: null,

        init() {
            this.$nextTick(() => {
                const list = document.getElementById('blocks-list');
                if (list) {
                    Sortable.create(list, {
                        handle: '.cursor-move',
                        animation: 150,
                        onEnd: (evt) => {
                            const order = Array.from(list.querySelectorAll('.block-card')).map(el => el.dataset.id);
                            fetch('{{ route("site.blocks.order") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({ order: order })
                            });
                        }
                    });
                }
            });
        },

        updateWidth(blockId, width) {
            fetch(`/site/blocks/${blockId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: `width=${width}&_method=PUT`
            }).then(() => location.reload());
        },

        togglePublished(blockId) {
            fetch(`/site/blocks/${blockId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: `toggle_published=1&_method=PUT`
            }).then(() => location.reload());
        },

        openGrapesjs(blockId, blockTitle) {
            this.editingBlockId = blockId;
            this.editingBlockTitle = blockTitle;
            this.showGrapesjs = true;

            this.$nextTick(() => {
                this.loadGrapesjsScript(blockId);
            });
        },

        loadGrapesjsScript(blockId) {
            if (this.grapesjsEditor) {
                this.grapesjsEditor.destroy();
                this.grapesjsEditor = null;
            }

            // Load GrapesJS dynamically
            const cssLink = document.createElement('link');
            cssLink.rel = 'stylesheet';
            cssLink.href = 'https://unpkg.com/grapesjs@0.21.10/dist/css/grapes.min.css';
            document.head.appendChild(cssLink);

            const script = document.createElement('script');
            script.src = 'https://unpkg.com/grapesjs@0.21.10';
            script.onload = () => {
                fetch(`/site/blocks/${blockId}/grapesjs/load`)
                    .then(r => r.json())
                    .then(data => {
                        this.grapesjsEditor = grapesjs.init({
                            container: '#gjs-editor',
                            height: '100%',
                            storageManager: false,
                            fromElement: false,
                            components: data.html || '',
                            style: data.css || '',
                        });
                    });
            };
            document.head.appendChild(script);
        },

        saveGrapesjs() {
            if (!this.grapesjsEditor || !this.editingBlockId) return;

            const project = JSON.stringify(this.grapesjsEditor.getProjectData());
            const html = this.grapesjsEditor.getHtml();
            const css = this.grapesjsEditor.getCss();

            fetch(`/site/blocks/${this.editingBlockId}/grapesjs`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: `project=${encodeURIComponent(project)}&html=${encodeURIComponent(html)}&css=${encodeURIComponent(css)}`
            }).then(() => {
                this.closeGrapesjs();
                location.reload();
            });
        },

        closeGrapesjs() {
            this.showGrapesjs = false;
            this.editingBlockId = null;
            if (this.grapesjsEditor) {
                this.grapesjsEditor.destroy();
                this.grapesjsEditor = null;
            }
        },
    }
}
</script>
@endsection
