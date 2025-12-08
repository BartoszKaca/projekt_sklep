# ❓ FAQ - Często zadawane pytania

## Deployment

### Q: Czy muszę zatrzymywać całą aplikację?
**A:** Nie! Skrypt `quick-fix.sh` robi restart tylko Nginx i app. Możesz też zrobić graceful restart bez downtime:
```bash
docker-compose -f docker-compose.prod.yml restart app nginx
```

### Q: Jak długo trwa deployment?
**A:** 2-3 minuty całkowicie, downtime (jeśli jest) to maksymalnie 10-30 sekund.

### Q: Czy potrzebuję migracji bazy danych?
**A:** Nie! Wszystkie zmiany są tylko w kodzie PHP i konfiguracji Nginx.

### Q: Co jeśli coś pójdzie nie tak?
**A:** Masz automatyczny backup bazy danych. Możesz też zrobić rollback:
```bash
git checkout app/Observers/OrderObserver.php
git checkout app/Http/Controllers/Admin/ReportController.php
git checkout docker/nginx/conf.d/default.conf
docker-compose -f docker-compose.prod.yml restart
```

## Problemy z phpMyAdmin

### Q: phpMyAdmin pokazuje błąd 502 Bad Gateway
**A:** Sprawdź czy kontener działa:
```bash
docker ps | grep phpmyadmin
docker logs sklep_phpmyadmin
```
Jeśli nie działa:
```bash
docker-compose -f docker-compose.prod.yml restart phpmyadmin
```

### Q: phpMyAdmin się ładuje ale bez styli CSS
**A:** To problem z sub_filter. Sprawdź:
```bash
docker logs sklep_nginx | grep pma
```
Możesz wymusić przeładowanie:
```bash
docker-compose -f docker-compose.prod.yml restart nginx
```

### Q: Nie mogę się zalogować do phpMyAdmin
**A:** Sprawdź hasło w .env:
```bash
cat .env | grep DB_ROOT_PASSWORD
```
Domyślnie: root/root

### Q: phpMyAdmin przekierowuje mnie na /index.php
**A:** Problem z proxy_redirect. Upewnij się, że używasz najnowszej wersji default.conf i zrestartuj Nginx.

## Problemy z raportami

### Q: Raport inwentarza pokazuje błąd 500
**A:** Sprawdź logi:
```bash
docker exec sklep_app tail -n 100 /var/www/html/storage/logs/laravel.log
```
Jeśli widzisz błąd SQL, upewnij się że ReportController.php jest zaktualizowany.

### Q: Wartości w raporcie to 0 mimo że mam produkty
**A:** Sprawdź:
1. Czy produkty mają ustawioną cenę
2. Czy stock_quantity > 0
3. Czy są błędy w logach przy kalkulacji

### Q: Widzę błąd "whereHas" lub "subquery"
**A:** Stara wersja ReportController.php. Upewnij się że wgrałeś nową wersję.

## Problemy z zamówieniami

### Q: Nadal widzę błąd "_oldStatus"
**A:** 
1. Sprawdź czy OrderObserver.php ma `private static` (nie `protected static`)
2. Wyczyść cache:
```bash
docker exec sklep_app php artisan cache:clear
docker exec sklep_app php artisan config:clear
```

### Q: Emaile nie są wysyłane przy zmianie statusu
**A:** To oddzielny problem z SMTP. Sprawdź:
```bash
docker exec sklep_app tail /var/www/html/storage/logs/laravel.log | grep -i mail
```
Skonfiguruj SMTP w .env:
```
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
```

## Problemy z zarządzaniem stanami

### Q: /admin/products/{id}/stock pokazuje 404
**A:** Route jest OK, ale sprawdź:
1. Czy produkt o tym ID istnieje
2. Czy jesteś zalogowany jako admin
3. Czy nie ma innych błędów PHP

### Q: Nie mogę dodać/odjąć stanu
**A:** Sprawdź:
```bash
docker logs sklep_app | grep -i stock
```
Problem może być w StockController.php lub brakującej relacji.

## Docker

### Q: Kontenery nie startują po restarcie
**A:** Sprawdź logi:
```bash
docker-compose -f docker-compose.prod.yml logs
```
Najczęstsze przyczyny:
- Brak miejsca na dysku: `df -h`
- Port zajęty: `netstat -tulpn | grep :80`
- Błąd w konfiguracji: `docker-compose -f docker-compose.prod.yml config`

### Q: Jak sprawdzić użycie zasobów?
**A:**
```bash
docker stats
```

### Q: Kontenery zajmują dużo miejsca
**A:** Wyczyść stare obrazy i kontenery:
```bash
docker system prune -a
```

## Baza danych

### Q: Jak zrobić pełny backup?
**A:**
```bash
docker exec sklep_db mysqldump -u root -proot --all-databases > full_backup.sql
```

### Q: Jak przywrócić backup?
**A:**
```bash
docker exec -i sklep_db mysql -u root -proot sklep_laravel < backup.sql
```

### Q: Jak połączyć się z MySQL z zewnątrz?
**A:** Port 3306 jest wystawiony:
```bash
mysql -h bartoszkaca.online -u root -proot sklep_laravel
```

## Performance

### Q: Aplikacja jest wolna
**A:** Sprawdź:
1. Cache jest włączony: `docker exec sklep_app php artisan config:cache`
2. Redis działa: `docker ps | grep redis`
3. Optymalizuj obrazy: `php artisan storage:optimize`

### Q: phpMyAdmin jest wolny
**A:** Zwiększ limity w docker-compose.prod.yml:
```yaml
phpmyadmin:
  environment:
    MEMORY_LIMIT: 512M
    MAX_EXECUTION_TIME: 600
```

## Monitoring

### Q: Jak monitorować aplikację na żywo?
**A:**
```bash
# Logi wszystkich kontenerów
docker-compose -f docker-compose.prod.yml logs -f

# Tylko aplikacja
docker logs -f sklep_app

# Tylko Laravel
docker exec sklep_app tail -f /var/www/html/storage/logs/laravel.log
```

### Q: Jak sprawdzić błędy z ostatnich 24h?
**A:**
```bash
docker exec sklep_app tail -n 1000 /var/www/html/storage/logs/laravel.log | grep ERROR
```

## Bezpieczeństwo

### Q: Czy phpMyAdmin jest bezpieczny?
**A:** Domyślnie tak, ale zalecane jest dodanie Basic Auth (patrz FIXES_COMPLETE.md).

### Q: Jak zmienić hasło do bazy danych?
**A:**
1. Zmień w .env
2. Zmień w MySQL:
```bash
docker exec -it sklep_db mysql -u root -proot
ALTER USER 'root'@'%' IDENTIFIED BY 'nowe_haslo';
FLUSH PRIVILEGES;
```
3. Restart kontenerów

## Inne

### Q: Gdzie są pliki logów?
**A:**
- Laravel: `/var/www/html/storage/logs/laravel.log` (w kontenerze)
- Nginx: `/var/log/nginx/` (w kontenerze)
- Docker: `docker logs <container_name>`

### Q: Jak zaktualizować Laravel?
**A:** To wymaga osobnego procesu. Zobacz dokumentację Laravel upgrade guide.

### Q: Czy mogę używać tego na subdomain zamiast /pma?
**A:** Tak, możesz skonfigurować osobny server block w Nginx dla pma.bartoszkaca.online

---

**Nie znalazłeś odpowiedzi?**

Sprawdź logi:
```bash
docker-compose -f docker-compose.prod.yml logs --tail=100
```

Lub otwórz issue w dokumentacji projektu.
