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
        Schema::create('layout_settings', function (Blueprint $table) {
            $table->id();

            // Basic Settings
            $table->boolean('is_active')->default(true);

            // Admin Panel Logo & Branding
            $table->string('admin_logo')->nullable(); // File path: logos/admin/filename.png
            $table->string('admin_favicon')->nullable(); // File path: favicons/admin/filename.ico
            $table->string('admin_app_name')->default('Admin Panel');
            $table->string('admin_icon')->default('fas fa-shield-halved'); // Default icon when no logo

            // Frontend Logo & Branding
            $table->string('frontend_logo')->nullable(); // File path: logos/frontend/filename.png
            $table->string('frontend_favicon')->nullable(); // File path: favicons/frontend/filename.ico
            $table->string('frontend_app_name')->default('MyShop');
            $table->string('frontend_icon')->default('fas fa-store'); // Default icon when no logo

            // Header/Title Settings
            $table->string('site_title')->default('MyShop');
            $table->string('title_bg_color')->default('#ffffff');
            $table->string('title_text_color')->default('#212529');

            // Logo Display Settings
            $table->integer('logo_size')->default(45); // Logo height in pixels
            $table->integer('footer_logo_size')->default(40); // Footer logo height in pixels

            // Contact Information
            $table->json('contact_email')->nullable();
            $table->json('contact_phone')->nullable();

            // Footer Settings
            $table->string('footer_logo_path')->nullable(); // Alternative footer logo
            $table->string('footer_text')->nullable();
            $table->string('footer_bg_color')->default('#f8f9fa');
            $table->string('footer_text_color')->default('#6c757d');

            // Menu & Navigation
            $table->json('menu_items')->nullable(); // For custom menu items

            // Social Media Links
            $table->json('social_links')->nullable(); // {facebook: url, twitter: url, instagram: url, linkedin: url}

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layout_settings');
    }
};
