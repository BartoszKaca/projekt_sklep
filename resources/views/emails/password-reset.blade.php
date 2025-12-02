<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zresetuj hasło</title>
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
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            color: white !important;
            padding: 16px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            margin: 25px 0;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .note {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎵 Rap Shop</h1>
            <p>Zresetuj hasło</p>
        </div>

        <div class="content">
            <div class="icon">🔐</div>

            <h2>Reset hasła</h2>

            <p>Otrzymaliśmy prośbę o zresetowanie hasła do Twojego konta.</p>

            <p>Kliknij poniższy przycisk, aby ustawić nowe hasło:</p>

            <a href="{{ $resetUrl }}" class="btn">Zresetuj hasło</a>

            <div class="note">
                <p>Link jest ważny przez 60 minut.</p>
                <p>Jeśli nie prosiłeś/aś o reset hasła, zignoruj tę wiadomość. Twoje hasło pozostanie niezmienione.</p>
            </div>

            <p style="font-size: 13px; color: #64748b;">
                Jeśli przycisk nie działa, skopiuj i wklej poniższy link do przeglądarki:<br>
                <a href="{{ $resetUrl }}" style="color: #6366f1; word-break: break-all;">{{ $resetUrl }}</a>
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Rap Shop. Wszystkie prawa zastrzeżone.</p>
        </div>
    </div>
</body>
</html>
