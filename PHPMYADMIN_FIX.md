# 🔧 Naprawa phpMyAdmin - dostęp przez /pma

## Problem
phpMyAdmin nie działa przez `/pma` - kontener działa, ale proxy Nginx nie działa poprawnie.

## Rozwiązanie

### 1. **Poprawiona konfiguracja Nginx**
   - Użyto nazwy serwisu `phpmyadmin` zamiast nazwy kontenera
   - Uproszczono konfigurację proxy
   - Dodano sub_filter dla naprawy ścieżek w HTML/CSS/JS

### 2. **Dodano zmienną środowiskową dla Apache**
   - `APACHE_SERVER_NAME: localhost` - usuwa ostrzeżenie Apache

## Testowanie na serwerze:

### 1. Sprawdź czy kontener działa:
```bash
docker ps | grep phpmyadmin
```

### 2. Sprawdź logi Nginx:
```bash
docker logs sklep_nginx --tail 50
```

### 3. Sprawdź czy Nginx może połączyć się z phpMyAdmin:
```bash
docker exec sklep_nginx wget -O- http://phpmyadmin:80/
```

### 4. Sprawdź logi phpMyAdmin:
```bash
docker logs sklep_phpmyadmin --tail 50
```

### 5. Przetestuj dostęp:
```bash
curl -I http://localhost/pma/
```

## Jeśli nadal nie działa:

### Opcja 1: Sprawdź nazwę serwisu w docker-compose
```bash
docker compose -f docker-compose.prod.yml ps
```

Nazwa serwisu powinna być `phpmyadmin` (nie `sklep_phpmyadmin`).

### Opcja 2: Sprawdź sieć Docker
```bash
docker network inspect sklep_sklep_network | grep phpmyadmin
```

### Opcja 3: Tymczasowo wystaw port phpMyAdmin
W `docker-compose.prod.yml` zmień:
```yaml
phpmyadmin:
  ports:
    - "8080:80"  # Tymczasowo
```

I sprawdź czy działa na `http://domena:8080`

### Opcja 4: Sprawdź konfigurację Nginx
```bash
docker exec sklep_nginx nginx -t
docker exec sklep_nginx cat /etc/nginx/conf.d/default.conf | grep -A 20 "location /pma"
```

## Wdrożenie:

```bash
cd /var/www/sklep
git pull origin main
docker compose -f docker-compose.prod.yml restart nginx
docker compose -f docker-compose.prod.yml restart phpmyadmin
```

## Alternatywne rozwiązanie - bezpośredni dostęp przez port:

Jeśli proxy nadal nie działa, możesz tymczasowo wystawić phpMyAdmin na porcie:

```yaml
phpmyadmin:
  ports:
    - "8080:80"
```

I dostęp będzie przez: `http://domena:8080`

---

**Po wdrożeniu sprawdź:**
1. ✅ `http://domena/pma/` - powinno przekierować na phpMyAdmin
2. ✅ Sprawdź logi Nginx jeśli nie działa
3. ✅ Sprawdź czy kontener phpMyAdmin jest w tej samej sieci co Nginx
