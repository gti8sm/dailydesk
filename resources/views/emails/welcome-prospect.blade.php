<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(to right, #2563eb, #6366f1); padding: 24px; }
        .header h1 { color: white; margin: 0; font-size: 22px; }
        .body { padding: 24px; line-height: 1.6; color: #1f2937; font-size: 14px; }
        .btn { display: inline-block; background: #2563eb; color: white; text-decoration: none; padding: 12px 32px; border-radius: 8px; font-weight: bold; margin: 16px 0; }
        .footer { padding: 16px 24px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Bienvenue sur DailyDesk !</h1>
        </div>
        <div class="body">
            <p>Bonjour {{ $name }},</p>
            <p>Votre demande de démo a bien été enregistrée. Votre espace DailyDesk est prêt !</p>
            <p><strong>Votre URL d'accès :</strong> <a href="{{ $domain }}">{{ $domain }}</a></p>
            <p>Pour accéder à votre espace, il vous suffit de définir votre mot de passe :</p>
            <div style="text-align: center;">
                <a href="{{ $url }}" class="btn">Définir mon mot de passe</a>
            </div>
            <p>Ce lien est valable 24 heures. Une fois votre mot de passe défini, vous pourrez vous connecter avec votre adresse email <strong>{{ $email }}</strong>.</p>
            <p>Vous bénéficiez d'une période d'essai de 30 jours, sans engagement.</p>
            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
        </div>
        <div class="footer">
            DailyDesk — Site développé à Libourne par SmallWebConcept
        </div>
    </div>
</body>
</html>
