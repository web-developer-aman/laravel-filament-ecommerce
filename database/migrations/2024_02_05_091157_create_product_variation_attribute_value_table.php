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
        Schema::create('product_variation_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_product_variation_id')->nullable()->cascadeOnDelete();
            $table->foreignId('shop_attribute_value_id')->nullable()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variation_attribute_value');
    }
};
