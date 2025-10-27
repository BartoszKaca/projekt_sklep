<?php

// database/migrations/2024_01_01_000005_add_shop_fields_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Ta migracja dodaje kolumny potrzebne dla sklepu do istniejącej 
     * tabeli users (utworzonej przez Laravel Breeze/UI)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dodaj tylko jeśli kolumna nie istnieje
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'customer'])->default('customer')->after('email');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'is_active']);
        });
    }
};
