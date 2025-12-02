@extends('layouts.shop')

@section('title', 'O nas')

@section('content')
<div class="container" style="max-width: 900px; padding: 3rem 2rem;">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; color: var(--dark);">O nas</h1>
    
    <div style="background: white; border-radius: 16px; padding: 2.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
            <i class="fas fa-store"></i> RAP SHOP - Twój sklep z polskim hip-hopem
        </h2>
        
        <p style="margin-bottom: 1.5rem; line-height: 1.8; color: var(--gray);">
            Jesteśmy pasjonatami polskiego rapu i hip-hopu. Nasza misja to dostarczanie fanom najlepszych produktów - 
            od płyt CD i vinylu, po limitowane edycje i oficjalny merch ich ulubionych artystów.
        </p>

        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 2rem 0 1rem; color: var(--dark);">
            <i class="fas fa-heart"></i> Nasza pasja
        </h3>
        <p style="margin-bottom: 1.5rem; line-height: 1.8; color: var(--gray);">
            Od lat wspieramy polską scenę hip-hopową, oferując największy wybór płyt, vinylu i merchandisingu. 
            Współpracujemy bezpośrednio z artystami i wytwórniami, aby zapewnić naszym klientom dostęp do 
            najnowszych wydawnictw i ekskluzywnych produktów.
        </p>

        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 2rem 0 1rem; color: var(--dark);">
            <i class="fas fa-star"></i> Dlaczego my?
        </h3>
        <ul style="list-style: none; padding: 0;">
            <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--border); color: var(--gray);">
                <i class="fas fa-check" style="color: var(--success); margin-right: 0.5rem;"></i>
                Najszerszy wybór płyt i vinylu w Polsce
            </li>
            <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--border); color: var(--gray);">
                <i class="fas fa-check" style="color: var(--success); margin-right: 0.5rem;"></i>
                100% oryginalne wydania
            </li>
            <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--border); color: var(--gray);">
                <i class="fas fa-check" style="color: var(--success); margin-right: 0.5rem;"></i>
                Szybka wysyłka w 24h
            </li>
            <li style="padding: 0.75rem 0; border-bottom: 1px solid var(--border); color: var(--gray);">
                <i class="fas fa-check" style="color: var(--success); margin-right: 0.5rem;"></i>
                Bezpieczne płatności
            </li>
            <li style="padding: 0.75rem 0; color: var(--gray);">
                <i class="fas fa-check" style="color: var(--success); margin-right: 0.5rem;"></i>
                Profesjonalna obsługa klienta
            </li>
        </ul>

        <div style="margin-top: 2.5rem; padding: 1.5rem; background: var(--light); border-radius: 12px;">
            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; color: var(--dark);">
                <i class="fas fa-envelope"></i> Skontaktuj się z nami
            </h3>
            <p style="color: var(--gray); margin-bottom: 0.5rem;">
                <strong>Email:</strong> kontakt@rapshop.pl
            </p>
            <p style="color: var(--gray); margin-bottom: 0.5rem;">
                <strong>Telefon:</strong> +48 123 456 789
            </p>
            <p style="color: var(--gray);">
                <strong>Godziny otwarcia:</strong> Poniedziałek - Piątek, 9:00 - 17:00
            </p>
        </div>
    </div>
</div>
@endsection
