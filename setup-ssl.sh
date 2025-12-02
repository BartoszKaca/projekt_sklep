#!/bin/bash

# ===========================================================================
# SSL Certificate Generation and Auto-Renewal Setup
# ===========================================================================
# Skrypt do generowania SSL certyfikatów Let's Encrypt i auto-renewal

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; exit 1; }

# ===========================================================================
# Konfiguracja
# ===========================================================================
DOMAIN="${1:-rapshop.pl}"
EMAIL="${2:-your-email@example.com}"

# ===========================================================================
# Sprawdzenia
# ===========================================================================
log_info "=== SSL CERTIFICATE SETUP ==="
log_info "Domain: $DOMAIN"
log_info "Email: $EMAIL"

if ! command -v certbot &> /dev/null; then
    log_error "Certbot nie zainstalowany!"
fi
log_success "Certbot zainstalowany"

# ===========================================================================
# Generowanie Certyfikatu
# ===========================================================================
log_info "Generowanie certyfikatu SSL..."

sudo certbot certonly \
    --standalone \
    --non-interactive \
    --agree-tos \
    --email $EMAIL \
    --domain $DOMAIN \
    --domain www.$DOMAIN

if [ $? -eq 0 ]; then
    log_success "Certyfikat wygenerowany!"
    echo ""
    log_info "Lokalizacja certyfikatu:"
    echo "  Fullchain: /etc/letsencrypt/live/$DOMAIN/fullchain.pem"
    echo "  Privkey: /etc/letsencrypt/live/$DOMAIN/privkey.pem"
else
    log_error "Błąd przy generowaniu certyfikatu!"
fi

# ===========================================================================
# Auto-Renewal Setup
# ===========================================================================
log_info "Konfiguracja auto-renewal..."

# Sprawdź czy systemd timer istnieje
if systemctl list-timers --all | grep -q certbot-renewal; then
    log_success "Certbot renewal timer aktywny"
    systemctl status certbot-renewal.timer
else
    log_warning "Włączam certbot renewal timer..."
    sudo systemctl enable certbot-renewal.timer
    sudo systemctl start certbot-renewal.timer
    log_success "Certbot renewal timer włączony"
fi

# ===========================================================================
# Nginx post-renewal hook
# ===========================================================================
log_info "Konfiguracja Nginx reload po renewal..."

sudo mkdir -p /etc/letsencrypt/renewal-hooks/post

cat | sudo tee /etc/letsencrypt/renewal-hooks/post/nginx.sh > /dev/null << 'EOF'
#!/bin/bash
nginx -t && systemctl reload nginx
EOF

sudo chmod 755 /etc/letsencrypt/renewal-hooks/post/nginx.sh

log_success "Nginx reload hook skonfigurowany"

# ===========================================================================
# Test renewal
# ===========================================================================
log_info "Test dry-run renewal..."
sudo certbot renew --dry-run

if [ $? -eq 0 ]; then
    log_success "Test dry-run OK!"
else
    log_warning "Test dry-run nieudany - sprawdź logi"
fi

# ===========================================================================
# Summary
# ===========================================================================
echo ""
log_success "SSL Setup Completed!"
echo ""
echo "Informacje o certyfikacie:"
sudo certbot certificates

echo ""
echo "Logi renewal:"
echo "  cat /var/log/letsencrypt/letsencrypt.log"
echo ""
