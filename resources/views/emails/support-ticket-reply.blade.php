<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(to right, #f97316, #fb923c); padding: 20px 24px; }
        .header h1 { color: white; margin: 0; font-size: 20px; }
        .body { padding: 24px; }
        .field { margin-bottom: 16px; }
        .field-label { font-size: 12px; color: #6b7280; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }
        .field-value { font-size: 14px; color: #1f2937; }
        .reply-box { background: #f9fafb; border-left: 4px solid #f97316; padding: 12px 16px; border-radius: 8px; margin: 16px 0; }
        .footer { padding: 16px 24px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; text-align: center; }
        .btn { display: inline-block; background: #f97316; color: white; text-decoration: none; padding: 10px 24px; border-radius: 8px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Réponse à votre ticket de support</h1>
        </div>
        <div class="body">
            <p>Bonjour {{ $ticket->user_name }},</p>
            <p>Vous avez reçu une réponse à votre ticket <strong>{{ $ticket->subject }}</strong>.</p>

            <div class="reply-box">
                <div style="font-size: 12px; color: #6b7280; margin-bottom: 8px;">
                    Réponse de {{ $comment->user_name }} - {{ $comment->created_at->format('d/m/Y H:i') }}
                </div>
                <div style="font-size: 14px; color: #1f2937; white-space: pre-line;">{{ $comment->content }}</div>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ config('app.url') }}/support/{{ $ticket->id }}" class="btn">Voir le ticket</a>
            </div>
        </div>
        <div class="footer">
            {{ $appName }} - Ne pas répondre à cet email.
        </div>
    </div>
</body>
</html>
