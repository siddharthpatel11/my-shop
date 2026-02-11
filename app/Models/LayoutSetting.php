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
        'contact_address',
        'address_link',
        'map_embed',

        // Footer Settings
        'footer_logo_path',
        'footer_text',
        'footer_bg_color',
        'footer_text_color',

        // Menu & Navigation
        'menu_items',
        'footer_menu',

        // Social Media
        'social_links',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'menu_items' => 'array',
        'footer_menu' => 'array',
        'social_links' => 'array',
        'contact_email' => 'array',
        'contact_phone' => 'array',
        'logo_size' => 'integer',
        'footer_logo_size' => 'integer',
    ];

    protected $appends = [
        'admin_logo_url',
        'admin_favicon_url',
        'frontend_logo_url',
        'frontend_favicon_url',
        'footer_logo_url',
        'map_html',
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
        // Fallback to frontend logo if footer logo is not set
        $path = $this->footer_logo_path ?: $this->frontend_logo;
        return $path ? asset('storage/' . $path) : null;
    }

    /**
     * Get footer logo size with fallback
     */
    public function getFooterLogoSizeAttribute()
    {
        // Use footer_logo_size if it's explicitly set to something non-zero/non-null
        // And it's DIFFERENT from the default 40 if we want to follow logo_size fallback.
        // Actually, if we're falling back on the logo, we should probably follow the logo_size too.

        $footerSize = $this->getRawOriginal('footer_logo_size');

        if ($this->footer_logo_path) {
            return $footerSize ?: 50;
        }

        return $this->logo_size ?? 50;
    }

    /**
     * Get the map HTML (either specialized embed code or fallback from link/address)
     */
    public function getMapHtmlAttribute()
    {
        // 1. If we have explicit embed code, use it
        if ($this->map_embed) {
            return $this->map_embed;
        }

        // 2. Identify a search query for fallback
        $query = null;

        // Try link first (extracting from google maps search URL if possible)
        if ($this->address_link) {
            if (preg_match('/maps\/search\/(.*?)\//', $this->address_link, $matches)) {
                $query = urldecode($matches[1]);
            } elseif (preg_match('/q=(.*?)(&|$)/', $this->address_link, $matches)) {
                $query = urldecode($matches[1]);
            } else {
                // If it's a generic link, we might not be able to embed it easily,
                // but we can try to use it as a query if it's short.
                $query = $this->address_link;
            }
        }

        // If no query from link, use contact address
        if (!$query && $this->contact_address) {
            $query = $this->contact_address;
        }

        if ($query) {
            $encodedQuery = urlencode($query);
            return '<iframe src="https://maps.google.com/maps?q=' . $encodedQuery . '&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>';
        }

        return null;
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
}
