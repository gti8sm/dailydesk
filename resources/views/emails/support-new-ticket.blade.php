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
        .footer { padding: 16px 24px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Nouveau ticket de support</h1>
        </div>
        <div class="body">
            <div class="field">
                <div class="field-label">De</div>
                <div class="field-value">{{ $ticket->user_name }}</div>
            </div>
            <div class="field">
                <div class="field-label">Sujet</div>
                <div class="field-value">{{ $ticket->subject }}</div>
            </div>
            <div class="field">
                <div class="field-label">Catégorie</div>
                <div class="field-value">{{ ucfirst($ticket->category) }} - Priorité : {{ ucfirst($ticket->priority) }}</div>
            </div>
            <div class="field">
                <div class="field-label">Description</div>
                <div class="field-value" style="white-space: pre-line;">{{ $ticket->description }}</div>
            </div>
            @if(!empty($ticket->attachments))
            <div class="field">
                <div class="field-label">Pièces jointes</div>
                <div class="field-value">{{ count($ticket->attachments) }} fichier(s) joint(s)</div>
            </div>
            @endif
        </div>
        <div class="footer">
            Connectez-vous au panel super admin pour répondre à ce ticket.
        </div>
    </div>
</body>
</html>
