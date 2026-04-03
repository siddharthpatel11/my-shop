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
        Schema::table('customers', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->enum('theme_mode', ['light', 'dark', 'system'])->default('system')->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn('theme_mode');
        });
    }
};
