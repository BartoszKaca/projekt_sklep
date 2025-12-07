# 🔧 Naprawa problemu z PayU - "System niedostępny"

## Problem
Komunikat "PayU system is unavailable" może być spowodowany kilkoma rzeczami:

1. **Błędna konfiguracja zmiennych środowiskowych**
   - W `.env` jest `PAYU_ENV=sandbox`
   - W `config/payu.php` szukało `PAYU_ENVIRONMENT`
   - ✅ **NAPRAWIONE** - teraz obsługuje obie nazwy

2. **Tryb sandbox vs production**
   - `PAYU_ENV=sandbox` - dla testów
   - `PAYU_ENV=secure` - dla produkcji
   - Sandbox może nie działać lub mieć ograniczenia

3. **Błędne dane dostępowe**
   - POS ID, Signature Key, Client ID, Client Secret
   - Muszą pasować do wybranego środowiska (sandbox/secure)

4. **Brakujące zmienne**
   - `PAYU_MERCHANT_POS_ID` lub `PAYU_POS_ID`
   - `PAYU_SIGNATURE_KEY`
   - `PAYU_OAUTH_CLIENT_ID` lub `PAYU_CLIENT_ID`
   - `PAYU_OAUTH_CLIENT_SECRET` lub `PAYU_CLIENT_SECRET`

## Co zostało naprawione:

1. ✅ Konfiguracja obsługuje teraz obie nazwy zmiennych:
   - `PAYU_ENV` lub `PAYU_ENVIRONMENT`
   - `PAYU_MERCHANT_POS_ID` lub `PAYU_POS_ID`
   - `PAYU_OAUTH_CLIENT_ID` lub `PAYU_CLIENT_ID`
   - `PAYU_OAUTH_CLIENT_SECRET` lub `PAYU_CLIENT_SECRET`

2. ✅ Lepsze logowanie błędów PayU
3. ✅ Dokładniejsze komunikaty błędów dla użytkownika

## Co sprawdzić na serwerze:

### 1. Sprawdź zmienne w .env:

```bash
# Na serwerze
cd /var/www/sklep
docker compose -f docker-compose.prod.yml exec app cat .env | grep PAYU
```

Powinno być:
```env
PAYU_ENV=secure  # dla produkcji, sandbox dla testów
PAYU_MERCHANT_POS_ID=twoj_pos_id
PAYU_SIGNATURE_KEY=twoj_klucz
PAYU_OAUTH_CLIENT_ID=twoj_client_id
PAYU_OAUTH_CLIENT_SECRET=twoj_secret
```

### 2. Sprawdź logi:

```bash
./deploy.sh logs app | grep -i payu
```

### 3. Sprawdź konfigurację:

```bash
./deploy.sh artisan tinker
>>> config('payu')
```

Powinno pokazać wszystkie zmienne skonfigurowane.

### 4. Przetestuj konfigurację:

```bash
./deploy.sh artisan tinker
>>> \OpenPayU_Configuration::setEnvironment('secure');  # lub 'sandbox'
>>> \OpenPayU_Configuration::setMerchantPosId(config('payu.pos_id'));
>>> \OpenPayU_Configuration::setSignatureKey(config('payu.signature_key'));
>>> \OpenPayU_Configuration::setOauthClientId(config('payu.client_id'));
>>> \OpenPayU_Configuration::setOauthClientSecret(config('payu.client_secret'));
```

## Ważne:

### Dla PRODUKCJI:
```env
PAYU_ENV=secure  # NIE sandbox!
```

### Dla TESTÓW:
```env
PAYU_ENV=sandbox
```

**Uwaga:** Dane dostępowe (POS ID, klucze) są różne dla sandbox i secure!
- Sandbox - dane testowe z panelu testowego PayU
- Secure - dane produkcyjne z panelu produkcyjnego PayU

## Jeśli nadal nie działa:

1. Sprawdź czy masz poprawne dane dostępowe dla wybranego środowiska
2. Sprawdź czy PayU działa (może być awaria)
3. Sprawdź logi w panelu PayU
4. Wyczyść cache konfiguracji:
   ```bash
   ./deploy.sh artisan config:clear
   ./deploy.sh artisan config:cache
   ```
