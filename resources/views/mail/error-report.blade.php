<x-mail::message>
# Erreur en production

Une erreur est survenue sur **DailyDesk**. Détails :

<x-mail::panel>
**Exception :** {{ $exceptionClass }}<br>
**Message :** {{ $message }}<br>
**Fichier :** {{ $file }} (ligne {{ $line }})<br>
**Date :** {{ now()->format('d/m/Y H:i:s') }}
</x-mail::panel>

<x-mail::table>
| Information | Valeur |
|-------------|--------|
| URL | `{{ $url }}` |
| Utilisateur | {{ $user ?? '—' }} |
| Tenant | {{ $tenant ?? '—' }} |
</x-mail::table>

## Trace

```
{{ $trace }}
```

Pour retrouver le contexte complet, consultez `storage/logs/laravel.log` sur le serveur.

L'équipe DailyDesk
</x-mail::message>
