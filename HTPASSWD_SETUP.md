# Instrukcje Generowania .htpasswd dla PHPMyAdmin

## Co to jest .htpasswd?

`.htpasswd` to plik zawierający zaszyfrowane hasła dla Basic Authentication w Nginx/Apache.
Dodatkowa warstwa bezpieczeństwa dla PHPMyAdmin.

## Generowanie na Twojej Maszynie (Lokalnie)

### Wymagania
```bash
# Zainstaluj Apache utils
# macOS:
brew install httpd

# Linux (Ubuntu/Debian):
sudo apt-get install apache2-utils

# Linux (Amazon Linux 2):
sudo yum install httpd-tools
```

### Kroki

#### 1. Wygeneruj hasło
```bash
openssl passwd -apr1
# lub
htpasswd -c .htpasswd admin
```

Jeśli używasz `openssl`:
```bash
$ openssl passwd -apr1
Password: ••••••••  (wpisz hasło, np. admin123)
Verifying - Password: ••••••••
$apr1$r31....kx0  # To jest wygenerowane hasło
```

#### 2. Zaktualizuj .htpasswd
```bash
# Jeśli używasz openssl:
echo "admin:$apr1$r31....kx0" > .htpasswd

# Jeśli używasz htpasswd:
htpasswd -c .htpasswd admin
htpasswd -b .htpasswd admin YOUR_PASSWORD
```

#### 3. Skopiuj na serwer EC2
```bash
scp -i your-key.pem .htpasswd ec2-user@YOUR_ELASTIC_IP:~/projekt_sklep/

# Następnie na serwerze:
sudo cp .htpasswd /etc/nginx/.htpasswd
sudo chown www-data:www-data /etc/nginx/.htpasswd
sudo chmod 600 /etc/nginx/.htpasswd
```

#### 4. Weryfikuj
```bash
# Sprawdzenie formatu
cat /etc/nginx/.htpasswd

# Powinno wyglądać:
# admin:$apr1$r31....kx0
```

#### 5. Reload Nginx
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## Test Dostępu do PHPMyAdmin

```bash
# Spróbuj dostać się bez hasła (powinna być odmowa)
curl https://rapshop.pl/admin/phpmyadmin

# Spróbuj z hasłem
curl -u admin:YOUR_PASSWORD https://rapshop.pl/admin/phpmyadmin
```

---

## Dodawanie Kolejnych Użytkowników

```bash
# Dodaj nowego użytkownika (bez -c!)
sudo htpasswd /etc/nginx/.htpasswd newuser

# Zmień istniejące hasło
sudo htpasswd /etc/nginx/.htpasswd admin
```

---

## Zmiana Hasła

```bash
# Zmień hasło dla użytkownika
sudo htpasswd /etc/nginx/.htpasswd admin

# Lub ręcznie - wygeneruj nowe hasło
openssl passwd -apr1
# Skopiuj hash

# Edytuj plik
sudo nano /etc/nginx/.htpasswd
# Zmień tylko część po dwukropku
```

---

## Troubleshooting

### "401 Unauthorized"
- ✅ Sprawdź czy .htpasswd istnieje: `sudo cat /etc/nginx/.htpasswd`
- ✅ Sprawdź czy nginx ma dostęp: `sudo chown www-data:www-data /etc/nginx/.htpasswd`
- ✅ Sprawdź uprawnienia: `sudo chmod 600 /etc/nginx/.htpasswd`
- ✅ Reload nginx: `sudo systemctl reload nginx`

### "Can't Read File"
```bash
sudo ls -la /etc/nginx/.htpasswd
# Powinno być:
# -rw------- 1 www-data www-data 50 Dec 2 12:00 /etc/nginx/.htpasswd
```

### Nginx test Failed
```bash
sudo nginx -t
# Sprawdź błędy w konfiguracji
sudo tail -f /var/log/nginx/rapshop_error.log
```

---

## Domyślne Hasła

Jeśli nie zmienisz, domyślnie:
- **Username**: `admin`
- **Password**: `admin123`

⚠️ **ZMIEŃ TO PRZED WDROŻENIEM NA PRODUKCJĘ!**

---

## Bezpieczeństwo

### Dodatkowe Kroki

1. **Wyłącz dostęp publicznie** - `.htpasswd` zawiera tylko IP whitelist w nginx.conf

2. **Zmień domyślne hasło PHPMyAdmin** w Docker:
   ```env
   PHPMYADMIN_ROOT_PASSWORD=your_strong_password
   ```

3. **Zmieniaj hasła regularnie** (co 3 miesiące)

4. **Loguj dostępy**:
   ```bash
   sudo tail -f /var/log/nginx/rapshop_access.log | grep phpmyadmin
   ```

---

## Alternatywy

### 1. Użyj tylko IP Whitelist (brak hasła)
W `nginx.conf`:
```nginx
allow 127.0.0.1;
allow YOUR.IP;
deny all;
```

### 2. OAuth2 Proxy
Bardziej zaawansowane rozwiązanie z Google/GitHub login

### 3. SSH Tunnel + Local PHPMyAdmin
```bash
ssh -i your-key.pem -L 8080:localhost:8080 ec2-user@YOUR_IP
# Otwórz http://localhost:8080
```

---

**Data**: 2 grudnia 2025
