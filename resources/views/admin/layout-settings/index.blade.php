@extends('layouts.admin')

@section('title', 'Layout Settings')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">
                    <i class="fas fa-palette me-2"></i>
                    Layout Settings
                </h2>
                <p class="text-muted">Customize your application's branding, colors, and appearance</p>
            </div>
        </div>

        <!-- Session messages handled globally -->

        <form action="{{ route('layout-settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">

                {{--  <!-- ========================================ADMIN PANEL SETTINGS========================================= -->  --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user-shield me-2"></i>
                                Admin Panel Settings
                            </h5>
                        </div>
                        <div class="card-body">

                            <!-- Admin App Name -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-heading me-2"></i>Admin App Name
                                </label>
                                <input type="text" class="form-control @error('admin_app_name') is-invalid @enderror"
                                    name="admin_app_name"
                                    value="{{ old('admin_app_name', $settings->admin_app_name ?? 'Admin Panel') }}"
                                    placeholder="Admin Panel" required>
                                @error('admin_app_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Admin Icon Selector -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-icons me-2"></i>Admin Icon (Default)
                                </label>
                                <p class="small text-muted mb-3">Select an icon to display when no logo is uploaded</p>

                                <div class="icon-selector">
                                    <div class="row g-2">
                                        @php
                                            $adminIcons = [
                                                'fas fa-shield-halved' => 'Shield',
                                                'fas fa-crown' => 'Crown',
                                                'fas fa-user-shield' => 'User Shield',
                                                'fas fa-cog' => 'Settings',
                                                'fas fa-rocket' => 'Rocket',
                                                'fas fa-gem' => 'Gem',
                                                'fas fa-bolt' => 'Bolt',
                                                'fas fa-star' => 'Star',
                                            ];
                                            $selectedAdminIcon = old(
                                                'admin_icon',
                                                $settings->admin_icon ?? 'fas fa-shield-halved',
                                            );
                                        @endphp

                                        @foreach ($adminIcons as $iconClass => $iconName)
                                            <div class="col-3">
                                                <input type="radio" class="btn-check" name="admin_icon"
                                                    id="admin_icon_{{ $loop->index }}" value="{{ $iconClass }}"
                                                    {{ $selectedAdminIcon == $iconClass ? 'checked' : '' }}>
                                                <label
                                                    class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3"
                                                    for="admin_icon_{{ $loop->index }}">
                                                    <i class="{{ $iconClass }} fa-2x mb-2"></i>
                                                    <small>{{ $iconName }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Logo Upload -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-image me-2"></i>Admin Logo (Optional)
                                </label>
                                <p class="small text-muted mb-3">Upload a custom logo to replace the icon</p>

                                @if (isset($settings) && $settings->admin_logo)
                                    <div class="mb-3 p-3 bg-light rounded text-center">
                                        <img src="{{ asset('storage/' . $settings->admin_logo) }}" alt="Admin Logo"
                                            class="img-fluid" style="max-height: 100px; max-width: 200px;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="deleteLogo('admin_logo')">
                                                <i class="fas fa-trash me-1"></i>Remove Logo
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-3 p-3 bg-light rounded text-center" id="adminLogoPlaceholder">
                                        <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                        <p class="text-muted mb-0 small">No logo uploaded</p>
                                    </div>
                                @endif

                                <input type="file" class="form-control" name="admin_logo"
                                    accept="image/jpeg,image/png,image/jpg,image/svg+xml">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    PNG or SVG recommended, max 2MB
                                </div>
                            </div>

                            <!-- Admin Favicon -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-star me-2"></i>Admin Favicon
                                </label>

                                @if (isset($settings) && $settings->admin_favicon)
                                    <div class="mb-3 p-3 bg-light rounded text-center">
                                        <img src="{{ asset('storage/' . $settings->admin_favicon) }}" alt="Admin Favicon"
                                            style="max-height: 48px; max-width: 48px;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="deleteLogo('admin_favicon')">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <input type="file" class="form-control" name="admin_favicon"
                                    accept="image/x-icon,image/png,image/jpg">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    ICO or PNG, max 512KB, 32x32px or 64x64px
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--  <!-- ========================================FRONTEND SETTINGS========================================= -->  --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-globe me-2"></i>
                                Frontend Settings
                            </h5>
                        </div>
                        <div class="card-body">

                            <!-- Frontend App Name -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-heading me-2"></i>Frontend App Name
                                </label>
                                <input type="text" class="form-control" name="frontend_app_name"
                                    value="{{ old('frontend_app_name', $settings->frontend_app_name ?? 'MyShop') }}"
                                    placeholder="MyShop" required>
                            </div>

                            <!-- Frontend Icon Selector -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-icons me-2"></i>Frontend Icon (Default)
                                </label>
                                <p class="small text-muted mb-3">Select an icon to display when no logo is uploaded</p>

                                <div class="icon-selector">
                                    <div class="row g-2">
                                        @php
                                            $frontendIcons = [
                                                'fas fa-store' => 'Store',
                                                'fas fa-shopping-bag' => 'Shopping Bag',
                                                'fas fa-cart-shopping' => 'Cart',
                                                'fas fa-tag' => 'Tag',
                                                'fas fa-heart' => 'Heart',
                                                'fas fa-shop' => 'Shop',
                                                'fas fa-gift' => 'Gift',
                                                'fas fa-basket-shopping' => 'Basket',
                                            ];
                                            $selectedFrontendIcon = old(
                                                'frontend_icon',
                                                $settings->frontend_icon ?? 'fas fa-store',
                                            );
                                        @endphp

                                        @foreach ($frontendIcons as $iconClass => $iconName)
                                            <div class="col-3">
                                                <input type="radio" class="btn-check" name="frontend_icon"
                                                    id="frontend_icon_{{ $loop->index }}" value="{{ $iconClass }}"
                                                    {{ $selectedFrontendIcon == $iconClass ? 'checked' : '' }}>
                                                <label
                                                    class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3"
                                                    for="frontend_icon_{{ $loop->index }}">
                                                    <i class="{{ $iconClass }} fa-2x mb-2"></i>
                                                    <small>{{ $iconName }}</small>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Frontend Logo Upload -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-image me-2"></i>Frontend Logo (Optional)
                                </label>

                                @if (isset($settings) && $settings->frontend_logo)
                                    <div class="mb-3 p-3 bg-light rounded text-center">
                                        <img src="{{ asset('storage/' . $settings->frontend_logo) }}" alt="Frontend Logo"
                                            class="img-fluid" style="max-height: 100px; max-width: 200px;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="deleteLogo('frontend_logo')">
                                                <i class="fas fa-trash me-1"></i>Remove Logo
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <input type="file" class="form-control" name="frontend_logo"
                                    accept="image/jpeg,image/png,image/jpg,image/svg+xml">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    PNG or SVG recommended, max 2MB
                                </div>
                            </div>

                            <!-- Frontend Favicon -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-star me-2"></i>Frontend Favicon
                                </label>

                                @if (isset($settings) && $settings->frontend_favicon)
                                    <div class="mb-3 p-3 bg-light rounded text-center">
                                        <img src="{{ asset('storage/' . $settings->frontend_favicon) }}"
                                            alt="Frontend Favicon" style="max-height: 48px; max-width: 48px;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="deleteLogo('frontend_favicon')">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <input type="file" class="form-control" name="frontend_favicon"
                                    accept="image/x-icon,image/png,image/jpg">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    ICO or PNG, max 512KB
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--  <!-- ========================================HEADER & TITLE SETTINGS========================================= -->  --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-window-maximize me-2"></i>
                                Header & Title Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Site Title -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-heading me-2"></i>Site Title
                                </label>
                                <input type="text" class="form-control" name="site_title"
                                    value="{{ old('site_title', $settings->site_title ?? 'MyShop') }}"
                                    placeholder="MyShop">
                            </div>

                            <!-- Title Background Color -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-fill-drip me-2"></i>Title Background Color
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" name="title_bg_color"
                                        value="{{ old('title_bg_color', $settings->title_bg_color ?? '#ffffff') }}"
                                        style="width: 60px;">
                                    <input type="text" class="form-control"
                                        value="{{ old('title_bg_color', $settings->title_bg_color ?? '#ffffff') }}"
                                        readonly>
                                </div>
                            </div>

                            <!-- Title Text Color -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-paint-brush me-2"></i>Title Text Color
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" name="title_text_color"
                                        value="{{ old('title_text_color', $settings->title_text_color ?? '#212529') }}"
                                        style="width: 60px;">
                                    <input type="text" class="form-control"
                                        value="{{ old('title_text_color', $settings->title_text_color ?? '#212529') }}"
                                        readonly>
                                </div>
                            </div>

                            <!-- Header Menu Items -->
                            <div class="mb-3 border-top pt-3">
                                <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-list me-2"></i>Header Menu Items</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addMenuItem('header')">
                                        <i class="fas fa-plus me-1"></i>Add Link
                                    </button>
                                </label>
                                <div id="header_menu_container">
                                    @php
                                        $headerMenu = old('menu_label', $settings->menu_items ?? []);
                                    @endphp
                                    @foreach ($headerMenu as $item)
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="menu_label[]"
                                                value="{{ $item['label'] ?? '' }}" placeholder="Label">
                                            <input type="text" class="form-control" name="menu_url[]"
                                                value="{{ $item['url'] ?? '' }}" placeholder="URL (e.g. /shop)">
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="this.parentElement.remove()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Logo Size -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-expand-arrows-alt me-2"></i>Logo Size (Height in pixels)
                                </label>
                                <input type="number" class="form-control" name="logo_size"
                                    value="{{ old('logo_size', $settings->logo_size ?? 45) }}" min="20"
                                    max="200" placeholder="45">
                                <div class="form-text">Recommended: 40-50px for best appearance</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--  <!-- ========================================FOOTER SETTINGS========================================= -->  --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-shoe-prints me-2"></i>
                                Footer Settings
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Footer Text -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-align-left me-2"></i>Footer Text
                                </label>
                                <textarea class="form-control" name="footer_text" rows="2"
                                    placeholder="© 2024 MyCompany. All rights reserved.">{{ old('footer_text', $settings->footer_text ?? '') }}</textarea>
                            </div>

                            <!-- Footer Background Color -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-fill-drip me-2"></i>Footer Background Color
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color" name="footer_bg_color"
                                        value="{{ old('footer_bg_color', $settings->footer_bg_color ?? '#f8f9fa') }}"
                                        style="width: 60px;">
                                    <input type="text" class="form-control"
                                        value="{{ old('footer_bg_color', $settings->footer_bg_color ?? '#f8f9fa') }}"
                                        readonly>
                                </div>
                            </div>

                            <!-- Footer Text Color -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-paint-brush me-2"></i>Footer Text Color
                                </label>
                                <div class="input-group">
                                    <input type="color" class="form-control form-control-color"
                                        name="footer_text_color"
                                        value="{{ old('footer_text_color', $settings->footer_text_color ?? '#6c757d') }}"
                                        style="width: 60px;">
                                    <input type="text" class="form-control"
                                        value="{{ old('footer_text_color', $settings->footer_text_color ?? '#6c757d') }}"
                                        readonly>
                                </div>
                            </div>

                            <!-- Footer Menu Items -->
                            <div class="mb-3 border-top pt-3">
                                <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-list me-2"></i>Footer Menu Items</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addMenuItem('footer')">
                                        <i class="fas fa-plus me-1"></i>Add Link
                                    </button>
                                </label>
                                <div id="footer_menu_container">
                                    @php
                                        $footerMenu = old('footer_menu_label', $settings->footer_menu ?? []);
                                    @endphp
                                    @foreach ($footerMenu as $item)
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="footer_menu_label[]"
                                                value="{{ $item['label'] ?? '' }}" placeholder="Label">
                                            <input type="text" class="form-control" name="footer_menu_url[]"
                                                value="{{ $item['url'] ?? '' }}" placeholder="URL">
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="this.parentElement.remove()">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ======================================CONTACT INFORMATION========================================= -->
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-address-card me-2"></i>
                                Contact Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Contact Emails -->
                            <div class="mb-4">
                                <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-envelope me-2"></i>Contact Emails</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addContactField('email')">
                                        <i class="fas fa-plus me-1"></i>Add Email
                                    </button>
                                </label>
                                <div id="email_container">
                                    @php
                                        $emails = old('contact_email', $settings->contact_email ?? ['']);
                                        if (empty($emails)) {
                                            $emails = [''];
                                        }
                                    @endphp
                                    @foreach ($emails as $email)
                                        <div class="input-group mb-2">
                                            <input type="email" class="form-control" name="contact_email[]"
                                                value="{{ $email }}" placeholder="info@example.com">
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="removeContactField(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                @error('contact_email.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact Phones -->
                            <div class="mb-3">
                                <label class="form-label fw-bold d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-phone me-2"></i>Contact Phone Numbers</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                        onclick="addContactField('phone')">
                                        <i class="fas fa-plus me-1"></i>Add Phone
                                    </button>
                                </label>
                                <div id="phone_container">
                                    @php
                                        $phones = old('contact_phone', $settings->contact_phone ?? ['']);
                                        if (empty($phones)) {
                                            $phones = [''];
                                        }
                                    @endphp
                                    @foreach ($phones as $phone)
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="contact_phone[]"
                                                value="{{ $phone }}" placeholder="+1 234 567 8900">
                                            <button type="button" class="btn btn-outline-danger"
                                                onclick="removeContactField(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                @error('contact_phone.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Physical Address -->
                            <div class="mb-3 border-top pt-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-location-dot me-2"></i>Physical Address
                                </label>
                                <textarea class="form-control" name="contact_address" id="contact_address" rows="3"
                                    placeholder="123 Shopping Street, City, Country">{{ old('contact_address', $settings->contact_address ?? '') }}</textarea>
                            </div>

                            <!-- Google Maps Link -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-map-location-dot me-2"></i>Google Maps Link (for "Get Directions")
                                </label>
                                <input type="text" class="form-control" name="address_link" id="address_link"
                                    value="{{ old('address_link', $settings->address_link ?? '') }}"
                                    placeholder="https://maps.google.com/...">
                                <div class="form-text">Paste the full URL to your location on Google Maps</div>
                            </div>

                            <!-- Google Maps Embed Code -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-code me-2"></i>Google Maps Embed Code (Optional)
                                </label>
                                <textarea class="form-control" name="map_embed" id="map_embed" rows="3"
                                    placeholder='<iframe src="..." ...></iframe>'>{{ old('map_embed', $settings->map_embed ?? '') }}</textarea>
                                <div class="form-text">
                                    <p class="mb-1">Paste the <code>&lt;iframe&gt;</code> code from Google Maps to show
                                        a visual map in the footer.</p>
                                    <small class="text-primary fw-bold">
                                        <i class="fas fa-magic me-1"></i> Tip: If you leave this empty, the system will
                                        try to auto-generate a map from your Link or Address above!
                                    </small>
                                    <br>
                                    <a href="https://support.google.com/maps/answer/144361" target="_blank"
                                        class="small text-decoration-none">
                                        <i class="fas fa-question-circle me-1"></i> How to get embed code?
                                    </a>
                                </div>

                                <!-- Map Preview -->
                                <div id="map_preview_container" class="mt-3"
                                    style="display: {{ old('map_embed', $settings->map_embed) ? 'block' : 'none' }};">
                                    <label class="form-label small fw-bold">Map Preview:</label>
                                    <div id="map_preview" class="border rounded p-2 bg-white text-center"
                                        style="min-height: 200px;">
                                        {!! old('map_embed', $settings->map_embed ?? '') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--  <!-- ========================================SOCIAL MEDIA LINKS========================================= -->  --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-share-nodes me-2"></i>
                                Social Media Links
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="small text-muted mb-0">Add your social media profiles</p>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="addSocialLink()">
                                    <i class="fas fa-plus me-1"></i>Add Social Link
                                </button>
                            </div>

                            <div id="social_links_container">
                                @php
                                    $socialLinks = old('social_url', $settings->social_links ?? []);
                                    if (empty($socialLinks) && !old('social_url')) {
                                        // Mock some defaults if none exist and it's not a validation error return
    $socialLinks = [
        ['icon' => 'fab fa-facebook', 'title' => 'Facebook', 'url' => ''],
    ];
} elseif (old('social_url')) {
    // Reconstruct from old input
    $tempLinks = [];
    foreach (old('social_url') as $index => $url) {
        $tempLinks[] = [
            'icon' => old('social_icon')[$index] ?? '',
            'title' => old('social_title')[$index] ?? '',
            'url' => $url,
                                            ];
                                        }
                                        $socialLinks = $tempLinks;
                                    }
                                @endphp

                                @foreach ($socialLinks as $index => $link)
                                    <div class="social-link-item border rounded p-3 mb-3 bg-light position-relative">
                                        <button type="button"
                                            class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                            onclick="removeSocialLink(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Icon (FontAwesome)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i
                                                            class="{{ $link['icon'] ?? 'fab fa-link' }}"></i></span>
                                                    <input type="text"
                                                        class="form-control form-control-sm social-icon-input"
                                                        name="social_icon[]" value="{{ $link['icon'] ?? 'fab fa-link' }}"
                                                        placeholder="fab fa-facebook"
                                                        onkeyup="updateSocialIconPreview(this)">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Platform Title</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="social_title[]" value="{{ $link['title'] ?? '' }}"
                                                    placeholder="Facebook">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">Profile URL</label>
                                                <input type="url" class="form-control form-control-sm"
                                                    name="social_url[]" value="{{ $link['url'] ?? '' }}"
                                                    placeholder="https://facebook.com/yourpage">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-2 text-center">
                            <small class="text-muted">
                                <i class="fas fa-lightbulb me-1 text-warning"></i>
                                Need icons? Visit <a href="https://fontawesome.com/icons" target="_blank"
                                    class="text-primary text-decoration-none fw-bold">FontAwesome</a> to find and copy
                                class names.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save me-2"></i>Save All Settings
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .icon-selector .btn-check:checked+label {
            border-width: 2px;
            font-weight: 600;
        }

        .icon-selector label {
            cursor: pointer;
            transition: all 0.3s;
            height: 100%;
        }

        .icon-selector label:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control-color {
            height: 38px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Delete Logo Function
        function deleteLogo(type) {
            Swal.fire({
                title: 'Delete file?',
                text: "Are you sure you want to delete this file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/layout-settings/delete-logo/${type}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    text: 'File deleted successfully',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                setTimeout(() => location.reload(), 1000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    text: data.error || 'Failed to delete file'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                text: 'An error occurred while deleting the file'
                            });
                        });
                }
            });
        }

        // Color Picker Sync
        document.querySelectorAll('input[type="color"]').forEach(colorInput => {
            colorInput.addEventListener('input', function() {
                const textInput = this.parentElement.querySelector('input[type="text"]');
                if (textInput) {
                    textInput.value = this.value;
                }
            });
        });

        // Add dynamic contact fields
        function addContactField(type) {
            const container = document.getElementById(`${type}_container`);
            const placeholder = type === 'email' ? 'info@example.com' : '+1 234 567 8900';
            const inputType = type === 'email' ? 'email' : 'text';

            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="${inputType}" class="form-control" name="contact_${type}[]" placeholder="${placeholder}">
                <button type="button" class="btn btn-outline-danger" onclick="removeContactField(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(div);
        }

        function removeContactField(button) {
            const container = button.closest('[id$="_container"]');
            if (container.children.length > 1) {
                button.closest('.input-group').remove();
            } else {
                // Just clear the input instead of removing if it's the last one
                button.closest('.input-group').querySelector('input').value = '';
            }
        }

        // Social Links Multi-Entry
        function addSocialLink() {
            const container = document.getElementById('social_links_container');
            const div = document.createElement('div');
            div.className = 'social-link-item border rounded p-3 mb-3 bg-light position-relative';
            div.innerHTML = `
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="removeSocialLink(this)">
                    <i class="fas fa-times"></i>
                </button>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Icon (FontAwesome)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-link"></i></span>
                            <input type="text" class="form-control form-control-sm social-icon-input" name="social_icon[]" value="fab fa-link" onkeyup="updateSocialIconPreview(this)">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Platform Title</label>
                        <input type="text" class="form-control form-control-sm" name="social_title[]" placeholder="Platform Name">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">Profile URL</label>
                        <input type="url" class="form-control form-control-sm" name="social_url[]" placeholder="https://example.com/profile">
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        function removeSocialLink(button) {
            button.closest('.social-link-item').remove();
        }

        // Map Preview Logic
        const mapEmbedArea = document.getElementById('map_embed');
        const mapLinkInput = document.getElementById('address_link');
        const mapAddressArea = document.getElementById('contact_address');
        const mapPreviewContainer = document.getElementById('map_preview_container');
        const mapPreview = document.getElementById('map_preview');

        function updateMapPreview() {
            let code = mapEmbedArea.value.trim();

            if (!code) {
                // Try fallback from link or address
                let query = '';
                const link = mapLinkInput ? mapLinkInput.value.trim() : '';
                const address = mapAddressArea ? mapAddressArea.value.trim() : '';

                if (link) {
                    // Simple extraction logic for common Google Maps links
                    const searchRegex = /maps\/search\/(.*?)\//;
                    const qRegex = /q=(.*?)(&|$)/;
                    let match = link.match(searchRegex) || link.match(qRegex);

                    if (match) {
                        query = decodeURIComponent(match[1]);
                    } else {
                        query = link;
                    }
                } else if (address) {
                    query = address;
                }

                if (query && query.length > 3) {
                    const encoded = encodeURIComponent(query);
                    code =
                        `<iframe src="https://maps.google.com/maps?q=${encoded}&t=&z=13&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>`;
                }
            }

            if (code) {
                mapPreviewContainer.style.display = 'block';
                mapPreview.innerHTML = code;

                // Ensure iframes inside preview are responsive
                const iframes = mapPreview.getElementsByTagName('iframe');
                if (iframes.length > 0) {
                    iframes[0].style.width = '100%';
                    iframes[0].style.height = '200px';
                }
            } else {
                mapPreviewContainer.style.display = 'none';
                mapPreview.innerHTML = '';
            }
        }

        if (mapEmbedArea) mapEmbedArea.addEventListener('input', updateMapPreview);
        if (mapLinkInput) mapLinkInput.addEventListener('input', updateMapPreview);
        if (mapAddressArea) mapAddressArea.addEventListener('input', updateMapPreview);

        // Initial preview check
        updateMapPreview();

        function updateSocialIconPreview(input) {
            const iconPreview = input.previousElementSibling.querySelector('i');
            iconPreview.className = input.value || 'fas fa-link';
        }

        // Menu Helper
        function addMenuItem(type) {
            const container = document.getElementById(`${type}_menu_container`);
            const labelName = type === 'header' ? 'menu_label[]' : 'footer_menu_label[]';
            const urlName = type === 'header' ? 'menu_url[]' : 'footer_menu_url[]';
            const placeholder = type === 'header' ? 'URL (e.g. /shop)' : 'URL';

            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="text" class="form-control" name="${labelName}" placeholder="Label">
                <input type="text" class="form-control" name="${urlName}" placeholder="${placeholder}">
                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(div);
        }
    </script>
@endpush
