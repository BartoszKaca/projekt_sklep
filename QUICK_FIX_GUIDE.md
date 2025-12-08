# 🩹 QUICK FIX SUMMARY - Grudzień 2025

## Naprawione błędy:

### 1. ✅ Błąd 500: "Unknown column '_oldStatus'" 
**Plik:** `app/Observers/OrderObserver.php`
- Zmieniono `protected static` → `private static`
- Laravel nie będzie próbował zapisywać tych zmiennych do bazy danych

### 2. ✅ Błąd raportu inwentarza (/admin/reports/inventory)
**Plik:** `app/Http/Controllers/Admin/ReportController.php`
- Zastąpiono zagnieżdżone `whereHas` → `whereIn` z subqueries
- Dodano obsługę błędów dla wszystkich kalkulacji
- Naprawiono zapytania SQL dla albumsRevenue i merchRevenue

### 3. ✅ phpMyAdmin nie działa (/pma)
**Plik:** `docker/nginx/conf.d/default.conf`
- Dodano `X-Forwarded-Prefix` header
- Poprawiono proxy_redirect z regex
- Dodano więcej sub_filter dla różnych formatów URL
- Dodano osobną lokalizację dla statycznych zasobów
- Wyłączono kompresję dla sub_filter

## 🚀 Wdrożenie na produkcji:

### Opcja A: Automatyczne (ZALECANE)
```bash
# 1. Wgraj zaktualizowane pliki na serwer
scp app/Observers/OrderObserver.php root@bartoszkaca.online:/root/projekt_sklep/sklep/app/Observers/
scp app/Http/Controllers/Admin/ReportController.php root@bartoszkaca.online:/root/projekt_sklep/sklep/app/Http/Controllers/Admin/
scp docker/nginx/conf.d/default.conf root@bartoszkaca.online:/root/projekt_sklep/sklep/docker/nginx/conf.d/
scp quick-fix.sh root@bartoszkaca.online:/root/projekt_sklep/sklep/

# 2. Uruchom skrypt naprawy
ssh root@bartoszkaca.online
cd /root/projekt_sklep/sklep
chmod +x quick-fix.sh
./quick-fix.sh
```

### Opcja B: Manualne kroki
```bash
# 1. SSH na serwer
ssh root@bartoszkaca.online
cd /root/projekt_sklep/sklep

# 2. Backup
docker exec sklep_db mysqldump -u root -proot sklep_laravel > backup_$(date +%Y%m%d_%H%M%S).sql

# 3. Wgraj pliki (z lokalnej maszyny w osobnym terminalu):
scp app/Observers/OrderObserver.php root@bartoszkaca.online:/root/projekt_sklep/sklep/app/Observers/
scp app/Http/Controllers/Admin/ReportController.php root@bartoszkaca.online:/root/projekt_sklep/sklep/app/Http/Controllers/Admin/
scp docker/nginx/conf.d/default.conf root@bartoszkaca.online:/root/projekt_sklep/sklep/docker/nginx/conf.d/

# 4. Restart (na serwerze)
docker-compose -f docker-compose.prod.yml down
docker-compose -f docker-compose.prod.yml up -d

# 5. Wyczyść cache
docker exec sklep_app php artisan config:clear
docker exec sklep_app php artisan cache:clear
docker exec sklep_app php artisan route:clear
docker exec sklep_app php artisan view:clear

# 6. Restart Nginx
docker-compose -f docker-compose.prod.yml restart nginx
```

## ✅ Testy po wdrożeniu:

1. **phpMyAdmin**: http://bartoszkaca.online/pma
   - Login: root / root
   
2. **Raport inwentarza**: http://bartoszkaca.online/admin/reports/inventory

3. **Zarządzanie stanami**: http://bartoszkaca.online/admin/products/1/stock

4. **Sprawdź logi**:
   ```bash
   docker logs sklep_app --tail=50
   docker exec sklep_app tail /var/www/html/storage/logs/laravel.log
   ```

## 📊 Monitorowanie:

```bash
# Logi na żywo
docker logs -f sklep_app
docker logs -f sklep_nginx

# Status kontenerów
docker-compose -f docker-compose.prod.yml ps

# Logi Laravel
docker exec sklep_app tail -f /var/www/html/storage/logs/laravel.log
```

## ⚠️ Uwagi:

- Wszystkie zmiany są backward-compatible
- Nie ma potrzeby migracji bazy danych
- Zero downtime możliwe (restart tylko Nginx, nie PHP-FPM)
- Backupy tworzone automatycznie przez skrypt

## 🔄 Rollback (jeśli potrzebny):

```bash
# Przywróć poprzednie pliki
git checkout app/Observers/OrderObserver.php
git checkout app/Http/Controllers/Admin/ReportController.php
git checkout docker/nginx/conf.d/default.conf

# Restart
docker-compose -f docker-compose.prod.yml restart
```

---

**Czas wdrożenia:** ~2-3 minuty  
**Downtime:** 0-30 sekund (opcjonalny)  
**Poziom ryzyka:** Niski ✅
