<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potwierdzenie płatności</title>
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
            background: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
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
            background: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }
        .success-box {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .success-box h2 {
            margin-top: 0;
            color: #15803d;
        }
        .order-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: left;
        }
        .order-number {
            font-size: 18px;
            font-weight: bold;
            color: #6366f1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            text-align: left;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f8fafc;
            font-weight: 600;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
        }
        .total-row td {
            border-top: 2px solid #e2e8f0;
        }
        .payment-info {
            background: #f0fdf4;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .payment-info h3 {
            margin-top: 0;
            color: #15803d;
            font-size: 14px;
            text-transform: uppercase;
        }
        .footer {
            background: #0f172a;
            color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #6366f1 0%, #ec4899 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Płatność potwierdzona!</h1>
            <p>Dziękujemy za płatność</p>
        </div>

        <div class="content">
            <div class="icon">💳</div>

            <div class="success-box">
                <h2>Twoja płatność została zrealizowana!</h2>
                <p>Otrzymaliśmy płatność za Twoje zamówienie. Zamówienie jest teraz w realizacji i wkrótce zostanie wysłane.</p>
            </div>

            <div class="order-info">
                <span class="order-number">Zamówienie #{{ $order->order_number }}</span>
                <br>
                <small>Data: {{ $order->created_at->format('d.m.Y H:i') }}</small>
                @if($order->paid_at)
                <br>
                <small>Opłacone: {{ $order->paid_at->format('d.m.Y H:i') }}</small>
                @endif
            </div>

            <h2>Szczegóły płatności</h2>
            
            <div class="payment-info">
                <h3>Metoda płatności</h3>
                <p>
                    @switch($order->payment_method)
                        @case('cash_on_delivery')
                            Płatność przy odbiorze
                            @break
                        @case('bank_transfer')
                            Przelew bankowy
                            @break
                        @case('payu')
                            PayU (płatność online)
                            @break
                        @default
                            {{ $order->payment_method }}
                    @endswitch
                </p>
                <p><strong>Kwota:</strong> {{ number_format($order->total, 2) }} zł</p>
                <p><strong>Status:</strong> <span style="color: #15803d; font-weight: bold;">Opłacone</span></p>
            </div>

            <h2>Zamówione produkty</h2>
            <table>
                <thead>
                    <tr>
                        <th>Produkt</th>
                        <th>Ilość</th>
                        <th>Cena</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}
                            @if($item->variant_name)
                                <br><small style="color: #64748b;">{{ $item->variant_name }}</small>
                            @endif
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->total, 2) }} zł</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="2">Wartość produktów</td>
                        <td>{{ number_format($order->subtotal, 2) }} zł</td>
                    </tr>
                    <tr>
                        <td colspan="2">Dostawa</td>
                        <td>{{ number_format($order->shipping_cost, 2) }} zł</td>
                    </tr>
                    @if($order->discount > 0)
                    <tr>
                        <td colspan="2">Rabat</td>
                        <td>-{{ number_format($order->discount, 2) }} zł</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td colspan="2">RAZEM ZAPŁACONO</td>
                        <td>{{ number_format($order->total, 2) }} zł</td>
                    </tr>
                </tbody>
            </table>

            @if($order->shipping)
            <div style="background: #f8fafc; border-radius: 8px; padding: 15px; margin: 15px 0; text-align: left;">
                <h3 style="margin-top: 0; color: #6366f1; font-size: 14px; text-transform: uppercase;">📍 Adres dostawy</h3>
                <p>
                    {{ $order->shipping->first_name }} {{ $order->shipping->last_name }}<br>
                    {{ $order->shipping->street_address }}
                    @if($order->shipping->apartment)
                        {{ $order->shipping->apartment }}
                    @endif
                    <br>
                    {{ $order->shipping->postal_code }} {{ $order->shipping->city }}<br>
                    {{ $order->shipping->country }}
                </p>
            </div>
            @endif

            <p>Zamówienie jest teraz w realizacji. Otrzymasz kolejną wiadomość email, gdy zostanie wysłane.</p>

            <p>Jeśli masz pytania dotyczące zamówienia, skontaktuj się z nami pod adresem kontakt@rapshop.pl.</p>

            <p style="font-weight: 600;">Dziękujemy za zakupy w Rap Shop! 🎧</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Rap Shop. Wszystkie prawa zastrzeżone.</p>
            <p>
                <a href="{{ url('/') }}" style="color: #6366f1;">Odwiedź nasz sklep</a>
            </p>
        </div>
    </div>
</body>
</html>
