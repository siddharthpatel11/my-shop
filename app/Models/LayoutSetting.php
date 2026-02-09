<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LayoutSetting extends Model
{
    use HasFactory;

    protected $table = 'layout_settings';

    protected $fillable = [
        'is_active',

        // Admin Settings
        'admin_logo',
        'admin_favicon',
        'admin_app_name',
        'admin_icon',

        // Frontend Settings
        'frontend_logo',
        'frontend_favicon',
        'frontend_app_name',
        'frontend_icon',

        // Header/Title Settings
        'site_title',
        'title_bg_color',
        'title_text_color',

        // Logo Display Settings
        'logo_size',
        'footer_logo_size',

        // Contact Information
        'contact_email',
        'contact_phone',

        // Footer Settings
        'footer_logo_path',
        'footer_text',
        'footer_bg_color',
        'footer_text_color',

        // Menu & Navigation
        'menu_items',

        // Social Media
        'social_links',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'menu_items' => 'array',
        'social_links' => 'array',
        'contact_email' => 'array',
        'contact_phone' => 'array',
        'logo_size' => 'integer',
        'footer_logo_size' => 'integer',
    ];

    /**
     * Get the active layout settings
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first() ?? self::first();
    }

    /**
     * Get admin logo URL
     */
    public function getAdminLogoUrlAttribute()
    {
        return $this->admin_logo
            ? asset('storage/' . $this->admin_logo)
            : null;
    }

    /**
     * Get admin favicon URL
     */
    public function getAdminFaviconUrlAttribute()
    {
        return $this->admin_favicon
            ? asset('storage/' . $this->admin_favicon)
            : null;
    }

    /**
     * Get frontend logo URL
     */
    public function getFrontendLogoUrlAttribute()
    {
        return $this->frontend_logo
            ? asset('storage/' . $this->frontend_logo)
            : null;
    }

    /**
     * Get frontend favicon URL
     */
    public function getFrontendFaviconUrlAttribute()
    {
        return $this->frontend_favicon
            ? asset('storage/' . $this->frontend_favicon)
            : null;
    }

    /**
     * Get footer logo URL
     */
    public function getFooterLogoUrlAttribute()
    {
        return $this->footer_logo_path
            ? asset('storage/' . $this->footer_logo_path)
            : null;
    }

    /**
     * Delete old logo file when updating
     */
    public function deleteOldFile($field)
    {
        if ($this->$field && Storage::disk('public')->exists($this->$field)) {
            Storage::disk('public')->delete($this->$field);
        }
    }

    /**
     * Get parsed menu items
     */
    public function getMenuItemsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Get parsed social links
     */
    public function getSocialLinksAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }
}
