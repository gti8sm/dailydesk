<x-mail::message>
# Code de connexion

Bonjour {{ $user->name }},

Voici votre code de vérification pour vous connecter à DailyDesk :

<x-mail::panel>
# {{ $code }}
</x-mail::panel>

Ce code est valable **10 minutes**. Si vous n'êtes pas à l'origine de cette tentative de connexion, contactez rapidement votre administrateur.

Merci,
L'équipe DailyDesk
</x-mail::message>
