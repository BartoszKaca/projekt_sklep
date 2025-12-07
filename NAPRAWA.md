# 🔧 Naprawa problemów z kontenerami

## Problem
Aplikacja wymaga Redis, ale:
1. Dockerfile lokalny nie miał rozszerzenia Redis
2. docker-compose.yml nie miał serwisu Redis

## Rozwiązanie

### Lokalnie:

1. **Przebuduj kontenery:**
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

2. **Sprawdź czy wszystko działa:**
```bash
docker-compose ps
docker-compose logs app
curl http://localhost:8000
```

3. **Wyczyść cache Laravel:**
```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Na serwerze:

1. **Połącz się z serwerem:**
```bash
ssh -i klucz.pem ubuntu@TWOJ_IP
cd /var/www/sklep
```

2. **Pobierz najnowsze zmiany:**
```bash
git pull origin main
```

3. **Przebuduj kontenery:**
```bash
./deploy.sh rebuild
```

4. **Sprawdź logi:**
```bash
./deploy.sh logs app
./deploy.sh status
```

5. **Jeśli nadal są problemy:**
```bash
# Wyczyść cache
./deploy.sh artisan config:clear
./deploy.sh artisan cache:clear
./deploy.sh artisan route:clear
./deploy.sh artisan view:clear

# Sprawdź uprawnienia
docker compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache
docker compose -f docker-compose.prod.yml exec app chown -R www:www storage bootstrap/cache

# Restart
docker compose -f docker-compose.prod.yml restart app nginx
```

## Co zostało zmienione:

1. **Dockerfile** - dodano instalację rozszerzenia Redis
2. **docker-compose.yml** - dodano serwis Redis i zależności
