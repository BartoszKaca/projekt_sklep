#!/bin/bash

# ===========================================================================
# Quick Health Check
# ===========================================================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() { echo -e "${BLUE}ℹ️  $1${NC}"; }
log_success() { echo -e "${GREEN}✅ $1${NC}"; }
log_error() { echo -e "${RED}❌ $1${NC}"; }

echo -e "${BLUE}=== SYSTEM HEALTH CHECK ===${NC}"
echo ""

# Docker status
log_info "Docker status:"
if docker-compose -f docker-compose.prod.yml ps | grep -q "Up"; then
    log_success "All containers running"
else
    log_error "Some containers not running!"
fi
echo ""

# Database
log_info "Database connection:"
if docker-compose -f docker-compose.prod.yml exec -T db mysqladmin ping -h localhost -u root > /dev/null 2>&1; then
    log_success "MySQL OK"
else
    log_error "MySQL DOWN!"
fi
echo ""

# Laravel
log_info "Laravel status:"
if curl -s http://localhost | grep -q "Rap Shop" > /dev/null 2>&1; then
    log_success "Laravel responding"
else
    log_error "Laravel not responding"
fi
echo ""

# Mail configuration
log_info "Mail configuration:"
docker-compose -f docker-compose.prod.yml exec -T app php artisan config:show mail.default 2>&1 | head -1
echo ""

# Disk usage
log_info "Disk usage:"
docker system df
echo ""

# Backups
log_info "Latest backup:"
if [ -d ~/backups ]; then
    ls -lh ~/backups | tail -1
else
    log_error "No backups directory"
fi
echo ""

log_success "Health check complete!"
