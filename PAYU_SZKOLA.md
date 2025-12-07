# 💡 PayU dla Projektu Szkolnego

## Problem rozwiązany ✅

Projekt jest projektem szkolnym, więc nie potrzebujesz prawdziwego PayU. Dodałem tryb symulacji, który automatycznie obsługuje płatności bez łączenia się z PayU.

## Konfiguracja dla projektu szkolnego:

### W pliku `.env` dodaj:

```env
# Włącz tryb symulacji dla projektu szkolnego
PAYU_SIMULATE=true
```

### Co to robi:

1. **Gdy `PAYU_SIMULATE=true`**:
   - Płatności PayU są automatycznie symulowane
   - Zamówienia są automatycznie oznaczane jako opłacone
   - Nie ma potrzeby konfigurowania PayU sandbox
   - Idealne dla projektów szkolnych i prezentacji

2. **Gdy PayU sandbox nie działa** (nawet jeśli `PAYU_SIMULATE=false`):
   - System automatycznie wykryje błędy sandbox
   - W trybie debug/local automatycznie symuluje płatność
   - Pokazuje komunikat że to symulacja dla projektu szkolnego

## Jak używać:

### Opcja 1: Włącz symulację (zalecane dla projektów szkolnych)

W `.env`:
```env
PAYU_SIMULATE=true
```

### Opcja 2: Automatyczna symulacja przy błędach

Jeśli nie ustawisz `PAYU_SIMULATE=true`, system automatycznie symuluje płatność gdy:
- PayU sandbox jest niedostępny
- Występują błędy autoryzacji w sandbox
- Aplikacja jest w trybie debug/local

## Co się dzieje przy symulacji:

1. Użytkownik wybiera PayU jako metodę płatności
2. System automatycznie oznacza zamówienie jako opłacone
3. Użytkownik jest przekierowany do strony sukcesu
4. Widzi komunikat że płatność została symulowana

## Dla prezentacji/demo:

Możesz pokazać:
- ✅ Pełny przepływ płatności
- ✅ Wybór metody płatności
- ✅ Zamówienia oznaczone jako opłacone
- ✅ Wszystko działa bez prawdziwego PayU

## Jeśli chcesz przetestować prawdziwy PayU sandbox:

```env
PAYU_SIMULATE=false
PAYU_ENV=sandbox
# ... dane PayU sandbox
```

Ale dla projektu szkolnego **nie jest to konieczne** - symulacja wystarczy!

---

**Uwaga:** W produkcji (prawdziwym sklepie) ustaw `PAYU_SIMULATE=false` i użyj prawdziwych danych PayU.
