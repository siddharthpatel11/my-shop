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
        Schema::create('wishlists', function (Blueprint $col) {
            $col->id();
            $col->unsignedBigInteger('customer_id');
            $col->unsignedBigInteger('product_id');
            $col->timestamps();

            $col->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $col->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
