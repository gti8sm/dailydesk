<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance en cours — DailyDesk</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 50%, #3b82f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 24px;
            padding: 48px 40px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 80px rgba(0,0,0,0.3);
        }
        .icon {
            width: 88px;
            height: 88px;
            margin: 0 auto 28px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: float 3s ease-in-out infinite;
        }
        .icon svg { width: 44px; height: 44px; }
        h1 { color: #111827; font-size: 26px; margin-bottom: 12px; }
        p { color: #6b7280; font-size: 16px; line-height: 1.6; margin-bottom: 8px; }
        .spinner { margin: 28px auto 24px; }
        .spinner span {
            display: inline-block;
            width: 10px; height: 10px;
            margin: 0 4px;
            background: #3b82f6;
            border-radius: 50%;
            animation: bounce 1.4s ease-in-out infinite;
        }
        .spinner span:nth-child(2) { animation-delay: 0.16s; }
        .spinner span:nth-child(3) { animation-delay: 0.32s; }
        .badge {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            font-size: 13px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 999px;
            margin-bottom: 20px;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @keyframes bounce {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
            40% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="badge">DailyDesk</span>
        <div class="icon">
            <svg fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>
        <h1>Maintenance en cours</h1>
        <p>Nous améliorons la plateforme pour mieux vous servir.</p>
        <p>Cette intervention ne devrait durer que quelques minutes — votre patience est appréciée.</p>
        <div class="spinner"><span></span><span></span><span></span></div>
        <p style="font-size:14px; color:#9ca3af;">
            <svg style="width:14px;height:14px;vertical-align:-2px;fill:none;stroke:#9ca3af;" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h5M20 20v-5h-5M4 9a8 8 0 0114-3M20 15a8 8 0 01-14 3"/></svg>
            Revenez dans un instant ou rechargez cette page (F5)
        </p>
    </div>
</body>
</html>
