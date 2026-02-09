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
        Schema::table('layout_settings', function (Blueprint $table) {
            $table->json('footer_menu')->nullable()->after('menu_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('layout_settings', function (Blueprint $table) {
            $table->dropColumn('footer_menu');
        });
    }
};
