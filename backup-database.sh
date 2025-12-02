#!/bin/bash

# ===========================================================================
# Database Backup Script
# ===========================================================================
# Skrypt do tworzenia backupów bazy danych

set -e

BACKUP_DIR="${1:-~/backups}"
RETENTION_DAYS=30
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$BACKUP_DIR/db_sklep_laravel_$TIMESTAMP.sql"

# Utwórz katalog jeśli nie istnieje
mkdir -p "$BACKUP_DIR"

echo "Creating database backup..."
echo "Destination: $BACKUP_FILE"

# Backup - z docker-compose
docker-compose -f docker-compose.prod.yml exec -T db mysqldump \
    -u laravel \
    -plasyon \
    sklep_laravel > "$BACKUP_FILE"

# Kompresja
gzip "$BACKUP_FILE"
BACKUP_FILE="$BACKUP_FILE.gz"

echo "✅ Backup created: $BACKUP_FILE"
echo "Size: $(du -h "$BACKUP_FILE" | cut -f1)"

# Cleanup - usuń stare backupy
echo "Cleaning up old backups (older than $RETENTION_DAYS days)..."
find "$BACKUP_DIR" -name "db_sklep_laravel_*.sql.gz" -mtime +$RETENTION_DAYS -delete

echo "✅ Backup complete!"
