@extends('layouts.shop')

@section('title', 'Koszyk')

@section('content')
<div class="container section">
    <div class="section-header">
        <h2 class="section-title">Twój koszyk</h2>
    </div>

    @php
        $items = $cart['items'] ?? [];
    @endphp

    @if(empty($items))
        <div style="padding:3rem; text-align:center;">
            <i class="fas fa-shopping-bag" style="font-size:4rem; color:var(--gray);"></i>
            <p style="margin-top:1rem; color:var(--gray);">Twój koszyk jest pusty.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Kontynuuj zakupy</a>
        </div>
    @else
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:2rem;">
            <div>
                @foreach($items as $key => $it)
                    <div style="display:flex; gap:1rem; align-items:center; padding:1rem; border:1px solid var(--border); border-radius:8px; margin-bottom:1rem; background:white;">
                        <div style="width:90px;">
                            @if($it['image'])
                                <img src="{{ asset('storage/'.$it['image']) }}" style="width:90px; height:90px; object-fit:cover; border-radius:8px;">
                            @else
                                <div style="width:90px; height:90px; background:var(--light); display:flex; align-items:center; justify-content:center; border-radius:8px;">
                                    <i class="fas fa-compact-disc" style="color:var(--gray)"></i>
                                </div>
                            @endif
                        </div>

                        <div style="flex:1;">
                            <a href="{{ route('products.show', $it['slug'] ?? $it['product_id']) }}"><strong>{{ $it['name'] }}</strong></a>
                            @if(!empty($it['size']) || !empty($it['color']))
                                <div style="margin-top:0.25rem; font-size:0.875rem; color:var(--gray);">
                                    @if(!empty($it['size']))
                                        <span style="background:var(--light-gray); padding:0.2rem 0.5rem; border-radius:4px; font-weight:600;">{{ $it['size'] }}</span>
                                    @endif
                                    @if(!empty($it['color']))
                                        <span style="margin-left:0.25rem;">{{ $it['color'] }}</span>
                                    @endif
                                </div>
                            @endif
                            <div style="margin-top:0.25rem;">
                                Cena: {{ number_format($it['price'],2) }} zł
                            </div>
                            <div style="margin-top:0.5rem;">
                                Ilość:
                                <input type="number" min="0" value="{{ $it['quantity'] }}" style="width:80px; padding:0.4rem;" onchange="updateCart('{{ $key }}', this.value)">
                                <button style="margin-left:0.5rem;" onclick="removeItem('{{ $key }}')">Usuń</button>
                            </div>
                        </div>

                        <div style="text-align:right;">
                            <div style="font-weight:800;">{{ number_format($it['price'] * $it['quantity'],2) }} zł</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <aside style="background:white; padding:1rem; border-radius:8px; border:1px solid var(--border); height:fit-content;">
                <h3>Podsumowanie</h3>
                <div style="display:flex; justify-content:space-between; margin-top:1rem;">
                    <div>Razem</div>
                    <div style="font-weight:800;">{{ number_format($cart['total'] ?? 0, 2) }} zł</div>
                </div>
                <div style="margin-top:1rem;">
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary" style="width:100%;">Przejdź do płatności</a>
                </div>
            </aside>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    async function updateCart(itemKey, qty) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch("{{ route('cart.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ item_key: itemKey, quantity: parseInt(qty, 10) })
        });
        const data = await res.json();
        if (!res.ok || !data.success) { alert(data.message || 'Błąd'); return; }
        location.reload();
    }

    async function removeItem(itemKey) {
        if (!confirm('Usunąć pozycję z koszyka?')) return;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch("{{ route('cart.remove') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ item_key: itemKey })
        });
        const data = await res.json();
        if (!res.ok || !data.success) { alert(data.message || 'Błąd'); return; }
        location.reload();
    }
</script>
@endpush