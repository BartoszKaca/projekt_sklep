# ✅ DEPLOYMENT CHECKLIST

## Przed wdrożeniem

- [ ] Sprawdź aktualny status produkcji
- [ ] Upewnij się, że masz dostęp SSH do serwera
- [ ] Sprawdź czy masz backup przed zmianami
- [ ] Przeczytaj QUICK_FIX_GUIDE.md

## Wdrożenie

### Krok 1: Przygotowanie
```bash
# Na lokalnej maszynie
cd /Users/kaca/Documents/projekt_sklep/sklep

# Oznacz skrypty jako wykonywalne
chmod +x quick-fix.sh
chmod +x test-fixes.sh
```

- [ ] Skrypty oznaczone jako wykonywalne

### Krok 2: Upload plików na serwer
```bash
# Wgraj naprawione pliki
scp app/Observers/OrderObserver.php root@bartoszkaca.online:/root/projekt_sklep/sklep/app/Observers/

scp app/Http/Controllers/Admin/ReportController.php root@bartoszkaca.online:/root/projekt_sklep/sklep/app/Http/Controllers/Admin/

scp docker/nginx/conf.d/default.conf root@bartoszkaca.online:/root/projekt_sklep/sklep/docker/nginx/conf.d/

# Wgraj skrypt wdrożeniowy
scp quick-fix.sh root@bartoszkaca.online:/root/projekt_sklep/sklep/
```

- [ ] OrderObserver.php wgrany
- [ ] ReportController.php wgrany
- [ ] default.conf wgrany
- [ ] quick-fix.sh wgrany

### Krok 3: Uruchomienie na serwerze
```bash
# Połącz się z serwerem
ssh root@bartoszkaca.online

# Przejdź do katalogu projektu
cd /root/projekt_sklep/sklep

# Nadaj uprawnienia
chmod +x quick-fix.sh

# Uruchom skrypt naprawy
./quick-fix.sh
```

- [ ] Połączono z serwerem
- [ ] Skrypt uruchomiony
- [ ] Brak błędów podczas wykonywania

### Krok 4: Weryfikacja podstawowa

Na serwerze:
```bash
# Sprawdź status kontenerów
docker-compose -f docker-compose.prod.yml ps

# Sprawdź logi
docker logs sklep_app --tail=50
docker logs sklep_nginx --tail=50
```

- [ ] Wszystkie kontenery działają (Up)
- [ ] Brak błędów w logach

## Po wdrożeniu - Testy manualne

### Test 1: phpMyAdmin
1. Otwórz: http://bartoszkaca.online/pma
2. Zaloguj się: root / root (lub hasło z .env)
3. Sprawdź czy:
   - [ ] Strona się ładuje
   - [ ] Style CSS działają
   - [ ] Możesz przeglądać tabele
   - [ ] Możesz wykonać zapytanie SQL

### Test 2: Raport inwentarza
1. Zaloguj się do panelu admina
2. Przejdź do: Admin → Raporty → Inwentarz
3. Sprawdź czy:
   - [ ] Strona się ładuje bez błędu 500
   - [ ] Wyświetlają się dane
   - [ ] Wszystkie sekcje działają (Niski stan, Brak w magazynie)
   - [ ] Wartość zapasów jest policzona

### Test 3: Zarządzanie stanami magazynowymi
1. Przejdź do: Admin → Produkty
2. Wybierz dowolny produkt
3. Kliknij "Zarządzaj stanem"
4. Sprawdź czy:
   - [ ] Strona /admin/products/{id}/stock się ładuje
   - [ ] Możesz dodać/odjąć stan
   - [ ] Historia zmian się wyświetla

### Test 4: Aktualizacja statusów zamówień
1. Przejdź do: Admin → Zamówienia
2. Wybierz dowolne zamówienie
3. Zmień status zamówienia
4. Sprawdź czy:
   - [ ] Status się zmienia bez błędu
   - [ ] Brak błędu "_oldStatus" w logach
   - [ ] Email został wysłany (sprawdź logi)

## Monitoring przez 24h

### Logi do obserwacji
```bash
# Na serwerze
docker logs -f sklep_app | grep -i "error\|exception\|failed"
docker exec sklep_app tail -f /var/www/html/storage/logs/laravel.log
```

- [ ] Brak błędów _oldStatus
- [ ] Brak błędów w raportach
- [ ] phpMyAdmin działa stabilnie
- [ ] Email notifications działają

## W razie problemów

### Rollback
```bash
# Na serwerze
cd /root/projekt_sklep/sklep
docker-compose -f docker-compose.prod.yml down

# Przywróć backup
cd ..
rm -rf sklep
mv sklep_backup_[DATA] sklep
cd sklep

# Uruchom ponownie
docker-compose -f docker-compose.prod.yml up -d
```

### Support
- [ ] Sprawdź logi: `docker logs sklep_app`
- [ ] Sprawdź Laravel logs: `docker exec sklep_app tail /var/www/html/storage/logs/laravel.log`
- [ ] Sprawdź status: `docker-compose -f docker-compose.prod.yml ps`

## Podsumowanie wdrożenia

Data: ________________
Godzina: ________________
Wykonał: ________________

### Naprawione problemy:
- [ ] ✅ Błąd "_oldStatus" w OrderObserver
- [ ] ✅ Błąd 500 w raporcie inwentarza
- [ ] ✅ phpMyAdmin przez proxy Nginx
- [ ] ✅ Routing dla zarządzania stanami

### Status po wdrożeniu:
- [ ] Wszystkie testy przeszły
- [ ] Brak błędów w logach
- [ ] Monitoring aktywny

### Uwagi:
_________________________________________________
_________________________________________________
_________________________________________________

---

**Ważne:** Zachowaj ten checklist dla dokumentacji!
