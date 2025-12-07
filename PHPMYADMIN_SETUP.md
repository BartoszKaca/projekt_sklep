# 🔧 Konfiguracja phpMyAdmin na subdomenie

## Problem
Brak dostępu do phpMyAdmin pod adresem `pma.domenaserwera`.

## Rozwiązanie

### 1. **Utworzono konfigurację Nginx dla phpMyAdmin**
   - Nowy plik: `docker/nginx/conf.d/phpmyadmin.conf`
   - Konfiguracja proxy do kontenera phpMyAdmin
   - Obsługuje subdomenę `pma.*` (możesz zmienić na konkretną domenę)

### 2. **Zaktualizowano docker-compose.prod.yml**
   - Zmieniono mapowanie portów phpMyAdmin z `8080:80` na `expose: 80`
   - phpMyAdmin dostępne tylko wewnątrz sieci Docker (bezpieczniejsze)
   - Dodano `phpmyadmin` do zależności Nginx
   - Zmieniono mapowanie wolumenów Nginx, aby ładować wszystkie pliki z `conf.d/`

### 3. **Konfiguracja DNS**

Musisz skonfigurować rekord DNS dla subdomeny `pma`:

```
Typ: A
Nazwa: pma
Wartość: IP_serwera (to samo IP co główna domena)
TTL: 3600
```

Przykład:
- Główna domena: `twoja-domena.pl` → `123.456.789.0`
- Subdomena phpMyAdmin: `pma.twoja-domena.pl` → `123.456.789.0`

### 4. **Dostęp do phpMyAdmin**

Po konfiguracji DNS będzie dostępne pod:
- **HTTP**: `http://pma.twoja-domena.pl`
- **HTTPS** (po konfiguracji SSL): `https://pma.twoja-domena.pl`

### 5. **Wdrożenie zmian na serwerze**

```bash
cd /var/www/sklep
git pull origin main

# Przeładuj konfigurację Nginx
./deploy.sh rebuild

# Lub jeśli kontenery już działają:
docker compose -f docker-compose.prod.yml restart nginx
docker compose -f docker-compose.prod.yml restart phpmyadmin
```

### 6. **Weryfikacja**

```bash
# Sprawdź czy kontener phpMyAdmin działa
docker ps | grep phpmyadmin

# Sprawdź logi Nginx
docker logs sklep_nginx | grep pma

# Sprawdź logi phpMyAdmin
docker logs sklep_phpmyadmin

# Test dostępu (z serwera)
curl -I http://localhost -H "Host: pma.twoja-domena.pl"
```

### 7. **Uwagi bezpieczeństwa**

⚠️ **WAŻNE**: 
- phpMyAdmin jest dostępne publicznie - upewnij się, że masz silne hasło do bazy danych
- Rozważ dodanie dodatkowego uwierzytelniania (np. HTTP Basic Auth)
- Po konfiguracji SSL, włącz HTTPS i wyłącz HTTP
- Rozważ ograniczenie dostępu do phpMyAdmin tylko z określonych IP

### 8. **Opcjonalne: HTTP Basic Auth**

Możesz dodać dodatkowe zabezpieczenie w konfiguracji Nginx:

```nginx
# W docker/nginx/conf.d/phpmyadmin.conf
location / {
    auth_basic "phpMyAdmin Access";
    auth_basic_user_file /etc/nginx/.htpasswd;
    
    proxy_pass http://phpmyadmin:80;
    # ... reszta konfiguracji
}
```

Utwórz plik z hasłem:
```bash
htpasswd -c /etc/nginx/.htpasswd admin
```

I dodaj do docker-compose.prod.yml:
```yaml
volumes:
  - ./docker/nginx/.htpasswd:/etc/nginx/.htpasswd:ro
```

### 9. **Troubleshooting**

**Problem: 502 Bad Gateway**
- Sprawdź czy kontener phpMyAdmin działa: `docker ps | grep phpmyadmin`
- Sprawdź logi: `docker logs sklep_phpmyadmin`

**Problem: 404 Not Found**
- Sprawdź konfigurację DNS
- Sprawdź logi Nginx: `docker logs sklep_nginx`

**Problem: Connection refused**
- Sprawdź czy phpMyAdmin jest w tej samej sieci co Nginx
- Sprawdź `docker network inspect sklep_sklep_network`

---

**Po wdrożeniu zmian, pamiętaj o:**
1. ✅ Konfiguracji DNS dla subdomeny `pma`
2. ✅ Przeładowaniu Nginx (`docker compose restart nginx`)
3. ✅ Weryfikacji dostępu
