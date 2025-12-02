<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktualizacja statusu zamówienia</title>
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
        .status-box {
            background: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .order-number {
            font-size: 18px;
            font-weight: bold;
            color: #6366f1;
            margin-bottom: 20px;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 16px;
            margin: 10px;
        }
        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }
        .status-shipped {
            background: #e0e7ff;
            color: #4338ca;
        }
        .status-delivered {
            background: #d1fae5;
            color: #065f46;
        }
        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }
        .arrow {
            font-size: 24px;
            color: #94a3b8;
            margin: 0 10px;
        }
        .tracking-box {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        .tracking-box h3 {
            margin-top: 0;
            color: #15803d;
        }
        .info-box {
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
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
            <h1>📦 Aktualizacja statusu zamówienia</h1>
        </div>

        <div class="content">
            <p>Cześć{{ $order->shipping ? ', ' . $order->shipping->first_name : '' }}!</p>

            <p>Twoje zamówienie zmieniło status:</p>

            <div class="status-box">
                <div class="order-number">Zamówienie #{{ $order->order_number }}</div>
                
                <div style="display: flex; align-items: center; justify-content: center; flex-wrap: wrap;">
                    <span class="status-badge status-{{ $oldStatus }}">
                        {{ $oldStatusName }}
                    </span>
                    <span class="arrow">→</span>
                    <span class="status-badge status-{{ $newStatus }}">
                        {{ $newStatusName }}
                    </span>
                </div>
            </div>

            @if($newStatus === 'processing')
                <div class="info-box">
                    <h3>✅ Zamówienie w realizacji</h3>
                    <p>Twoje zamówienie jest obecnie pakowane i przygotowywane do wysyłki. Poinformujemy Cię, gdy zostanie wysłane.</p>
                </div>
            @elseif($newStatus === 'shipped')
                <div class="tracking-box">
                    <h3>🚚 Zamówienie wysłane!</h3>
                    <p>Twoja paczka została nadana i jest w drodze do Ciebie.</p>
                    
                    @if($order->tracking_number)
                        <p><strong>Numer śledzenia:</strong> {{ $order->tracking_number }}</p>
                    @endif
                    
                    @if($order->carrier)
                        <p><strong>Kurier:</strong> {{ $order->carrier }}</p>
                    @endif
                    
                    @if($order->shipped_at)
                        <p><strong>Data wysyłki:</strong> {{ $order->shipped_at->format('d.m.Y H:i') }}</p>
                    @endif
                </div>
            @elseif($newStatus === 'delivered')
                <div class="info-box">
                    <h3>🎉 Zamówienie dostarczone!</h3>
                    <p>Mamy nadzieję, że jesteś zadowolony/a z zakupów. Dziękujemy za zaufanie!</p>
                    
                    @if($order->delivered_at)
                        <p><strong>Data dostawy:</strong> {{ $order->delivered_at->format('d.m.Y H:i') }}</p>
                    @endif
                </div>
            @elseif($newStatus === 'cancelled')
                <div class="info-box" style="background: #fee2e2;">
                    <h3 style="color: #991b1b;">❌ Zamówienie anulowane</h3>
                    <p>Twoje zamówienie zostało anulowane. Jeśli masz pytania, skontaktuj się z nami.</p>
                </div>
            @endif

            <h3>Szczegóły zamówienia</h3>
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
                    <tr style="font-weight: bold;">
                        <td colspan="2">RAZEM</td>
                        <td>{{ number_format($order->total, 2) }} zł</td>
                    </tr>
                </tbody>
            </table>

            @if($order->shipping)
            <div class="info-box">
                <h3>📍 Adres dostawy</h3>
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

            <p>Jeśli masz pytania dotyczące zamówienia, skontaktuj się z nami pod adresem kontakt@rapshop.pl.</p>
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
