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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('detail');
            $table->string('image')->nullable();
            // $table->foreignId('size_id')->nullable()->constrained('sizes')->nullOnDelete();
            // $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            // $table->json('size_id')->nullable();
            // $table->json('color_id')->nullable();
            $table->string('size_id')->nullable();
            $table->string('color_id')->nullable();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->decimal('price', 10, 2);
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
