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
            $table->string('name_sa')->nullable()->after('name_hi');
            $table->string('name_bn')->nullable()->after('name_sa');
        });
        Schema::table('colors', function (Blueprint $table) {
            $table->string('name_sa')->nullable()->after('name_hi');
            $table->string('name_bn')->nullable()->after('name_sa');
        });
        Schema::table('sizes', function (Blueprint $table) {
            $table->string('name_sa')->nullable()->after('name_hi');
            $table->string('name_bn')->nullable()->after('name_sa');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_sa')->nullable()->after('name_hi');
            $table->string('name_bn')->nullable()->after('name_sa');
            $table->text('detail_sa')->nullable()->after('detail_hi');
            $table->text('detail_bn')->nullable()->after('detail_sa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', fn($t) => $t->dropColumn(['name_sa', 'name_bn']));
        Schema::table('colors',     fn($t) => $t->dropColumn(['name_sa', 'name_bn']));
        Schema::table('sizes',      fn($t) => $t->dropColumn(['name_sa', 'name_bn']));
        Schema::table('products',   fn($t) => $t->dropColumn(['name_sa', 'name_bn', 'detail_sa', 'detail_bn']));
    }
};
