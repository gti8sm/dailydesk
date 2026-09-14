<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trop de requêtes — DailyDesk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center">
        <div class="mb-8">
            <i class="fas fa-clock text-6xl text-orange-400"></i>
        </div>
        <h1 class="text-5xl font-extrabold text-gray-800 mb-4">429</h1>
        <p class="text-xl text-gray-600 mb-2">Trop de tentatives.</p>
        <p class="text-sm text-gray-400 mb-8">Vous avez effectué trop de requêtes. Veuillez patienter une minute avant de réessayer.</p>
        <a href="{{ url('/') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
            <i class="fas fa-home mr-2"></i> Retour à l'accueil
        </a>
    </div>
</body>
</html>
