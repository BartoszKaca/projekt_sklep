@extends('layouts.shop')

@section('title', 'Dostawa i płatność')

@section('content')
<div class="container" style="max-width: 900px; padding: 3rem 2rem;">
    <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; color: var(--dark);">Dostawa i płatność</h1>
    
    <div style="background: white; border-radius: 16px; padding: 2.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        
        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                <i class="fas fa-shipping-fast"></i> Metody dostawy
            </h2>
            
            <div style="margin-bottom: 1.5rem; padding: 1.5rem; background: var(--light); border-radius: 12px;">
                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--dark);">
                    📦 Kurier InPost / DPD / DHL
                </h3>
                <p style="margin-bottom: 0.5rem; color: var(--gray);">
                    <strong>Koszt:</strong> 14,99 zł (darmowa dostawa od 100 zł)
                </p>
                <p style="margin-bottom: 0.5rem; color: var(--gray);">
                    <strong>Czas dostawy:</strong> 1-2 dni robocze
                </p>
                <p style="color: var(--gray);">
                    Przesyłka dostarczana jest bezpośrednio pod wskazany adres. 
                    Kurier kontaktuje się telefonicznie w celu ustalenia dogodnego terminu.
                </p>
            </div>

            <div style="margin-bottom: 1.5rem; padding: 1.5rem; background: var(--light); border-radius: 12px;">
                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--dark);">
                    📮 Paczkomaty InPost
                </h3>
                <p style="margin-bottom: 0.5rem; color: var(--gray);">
                    <strong>Koszt:</strong> 11,99 zł (darmowa dostawa od 100 zł)
                </p>
                <p style="margin-bottom: 0.5rem; color: var(--gray);">
                    <strong>Czas dostawy:</strong> 1-2 dni robocze
                </p>
                <p style="color: var(--gray);">
                    Paczka trafia do wybranego przez Ciebie Paczkomatu InPost. 
                    Możesz odebrać ją o dowolnej porze, 24/7.
                </p>
            </div>

            <div style="margin-bottom: 1.5rem; padding: 1.5rem; background: var(--light); border-radius: 12px;">
                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--dark);">
                    📬 Poczta Polska
                </h3>
                <p style="margin-bottom: 0.5rem; color: var(--gray);">
                    <strong>Koszt:</strong> 10,99 zł
                </p>
                <p style="margin-bottom: 0.5rem; color: var(--gray);">
                    <strong>Czas dostawy:</strong> 2-4 dni robocze
                </p>
                <p style="color: var(--gray);">
                    Przesyłka dostarczana przez listonosza. 
                    W przypadku nieobecności zostanie pozostawione zawiadomienie o awizo.
                </p>
            </div>

            <div style="padding: 1.5rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 12px; color: white;">
                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.75rem;">
                    <i class="fas fa-gift"></i> Darmowa dostawa!
                </h3>
                <p style="margin-bottom: 0;">
                    Przy zamówieniach powyżej 100 zł dostawa kurierem lub do Paczkomatu jest całkowicie darmowa!
                </p>
            </div>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                <i class="fas fa-clock"></i> Czas realizacji
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Zamówienia składane do godziny 14:00 w dni robocze są pakowane i wysyłane tego samego dnia. 
                Zamówienia złożone po godzinie 14:00 oraz w weekendy są realizowane następnego dnia roboczego.
            </p>
            <p style="line-height: 1.8; color: var(--gray);">
                Po nadaniu przesyłki otrzymasz e-mail z numerem śledzenia, dzięki któremu będziesz mógł śledzić 
                status swojej paczki w czasie rzeczywistym.
            </p>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                <i class="fas fa-credit-card"></i> Metody płatności
            </h2>
            
            <div style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--dark);">
                    💳 Płatność online (PayU, Przelewy24)
                </h3>
                <p style="margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                    Szybka i bezpieczna płatność online za pomocą:
                </p>
                <ul style="margin-left: 2rem; margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                    <li>Karty płatnicze (Visa, Mastercard, Maestro)</li>
                    <li>BLIK</li>
                    <li>Przelew bankowy</li>
                    <li>PayPal</li>
                    <li>Apple Pay / Google Pay</li>
                </ul>
                <p style="color: var(--gray); line-height: 1.8;">
                    Po dokonaniu płatności zamówienie jest automatycznie przekazywane do realizacji.
                </p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--dark);">
                    🏦 Przelew tradycyjny
                </h3>
                <p style="margin-bottom: 1rem; color: var(--gray); line-height: 1.8;">
                    Możesz dokonać przelewu na nasz rachunek bankowy:
                </p>
                <p style="margin-bottom: 0.5rem; color: var(--gray);">
                    <strong>Odbiorca:</strong> [Nazwa firmy]
                </p>
                <p style="margin-bottom: 0.5rem; color: var(--gray);">
                    <strong>Numer konta:</strong> XX XXXX XXXX XXXX XXXX XXXX XXXX
                </p>
                <p style="margin-bottom: 0.5rem; color: var(--gray);">
                    <strong>Tytuł przelewu:</strong> Zamówienie [numer zamówienia]
                </p>
                <p style="color: var(--gray); line-height: 1.8; margin-top: 1rem;">
                    <em>Uwaga: Zamówienie zostanie zrealizowane po zaksięgowaniu wpłaty na naszym koncie (zazwyczaj 1-2 dni robocze).</em>
                </p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.75rem; color: var(--dark);">
                    💵 Płatność za pobraniem
                </h3>
                <p style="margin-bottom: 0.5rem; color: var(--gray); line-height: 1.8;">
                    <strong>Dodatkowy koszt:</strong> 5 zł
                </p>
                <p style="color: var(--gray); line-height: 1.8;">
                    Płatność gotówką lub kartą przy odbiorze przesyłki od kuriera. 
                    Ta opcja dostępna jest tylko dla przesyłek kurierskich.
                </p>
            </div>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                <i class="fas fa-shield-alt"></i> Bezpieczeństwo płatności
            </h2>
            <p style="margin-bottom: 1rem; line-height: 1.8; color: var(--gray);">
                Wszystkie transakcje są zabezpieczone certyfikatem SSL. Dane płatnicze są przetwarzane 
                przez licencjonowanych operatorów płatności (PayU, Przelewy24), którzy spełniają najwyższe 
                standardy bezpieczeństwa PCI DSS.
            </p>
            <p style="line-height: 1.8; color: var(--gray);">
                Nie przechowujemy danych kart płatniczych na naszych serwerach.
            </p>
        </section>

        <section style="margin-bottom: 2.5rem;">
            <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary);">
                <i class="fas fa-question-circle"></i> Najczęściej zadawane pytania
            </h2>
            
            <div style="margin-bottom: 1rem; padding: 1rem; border-left: 4px solid var(--primary); background: var(--light);">
                <h4 style="font-weight: 700; margin-bottom: 0.5rem; color: var(--dark);">
                    Czy mogę odebrać zamówienie osobiście?
                </h4>
                <p style="color: var(--gray);">
                    Obecnie nie oferujemy możliwości odbioru osobistego, ale pracujemy nad uruchomieniem tego rozwiązania.
                </p>
            </div>

            <div style="margin-bottom: 1rem; padding: 1rem; border-left: 4px solid var(--primary); background: var(--light);">
                <h4 style="font-weight: 700; margin-bottom: 0.5rem; color: var(--dark);">
                    Co jeśli nie ma mnie w domu przy dostawie kurierskiej?
                </h4>
                <p style="color: var(--gray);">
                    Kurier skontaktuje się z Tobą telefonicznie, aby ustalić dogodny termin dostawy. 
                    Możesz też przekierować paczkę do najbliższego punktu odbioru.
                </p>
            </div>

            <div style="padding: 1rem; border-left: 4px solid var(--primary); background: var(--light);">
                <h4 style="font-weight: 700; margin-bottom: 0.5rem; color: var(--dark);">
                    Czy wysyłacie za granicę?
                </h4>
                <p style="color: var(--gray);">
                    Obecnie wysyłamy tylko na terenie Polski. Jeśli jesteś zainteresowany dostawą zagraniczną, 
                    skontaktuj się z nami: kontakt@rapshop.pl
                </p>
            </div>
        </section>

        <div style="padding: 1.5rem; background: var(--light); border-radius: 12px; text-align: center;">
            <p style="color: var(--gray); margin-bottom: 0.5rem;">
                <strong>Masz pytania?</strong>
            </p>
            <p style="color: var(--gray);">
                Skontaktuj się z nami: <strong>kontakt@rapshop.pl</strong> lub <strong>+48 123 456 789</strong>
            </p>
        </div>
    </div>
</div>
@endsection
