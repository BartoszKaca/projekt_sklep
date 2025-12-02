@extends('layouts.shop')

@section('title', 'Zwroty i reklamacje')

@section('content')
<div class="container" style="max-width: 900px; padding: 3rem 2rem;">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; color: var(--dark);">Zwroty i reklamacje</h1>
    
    <div style="background: white; border-radius: 16px; padding: 2.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        
        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                <i class="fas fa-undo"></i> Prawo odstąpienia od umowy (zwrot)
            </h2>
            
            <div style="margin-bottom: 1.5rem; padding: 1.5rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 12px; color: white;">
                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.75rem;">
                    📅 14 dni na zwrot bez podania przyczyny
                </h3>
                <p>
                    Zgodnie z przepisami prawa konsumenckiego, masz prawo odstąpić od umowy w ciągu 14 dni 
                    kalendarzowych od dnia otrzymania towaru - bez podawania przyczyny!
                </p>
            </div>

            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 1.5rem 0 1rem; color: var(--dark);">
                Jak dokonać zwrotu?
            </h3>
            
            <ol style="margin-left: 1.5rem; color: var(--gray); line-height: 1.8;">
                <li style="margin-bottom: 1rem;">
                    <strong>Powiadom nas o zwrocie</strong><br>
                    Wyślij e-mail na adres: <strong>kontakt@rapshop.pl</strong> z tytułem "Zwrot - zamówienie [numer]"
                </li>
                <li style="margin-bottom: 1rem;">
                    <strong>Zapakuj produkt</strong><br>
                    Zapakuj produkt w oryginalne opakowanie (jeśli to możliwe), dołącz kopię faktury lub paragonu
                </li>
                <li style="margin-bottom: 1rem;">
                    <strong>Wyślij paczkę</strong><br>
                    Odeślij produkt na adres:<br>
                    <strong>[Nazwa firmy]</strong><br>
                    <strong>[Ulica i numer]</strong><br>
                    <strong>[Kod pocztowy] [Miasto]</strong>
                </li>
                <li>
                    <strong>Otrzymaj zwrot pieniędzy</strong><br>
                    Po otrzymaniu i weryfikacji przesyłki, zwrócimy Ci pieniądze w ciągu 14 dni na konto, 
                    z którego dokonałeś płatności
                </li>
            </ol>

            <div style="margin-top: 1.5rem; padding: 1rem; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 8px;">
                <p style="color: #92400e; margin: 0;">
                    <strong>⚠️ Ważne:</strong> Koszt odesłania produktu ponosi klient. 
                    Rekomendujemy wysyłkę z potwierdzeniem odbioru.
                </p>
            </div>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                ✅ Warunki zwrotu
            </h2>
            
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Aby zwrot był możliwy, produkt musi spełniać następujące warunki:
            </p>

            <div style="display: grid; gap: 1rem;">
                <div style="padding: 1rem; background: var(--light); border-radius: 8px; border-left: 4px solid var(--success);">
                    <p style="color: var(--gray); margin: 0;">
                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                        <strong> Produkt nieużywany</strong> - w stanie niezmienionym, nieuszkodzonym
                    </p>
                </div>
                <div style="padding: 1rem; background: var(--light); border-radius: 8px; border-left: 4px solid var(--success);">
                    <p style="color: var(--gray); margin: 0;">
                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                        <strong> Oryginalne opakowanie</strong> - jeśli to możliwe, produkt w oryginalnym opakowaniu
                    </p>
                </div>
                <div style="padding: 1rem; background: var(--light); border-radius: 8px; border-left: 4px solid var(--success);">
                    <p style="color: var(--gray); margin: 0;">
                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                        <strong> Pełna kompletność</strong> - wszystkie akcesoria i dokumenty
                    </p>
                </div>
                <div style="padding: 1rem; background: var(--light); border-radius: 8px; border-left: 4px solid var(--success);">
                    <p style="color: var(--gray); margin: 0;">
                        <i class="fas fa-check-circle" style="color: var(--success);"></i>
                        <strong> Folia ochronna</strong> - w przypadku płyt, folia nie może być usunięta
                    </p>
                </div>
            </div>

            <div style="margin-top: 1.5rem; padding: 1rem; background: #fee2e2; border-left: 4px solid var(--danger); border-radius: 8px;">
                <p style="color: #991b1b; margin: 0;">
                    <strong>❌ Nie przyjmiemy zwrotu:</strong> Płyt i produktów audio z usuniętą folią ochronną 
                    (ze względów higienicznych i praw autorskich), produktów uszkodzonych mechanicznie, 
                    produktów wykonanych na specjalne zamówienie.
                </p>
            </div>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                <i class="fas fa-tools"></i> Reklamacja - Gwarancja
            </h2>
            
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Wszystkie produkty objęte są ustawową gwarancją. Jeśli otrzymany produkt jest uszkodzony 
                lub niezgodny z zamówieniem, masz prawo do reklamacji.
            </p>

            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 1.5rem 0 1rem; color: var(--dark);">
                Podstawy do reklamacji:
            </h3>

            <ul style="margin-left: 2rem; margin-bottom: 1.5rem; color: var(--gray); line-height: 1.8;">
                <li>Produkt uszkodzony podczas transportu</li>
                <li>Produkt niezgodny z opisem</li>
                <li>Wada fabryczna produktu</li>
                <li>Brak komponentów lub akcesoriów</li>
                <li>Uszkodzenie opakowania (płyta, okładka)</li>
            </ul>

            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 1.5rem 0 1rem; color: var(--dark);">
                Jak zgłosić reklamację?
            </h3>

            <ol style="margin-left: 1.5rem; color: var(--gray); line-height: 1.8;">
                <li style="margin-bottom: 1rem;">
                    <strong>Wyślij zgłoszenie</strong><br>
                    E-mail na: <strong>kontakt@rapshop.pl</strong> z tytułem "Reklamacja - zamówienie [numer]"
                </li>
                <li style="margin-bottom: 1rem;">
                    <strong>Dołącz dokumentację</strong><br>
                    - Zdjęcia uszkodzonego produktu (z różnych kątów)<br>
                    - Opis problemu<br>
                    - Kopię faktury/paragonu<br>
                    - Numer zamówienia
                </li>
                <li style="margin-bottom: 1rem;">
                    <strong>Otrzymasz odpowiedź</strong><br>
                    Odpowiemy w ciągu 24 godzin i poinformujemy o dalszych krokach
                </li>
                <li>
                    <strong>Rozpatrzenie reklamacji</strong><br>
                    Reklamacje rozpatrujemy w terminie do 14 dni roboczych
                </li>
            </ol>

            <div style="margin-top: 1.5rem; padding: 1.5rem; background: var(--light); border-radius: 12px;">
                <h4 style="font-weight: 700; margin-bottom: 0.75rem; color: var(--dark);">
                    💚 Produkty uszkodzone przy dostawie
                </h4>
                <p style="color: var(--gray); margin: 0; line-height: 1.8;">
                    Jeśli otrzymasz uszkodzony produkt, koniecznie sporządź protokół szkody z kurierem 
                    w momencie odbioru! To znacznie przyspieszy proces reklamacji. Skontaktuj się z nami 
                    niezwłocznie - wyślemy nowy produkt lub zwrócimy pieniądze.
                </p>
            </div>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                <i class="fas fa-exchange-alt"></i> Wymiana produktu
            </h2>
            
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Chcesz wymienić produkt na inny? Żaden problem!
            </p>

            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Możesz zwrócić zakupiony produkt (zgodnie z procedurą zwrotu) i złożyć nowe zamówienie 
                na wybrany przez siebie produkt. Jeśli wolisz, możesz też skontaktować się z nami, 
                a pomożemy Ci w procesie wymiany.
            </p>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                <i class="fas fa-question-circle"></i> Najczęściej zadawane pytania
            </h2>
            
            <div style="margin-bottom: 1rem; padding: 1rem; border-left: 4px solid var(--primary); background: var(--light);">
                <h4 style="font-weight: 700; margin-bottom: 0.5rem; color: var(--dark);">
                    Kiedy otrzymam zwrot pieniędzy?
                </h4>
                <p style="color: var(--gray); margin: 0;">
                    Zwrot pieniędzy następuje w ciągu 14 dni od dnia otrzymania przez nas zwracanego towaru 
                    i jego weryfikacji. Środki zostaną przekazane na konto, z którego dokonano płatności.
                </p>
            </div>

            <div style="margin-bottom: 1rem; padding: 1rem; border-left: 4px solid var(--primary); background: var(--light);">
                <h4 style="font-weight: 700; margin-bottom: 0.5rem; color: var(--dark);">
                    Czy mogę zwrócić produkt z usuniętą folią?
                </h4>
                <p style="color: var(--gray); margin: 0;">
                    Niestety, ze względów higienicznych i praw autorskich, nie przyjmujemy zwrotów płyt 
                    i produktów audio z usuniętą folią ochronną, chyba że produkt jest uszkodzony (reklamacja).
                </p>
            </div>

            <div style="margin-bottom: 1rem; padding: 1rem; border-left: 4px solid var(--primary); background: var(--light);">
                <h4 style="font-weight: 700; margin-bottom: 0.5rem; color: var(--dark);">
                    Kto płaci za zwrot?
                </h4>
                <p style="color: var(--gray); margin: 0;">
                    W przypadku odstąpienia od umowy (zwrot bez podania przyczyny), koszty odesłania ponosi klient. 
                    W przypadku reklamacji (wada produktu), pokrywamy koszty przesyłki zwrotnej.
                </p>
            </div>

            <div style="padding: 1rem; border-left: 4px solid var(--primary); background: var(--light);">
                <h4 style="font-weight: 700; margin-bottom: 0.5rem; color: var(--dark);">
                    Jak długo trwa rozpatrzenie reklamacji?
                </h4>
                <p style="color: var(--gray); margin: 0;">
                    Reklamacje rozpatrujemy w terminie do 14 dni roboczych od dnia otrzymania zgłoszenia. 
                    W większości przypadków proces jest dużo szybszy.
                </p>
            </div>
        </section>

        <div style="padding: 2rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 12px; color: white; text-align: center;">
            <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">
                <i class="fas fa-headset"></i> Potrzebujesz pomocy?
            </h3>
            <p style="margin-bottom: 1rem; opacity: 0.9;">
                Nasz zespół obsługi klienta jest tutaj, aby Ci pomóc!
            </p>
            <p style="margin: 0; font-size: 1.125rem;">
                <strong>Email:</strong> kontakt@rapshop.pl<br>
                <strong>Telefon:</strong> +48 123 456 789<br>
                <strong>Godziny:</strong> Pon-Pt, 9:00-17:00
            </p>
        </div>
    </div>
</div>
@endsection
