<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Napraw kolumnę subscribed_at - ustaw nullable
     */
    public function up(): void
    {
        // Sposób 1: Za pomocą raw SQL
        DB::statement('ALTER TABLE newsletter_subscribers MODIFY subscribed_at TIMESTAMP NULL');
    }

    /**
     * Cofnij zmiany
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE newsletter_subscribers MODIFY subscribed_at TIMESTAMP NOT NULL');
    }
};
