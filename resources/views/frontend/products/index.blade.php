@extends('layouts.frontend.app')

@section('title', 'Our Products')

@section('content')
    {{-- Flipkart Dashboard Layout --}}
    @php
        // Fetch categories for the top strip
        $activeCategories = \App\Models\Category::where('status', 'active')->get();

        // Fetch active banners for the dynamic ad banners
        $featuredAds = \App\Models\Banner::where('status', 'active')->orderBy('order')->get();
        $isProductFallback = false;

        // Fallback to products if no banners exist
        if ($featuredAds->isEmpty()) {
            $featuredAds = \App\Models\Product::inRandomOrder()->take(4)->get();
            $isProductFallback = true;
        }

        // Vibrant Gradient palettes for product fallback banners
        $gradients = [
            'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)', // Yellow/Gold
            'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)', // Light Blue
            'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)', // Pink
            'linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%)', // Mint Green
        ];

        // Function to guess icon based on category name
        if (!function_exists('getCategoryIcon')) {
            function getCategoryIcon($name)
            {
                $name = strtolower($name);
                if (str_contains($name, 'mobile') || str_contains($name, 'phone')) {
                    return 'fas fa-mobile-alt';
                }
                if (str_contains($name, 'laptop') || str_contains($name, 'computer')) {
                    return 'fas fa-laptop';
                }
                if (str_contains($name, 'fashion') || str_contains($name, 'cloth')) {
                    return 'fas fa-tshirt';
                }
                if (str_contains($name, 'beauty') || str_contains($name, 'cosmetic')) {
                    return 'fas fa-magic';
                }
                if (str_contains($name, 'home') || str_contains($name, 'furniture')) {
                    return 'fas fa-couch';
                }
                if (str_contains($name, 'electronic') || str_contains($name, 'appliance')) {
                    return 'fas fa-tv';
                }
                if (str_contains($name, 'toy')) {
                    return 'fas fa-gamepad';
                }
                if (str_contains($name, 'grocery') || str_contains($name, 'food')) {
                    return 'fas fa-shopping-basket';
                }
                return 'fas fa-box';
            }
        }
    @endphp

    {{-- Category Strip --}}
    <div class="bg-white shadow-sm mb-3 flipkart-category-strip-container">
        <div class="container-fluid px-2 px-md-4">
            <div
                class="d-flex align-items-end justify-content-start justify-content-lg-center gap-3 gap-md-5 overflow-auto py-3 px-2 hide-scrollbar flipkart-category-strip">

                {{-- 'For You' / 'All Products' Base Item --}}
                <a href="{{ route('frontend.products.index') }}"
                    class="category-item text-center flex-shrink-0 text-decoration-none {{ !request('category') ? 'active-cat' : '' }}">
                    <div class="category-icon-wrapper mx-auto mb-1 d-flex align-items-center justify-content-center">
                        <i class="fas fa-gem fa-lg"></i>
                    </div>
                    <span class="category-name fw-bold" style="font-size: 0.85rem;">For You</span>
                </a>

                @foreach ($activeCategories as $category)
                    <a href="{{ route('frontend.products.index', ['category' => $category->id]) }}"
                        class="category-item text-center flex-shrink-0 text-decoration-none {{ request('category') == $category->id ? 'active-cat' : '' }}">
                        <div class="category-icon-wrapper mx-auto mb-1 d-flex align-items-center justify-content-center">
                            <i class="{{ getCategoryIcon($category->name) }} fa-lg"></i>
                        </div>
                        <span class="category-name" style="font-size: 0.85rem;">{{ $category->name }}</span>
                    </a>
                @endforeach

            </div>
        </div>
    </div>

    {{-- Horizontal Ad Banners --}}
    @if ($featuredAds->count() > 0)
        <div class="container-fluid px-2 px-md-4 mb-4">
            <div class="d-flex gap-3 overflow-auto hide-scrollbar pb-3 snap-x">
                @foreach ($featuredAds as $index => $ad)
                    @php
                        if ($isProductFallback) {
                            $adImages = $ad->image ? explode(',', $ad->image) : [];
                            $imageUrl = asset('images/products/' . ($adImages[0] ?? 'no-image.png'));
                            $title = $ad->name;
                            $subtitle = 'From ₹' . number_format($ad->price);
                            $badgeText = $index === 0 ? 'SUMMER SALE' : 'Mega Deal';
                            $link = route('frontend.products.show', $ad->id);
                            $bgGradient = $gradients[$index % count($gradients)];
                            $textColor = 'text-dark';
                        } else {
                            $imageUrl = $ad->image ? asset('images/banners/' . $ad->image) : '';
                            $title = $ad->title;
                            $subtitle = $ad->subtitle;
                            $badgeText = ''; // Removed standard badges for custom banners; text handles it
                            $link = $ad->link ?? '#';
                            $bgGradient = $ad->background_color;
                            $textColor = $ad->text_color;
                        }
                    @endphp
                    <div class="banner-card flex-shrink-0 rounded-4 p-3 position-relative overflow-hidden snap-center"
                        style="background: {{ $bgGradient }}; width: 85vw; max-width: 500px; min-height: 200px;">
                        <a href="{{ $link }}" class="text-decoration-none">
                            <div class="row h-100 align-items-center position-relative z-1">
                                <div class="col-7 col-sm-7 ps-3 pe-0">
                                    @if ($isProductFallback)
                                        @if ($index === 0)
                                            <h4 class="fw-black text-danger mb-1 fst-italic"
                                                style="font-weight: 900; letter-spacing: 1px;">{{ $badgeText }}</h4>
                                        @else
                                            <span
                                                class="badge bg-white text-dark mb-2 shadow-sm rounded-pill px-3 py-1 fw-bold"
                                                style="font-size: 0.7rem;">
                                                <i class="fas fa-bolt text-warning me-1"></i> {{ $badgeText }}
                                            </span>
                                        @endif
                                    @endif

                                    <h3 class="fw-bold {{ $textColor }} mb-2 text-truncate-2 mt-1"
                                        style="font-size: 1.3rem; line-height: 1.2;">{{ $title }}</h3>

                                    @if ($subtitle)
                                        <div class="mb-3">
                                            <span class="{{ $textColor }} fw-bold"
                                                style="font-size: 1.1rem; opacity: 0.9;">{{ $subtitle }}</span>
                                        </div>
                                    @endif

                                    <span class="btn btn-primary btn-sm rounded-1 px-4 fw-bold shadow-sm"
                                        style="background-color: #2874f0; border-color: #2874f0;">
                                        Shop Now
                                    </span>
                                </div>
                                <div class="col-5 col-sm-5 text-center px-1">
                                    @if ($imageUrl)
                                        <img src="{{ $imageUrl }}" alt="{{ $title }}"
                                            class="img-fluid banner-img"
                                            style="max-height: 160px; object-fit: contain; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));">
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="container mb-5">

        {{-- Products Grid --}}
        <div class="row g-4" id="productsContainer">
            @forelse ($products as $product)
                @php
                    $images = $product->image ? explode(',', $product->image) : [];
                    $sizeIds = $product->size_id ? explode(',', $product->size_id) : [];
                    $colorIds = $product->color_id ? explode(',', $product->color_id) : [];
                @endphp

                <div class="col-lg-3 col-md-4 col-sm-6 product-item" data-name="{{ strtolower($product->name) }}"
                    data-price="{{ $product->price }}" data-category="{{ $product->category_id }}"
                    data-colors="{{ implode(',', $colorIds) }}" data-sizes="{{ implode(',', $sizeIds) }}">
                    <div class="card h-100 border-0 shadow-sm product-card">

                        {{-- Product Image --}}
                        <div class="product-image-wrapper position-relative text-center">
                            <a href="{{ route('frontend.products.show', $product->id) }}"
                                class="d-block w-100 h-100 px-3 py-4">
                                <img src="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                                    alt="{{ $product->name }}" class="product-image">
                            </a>

                            {{-- Badges --}}
                            <div class="position-absolute top-0 start-0 p-2">
                                @if ($product->created_at && $product->created_at->diffInDays(now()) < 7)
                                    <span
                                        class="badge bg-success shadow-sm rounded-pill">{{ __('products.new_badge') }}</span>
                                @endif
                            </div>

                            {{-- Wishlist Indicator (Top Right) --}}
                            @auth('customer')
                                <div class="position-absolute top-0 end-0 p-2">
                                    <a href="javascript:void(0)" onclick="addToWishlist(this)"
                                        data-product-id="{{ $product->id }}"
                                        class="wishlist-btn-top {{ in_array($product->id, $wishlistProductIds ?? []) ? 'active' : '' }}">
                                        <i
                                            class="{{ in_array($product->id, $wishlistProductIds ?? []) ? 'fas' : 'far' }} fa-heart text-danger"></i>
                                    </a>
                                </div>
                            @endauth
                        </div>

                        <div class="card-body d-flex flex-column p-3">
                            <div class="flex-grow-1">
                                {{-- Product Name --}}
                                <a href="{{ route('frontend.products.show', $product->id) }}" class="text-decoration-none">
                                    <h5 class="card-title fw-bold mb-2 text-dark title-clamp" title="{{ $product->name }}">
                                        {{ $product->name }}
                                    </h5>
                                </a>

                                {{-- Passive Color Dots --}}
                                @if (!empty($colorIds))
                                    <div class="d-flex align-items-center gap-1 mb-2">
                                        @foreach (array_slice($colorIds, 0, 4) as $cid)
                                            @php $color = $colors->firstWhere('id', (int) $cid); @endphp
                                            @if ($color)
                                                <span class="passive-color-dot"
                                                    style="background-color: {{ $color->hex_code }}; border: 1px solid #ddd;"
                                                    title="{{ $color->name }}"></span>
                                            @endif
                                        @endforeach
                                        @if (count($colorIds) > 4)
                                            <span class="text-muted small ms-1">+{{ count($colorIds) - 4 }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="mb-2" style="height: 16px;"></div> {{-- Spacer --}}
                                @endif

                                {{-- Price --}}
                                <div class="mb-2 d-flex align-items-baseline gap-2">
                                    <h4 class="text-dark fw-bold mb-0" style="font-size: 1.25rem;">
                                        ₹{{ number_format($product->price, 2) }}
                                    </h4>
                                </div>

                                {{-- Stock Status --}}
                                <div style="min-height: 20px;" class="mb-3">
                                    @if ($product->stock <= 0)
                                        <div class="text-danger fw-bold small" style="color: #b91c1c !important;">Out Of
                                            Stock</div>
                                    @elseif($product->stock <= 5)
                                        <div class="text-warning fw-bold small" style="color: #b45309 !important;">Only
                                            {{ $product->stock }} left</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-auto">
                                @if ($product->stock > 0)
                                    @if (!empty($colorIds) || !empty($sizeIds))
                                        <a href="{{ route('frontend.products.show', $product->id) }}"
                                            class="btn btn-outline-primary btn-sm w-100 rounded-pill fw-semibold">
                                            See Options
                                        </a>
                                    @else
                                        @if (in_array($product->id, $cartProductIds ?? []))
                                            <a href="{{ route('frontend.cart') }}"
                                                class="btn btn-warning btn-sm w-100 rounded-pill fw-semibold">
                                                Go to Cart
                                            </a>
                                        @else
                                            <button
                                                class="btn btn-warning btn-sm w-100 rounded-pill fw-semibold add-to-cart-btn"
                                                data-product-id="{{ $product->id }}"
                                                data-product-price="{{ $product->price }}" onclick="addToCart(this)">
                                                Add to Cart
                                            </button>
                                        @endif
                                    @endif
                                @else
                                    <button class="btn btn-secondary btn-sm w-100 disabled rounded-pill"
                                        style="opacity: 0.6;">
                                        Unavailable
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">{{ __('products.no_products') }}</h4>
                        <p class="text-muted">{{ __('products.no_products_sub') }}</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- No Results Message --}}
        <div class="row" id="noResultsMessage" style="display: none;">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">{{ __('products.no_results') }}</h4>
                    <p class="text-muted">{{ __('products.no_results_sub') }}</p>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        @if (method_exists($products, 'hasPages') && $products->hasPages())
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Styles --}}
    <style>
        .title-clamp {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            font-size: 1rem;
            line-height: 1.3;
            height: 2.6rem;
        }

        .passive-color-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-block;
        }

        .text-truncate-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Scrollbar styles for horizontal areas */
        .hide-scrollbar::-webkit-scrollbar {
            height: 6px;
        }

        .hide-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .hide-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }

        .hide-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            .hide-scrollbar::-webkit-scrollbar {
                display: none;
            }

            .hide-scrollbar {
                -ms-overflow-style: none;
                /* IE and Edge */
                scrollbar-width: none;
                /* Firefox */
            }
        }

        .flipkart-category-strip .category-item {
            min-width: 65px;
            color: #666;
            transition: all 0.2s ease;
        }

        .flipkart-category-strip .category-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #f1f3f6;
            transition: all 0.2s ease;
        }

        .flipkart-category-strip .category-item:hover .category-icon-wrapper,
        .flipkart-category-strip .active-cat .category-icon-wrapper {
            background: #2874f0;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(40, 116, 240, 0.3);
        }

        .flipkart-category-strip .category-item:hover .category-name,
        .flipkart-category-strip .active-cat .category-name {
            color: #2874f0;
            font-weight: bold;
        }

        .active-cat .category-name {
            border-bottom: 2px solid #2874f0;
            padding-bottom: 2px;
        }

        .snap-x {
            scroll-snap-type: x mandatory;
        }

        .snap-center {
            scroll-snap-align: center;
        }

        .banner-card {
            transition: transform 0.3s ease;
            cursor: pointer;
        }

        .banner-card:hover {
            transform: scale(0.98);
        }

        .banner-img {
            transition: transform 0.5s ease;
        }

        .banner-card:hover .banner-img {
            transform: scale(1.1);
        }

        .product-card {
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.04) !important;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
            border-color: #cbd5e1 !important;
            z-index: 2;
        }

        .product-image-wrapper {
            height: 220px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.03);
            transition: background 0.3s;
        }

        .product-card:hover .product-image-wrapper {
            background: #f8fafc;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: transform 0.4s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.08);
        }



        @media (max-width: 768px) {
            .product-image-wrapper {
                height: 200px;
            }

            .display-4 {
                font-size: 2rem;
            }
        }

        /* Wishlist Button Top Right */
        .wishlist-btn-top {
            background: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .wishlist-btn-top:hover {
            transform: scale(1.15);
            background: #fff0f2;
            border-color: #ffe4e6;
        }

        .wishlist-btn-top i {
            color: #ccc;
            font-size: 16px;
        }

        .wishlist-btn-top:hover i {
            color: #dc3545;
        }

        .wishlist-btn-top.active i {
            color: #dc3545;
        }

        /* --- Dark Mode Overrides --- */
        [data-bs-theme="dark"] .flipkart-category-strip-container {
            background-color: #1e1e1e !important;
            border-bottom: 1px solid #333;
        }

        [data-bs-theme="dark"] .flipkart-category-strip .category-icon-wrapper {
            background: #2a2a2a;
        }

        [data-bs-theme="dark"] .flipkart-category-strip .category-name {
            color: #d1d5db;
        }

        [data-bs-theme="dark"] .product-image-wrapper {
            background: #2a2a2a;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        [data-bs-theme="dark"] .product-card:hover .product-image-wrapper {
            background: #333;
        }

        [data-bs-theme="dark"] .product-card {
            background: #1e1e1e !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        [data-bs-theme="dark"] .product-card:hover {
            border-color: #4b5563 !important;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3) !important;
        }

        [data-bs-theme="dark"] .product-card .text-dark {
            color: #f8fafc !important;
        }

        [data-bs-theme="dark"] .wishlist-btn-top {
            background: #333;
        }

        [data-bs-theme="dark"] .wishlist-btn-top i:not(.text-danger) {
            color: #888;
        }

        [data-bs-theme="dark"] .wishlist-btn-top:hover {
            background: #444;
            border-color: #555;
        }
    </style>

    {{-- Scripts --}}
    <script>
        @php
            $appLang = [
                'please_select_color' => __('products.please_select_color'),
                'please_select_size' => __('products.please_select_size'),
                'added_to_cart' => __('products.added_to_cart'),
                'something_went_wrong' => __('products.something_went_wrong'),
                'go_to_cart' => __('products.go_to_cart'),
                'in_wishlist' => __('products.in_wishlist'),
                'wishlist' => __('products.wishlist'),
            ];
        @endphp
        window.AppLang = @json($appLang);
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Handle Add to Cart with selected options
        function addToCart(button, mode = 'increment', callback = null) {

            const productId = button.dataset.productId;
            const price = button.dataset.productPrice;

            const colorSelect = document.querySelector(`.color-select[data-product-id="${productId}"]`);
            const sizeSelect = document.querySelector(`.size-select[data-product-id="${productId}"]`);

            let colorId = colorSelect ? colorSelect.value : null;
            let sizeId = sizeSelect ? sizeSelect.value : null;

            if (colorSelect && !colorId) {
                Swal.fire({
                    icon: 'warning',
                    text: window.AppLang.please_select_color,
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            if (sizeSelect && !sizeId) {
                Swal.fire({
                    icon: 'warning',
                    text: window.AppLang.please_select_size,
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            fetch("{{ route('cart.add') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1,
                        price: price,
                        color_id: colorId,
                        size_id: sizeId,
                        mode: mode
                    })
                })
                .then(async response => {
                    if (response.status === 401) {
                        window.location.href = "{{ route('customer.login') }}";
                        return;
                    }

                    const data = await response.json();

                    if (!response.ok) {
                        Swal.fire({
                            icon: 'error',
                            text: data.message || 'Error adding to cart'
                        });
                        return;
                    }

                    if (data.success) {
                        // Update cart count badge
                        const cartBadges = document.querySelectorAll('.cart-badge');
                        if (data.cart_count !== undefined) {
                            if (cartBadges.length > 0) {
                                cartBadges.forEach(badge => {
                                    badge.textContent = data.cart_count;
                                });
                            } else {
                                const cartIconWrapper = document.querySelector('.cart-icon-wrapper');
                                if (cartIconWrapper) {
                                    const badge = document.createElement('span');
                                    badge.className = 'cart-badge';
                                    badge.textContent = data.cart_count;
                                    cartIconWrapper.appendChild(badge);
                                }
                            }
                        }

                        // Refresh Cart Dropdown Preview HTML
                        refreshCartPreview();

                        if (callback) {
                            callback(data);
                        } else {
                            showNotification(window.AppLang.added_to_cart, 'success');
                            // Dynamic button switch
                            const actionContainer = document.getElementById(`product-actions-${productId}`);
                            if (actionContainer) {
                                const addToCartBtn = actionContainer.querySelector('.add-to-cart-btn');
                                if (addToCartBtn) {
                                    const goCartHtml = `
                                        <a href="{{ route('frontend.cart') }}" class="btn btn-warning btn-sm flex-fill">
                                            <i class="fas fa-arrow-right"></i> ${window.AppLang.go_to_cart}
                                        </a>
                                    `;
                                    addToCartBtn.outerHTML = goCartHtml;
                                }
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire({
                        icon: 'error',
                        text: window.AppLang.something_went_wrong
                    });
                });
        }

        function refreshCartPreview() {
            fetch("{{ route('cart.preview-items') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const previewContainer = document.getElementById('cartPreviewDropdown');
                        if (previewContainer) {
                            previewContainer.innerHTML = data.html;
                        }
                    }
                })
                .catch(error => console.error('Error refreshing cart preview:', error));
        }

        // Buy Now function for listing page

        function buyNow(button) {
            addToCart(button, 'replace', function(data) {
                if (data && data.cart_item_id) {
                    // Redirect to cart with specific item ID
                    window.location.href = "{{ route('frontend.cart') }}?buy_item_id=" + data.cart_item_id;
                } else {
                    // Fallback to general checkout if ID is missing
                    window.location.href = "{{ route('frontend.cart') }}?checkout=1";
                }
            });
        }


        {{--  function handleAddToCart(event, productId) {
            event.preventDefault();

            const button = event.currentTarget;
            const colorSelect = document.querySelector(`.color-select[data-product-id="${productId}"]`);
            const sizeSelect = document.querySelector(`.size-select[data-product-id="${productId}"]`);

            // Get selected values
            const selectedColor = colorSelect ? colorSelect.value : null;
            const selectedSize = sizeSelect ? sizeSelect.value : null;

            // Check if color is required and selected
            if (colorSelect && !selectedColor) {
                showNotification('Please select a color', 'warning');
                colorSelect.focus();
                return false;
            }

            // Check if size is required and selected
            if (sizeSelect && !selectedSize) {
                showNotification('Please select a size', 'warning');
                sizeSelect.focus();
                return false;
            }

            // Get product details
            const productName = button.dataset.productName;
            const productPrice = parseFloat(button.dataset.productPrice);
            const productImage = button.dataset.productImage;
            const productCategory = button.dataset.productCategory;

            // Get color details
            let colorId = null;
            let colorName = null;
            let colorHex = null;

            if (colorSelect && selectedColor) {
                const colorOption = colorSelect.options[colorSelect.selectedIndex];
                colorId = selectedColor;
                colorName = colorOption.text;
                colorHex = colorOption.dataset.hex;
            }

            // Get size details
            let sizeId = null;
            let sizeName = null;

            if (sizeSelect && selectedSize) {
                const sizeOption = sizeSelect.options[sizeSelect.selectedIndex];
                sizeId = selectedSize;
                sizeName = sizeOption.text;
            }

            // Create cart item
            const cartItem = {
                productId: productId,
                name: productName,
                category: productCategory,
                price: productPrice,
                quantity: 1,
                image: productImage,
                colorId: colorId,
                color: colorName,
                colorHex: colorHex,
                sizeId: sizeId,
                size: sizeName,
                timestamp: new Date().getTime()
            };

            // Get existing cart
            let cart = JSON.parse(localStorage.getItem('shoppingCart') || '[]');

            // Check if item already exists
            const existingItemIndex = cart.findIndex(item =>
                item.productId === cartItem.productId &&
                item.colorId === cartItem.colorId &&
                item.sizeId === cartItem.sizeId
            );

            if (existingItemIndex > -1) {
                // Update quantity
                cart[existingItemIndex].quantity += 1;
                if (cart[existingItemIndex].quantity > 10) {
                    cart[existingItemIndex].quantity = 10;
                    showNotification('Maximum quantity is 10', 'warning');
                }
            } else {
                // Add new item
                cart.push(cartItem);
            }

            // Save to localStorage
            localStorage.setItem('shoppingCart', JSON.stringify(cart));

            // Dispatch event to update cart count
            window.dispatchEvent(new Event('cartUpdated'));

            // Show success message
            showNotification('Added to cart successfully!', 'success', cartItem);

            // Optional: Redirect to cart or product detail page
            // Uncomment one of these if you want automatic redirect:
            // window.location.href = "{{ route('frontend.cart') }}?added=true";
            // window.location.href = button.href;

            return false;
        }  --}}

        function showNotification(message, type = 'success', product = null) {
            Swal.fire({
                icon: type,
                title: product ? product.name : '',
                text: message,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        function addToWishlist(button) {
            const productId = button.getAttribute('data-product-id');
            const icon = button.querySelector('i');
            const textNode = button.childNodes[1]; // The text part of the button

            // Find the top-right heart as well if it exists
            const topHeart = document.querySelector(`.wishlist-btn-top[data-product-id="${productId}"]`);
            const topIcon = topHeart ? topHeart.querySelector('i') : null;

            fetch("{{ route('wishlist.add') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(async response => {
                    if (response.status === 401) {
                        window.location.href = "{{ route('customer.login') }}";
                        return;
                    }

                    const data = await response.json();

                    if (data.status === 'success') {
                        showNotification(data.message, 'success');

                        if (data.action === 'added') {
                            // Update button
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                            button.classList.add('active');
                            icon.style.color = '#dc3545';
                            if (textNode && textNode.nodeType === Node.TEXT_NODE) textNode.textContent =
                                ' ' + window.AppLang.in_wishlist;

                            // Update top-right icon
                            if (topIcon) {
                                topIcon.classList.remove('far');
                                topIcon.classList.add('fas');
                                topHeart.classList.add('active');
                            }
                        } else {
                            // Update button
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                            button.classList.remove('active');
                            icon.style.color = '';
                            if (textNode && textNode.nodeType === Node.TEXT_NODE) textNode.textContent =
                                ' ' + window.AppLang.wishlist;

                            // Update top-right icon
                            if (topIcon) {
                                topIcon.classList.remove('fas');
                                topIcon.classList.add('far');
                                topHeart.classList.remove('active');
                            }
                        }

                        // Reload to update badge in nav after a delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showNotification(data.message || 'Error updating wishlist', 'error');
                    }
                })
                .catch(error => {
                    console.error(error);
                    showNotification(window.AppLang.something_went_wrong, 'error');
                });
        }
    </script>
@endsection
