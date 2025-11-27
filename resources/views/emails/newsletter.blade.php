<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dziękujemy za zapisanie do newslettera</title>
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
        .content {
            padding: 30px;
            text-align: center;
        }
        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        .discount-box {
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin: 25px 0;
        }
        .discount-code {
            background: white;
            color: #6366f1;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 3px;
            display: inline-block;
            margin-top: 15px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            color: white !important;
            padding: 14px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
        }
        .features {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            padding: 20px 0;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }
        .feature {
            text-align: center;
            padding: 10px;
        }
        .feature-icon {
            font-size: 30px;
            margin-bottom: 10px;
        }
        .feature-text {
            font-size: 14px;
            color: #64748b;
        }
        .footer {
            background: #0f172a;
            color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .unsubscribe {
            color: rgba(255, 255, 255, 0.6);
            font-size: 12px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎵 Rap Shop Newsletter</h1>
        </div>

        <div class="content">
            <div class="icon">🎉</div>

            <h2>Witaj w rodzinie Rap Shop!</h2>

            <p>Dziękujemy za zapisanie do naszego newslettera. Od teraz będziesz na bieżąco z:</p>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🆕</div>
                    <div class="feature-text">Nowościami</div>
                </div>
                <div class="feature">
                    <div class="feature-icon">💰</div>
                    <div class="feature-text">Promocjami</div>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎁</div>
                    <div class="feature-text">Prezentami</div>
                </div>
            </div>

            <div class="discount-box">
                <p style="margin: 0; font-size: 18px;">Twój kod rabatowy na pierwsze zakupy:</p>
                <div class="discount-code">WELCOME10</div>
                <p style="margin: 15px 0 0; font-size: 14px; opacity: 0.9;">-10% na całe zamówienie</p>
            </div>

            <a href="{{ url('/') }}" class="btn">Przejdź do sklepu</a>

            <p style="color: #64748b; font-size: 14px;">Do zobaczenia wkrótce!</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Rap Shop. Wszystkie prawa zastrzeżone.</p>
            <p class="unsubscribe">
                Otrzymujesz tę wiadomość, ponieważ zapisałeś/aś się do newslettera Rap Shop.<br>
                <a href="{{ url('/newsletter/unsubscribe?email=' . urlencode($email)) }}" style="color: rgba(255,255,255,0.6);">Wypisz mnie z newslettera</a>
            </p>
        </div>
    </div>
</body>
</html>