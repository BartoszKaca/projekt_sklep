#!/bin/bash

# ===========================================================================
# Complete Setup Verification Script
# ===========================================================================
# Weryfikuje czy wszystkie pliki są w miejscu i prawidłowo skonfigurowane

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_warning() { echo -e "${YELLOW}⚠️  $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }

echo -e "${BLUE}=== AWS DEPLOYMENT FILES VERIFICATION ===${NC}"
echo ""

# Sprawdzenie dokumentacji
log_info "Checking documentation files..."
files_found=0
files_needed=0

check_file() {
    files_needed=$((files_needed + 1))
    if [ -f "$1" ]; then
        files_found=$((files_found + 1))
        log_success "$1"
    else
        log_error "$1 - NOT FOUND!"
    fi
}

echo ""
log_info "Documentation:"
check_file "AWS_EC2_DEPLOYMENT.md"
check_file "DEPLOYMENT_QUICK_START.md"
check_file "DEPLOYMENT_SUMMARY.md"
check_file "HTPASSWD_SETUP.md"

echo ""
log_info "Docker configuration:"
check_file "docker-compose.prod.yml"
check_file "Dockerfile.prod"
check_file ".dockerignore"

echo ""
log_info "PHP configuration:"
check_file "docker/php/php-production.ini"
check_file "docker/php/php-fpm.conf"
check_file "docker/php/opcache.ini"

echo ""
log_info "Environment files:"
check_file ".env.production"
check_file ".env.docker.prod"

echo ""
log_info "Nginx configuration:"
check_file "nginx.conf"
check_file ".htpasswd"

echo ""
log_info "Automation scripts:"
check_file "deploy-aws.sh"
check_file "setup-ssl.sh"
check_file "backup-database.sh"
check_file "test-smtp.sh"
check_file "health-check.sh"
check_file "verify-setup.sh"

echo ""
echo "=========================================="
echo "Files found: $files_found / $files_needed"
echo "=========================================="
echo ""

if [ $files_found -eq $files_needed ]; then
    log_success "All files are present!"
    echo ""
    log_info "Next steps:"
    echo "1. Read: DEPLOYMENT_QUICK_START.md"
    echo "2. Configure: .env.docker.prod"
    echo "3. Run: ./deploy-aws.sh rapshop.pl your@email.com"
else
    log_error "Some files are missing! Please check above."
    exit 1
fi

echo ""
log_info "File permissions:"
for script in deploy-aws.sh setup-ssl.sh backup-database.sh test-smtp.sh health-check.sh verify-setup.sh; do
    if [ -f "$script" ]; then
        chmod +x "$script"
        log_success "Made $script executable"
    fi
done

echo ""
log_success "Verification complete!"
