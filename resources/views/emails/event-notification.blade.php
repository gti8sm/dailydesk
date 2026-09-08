<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h2 style="color: #3b82f6;">{{ $appName }}</h2>

    <p>Bonjour,</p>

    <p>Un signalement a été enregistré pour <strong>{{ $childName }}</strong> en <strong>{{ $module }}</strong>.</p>

    <div style="background: #f8fafc; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0 0 10px 0; font-size: 16px; font-weight: bold; color: #1e293b;">{{ $title }}</p>
        <p style="margin: 0 0 10px 0; color: #475569;">{{ $description }}</p>
        <p style="margin: 0; font-size: 13px; color: #64748b;">
            Date : {{ $eventDate }}@if($eventTime) à {{ $eventTime }}@endif
            @if($severity) · Gravité : {{ $severity }}@endif
        </p>
    </div>

    <p>Vous pouvez consulter le détail de ce signalement sur votre espace parent.</p>

    <p style="color: #666; font-size: 12px; margin-top: 30px;">
        Cet email a été envoyé automatiquement par {{ $appName }}. Merci de ne pas y répondre.
    </p>
</div>
