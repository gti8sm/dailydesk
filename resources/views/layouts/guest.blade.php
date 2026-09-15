<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'DailyDesk'))</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    @php
        $appName = config('app.name', 'DailyDesk');
        try {
            $appName = \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));
        } catch (\Exception $e) {}
    @endphp

    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-utensils text-green-600 text-xl"></i>
                    <span class="font-bold text-gray-900 text-lg">{{ $appName }}</span>
                </div>
                @auth
                @php
                    $tenantSlug = function_exists('tenant') && tenant() ? tenant()->slug : null;
                    $userTenant = auth()->user()->tenant_id ? \App\Models\Tenant::find(auth()->user()->tenant_id)?->slug : null;
                    $slug = $tenantSlug ?? $userTenant;
                @endphp
                @if($slug)
                <a href="/{{ $slug }}/dashboard" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-home mr-1"></i>Mon espace
                </a>
                @endif
                @endauth
            </div>
        </header>

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-200 py-4">
            <div class="max-w-7xl mx-auto px-4 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ $appName }}
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
