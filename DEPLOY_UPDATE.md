# 🚀 Wdrożenie zmian na serwerze

## Szybka instrukcja

### 1. Na lokalnym komputerze:

```bash
# Zbuduj assets (jeśli były zmiany w JS/CSS)
npm run build

# Commit i push zmian
git add .
git commit -m "Fix: Naprawiono PayU sandbox, kuponów i wariantów produktów"
git push origin main
```

### 2. Na serwerze:

```bash
# Połącz się z serwerem
ssh -i twoj-klucz.pem ubuntu@TWOJ_IP

# Przejdź do katalogu projektu
cd /var/www/sklep

# Pobierz najnowsze zmiany
git pull origin main

# Wyczyść cache konfiguracji (ważne!)
./deploy.sh artisan config:clear
./deploy.sh artisan cache:clear
./deploy.sh artisan route:clear
./deploy.sh artisan view:clear

# Szybka aktualizacja (bez przebudowy kontenerów)
./deploy.sh update

# ALBO pełna przebudowa (jeśli były zmiany w Dockerfile)
# ./deploy.sh rebuild
```

### 3. Sprawdź czy działa:

```bash
# Status kontenerów
./deploy.sh status

# Logi aplikacji
./deploy.sh logs app | tail -50

# Sprawdź konfigurację PayU
./deploy.sh artisan tinker
>>> config('payu')
>>> exit
```

### 4. Jeśli są problemy:

```bash
# Sprawdź logi błędów
./deploy.sh logs app | grep -i error

# Wyczyść wszystko i zbuduj od nowa
./deploy.sh rebuild

# Sprawdź uprawnienia
docker compose -f docker-compose.prod.yml exec app chmod -R 775 storage bootstrap/cache
```

---

## ⚡ Najszybsza metoda (jedna komenda po git pull):

```bash
cd /var/www/sklep
git pull origin main
./deploy.sh artisan config:clear && ./deploy.sh update
```

---

## 📋 Checklist:

- [ ] Git push wykonany lokalnie
- [ ] Połączony z serwerem przez SSH
- [ ] `git pull origin main` wykonany
- [ ] Cache wyczyszczony (`config:clear`, `cache:clear`)
- [ ] `./deploy.sh update` wykonany
- [ ] Status kontenerów sprawdzony (`./deploy.sh status`)
- [ ] Aplikacja działa (sprawdź w przeglądarce)

---

**Gotowe! 🎉**
