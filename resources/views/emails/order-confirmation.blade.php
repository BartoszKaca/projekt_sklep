<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potwierdzenie zamówienia</title>
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
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
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
        .address-box {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .address-box h3 {
            margin-top: 0;
            color: #6366f1;
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
            <h1>🎵 Dziękujemy za zamówienie!</h1>
        </div>
        
        <div class="content">
            <p>Cześć{{ $order->shipping ? ', ' . $order->shipping->first_name : '' }}!</p>
            
            <p>Twoje zamówienie zostało przyjęte i jest przetwarzane. Poniżej znajdziesz szczegóły zamówienia.</p>
            
            <div class="order-info">
                <span class="order-number">Zamówienie #{{ $order->order_number }}</span>
                <br>
                <small>Data: {{ $order->created_at->format('d.m.Y H:i') }}</small>
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
                        <td colspan="2">RAZEM</td>
                        <td>{{ number_format($order->total, 2) }} zł</td>
                    </tr>
                </tbody>
            </table>
            
            @if($order->shipping)
            <div class="address-box">
                <h3>Adres dostawy</h3>
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
                @if($order->shipping->phone)
                    <p>Tel: {{ $order->shipping->phone }}</p>
                @endif
            </div>
            @endif
            
            <div class="address-box">
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
                            PayU (karta/BLIK)
                            @break
                        @default
                            {{ $order->payment_method }}
                    @endswitch
                </p>
                @if($order->payment_method === 'bank_transfer' && $order->payment_status !== 'paid')
                <p style="margin-top: 10px;">
                    <strong>Dane do przelewu:</strong><br>
                    Numer konta: XX XXXX XXXX XXXX XXXX XXXX XXXX<br>
                    Tytuł: {{ $order->order_number }}<br>
                    Kwota: {{ number_format($order->total, 2) }} zł
                </p>
                @endif
            </div>
            
            <p>Jeśli masz pytania dotyczące zamówienia, skontaktuj się z nami pod adresem kontakt@rapshop.pl.</p>
            
            <p>Dziękujemy za zakupy w Rap Shop! 🎧</p>
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
