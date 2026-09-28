@php
    $config = $block->config ?? [];
    $type = $block->block_type;
@endphp

<form action="{{ route('site.blocks.update', $block) }}" method="POST" class="space-y-4">
    @csrf @method('PUT')

    <!-- Title (all blocks) -->
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Titre du bloc</label>
        <input type="text" name="title" value="{{ old('title', $block->title) }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
    </div>

    @if($type === 'hero')
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Titre</label>
                <input type="text" name="config[title]" value="{{ $config['title'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Sous-titre</label>
                <input type="text" name="config[subtitle]" value="{{ $config['subtitle'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Image de fond (URL)</label>
                <input type="text" name="config[image_url]" value="{{ $config['image_url'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bouton (texte)</label>
                <input type="text" name="config[cta_label]" value="{{ $config['cta_label'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bouton (URL)</label>
                <input type="text" name="config[cta_url]" value="{{ $config['cta_url'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
        </div>

    @elseif($type === 'cantine-menus')
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Décalage mois (0 = courant)</label>
                <input type="number" name="config[month_offset]" value="{{ $config['month_offset'] ?? 0 }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Limite (nb de jours)</label>
                <input type="number" name="config[limit]" value="{{ $config['limit'] ?? 10 }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
        </div>

    @elseif($type === 'news-list')
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Limite</label>
                <input type="number" name="config[limit]" value="{{ $config['limit'] ?? 4 }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Affichage</label>
                <select name="config[layout]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="grid" @if(($config['layout'] ?? '') === 'grid') selected @endif>Grille</option>
                    <option value="carousel" @if(($config['layout'] ?? '') === 'carousel') selected @endif>Carousel</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="config[show_image]" value="1" @if($config['show_image'] ?? true) checked @endif
                           class="rounded border-gray-300 text-emerald-600">
                    <span class="text-sm text-gray-700">Afficher les images</span>
                </label>
            </div>
        </div>

    @elseif($type === 'events-upcoming')
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Limite</label>
                <input type="number" name="config[limit]" value="{{ $config['limit'] ?? 4 }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="col-span-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="config[show_image]" value="1" @if($config['show_image'] ?? true) checked @endif
                           class="rounded border-gray-300 text-emerald-600">
                    <span class="text-sm text-gray-700">Afficher les images</span>
                </label>
            </div>
        </div>

    @elseif($type === 'weather')
        <div>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="config[show_forecast]" value="1" @if($config['show_forecast'] ?? true) checked @endif
                       class="rounded border-gray-300 text-emerald-600">
                <span class="text-sm text-gray-700">Afficher les prévisions (3 jours)</span>
            </label>
        </div>

    @elseif($type === 'social')
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Facebook (URL)</label>
                <input type="text" name="config[facebook_url]" value="{{ $config['facebook_url'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="https://facebook.com/...">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Instagram (URL)</label>
                <input type="text" name="config[instagram_url]" value="{{ $config['instagram_url'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="https://instagram.com/...">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Twitter/X (URL)</label>
                <input type="text" name="config[twitter_url]" value="{{ $config['twitter_url'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="https://twitter.com/...">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">YouTube (URL)</label>
                <input type="text" name="config[youtube_url]" value="{{ $config['youtube_url'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="https://youtube.com/...">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Style</label>
                <select name="config[style]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="rounded" @if(($config['style'] ?? '') === 'rounded') selected @endif>Rond</option>
                    <option value="square" @if(($config['style'] ?? '') === 'square') selected @endif>Carré</option>
                </select>
            </div>
        </div>

    @elseif($type === 'hours')
        @php
            $days = ['monday' => 'Lundi', 'tuesday' => 'Mardi', 'wednesday' => 'Mercredi', 'thursday' => 'Jeudi', 'friday' => 'Vendredi', 'saturday' => 'Samedi', 'sunday' => 'Dimanche'];
        @endphp
        <div class="space-y-2">
            @foreach($days as $dayKey => $dayLabel)
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-sm font-medium text-gray-700 w-24">{{ $dayLabel }}</span>
                @php $dayConfig = $config[$dayKey] ?? null; @endphp
                <input type="time" name="config[{{ $dayKey }}][open]" value="{{ $dayConfig['open'] ?? '' }}"
                       class="px-2 py-1 border border-gray-300 rounded text-sm" placeholder="Ouverture">
                <input type="time" name="config[{{ $dayKey }}][close]" value="{{ $dayConfig['close'] ?? '' }}"
                       class="px-2 py-1 border border-gray-300 rounded text-sm" placeholder="Fermeture AM">
                <input type="time" name="config[{{ $dayKey }}][open2]" value="{{ $dayConfig['open2'] ?? '' }}"
                       class="px-2 py-1 border border-gray-300 rounded text-sm" placeholder="Ouverture PM">
                <input type="time" name="config[{{ $dayKey }}][close2]" value="{{ $dayConfig['close2'] ?? '' }}"
                       class="px-2 py-1 border border-gray-300 rounded text-sm" placeholder="Fermeture PM">
            </div>
            @endforeach
        </div>

    @elseif($type === 'procedures')
        <div x-data="{ items: @js(array_values($config['items'] ?? [])) }">
            <p class="text-xs text-gray-500 mb-3">Configurez les démarches proposées. Laissez l'URL vide pour une démarche à effectuer en mairie.</p>
            <template x-for="(item, index) in items" :key="index">
                <div class="bg-gray-50 rounded-lg p-3 mb-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input type="text" :name="`config[items][${index}][label]`" x-model="item.label" placeholder="Libellé * (ex: Carte d'identité)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <input type="url" :name="`config[items][${index}][url]`" x-model="item.url" placeholder="URL (https://www.service-public.fr/...)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <input type="text" :name="`config[items][${index}][icon]`" x-model="item.icon" placeholder="Icône Font Awesome (ex: fas fa-id-card)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <input type="text" :name="`config[items][${index}][category]`" x-model="item.category" placeholder="Catégorie (ex: État civil)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <button type="button" @click="items.splice(index, 1)"
                            class="mt-2 text-red-600 hover:text-red-800 text-xs font-medium">
                        <i class="fas fa-trash mr-1"></i>Supprimer cette démarche
                    </button>
                </div>
            </template>
            <button type="button" @click="items.push({ label: '', url: '', icon: '', category: '' })"
                    class="text-sm text-emerald-600 hover:text-emerald-800 font-medium">
                <i class="fas fa-plus mr-1"></i>Ajouter une démarche
            </button>
        </div>

    @elseif($type === 'documents')
        <div x-data="{ items: @js(array_values($config['items'] ?? [])) }">
            <p class="text-xs text-gray-500 mb-3">Listez les documents téléchargeables (PDF). Laissez l'URL vide si le document n'est pas encore disponible.</p>
            <template x-for="(item, index) in items" :key="index">
                <div class="bg-gray-50 rounded-lg p-3 mb-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input type="text" :name="`config[items][${index}][label]`" x-model="item.label" placeholder="Libellé * (ex: Bulletin municipal n°12)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <input type="url" :name="`config[items][${index}][url]`" x-model="item.url" placeholder="URL du document (https://...)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <input type="text" :name="`config[items][${index}][category]`" x-model="item.category" placeholder="Catégorie (ex: Bulletins)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <input type="date" :name="`config[items][${index}][date]`" x-model="item.date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <button type="button" @click="items.splice(index, 1)"
                            class="mt-2 text-red-600 hover:text-red-800 text-xs font-medium">
                        <i class="fas fa-trash mr-1"></i>Supprimer ce document
                    </button>
                </div>
            </template>
            <button type="button" @click="items.push({ label: '', url: '', category: '', date: '' })"
                    class="text-sm text-emerald-600 hover:text-emerald-800 font-medium">
                <i class="fas fa-plus mr-1"></i>Ajouter un document
            </button>
        </div>

    @elseif($type === 'council')
        <div x-data="{ members: @js(array_values($config['members'] ?? [])) }">
            <p class="text-xs text-gray-500 mb-3">Renseignez les membres du conseil municipal (nom, fonction, délégations).</p>
            <template x-for="(member, index) in members" :key="index">
                <div class="bg-gray-50 rounded-lg p-3 mb-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input type="text" :name="`config[members][${index}][name]`" x-model="member.name" placeholder="Nom complet *"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <input type="text" :name="`config[members][${index}][role]`" x-model="member.role" placeholder="Fonction (ex: Maire, 1er adjoint)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <input type="url" :name="`config[members][${index}][photo_url]`" x-model="member.photo_url" placeholder="URL photo (optionnel)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <input type="text" :name="`config[members][${index}][delegation]`" x-model="member.delegation" placeholder="Délégations (ex: Finances, Voirie)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <button type="button" @click="members.splice(index, 1)"
                            class="mt-2 text-red-600 hover:text-red-800 text-xs font-medium">
                        <i class="fas fa-trash mr-1"></i>Supprimer ce membre
                    </button>
                </div>
            </template>
            <button type="button" @click="members.push({ name: '', role: '', photo_url: '', delegation: '' })"
                    class="text-sm text-emerald-600 hover:text-emerald-800 font-medium">
                <i class="fas fa-plus mr-1"></i>Ajouter un membre
            </button>
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Lien vers les délibérations / comptes-rendus (URL)</label>
                <input type="url" name="config[deliberations_url]" value="{{ $config['deliberations_url'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="https://...">
            </div>
        </div>

    @elseif($type === 'contact')
        <p class="text-xs text-gray-500 mb-3">Laissez les champs vides pour utiliser les coordonnées de la mairie définies dans les paramètres.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Adresse (remplace celle de la mairie)</label>
                <input type="text" name="config[address_override]" value="{{ $config['address_override'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Téléphone</label>
                <input type="text" name="config[phone_override]" value="{{ $config['phone_override'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Email</label>
                <input type="text" name="config[email_override]" value="{{ $config['email_override'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Lien Google Maps / plan (URL)</label>
                <input type="text" name="config[map_url]" value="{{ $config['map_url'] ?? '' }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="https://maps.google.com/...">
            </div>
            <div class="col-span-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="config[show_form]" value="1" @if($config['show_form'] ?? true) checked @endif
                           class="rounded border-gray-300 text-emerald-600">
                    <span class="text-sm text-gray-700">Afficher le formulaire de contact</span>
                </label>
            </div>
        </div>

    @elseif($type === 'grapesjs')
        <div class="bg-gray-50 rounded-lg p-4 text-center">
            <p class="text-sm text-gray-600 mb-3">Cliquez sur le bouton ci-dessous pour ouvrir l'éditeur visuel GrapesJS.</p>
            <button type="button" @click="$root.closest('[x-data]').__x.$data.openGrapesjs({{ $block->id }}, '{{ addslashes($block->title) }}')"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                <i class="fas fa-pen-fancy mr-2"></i>Ouvrir GrapesJS
            </button>
        </div>
    @endif

    <div class="flex justify-end pt-2">
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
            <i class="fas fa-save mr-2"></i>Enregistrer
        </button>
    </div>
</form>
