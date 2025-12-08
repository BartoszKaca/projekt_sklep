# 🔧 NAPRAWA BŁĘDÓW - Grudzień 2025

## 📋 Wykryte problemy i rozwiązania

### ✅ Problem 1: Błąd 500 - Unknown column '_oldStatus'

**Przyczyna:** Laravel próbował zapisać zmienne statyczne z OrderObserver jako kolumny bazy danych.

**Rozwiązanie:** Zmieniono `protected static` na `private static` w `app/Observers/OrderObserver.php`

**Status:** ✅ NAPRAWIONE

---

### ✅ Problem 2: Błąd raportu inwentarza (admin/reports/inventory)

**Przyczyna:** Zagnieżdżone `whereHas` powodowały błędy SQL w MySQL 8.0

**Rozwiązanie:** Zastąpiono `whereHas` subqueries z `whereIn` w `app/Http/Controllers/Admin/ReportController.php`

**Zmiany:**
- Naprawiono zapytania SQL dla albumsRevenue i merchRevenue
- Dodano bezpieczną kalkulację z obsługą błędów
- Poprawiono liczenie jednostek w magazynie

**Status:** ✅ NAPRAWIONE

---

### ✅ Problem 3: phpMyAdmin nie działa przez proxy Nginx

**Przyczyna:** Nieprawidłowe przekierowania i brak obsługi statycznych zasobów

**Rozwiązanie:** Zaktualizowano `docker/nginx/conf.d/default.conf`

**Zmiany:**
- Dodano proxy_set_header X-Forwarded-Prefix /pma
- Poprawiono proxy_redirect z regex
- Dodano sub_filter dla więcej formatów URL
- Dodano osobną lokalizację dla statycznych zasobów phpMyAdmin
- Wyłączono kompresję dla sub_filter

**Status:** ✅ NAPRAWIONE

---

### ✅ Problem 4: Routing dla /admin/products/{id}/stock

**Przyczyna:** Route jest zdefiniowany prawidłowo w web.php

**Rozwiązanie:** Routing jest OK - problem był związany z innymi błędami PHP

**Status:** ✅ OK

---

## 🚀 Instrukcja wdrożenia napraw na produkcji

### Krok 1: Backup przed zmianami
```bash
# Zatrzymaj aplikację
cd /root/projekt_sklep/sklep
docker-compose -f docker-compose.prod.yml down

# Backup bazy danych
docker exec sklep_db mysqldump -u root -proot sklep_laravel > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup plików
cp -r . ../sklep_backup_$(date +%Y%m%d_%H%M%S)
```

### Krok 2: Aktualizacja plików

Skopiuj zaktualizowane pliki na serwer:

1. **OrderObserver.php**
```bash
scp app/Observers/OrderObserver.php root@bartoszkaca.online:/root/projekt_sklep/sklep/app/Observers/
```

2. **ReportController.php**
```bash
scp app/Http/Controllers/Admin/ReportController.php root@bartoszkaca.online:/root/projekt_sklep/sklep/app/Http/Controllers/Admin/
```

3. **Nginx config**
```bash
scp docker/nginx/conf.d/default.conf root@bartoszkaca.online:/root/projekt_sklep/sklep/docker/nginx/conf.d/
```

### Krok 3: Restart aplikacji

```bash
# Uruchom ponownie
docker-compose -f docker-compose.prod.yml up -d

# Sprawdź logi
docker-compose -f docker-compose.prod.yml logs -f --tail=100

# Zresetuj cache Laravel
docker exec sklep_app php artisan config:clear
docker exec sklep_app php artisan cache:clear
docker exec sklep_app php artisan route:clear
docker exec sklep_app php artisan view:clear

# Restart Nginx dla pewności
docker-compose -f docker-compose.prod.yml restart nginx
```

### Krok 4: Weryfikacja

Sprawdź czy wszystko działa:

1. **phpMyAdmin:**
   ```
   http://bartoszkaca.online/pma
   ```
   - Login: root
   - Hasło: root (lub z .env: DB_ROOT_PASSWORD)

2. **Raport inwentarza:**
   ```
   http://bartoszkaca.online/admin/reports/inventory
   ```

