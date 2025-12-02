<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weryfikacja adresu email</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8fafc;
        }
        .container {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        .code-box {
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin: 30px 0;
            font-size: 48px;
            font-weight: bold;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
        }
        .note {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #64748b;
        }
        .warning {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            font-size: 14px;
        }
        .footer {
            background: #0f172a;
            color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎵 Rap Shop</h1>
            <p>Kod weryfikacyjny</p>
        </div>

        <div class="content">
            <div class="icon">🔐</div>

            <h2>Cześć, {{ $user->name }}!</h2>

            <p>Dziękujemy za rejestrację w Rap Shop!</p>

            @php
                $payload = $verificationPayload ?? null;
                $isUrl = $payload && (strpos($payload, 'http') === 0);
            @endphp

            @if($isUrl)
                <p>Kliknij poniższy link, aby potwierdzić swój adres email:</p>
                <div class="code-box">
                    <a href="{{ $payload }}" style="color: white; text-decoration: none;">Zweryfikuj adres email</a>
                </div>
                <p style="font-size: 16px; color: #334155; margin-top: 10px;">Jeśli nie możesz kliknąć linku, wklej URL w przeglądarce:</p>
                <p style="font-size: 14px; color: #334155; word-break: break-all;">{{ $payload }}</p>
            @else
                <p>Oto Twój kod weryfikacyjny:</p>

                <div class="code-box">
                    {{ $payload }}
                </div>
            @endif

            <p style="font-size: 16px; color: #334155;">
                Wpisz ten kod na stronie weryfikacji, aby potwierdzić swój adres email.
            </p>

            <div class="note">
                <p><strong>⏰ Kod jest ważny przez 15 minut</strong></p>
                <p>Po tym czasie możesz poprosić o wysłanie nowego kodu.</p>
            </div>

            <div class="warning">
                <p><strong>⚠️ Uwaga bezpieczeństwa</strong></p>
                <p>Jeśli nie zakładałeś/aś konta w naszym sklepie, zignoruj tę wiadomość i nie udostępniaj tego kodu nikomu.</p>
            </div>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Rap Shop. Wszystkie prawa zastrzeżone.</p>
            <p style="font-size: 12px; opacity: 0.7;">
                Ten email został wysłany automatycznie. Prosimy nie odpowiadać na tę wiadomość.
            </p>
        </div>
    </div>
</body>
</html>
