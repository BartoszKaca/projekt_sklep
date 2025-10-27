<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Jeśli tabela users już istnieje (np. po instalacji Breeze),
        // dodajemy tylko brakujące kolumny
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['admin', 'customer'])->default('customer')->after('email');
                }
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->nullable()->after('role');
                }
                if (!Schema::hasColumn('users', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('phone');
                }
            });
        } else {
            // Jeśli tabela nie istnieje, tworzymy ją od zera
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->enum('role', ['admin', 'customer'])->default('customer');
                $table->string('phone')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        // Nie usuwamy tabeli users, tylko nasze dodatkowe kolumny
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['role', 'phone', 'is_active']);
            });
        }
    }
};

