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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('google_id')->nullable()->after('email');
            $table->string('facebook_id')->nullable()->after('google_id');
            $table->string('twitter_id')->nullable()->after('facebook_id');
            $table->string('avatar')->nullable()->after('twitter_id');
            $table->string('social_provider')->nullable()->after('avatar'); // google|facebook|twitter
            $table->string('password')->nullable()->change(); // nullable for social-only users
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'facebook_id', 'twitter_id', 'avatar', 'social_provider']);
        });
    }
};
