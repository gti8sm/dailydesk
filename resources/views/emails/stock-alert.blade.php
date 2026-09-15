<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>[{{ $appName }}] Alerte stock — {{ $item->name }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="background: #dc2626; color: white; padding: 20px;">
            <h1 style="margin: 0; font-size: 20px;">
                <i style="margin-right: 8px;">⚠</i> Alerte de stock
            </h1>
        </div>

        <div style="padding: 24px;">
            <p style="color: #374151; font-size: 14px;">Bonjour {{ $admin->name }},</p>

            <p style="color: #374151; font-size: 14px; margin-top: 16px;">
                L'article <strong>{{ $item->name }}</strong> a atteint un niveau de stock critique et nécessite une nouvelle commande.
            </p>

            <div style="background: #fef3c7; border-left: 4px solid #dc2626; padding: 16px; border-radius: 8px; margin: 20px 0;">
                <table style="width: 100%; font-size: 14px;">
                    <tr>
                        <td style="color: #6b7280; padding: 4px 0;">Article</td>
                        <td style="color: #111827; font-weight: bold; text-align: right;">{{ $item->name }}</td>
                    </tr>
                    @if($item->reference)
                    <tr>
                        <td style="color: #6b7280; padding: 4px 0;">Référence</td>
                        <td style="color: #111827; text-align: right;">{{ $item->reference }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="color: #6b7280; padding: 4px 0;">Lieu de stockage</td>
                        <td style="color: #111827; text-align: right;">{{ $item->location?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7280; padding: 4px 0;">Quantité actuelle</td>
                        <td style="color: #dc2626; font-weight: bold; text-align: right; font-size: 18px;">{{ number_format($item->quantity, 2) }} {{ $item->unit }}</td>
                    </tr>
                    <tr>
                        <td style="color: #6b7280; padding: 4px 0;">Seuil minimum</td>
                        <td style="color: #111827; text-align: right;">{{ number_format($item->min_quantity, 2) }} {{ $item->unit }}</td>
                    </tr>
                </table>
            </div>

            <p style="color: #6b7280; font-size: 13px; margin-top: 20px;">
                Connectez-vous à {{ $appName }} pour gérer cet article et enregistrer une nouvelle commande.
            </p>
        </div>

        <div style="background: #f9fafb; padding: 16px; text-align: center; font-size: 12px; color: #9ca3af;">
            {{ $appName }} — Notification automatique
        </div>
    </div>
</body>
</html>
