<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
    try {
        $appName = \App\Models\Setting::get('app_name', config('app.name', 'DailyDesk'));
    } catch (\Exception $e) {
        $appName = config('app.name', 'DailyDesk');
    }
@endphp
    <title>Merci — {{ $appName }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-gradient { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #6366f1 100%); }
        @keyframes check { 0% { transform: scale(0); } 50% { transform: scale(1.2); } 100% { transform: scale(1); } }
        .check-anim { animation: check 0.5s ease-out; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">

<div class="max-w-lg w-full">
    <div class="bg-white rounded-2xl shadow-2xl p-10 text-center">
        <div class="check-anim mx-auto bg-green-100 rounded-full w-20 h-20 flex items-center justify-center mb-6">
            <i class="fas fa-check text-green-600 text-4xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Merci pour votre demande !</h1>
        <p class="text-gray-600 text-lg mb-6">
            Votre demande de démo a bien été enregistrée. Notre équipe vous contactera
            dans les plus brefs délais pour configurer votre espace et vous accompagner.
        </p>
        <div class="bg-blue-50 rounded-xl p-6 mb-6 text-left">
            <h3 class="font-semibold text-gray-900 mb-3 text-sm">Prochaines étapes :</h3>
            <ol class="space-y-3 text-sm text-gray-600">
                <li class="flex items-start">
                    <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 flex-shrink-0 mt-0.5">1</span>
                    Notre équipe vérifie votre demande et vous contacte par email ou téléphone.
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 flex-shrink-0 mt-0.5">2</span>
                    Nous configurons votre espace {{ $appName }} selon vos besoins.
                </li>
                <li class="flex items-start">
                    <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 flex-shrink-0 mt-0.5">3</span>
                    Vous accédez à votre démo de 30 jours, sans engagement.
                </li>
            </ol>
        </div>
        <a href="{{ route('landing') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-medium transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Retour à l'accueil
        </a>
    </div>
    <p class="text-center text-sm text-gray-400 mt-6">
        &copy; {{ date('Y') }} {{ $appName }} — DailyDesk
    </p>
</div>

</body>
</html>
