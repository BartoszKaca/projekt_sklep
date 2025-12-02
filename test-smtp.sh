#!/bin/bash

# ===========================================================================
# Mailtrap SMTP Configuration Tester
# ===========================================================================
# Skrypt testujący połączenie SMTP do Mailtrap

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Konfiguracja
MAIL_HOST="${1:-smtp.mailtrap.io}"
MAIL_PORT="${2:-2525}"
MAIL_USERNAME="${3}"
MAIL_PASSWORD="${4}"

if [ -z "$MAIL_USERNAME" ] || [ -z "$MAIL_PASSWORD" ]; then
    echo -e "${RED}Użycie: $0 <host> <port> <username> <password>${NC}"
    echo "Przykład: $0 smtp.mailtrap.io 2525 123456 abcdef"
    exit 1
fi

echo -e "${YELLOW}Testing SMTP Connection...${NC}"
echo "Host: $MAIL_HOST"
echo "Port: $MAIL_PORT"
echo "Username: $MAIL_USERNAME"
echo ""

# Test połączenia
(
    echo "EHLO laravel"
    sleep 1
    echo "AUTH LOGIN"
    sleep 1
    echo "$(echo -n "$MAIL_USERNAME" | base64)"
    sleep 1
    echo "$(echo -n "$MAIL_PASSWORD" | base64)"
    sleep 1
    echo "QUIT"
) | nc -w 10 $MAIL_HOST $MAIL_PORT

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ SMTP Connection OK!${NC}"
else
    echo -e "${RED}❌ SMTP Connection FAILED!${NC}"
    exit 1
fi

# Test z Laravel
echo ""
echo -e "${YELLOW}Testing Laravel Mail...${NC}"

docker-compose -f docker-compose.prod.yml exec -T app php artisan tinker << 'PHP'
use Illuminate\Support\Facades\Mail;

try {
    Mail::raw('Test message from Laravel', function($message) {
        $message->to('delivery@mailtrap.io');
    });
    echo "✅ Email queued successfully!";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
PHP

echo ""
echo -e "${GREEN}Test complete!${NC}"
echo "Check Mailtrap dashboard for email: https://mailtrap.io"