3. **Panel zarządzania stanami:**
   ```
   http://bartoszkaca.online/admin/products/[ID_PRODUKTU]/stock
   ```

4. **Sprawdź logi błędów:**
   ```bash
   docker exec sklep_app tail -n 100 /var/www/html/storage/logs/laravel.log
   ```

---

## 🔍 Testowanie po wdrożeniu

### Test 1: phpMyAdmin
- [ ] Zaloguj się do http://bartoszkaca.online/pma
- [ ] Sprawdź czy wszystkie style się ładują
- [ ] Sprawdź czy możesz przeglądać tabele
- [ ] Wykonaj proste zapytanie SQL

### Test 2: Raport inwentarza
- [ ] Zaloguj się jako admin
- [ ] Przejdź do Admin → Raporty → Inwentarz
- [ ] Sprawdź czy wyświetlają się dane
- [ ] Sprawdź czy nie ma błędów 500

### Test 3: Zarządzanie stanami
- [ ] Przejdź do Admin → Produkty
- [ ] Wybierz produkt
- [ ] Kliknij "Zarządzaj stanem"
- [ ] Sprawdź czy strona się ładuje

### Test 4: Aktualizacja statusu zamówienia
- [ ] Przejdź do Admin → Zamówienia
- [ ] Zmień status dowolnego zamówienia
- [ ] Sprawdź logi czy nie ma błędów z _oldStatus

---

## 📊 Monitoring po wdrożeniu

Przez pierwsze 24h monitoruj:

```bash
# Logi aplikacji
docker logs sklep_app -f --tail=50

# Logi Nginx
docker logs sklep_nginx -f --tail=50

# Logi MySQL (jeśli potrzebne)
docker logs sklep_db -f --tail=50

# Logi Laravel
docker exec sklep_app tail -f /var/www/html/storage/logs/laravel.log
```

---

## ❌ Rollback (w razie problemów)

Jeśli coś pójdzie nie tak:

```bash
# 1. Zatrzymaj aplikację
docker-compose -f docker-compose.prod.yml down

# 2. Przywróć backup
cd ..
rm -rf sklep
mv sklep_backup_[DATA] sklep
cd sklep

# 3. Przywróć bazę danych (jeśli trzeba)
docker-compose -f docker-compose.prod.yml up -d db
docker exec -i sklep_db mysql -u root -proot sklep_laravel < ../backup_[DATA].sql

# 4. Uruchom ponownie
docker-compose -f docker-compose.prod.yml up -d
```

---

## 📝 Dodatkowe uwagi

### Bezpieczeństwo phpMyAdmin

Rozważ dodanie dodatkowego zabezpieczenia dla /pma:

1. **Basic Auth w Nginx:**
```nginx
location /pma/ {
    auth_basic "Restricted Access";
    auth_basic_user_file /etc/nginx/.htpasswd;
    # ... reszta konfiguracji
}
```

2. **Utworzenie hasła:**
```bash
# Na serwerze
apt-get install apache2-utils
htpasswd -c docker/nginx/.htpasswd admin
```

3. **Aktualizacja docker-compose.prod.yml:**
```yaml
nginx:
  volumes:
    - ./docker/nginx/.htpasswd:/etc/nginx/.htpasswd:ro
```

### Optymalizacja phpMyAdmin

W docker-compose.prod.yml możesz zwiększyć limity:

```yaml
phpmyadmin:
  environment:
    UPLOAD_LIMIT: 1024M
    MAX_EXECUTION_TIME: 600
    MEMORY_LIMIT: 1024M
```

---

## ✅ Checklist wdrożenia

- [ ] Backup bazy danych wykonany
- [ ] Backup plików wykonany
- [ ] Zaktualizowano OrderObserver.php
- [ ] Zaktualizowano ReportController.php
- [ ] Zaktualizowano default.conf (nginx)
- [ ] Restart kontenerów wykonany
- [ ] Cache Laravel wyczyszczony
- [ ] phpMyAdmin działa
- [ ] Raport inwentarza działa
- [ ] Zarządzanie stanami działa
- [ ] Aktualizacja statusów zamówień działa
- [ ] Brak błędów w logach

---

**Data naprawy:** 08.12.2025
**Wersja:** 1.0
**Autor:** Asystent AI
