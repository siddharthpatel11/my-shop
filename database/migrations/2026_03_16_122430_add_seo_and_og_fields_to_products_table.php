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
        Schema::table('products', function (Blueprint $table) {
            $table->string('seo_meta_title')->nullable();
            $table->text('seo_meta_description')->nullable();
            $table->string('seo_meta_key')->nullable();
            $table->string('seo_meta_image')->nullable();
            $table->string('seo_canonical')->nullable();

            $table->string('og_meta_title')->nullable();
            $table->text('og_meta_description')->nullable();
            $table->string('og_meta_key')->nullable();
            $table->string('og_meta_image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'seo_meta_title',
                'seo_meta_description',
                'seo_meta_key',
                'seo_meta_image',
                'seo_canonical',
                'og_meta_title',
                'og_meta_description',
                'og_meta_key',
                'og_meta_image',
            ]);
        });
    }
};
