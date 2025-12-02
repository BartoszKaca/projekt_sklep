@extends('layouts.shop')

@section('title', 'Polityka prywatności')

@section('content')
<div class="container" style="max-width: 900px; padding: 3rem 2rem;">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; color: var(--dark);">Polityka prywatności</h1>
    
    <div style="background: white; border-radius: 16px; padding: 2.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        
        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                1. Informacje ogólne
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Niniejsza Polityka Prywatności określa zasady przetwarzania i ochrony danych osobowych 
                przekazanych przez Użytkowników w związku z korzystaniem ze sklepu internetowego RAP SHOP.
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Administratorem danych osobowych jest [Nazwa Firmy], z siedzibą w [Adres], 
                NIP: [NIP], REGON: [REGON].
            </p>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                2. Rodzaj przetwarzanych danych
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                W ramach działalności sklepu przetwarzamy następujące dane osobowe:
            </p>
            <ul style="margin-left: 2rem; margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                <li>Imię i nazwisko</li>
                <li>Adres e-mail</li>
                <li>Numer telefonu</li>
                <li>Adres dostawy i rozliczeniowy</li>
                <li>Historia zamówień</li>
                <li>Dane dotyczące płatności (poprzez zewnętrznych operatorów płatności)</li>
            </ul>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                3. Cel i podstawa przetwarzania danych
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Dane osobowe są przetwarzane w celu:
            </p>
            <ul style="margin-left: 2rem; margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                <li>Realizacji zamówień złożonych w sklepie (art. 6 ust. 1 lit. b RODO)</li>
                <li>Prowadzenia obsługi klienta i komunikacji (art. 6 ust. 1 lit. b RODO)</li>
                <li>Prowadzenia działań marketingowych (art. 6 ust. 1 lit. a RODO - zgoda)</li>
                <li>Wypełnienia obowiązków prawnych (art. 6 ust. 1 lit. c RODO)</li>
                <li>Dochodzenia roszczeń (art. 6 ust. 1 lit. f RODO - prawnie uzasadniony interes)</li>
            </ul>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                4. Okres przechowywania danych
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Dane osobowe będą przechowywane przez okres:
            </p>
            <ul style="margin-left: 2rem; margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                <li>Niezbędny do realizacji umowy sprzedaży</li>
                <li>Wymagany przepisami prawa (np. rachunkowości - 5 lat)</li>
                <li>Do momentu wycofania zgody lub zgłoszenia sprzeciwu</li>
                <li>Do momentu przedawnienia roszczeń</li>
            </ul>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                5. Prawa użytkownika
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Przysługują Ci następujące prawa:
            </p>
            <ul style="margin-left: 2rem; margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                <li>Prawo dostępu do swoich danych</li>
                <li>Prawo do sprostowania danych</li>
                <li>Prawo do usunięcia danych</li>
                <li>Prawo do ograniczenia przetwarzania</li>
                <li>Prawo do przenoszenia danych</li>
                <li>Prawo do wniesienia sprzeciwu</li>
                <li>Prawo do cofnięcia zgody w dowolnym momencie</li>
                <li>Prawo do wniesienia skargi do organu nadzorczego (UODO)</li>
            </ul>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                6. Odbiorcy danych
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Twoje dane mogą być przekazywane następującym odbiorcom:
            </p>
            <ul style="margin-left: 2rem; margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                <li>Firmom kurierskim i pocztowym (w celu realizacji dostawy)</li>
                <li>Operatorom płatności (w celu realizacji transakcji)</li>
                <li>Dostawcom usług IT i hostingu</li>
                <li>Organom państwowym (na podstawie obowiązujących przepisów)</li>
            </ul>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                7. Pliki cookies
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Nasz sklep wykorzystuje pliki cookies w celu:
            </p>
            <ul style="margin-left: 2rem; margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                <li>Zapewnienia prawidłowego działania strony</li>
                <li>Zapamiętywania ustawień i preferencji</li>
                <li>Prowadzenia statystyk (Google Analytics)</li>
                <li>Remarketingu (Facebook Pixel, Google Ads)</li>
            </ul>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Możesz w każdej chwili zmienić ustawienia cookies w swojej przeglądarce.
            </p>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                8. Bezpieczeństwo danych
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Stosujemy odpowiednie środki techniczne i organizacyjne zapewniające ochronę 
                przetwarzanych danych osobowych, w tym:
            </p>
            <ul style="margin-left: 2rem; margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                <li>Szyfrowanie połączenia SSL</li>
                <li>Bezpieczne systemy płatności</li>
                <li>Regularne kopie zapasowe</li>
                <li>Ograniczony dostęp do danych</li>
                <li>Monitoring i audyt bezpieczeństwa</li>
            </ul>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                9. Kontakt
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                W sprawach dotyczących ochrony danych osobowych możesz skontaktować się z nami:
            </p>
            <p style="margin-bottom: 0.5rem; line-height: 1.8; color: var(--gray);">
                <strong>Email:</strong> kontakt@rapshop.pl
            </p>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                <strong>Telefon:</strong> +48 123 456 789
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
