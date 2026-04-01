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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_gu')->nullable()->after('name');
            $table->string('name_hi')->nullable()->after('name_gu');
        });

        Schema::table('colors', function (Blueprint $table) {
            $table->string('name_gu')->nullable()->after('name');
            $table->string('name_hi')->nullable()->after('name_gu');
        });

        Schema::table('sizes', function (Blueprint $table) {
            $table->string('name_gu')->nullable()->after('name');
            $table->string('name_hi')->nullable()->after('name_gu');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('name_gu')->nullable()->after('name');
            $table->string('name_hi')->nullable()->after('name_gu');
            $table->text('detail_gu')->nullable()->after('detail');
            $table->text('detail_hi')->nullable()->after('detail_gu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name_gu', 'name_hi']);
        });

        Schema::table('colors', function (Blueprint $table) {
            $table->dropColumn(['name_gu', 'name_hi']);
        });

        Schema::table('sizes', function (Blueprint $table) {
            $table->dropColumn(['name_gu', 'name_hi']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['name_gu', 'name_hi', 'detail_gu', 'detail_hi']);
        });
    }
};
