#!/bin/bash

# =============================================================================
# Setup Script for Rap Shop Laravel Application
# =============================================================================
# This script automates the initial setup of the application including:
# - Database creation and migrations
# - Database seeding
# - SQL views, triggers, and procedures
# - Storage linking
# =============================================================================

set -e  # Exit on error

echo "=========================================="
echo "  Rap Shop - Setup Script"
echo "=========================================="
echo ""

# Check if .env file exists
if [ ! -f .env ]; then
    echo "⚠️  .env file not found!"
    echo "Copying .env.example to .env..."
    cp .env.example .env
    echo "✅ .env file created. Please configure your database and other settings."
    echo ""
    read -p "Press Enter to continue after configuring .env..."
fi

# Install dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader
echo "✅ Composer dependencies installed."
echo ""

# Generate application key if not set
if grep -q "APP_KEY=$" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate
    echo "✅ Application key generated."
    echo ""
fi

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link
echo "✅ Storage link created."
echo ""

# Database setup
echo "🗄️  Setting up database..."
echo ""

# Check database connection
if php artisan db:show > /dev/null 2>&1; then
    echo "✅ Database connection successful."
else
    echo "⚠️  Could not connect to database. Please check your .env configuration."
    exit 1
fi

# Run migrations
echo "📊 Running migrations..."
php artisan migrate --force
echo "✅ Migrations completed."
echo ""

# Run seeders
echo "🌱 Seeding database with initial data..."
php artisan db:seed --force
echo "✅ Database seeding completed."
echo ""

# Execute SQL script for views, triggers, and procedures
echo "🔧 Creating database views, triggers, and procedures..."

# Check database type
DB_CONNECTION=$(php artisan tinker --execute="echo config('database.default');" 2>/dev/null | tail -n 1)

if [ "$DB_CONNECTION" = "mysql" ]; then
    # Get database credentials from .env
    DB_HOST=$(grep DB_HOST .env | cut -d '=' -f2)
    DB_PORT=$(grep DB_PORT .env | cut -d '=' -f2)
    DB_DATABASE=$(grep DB_DATABASE .env | cut -d '=' -f2)
    DB_USERNAME=$(grep DB_USERNAME .env | cut -d '=' -f2)
    DB_PASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)

    # Execute SQL script
    if [ -f "database/scripts/init_database.sql" ]; then
        mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" < database/scripts/init_database.sql
        echo "✅ SQL script executed successfully."
    else
        echo "⚠️  SQL script not found at database/scripts/init_database.sql"
    fi
else
    echo "⚠️  SQL script is designed for MySQL. Skipping for $DB_CONNECTION."
fi

echo ""

# Clear cache
echo "🧹 Clearing application cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Cache cleared."
echo ""

# Install NPM dependencies (optional)
read -p "Do you want to install NPM dependencies? (y/n): " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "📦 Installing NPM dependencies..."
    npm install
    echo "✅ NPM dependencies installed."
    echo ""
    
    read -p "Do you want to build assets? (y/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "🏗️  Building assets..."
        npm run build
        echo "✅ Assets built."
        echo ""
    fi
fi

# Final instructions
echo "=========================================="
echo "  Setup Complete! 🎉"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Configure your mail settings in .env (for email verification)"
echo "2. Configure PayU credentials in .env (for payments)"
echo "3. Run: php artisan serve"
echo "4. Visit: http://localhost:8000"
echo ""
echo "Default admin account:"
echo "  Email: admin@rapshop.pl"
echo "  Password: password"
echo ""
echo "⚠️  Remember to change the admin password after first login!"
echo "=========================================="
