<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $tenant->name)</title>
    <meta name="description" content="@yield('meta_description', 'Site officiel de ' . $tenant->name)">
    <meta property="og:title" content="@yield('og_title', $tenant->name)">
    <meta property="og:description" content="@yield('meta_description', 'Site officiel de ' . $tenant->name)">
    <meta property="og:type" content="website">
    @if($tenant->logo_path)
    <meta property="og:image" content="{{ asset('storage/' . $tenant->logo_path) }}">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '{{ $tenant->primary_color ?? '#3B82F6' }}',
                        secondary: '{{ $tenant->secondary_color ?? '#6366F1' }}',
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        .prose-public h1 { font-size: 2rem; font-weight: 700; margin-bottom: 1rem; }
        .prose-public h2 { font-size: 1.5rem; font-weight: 600; margin-top: 1.5rem; margin-bottom: 0.75rem; }
        .prose-public h3 { font-size: 1.25rem; font-weight: 600; margin-top: 1.25rem; margin-bottom: 0.5rem; }
        .prose-public p { margin-bottom: 1rem; line-height: 1.7; }
        .prose-public ul { list-style: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
        .prose-public ol { list-style: decimal; padding-left: 1.5rem; margin-bottom: 1rem; }
        .prose-public a { color: {{ $tenant->primary_color ?? '#3B82F6' }}; text-decoration: underline; }
        .prose-public img { max-width: 100%; height: auto; border-radius: 0.5rem; margin: 1rem 0; }
        .prose-public blockquote { border-left: 4px solid {{ $tenant->primary_color ?? '#3B82F6' }}; padding-left: 1rem; font-style: italic; margin: 1rem 0; color: #4b5563; }
        .prose-public table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .prose-public th, .prose-public td { border: 1px solid #e5e7eb; padding: 0.5rem 0.75rem; text-align: left; }
        .prose-public th { background-color: #f9fafb; font-weight: 600; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo + Nom -->
                <a href="{{ route('public.site', ['tenant' => $tenant->slug]) }}" class="flex items-center gap-3">
                    @if($tenant->logo_path)
                    <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="{{ $tenant->name }}" class="h-10 w-10 rounded-lg object-cover">
                    @else
                    <div class="bg-primary rounded-lg w-10 h-10 flex items-center justify-center">
                        <i class="fas fa-building text-white text-lg"></i>
                    </div>
                    @endif
                    <span class="font-bold text-gray-900 text-lg">{{ $tenant->name }}</span>
                </a>

                <!-- Nav desktop -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('public.site', ['tenant' => $tenant->slug]) }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary rounded-lg hover:bg-gray-100">Accueil</a>
                    @foreach($pages->where('slug', '!=', 'accueil') as $page)
                    <a href="{{ route('public.site.page', ['tenant' => $tenant->slug, 'pageSlug' => $page->slug]) }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary rounded-lg hover:bg-gray-100">{{ $page->title }}</a>
                    @endforeach
                    <a href="{{ route('public.site.news', ['tenant' => $tenant->slug]) }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary rounded-lg hover:bg-gray-100">Actualités</a>
                    <a href="{{ route('public.site.menus', ['tenant' => $tenant->slug]) }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary rounded-lg hover:bg-gray-100">Menus</a>
                    <a href="{{ route('public.site.schools', ['tenant' => $tenant->slug]) }}" class="px-3 py-2 text-sm font-medium text-gray-700 hover:text-primary rounded-lg hover:bg-gray-100">Écoles</a>
                </nav>

                <!-- Burger mobile -->
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Nav mobile -->
        <div x-show="mobileOpen" x-cloak class="md:hidden border-t border-gray-200 bg-white">
            <div class="px-4 py-2 space-y-1">
                <a href="{{ route('public.site', ['tenant' => $tenant->slug]) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Accueil</a>
                @foreach($pages->where('slug', '!=', 'accueil') as $page)
                <a href="{{ route('public.site.page', ['tenant' => $tenant->slug, 'pageSlug' => $page->slug]) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">{{ $page->title }}</a>
                @endforeach
                <a href="{{ route('public.site.news', ['tenant' => $tenant->slug]) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Actualités</a>
                <a href="{{ route('public.site.menus', ['tenant' => $tenant->slug]) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Menus</a>
                <a href="{{ route('public.site.schools', ['tenant' => $tenant->slug]) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Écoles</a>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        @if($tenant->logo_path)
                        <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="{{ $tenant->name }}" class="h-8 w-8 rounded-lg object-cover">
                        @else
                        <div class="bg-primary rounded-lg w-8 h-8 flex items-center justify-center">
                            <i class="fas fa-building text-white text-sm"></i>
                        </div>
                        @endif
                        <span class="font-bold text-white text-lg">{{ $tenant->name }}</span>
                    </div>
                    <p class="text-sm text-gray-400">Site officiel de la commune</p>
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">Contact</h4>
                    @if($tenant->address)
                    <p class="text-sm text-gray-400 mb-1"><i class="fas fa-map-marker-alt mr-2"></i>{{ $tenant->address }}</p>
                    @endif
                    @if($tenant->postal_code || $tenant->city)
                    <p class="text-sm text-gray-400 mb-1"><i class="fas fa-location-dot mr-2"></i>{{ $tenant->postal_code }} {{ $tenant->city }}</p>
                    @endif
                    @if($tenant->phone)
                    <p class="text-sm text-gray-400 mb-1"><i class="fas fa-phone mr-2"></i>{{ $tenant->phone }}</p>
                    @endif
                    @if($tenant->email)
                    <p class="text-sm text-gray-400 mb-1"><i class="fas fa-envelope mr-2"></i>{{ $tenant->email }}</p>
                    @endif
                </div>
                <div>
                    <h4 class="font-semibold text-white mb-3">Navigation</h4>
                    <ul class="space-y-1 text-sm">
                        <li><a href="{{ route('public.site', ['tenant' => $tenant->slug]) }}" class="text-gray-400 hover:text-white">Accueil</a></li>
                        <li><a href="{{ route('public.site.news', ['tenant' => $tenant->slug]) }}" class="text-gray-400 hover:text-white">Actualités</a></li>
                        <li><a href="{{ route('public.site.menus', ['tenant' => $tenant->slug]) }}" class="text-gray-400 hover:text-white">Menus cantine</a></li>
                        <li><a href="{{ route('public.site.schools', ['tenant' => $tenant->slug]) }}" class="text-gray-400 hover:text-white">Écoles</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-6 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} {{ $tenant->name }} — Site propulsé par <a href="{{ route('landing') }}" class="text-gray-400 hover:text-white">DailyDesk</a></p>
            </div>
        </div>
    </footer>
</body>
</html>
