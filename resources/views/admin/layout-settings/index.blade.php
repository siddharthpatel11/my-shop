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

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('layout-settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">

                <!-- ========================================
                     ADMIN PANEL SETTINGS
                ========================================= -->
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
                                <input type="text"
                                       class="form-control @error('admin_app_name') is-invalid @enderror"
                                       name="admin_app_name"
                                       value="{{ old('admin_app_name', $settings->admin_app_name ?? 'Admin Panel') }}"
                                       placeholder="Admin Panel"
                                       required>
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
                                            $selectedAdminIcon = old('admin_icon', $settings->admin_icon ?? 'fas fa-shield-halved');
                                        @endphp

                                        @foreach($adminIcons as $iconClass => $iconName)
                                            <div class="col-3">
                                                <input type="radio"
                                                       class="btn-check"
                                                       name="admin_icon"
                                                       id="admin_icon_{{ $loop->index }}"
                                                       value="{{ $iconClass }}"
                                                       {{ $selectedAdminIcon == $iconClass ? 'checked' : '' }}>
                                                <label class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3"
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
                                        <img src="{{ asset('storage/' . $settings->admin_logo) }}"
                                             alt="Admin Logo"
                                             class="img-fluid"
                                             style="max-height: 100px; max-width: 200px;">
                                        <div class="mt-2">
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
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

                                <input type="file"
                                       class="form-control"
                                       name="admin_logo"
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
                                        <img src="{{ asset('storage/' . $settings->admin_favicon) }}"
                                             alt="Admin Favicon"
                                             style="max-height: 48px; max-width: 48px;">
                                        <div class="mt-2">
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="deleteLogo('admin_favicon')">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <input type="file"
                                       class="form-control"
                                       name="admin_favicon"
                                       accept="image/x-icon,image/png,image/jpg">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    ICO or PNG, max 512KB, 32x32px or 64x64px
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================
                     FRONTEND SETTINGS
                ========================================= -->
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
                                <input type="text"
                                       class="form-control"
                                       name="frontend_app_name"
                                       value="{{ old('frontend_app_name', $settings->frontend_app_name ?? 'MyShop') }}"
                                       placeholder="MyShop"
                                       required>
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
                                            $selectedFrontendIcon = old('frontend_icon', $settings->frontend_icon ?? 'fas fa-store');
                                        @endphp

                                        @foreach($frontendIcons as $iconClass => $iconName)
                                            <div class="col-3">
                                                <input type="radio"
                                                       class="btn-check"
                                                       name="frontend_icon"
                                                       id="frontend_icon_{{ $loop->index }}"
                                                       value="{{ $iconClass }}"
                                                       {{ $selectedFrontendIcon == $iconClass ? 'checked' : '' }}>
                                                <label class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3"
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
                                        <img src="{{ asset('storage/' . $settings->frontend_logo) }}"
                                             alt="Frontend Logo"
                                             class="img-fluid"
                                             style="max-height: 100px; max-width: 200px;">
                                        <div class="mt-2">
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="deleteLogo('frontend_logo')">
                                                <i class="fas fa-trash me-1"></i>Remove Logo
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <input type="file"
                                       class="form-control"
                                       name="frontend_logo"
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
                                             alt="Frontend Favicon"
                                             style="max-height: 48px; max-width: 48px;">
                                        <div class="mt-2">
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="deleteLogo('frontend_favicon')">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <input type="file"
                                       class="form-control"
                                       name="frontend_favicon"
                                       accept="image/x-icon,image/png,image/jpg">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    ICO or PNG, max 512KB
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================
                     HEADER & TITLE SETTINGS
                ========================================= -->
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
                                <input type="text"
                                       class="form-control"
                                       name="site_title"
                                       value="{{ old('site_title', $settings->site_title ?? 'MyShop') }}"
                                       placeholder="MyShop">
                            </div>

                            <!-- Title Background Color -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-fill-drip me-2"></i>Title Background Color
                                </label>
                                <div class="input-group">
                                    <input type="color"
                                           class="form-control form-control-color"
                                           name="title_bg_color"
                                           value="{{ old('title_bg_color', $settings->title_bg_color ?? '#ffffff') }}"
                                           style="width: 60px;">
                                    <input type="text"
                                           class="form-control"
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
                                    <input type="color"
                                           class="form-control form-control-color"
                                           name="title_text_color"
                                           value="{{ old('title_text_color', $settings->title_text_color ?? '#212529') }}"
                                           style="width: 60px;">
                                    <input type="text"
                                           class="form-control"
                                           value="{{ old('title_text_color', $settings->title_text_color ?? '#212529') }}"
                                           readonly>
                                </div>
                            </div>

                            <!-- Logo Size -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-expand-arrows-alt me-2"></i>Logo Size (Height in pixels)
                                </label>
                                <input type="number"
                                       class="form-control"
                                       name="logo_size"
                                       value="{{ old('logo_size', $settings->logo_size ?? 45) }}"
                                       min="20"
                                       max="200"
                                       placeholder="45">
                                <div class="form-text">Recommended: 40-50px for best appearance</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================
                     FOOTER SETTINGS
                ========================================= -->
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
                                <textarea class="form-control"
                                          name="footer_text"
                                          rows="2"
                                          placeholder="© 2024 MyCompany. All rights reserved.">{{ old('footer_text', $settings->footer_text ?? '') }}</textarea>
                            </div>

                            <!-- Footer Background Color -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-fill-drip me-2"></i>Footer Background Color
                                </label>
                                <div class="input-group">
                                    <input type="color"
                                           class="form-control form-control-color"
                                           name="footer_bg_color"
                                           value="{{ old('footer_bg_color', $settings->footer_bg_color ?? '#f8f9fa') }}"
                                           style="width: 60px;">
                                    <input type="text"
                                           class="form-control"
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
                                    <input type="color"
                                           class="form-control form-control-color"
                                           name="footer_text_color"
                                           value="{{ old('footer_text_color', $settings->footer_text_color ?? '#6c757d') }}"
                                           style="width: 60px;">
                                    <input type="text"
                                           class="form-control"
                                           value="{{ old('footer_text_color', $settings->footer_text_color ?? '#6c757d') }}"
                                           readonly>
                                </div>
                            </div>

                            <!-- Footer Logo Size -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-expand-arrows-alt me-2"></i>Footer Logo Size (Height in pixels)
                                </label>
                                <input type="number"
                                       class="form-control"
                                       name="footer_logo_size"
                                       value="{{ old('footer_logo_size', $settings->footer_logo_size ?? 40) }}"
                                       min="20"
                                       max="100"
                                       placeholder="40">
                                <div class="form-text">Recommended: 30-40px</div>
                            </div>

                            <!-- Footer Logo Upload -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-image me-2"></i>Footer Logo (Optional)
                                </label>
                                <p class="small text-muted">Separate logo for footer (if different from header)</p>

                                @if (isset($settings) && $settings->footer_logo_path)
                                    <div class="mb-3 p-3 bg-light rounded text-center">
                                        <img src="{{ asset('storage/' . $settings->footer_logo_path) }}"
                                             alt="Footer Logo"
                                             style="max-height: 60px;">
                                        <div class="mt-2">
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="deleteLogo('footer_logo_path')">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                <input type="file"
                                       class="form-control"
                                       name="footer_logo"
                                       accept="image/jpeg,image/png,image/jpg,image/svg+xml">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================
                     CONTACT INFORMATION
                ========================================= -->
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="fas fa-address-card me-2"></i>
                                Contact Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-envelope me-2"></i>Contact Email
                                </label>
                                <input type="email"
                                       class="form-control"
                                       name="contact_email"
                                       value="{{ old('contact_email', $settings->contact_email ?? '') }}"
                                       placeholder="info@example.com">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-phone me-2"></i>Contact Phone
                                </label>
                                <input type="text"
                                       class="form-control"
                                       name="contact_phone"
                                       value="{{ old('contact_phone', $settings->contact_phone ?? '') }}"
                                       placeholder="+1 234 567 8900">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================
                     SOCIAL MEDIA LINKS
                ========================================= -->
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-share-nodes me-2"></i>
                                Social Media Links
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fab fa-facebook me-2"></i>Facebook
                                </label>
                                <input type="url"
                                       class="form-control"
                                       name="social_facebook"
                                       value="{{ old('social_facebook', $settings->social_links['facebook'] ?? '') }}"
                                       placeholder="https://facebook.com/yourpage">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fab fa-twitter me-2"></i>Twitter
                                </label>
                                <input type="url"
                                       class="form-control"
                                       name="social_twitter"
                                       value="{{ old('social_twitter', $settings->social_links['twitter'] ?? '') }}"
                                       placeholder="https://twitter.com/yourhandle">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fab fa-instagram me-2"></i>Instagram
                                </label>
                                <input type="url"
                                       class="form-control"
                                       name="social_instagram"
                                       value="{{ old('social_instagram', $settings->social_links['instagram'] ?? '') }}"
                                       placeholder="https://instagram.com/yourhandle">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fab fa-linkedin me-2"></i>LinkedIn
                                </label>
                                <input type="url"
                                       class="form-control"
                                       name="social_linkedin"
                                       value="{{ old('social_linkedin', $settings->social_links['linkedin'] ?? '') }}"
                                       placeholder="https://linkedin.com/company/yourcompany">
                            </div>
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
    .icon-selector .btn-check:checked + label {
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
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
        if (!confirm('Are you sure you want to delete this file?')) {
            return;
        }

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
                location.reload();
            } else {
                alert(data.error || 'Failed to delete file');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the file');
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
</script>
@endpush
