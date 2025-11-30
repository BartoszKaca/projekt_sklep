<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds indexes to improve query performance.
     */
    public function up(): void
    {
        // Products indexes for better search and filtering performance
        if (!$this->indexExists('products', 'idx_products_category')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index('category_id', 'idx_products_category');
            });
        }

        if (!$this->indexExists('products', 'idx_products_active')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index('is_active', 'idx_products_active');
            });
        }

        if (!$this->indexExists('products', 'idx_products_featured')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index('is_featured', 'idx_products_featured');
            });
        }

        if (!$this->indexExists('products', 'idx_products_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index('price', 'idx_products_price');
            });
        }

        if (!$this->indexExists('products', 'idx_products_type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index('type', 'idx_products_type');
            });
        }

        if (!$this->indexExists('products', 'idx_products_slug')) {
            Schema::table('products', function (Blueprint $table) {
                $table->index('slug', 'idx_products_slug');
            });
        }

        // Orders indexes
        if (!$this->indexExists('orders', 'idx_orders_user')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('user_id', 'idx_orders_user');
            });
        }

        if (!$this->indexExists('orders', 'idx_orders_status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('status', 'idx_orders_status');
            });
        }

        if (!$this->indexExists('orders', 'idx_orders_payment_status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('payment_status', 'idx_orders_payment_status');
            });
        }

        if (!$this->indexExists('orders', 'idx_orders_created_at')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('created_at', 'idx_orders_created_at');
            });
        }

        if (!$this->indexExists('orders', 'idx_orders_order_number')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->index('order_number', 'idx_orders_order_number');
            });
        }

        // Order items indexes
        if (!$this->indexExists('order_items', 'idx_order_items_order')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->index('order_id', 'idx_order_items_order');
            });
        }

        if (!$this->indexExists('order_items', 'idx_order_items_product')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->index('product_id', 'idx_order_items_product');
            });
        }

        // Users indexes
        if (!$this->indexExists('users', 'idx_users_email')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('email', 'idx_users_email');
            });
        }

        if (!$this->indexExists('users', 'idx_users_role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->index('role', 'idx_users_role');
            });
        }

        // Categories indexes
        if (!$this->indexExists('categories', 'idx_categories_slug')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->index('slug', 'idx_categories_slug');
            });
        }

        if (!$this->indexExists('categories', 'idx_categories_active')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->index('is_active', 'idx_categories_active');
            });
        }

        // Stock movements indexes
        if (!$this->indexExists('stock_movements', 'idx_stock_movements_product')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->index('product_id', 'idx_stock_movements_product');
            });
        }

        if (!$this->indexExists('stock_movements', 'idx_stock_movements_type')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->index('type', 'idx_stock_movements_type');
            });
        }

        // Wishlist indexes
        if (!$this->indexExists('wishlists', 'idx_wishlist_user')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->index('user_id', 'idx_wishlist_user');
            });
        }

        if (!$this->indexExists('wishlists', 'idx_wishlist_product')) {
            Schema::table('wishlists', function (Blueprint $table) {
                $table->index('product_id', 'idx_wishlist_product');
            });
        }

        // Newsletter subscribers indexes
        if (!$this->indexExists('newsletter_subscribers', 'idx_newsletter_email')) {
            Schema::table('newsletter_subscribers', function (Blueprint $table) {
                $table->index('email', 'idx_newsletter_email');
            });
        }

        if (!$this->indexExists('newsletter_subscribers', 'idx_newsletter_active')) {
            Schema::table('newsletter_subscribers', function (Blueprint $table) {
                $table->index('is_active', 'idx_newsletter_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all indexes
        $indexes = [
            'products' => ['idx_products_category', 'idx_products_active', 'idx_products_featured', 
                          'idx_products_price', 'idx_products_type', 'idx_products_slug'],
            'orders' => ['idx_orders_user', 'idx_orders_status', 'idx_orders_payment_status', 
                        'idx_orders_created_at', 'idx_orders_order_number'],
            'order_items' => ['idx_order_items_order', 'idx_order_items_product'],
            'users' => ['idx_users_email', 'idx_users_role'],
            'categories' => ['idx_categories_slug', 'idx_categories_active'],
            'stock_movements' => ['idx_stock_movements_product', 'idx_stock_movements_type'],
            'wishlists' => ['idx_wishlist_user', 'idx_wishlist_product'],
            'newsletter_subscribers' => ['idx_newsletter_email', 'idx_newsletter_active'],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) use ($tableIndexes) {
                    foreach ($tableIndexes as $index) {
                        $table->dropIndex($index);
                    }
                });
            }
        }
    }

    /**
     * Check if an index exists on a table.
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $doctrineSchemaManager = $connection->getDoctrineSchemaManager();
        $doctrineTable = $doctrineSchemaManager->introspectTable($table);
        
        return $doctrineTable->hasIndex($index);
    }
};
