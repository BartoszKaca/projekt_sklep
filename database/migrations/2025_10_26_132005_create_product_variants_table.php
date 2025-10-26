<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name'); // np. "XL Czarny", "Limited Edition"
            $table->string('size')->nullable(); // S, M, L, XL, XXL
            $table->string('color')->nullable();
            $table->decimal('price_modifier', 8, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->string('sku')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
