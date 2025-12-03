# 🖼️ PROBLEM: Obrazy się nie wyświetlają - JAK NAPRAWIĆ?

## 🚀 SZYBKIE ROZWIĄZANIE

### Jeśli używasz Docker:
```bash
chmod +x quick-fix-docker.sh
./quick-fix-docker.sh
```

### Jeśli kontenery nie działają:
```bash
chmod +x start-docker.sh
./start-docker.sh
```

### Jeśli nie używasz Docker (lokalne środowisko):
```bash
chmod +x start-local.sh
./start-local.sh
```

---

## 🔍 DIAGNOZA

Aby sprawdzić co jest nie tak:
```bash
chmod +x diagnoza-obrazy.sh
./diagnoza-obrazy.sh
```

---

## ❓ CO JEST PROBLEMEM?

Obrazy nie wyświetlają się z **dwóch powodów**:

### 1. ❌ Brak linku symbolicznego
Laravel przechowuje obrazy w `storage/app/public/`, ale przeglądarka ma dostęp tylko do `public/`.
Link symboliczny łączy te katalogi.

**Rozwiązanie:**
```bash
# Docker
docker-compose exec app php artisan storage:link

# Lokalnie
php artisan storage:link
```

### 2. ❌ Baza danych nie działa
W pliku `.env` jest `DB_HOST=db`, co oznacza że projekt wymaga Docker.

**Rozwiązanie Docker:**
```bash
docker-compose up -d              # Uruchom kontenery
docker-compose ps                 # Sprawdź status
```

**Rozwiązanie lokalne (bez Docker):**
Zmień w pliku `.env`:
```
DB_HOST=127.0.0.1  # zamiast DB_HOST=db
DB_USERNAME=root
DB_PASSWORD=twoje_haslo
```

---

## 📚 PEŁNA DOKUMENTACJA

- **ROZWIAZANIE_OBRAZY.md** - szczegółowy przewodnik
- **start-docker.sh** - automatyczna konfiguracja Docker
- **start-local.sh** - automatyczna konfiguracja lokalna
- **diagnoza-obrazy.sh** - sprawdzenie co jest nie tak
- **quick-fix-docker.sh** - szybka naprawa dla działającego Docker

---

## 🎯 KROK PO KROKU

### Docker (zalecane):

1. **Upewnij się że Docker Desktop działa**
2. **Uruchom setup:**
   ```bash
   ./start-docker.sh
   ```
3. **Otwórz przeglądarkę:** http://localhost:8000

### Lokalnie:

1. **Zainstaluj:** PHP 8.2+, MySQL 8.0+, Composer
2. **Utwórz bazę:**
   ```sql
   CREATE DATABASE sklep_laravel;
   ```
3. **Zmień .env:** `DB_HOST=127.0.0.1`
4. **Uruchom setup:**
   ```bash
   ./start-local.sh
   php artisan serve
   ```
5. **Otwórz przeglądarkę:** http://localhost:8000

---

## ✅ WERYFIKACJA

Sprawdź czy wszystko działa:

```bash
# 1. Link symboliczny istnieje?
ls -la public/ | grep storage
# Powinno: storage -> ../storage/app/public

# 2. Baza działa?
php artisan tinker
>>> DB::table('products')->count();

# 3. Katalog products istnieje?
ls -la storage/app/public/products/

# 4. URL działa?
# Otwórz: http://localhost:8000/storage/products/test.jpg
```

---

## 🆘 NADAL NIE DZIAŁA?

1. **Uruchom diagnostykę:**
   ```bash
   ./diagnoza-obrazy.sh
   ```

2. **Sprawdź logi:**
   ```bash
   # Docker
   docker-compose logs -f app
   
   # Lokalnie
   tail -f storage/logs/laravel.log
   ```

3. **Sprawdź Console w przeglądarce (F12)**
   - Otwórz zakładkę **Network**
   - Odśwież stronę
   - Zobacz jakie błędy są przy obrazkach

---

## 📞 KONTAKT / POMOC

Jeśli nic nie pomaga, wyślij output z:
```bash
./diagnoza-obrazy.sh > diagnostyka.txt
```

---

**Sukces!** 🎉 Po wykonaniu odpowiednich kroków, obrazy powinny się wyświetlać.
