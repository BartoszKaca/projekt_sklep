@extends('layouts.shop')

@section('title', 'Regulamin')

@section('content')
<div class="container" style="max-width: 900px; padding: 3rem 2rem;">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; color: var(--dark);">Regulamin sklepu</h1>
    
    <div style="background: white; border-radius: 16px; padding: 2.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        
        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                § 1. Postanowienia ogólne
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                1. Niniejszy Regulamin określa zasady korzystania ze sklepu internetowego RAP SHOP dostępnego pod adresem www.rapshop.pl.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                2. Właścicielem sklepu internetowego jest [Nazwa Firmy], z siedzibą w [Adres].
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                3. Kontakt ze Sprzedawcą możliwy jest pod adresem e-mail: kontakt@rapshop.pl oraz numerem telefonu: +48 123 456 789.
            </p>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                § 2. Składanie zamówień
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                1. Zamówienia można składać 24 godziny na dobę, 7 dni w tygodniu przez formularz zamówieniowy dostępny w sklepie internetowym.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                2. Do złożenia zamówienia niezbędne jest podanie danych osobowych oraz adresu dostawy.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                3. Ceny produktów są podane w złotych polskich (PLN) i zawierają podatek VAT.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                4. Zamówienie zostaje przyjęte do realizacji po dokonaniu płatności.
            </p>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                § 3. Płatności
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                1. Akceptujemy następujące formy płatności:
            </p>
            <ul style="margin-left: 2rem; margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                <li>Przelewy bankowe</li>
                <li>Płatności kartą (Visa, Mastercard)</li>
                <li>Płatności online (PayU, Przelewy24)</li>
                <li>Płatność przy odbiorze (za pobraniem)</li>
            </ul>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                2. Wszystkie płatności są realizowane przez bezpieczne systemy płatności.
            </p>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                § 4. Dostawa
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                1. Realizacja zamówienia następuje w ciągu 1-2 dni roboczych od momentu zaksięgowania płatności.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                2. Czas dostawy wynosi zazwyczaj 1-3 dni roboczych w zależności od wybranego kuriera.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                3. Koszty dostawy są podawane przy składaniu zamówienia i zależą od wagi przesyłki oraz wybranej formy dostawy.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                4. Oferujemy darmową dostawę dla zamówień powyżej 100 zł.
            </p>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                § 5. Prawo odstąpienia od umowy
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                1. Konsument ma prawo odstąpić od umowy w terminie 14 dni bez podania przyczyny.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                2. Termin do odstąpienia od umowy wygasa po upływie 14 dni od dnia, w którym Konsument wszedł w posiadanie rzeczy.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                3. Aby skorzystać z prawa odstąpienia od umowy, należy poinformować nas o swojej decyzji poprzez wysłanie wiadomości e-mail na adres: kontakt@rapshop.pl.
            </p>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                § 6. Reklamacje
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                1. Wszystkie produkty objęte są ustawową gwarancją.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                2. Reklamację należy zgłosić na adres e-mail: kontakt@rapshop.pl, dołączając zdjęcia produktu oraz opis problemu.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                3. Reklamacje rozpatrywane są w terminie 14 dni od daty ich otrzymania.
            </p>
        </section>

        <div style="margin-top: 2.5rem; padding: 1.5rem; background: var(--light); border-radius: 12px;">
            <p style="color: var(--gray); font-size: 0.875rem;">
                <strong>Ostatnia aktualizacja:</strong> {{ date('d.m.Y') }}
            </p>
        </div>
    </div>
</div>
@endsection
