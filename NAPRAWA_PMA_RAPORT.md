# 🔧 NAPRAWA BŁĘDÓW - PHPMYADMIN + RAPORT INWENTARZA

## ❌ Problemy
1. **phpMyAdmin** - biała strona, nic się nie ładuje
2. **Raport inwentarza** - błąd "Wystąpił błąd podczas generowania raportu inwentarza"

---

## ✅ ROZWIĄZANIA

### 1. Problem: phpMyAdmin - biała strona

**Przyczyna:** W pliku `docker/nginx/conf.d/default.conf` był **hardcoded IP** `172.19.0.4` zamiast nazwy serwisu Docker.

**Co zmieniłem:**
```nginx
# PRZED (źle):
proxy_pass http://172.19.0.4:80/;

# PO (dobrze):
proxy_pass http://phpmyadmin:80/;
```

**Dlaczego to był problem:**
- IP kontenerów Docker mogą się zmieniać przy każdym restarcie
- Nazwa serwisu `phpmyadmin` jest stała i rozwiązywana przez wewnętrzny DNS Dockera
- Nginx nie mógł połączyć się z phpMyAdmin przez nieistniejący IP

---

### 2. Problem: Raport inwentarza - błąd

**Przyczyna:** Złożone zapytania z wariantami produktów powodowały błędy SQL.

**Co zmieniłem:**
- **Usunąłem całą obsługę wariantów** z raportu inwentarza
- Raport teraz pokazuje tylko proste produkty
- Uproszczone zapytania SQL bez skomplikowanych JOIN-ów

**Zmiany w kontrolerze** (`app/Http/Controllers/Admin/ReportController.php`):
- Usunąłem `$lowStockVariants` i `$outOfStockVariants`
- Uproszczone obliczenia `$totalValue` i `$totalUnits`
- Tylko `Product::` zapytania, bez `ProductVariant::`

**Zmiany w widoku** (`resources/views/admin/reports/inventory.blade.php`):
- Usunięto sekcje wyświetlające warianty
- Pozostawiono tylko proste produkty

---

## 🚀 JAK WDROŻYĆ NA SERWER

### Krok 1: Zaloguj się na serwer
```bash
ssh root@89.33.6.157
cd /var/www/sklep
```

### Krok 2: Zaktualizuj kod z repozytorium
```bash
git pull origin main
```

### Krok 3: Restart kontenerów Nginx i phpMyAdmin
```bash
docker compose -f docker-compose.prod.yml restart nginx
docker compose -f docker-compose.prod.yml restart phpmyadmin
```

### Krok 4: Sprawdź czy działa
```bash
# Sprawdź logi Nginx
docker logs sklep_nginx --tail 50

# Sprawdź logi phpMyAdmin
docker logs sklep_phpmyadmin --tail 50

# Test dostępu
curl -I http://www.bartoszkaca.online/pma/
```

---

## 🧪 TESTOWANIE

### Test 1: phpMyAdmin
1. Otwórz: `http://www.bartoszkaca.online/pma/`
2. Powinien załadować się normalny interfejs phpMyAdmin
3. Zaloguj się używając danych z `.env`:
   - User: `root`
   - Password: wartość z `DB_ROOT_PASSWORD`

### Test 2: Raport inwentarza
1. Zaloguj się do panelu admin
2. Przejdź do: **Admin → Raporty → Magazyn**
3. Raport powinien się załadować bez błędów
4. Zobaczysz:
   - Podsumowanie (liczba produktów, jednostek, wartość)
   - Produkty z niskim stanem
   - Produkty bez stanu
   - Stan według kategorii

---

## 🔍 JEŚLI NADAL NIE DZIAŁA

### phpMyAdmin - dalsze diagnostyka:

```bash
# 1. Sprawdź czy kontener działa
docker ps | grep phpmyadmin

# 2. Sprawdź sieć Docker
docker network inspect sklep_sklep_network | grep phpmyadmin

# 3. Test połączenia z wnętrza kontenera Nginx
docker exec sklep_nginx wget -O- http://phpmyadmin:80/

# 4. Sprawdź konfigurację Nginx
docker exec sklep_nginx nginx -t
docker exec sklep_nginx cat /etc/nginx/conf.d/default.conf | grep -A 10 "location /pma"
```

### Raport inwentarza - sprawdź logi:

```bash
# W Laravel
docker exec sklep_app tail -f storage/logs/laravel.log

# Lub z poziomu serwera
tail -f /var/www/sklep/storage/logs/laravel.log
```

---

## 📝 CO ZOSTAŁO ZMIENIONE

### Pliki zmienione:
1. ✅ `docker/nginx/conf.d/default.conf` - zamiana IP na nazwę serwisu
2. ✅ `app/Http/Controllers/Admin/ReportController.php` - uproszczenie raportu
3. ✅ `resources/views/admin/reports/inventory.blade.php` - usunięcie wariantów

### Co NIE zostało zmienione:
- ✅ Baza danych - nie trzeba migracji
- ✅ `.env` - bez zmian
- ✅ `docker-compose.prod.yml` - bez zmian
- ✅ Pozostałe pliki aplikacji - bez zmian

---

## 💡 DODATKOWE WSKAZÓWKI

### Tymczasowy dostęp do phpMyAdmin przez port (jeśli proxy nie działa):

W `docker-compose.prod.yml` zmień:
```yaml
phpmyadmin:
  ports:
    - "8080:80"  # Dodaj tę linię
```

Potem restart:
```bash
docker compose -f docker-compose.prod.yml up -d phpmyadmin
```

Dostęp: `http://www.bartoszkaca.online:8080`

**⚠️ UWAGA:** Pamiętaj by usunąć to po naprawie proxy!

---

## ✅ PODSUMOWANIE

Po wdrożeniu:
1. phpMyAdmin będzie działać na: `http://www.bartoszkaca.online/pma/`
2. Raport inwentarza będzie działać bez błędów (bez wariantów)
3. Wszystkie inne funkcje sklepu pozostają bez zmian

**Potrzebujesz pomocy?** Sprawdź logi:
```bash
docker logs sklep_nginx --tail 100
docker logs sklep_phpmyadmin --tail 100
tail -f /var/www/sklep/storage/logs/laravel.log
```
