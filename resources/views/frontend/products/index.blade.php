@extends('layouts.frontend.app')

@section('title', 'Our Products')

@section('content')
    {{-- Hero Section --}}
    <div class="bg-gradient-primary mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-30 fw-bold text-white mb-1">{{ __('products.our_products') }}</h1>
                    <p class="lead text-white-50">{{ __('products.discover_text') }}</p>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="text-white">
                        <i class="fas fa-box-open fa-2x opacity-20"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        <div class="product-image-wrapper position-relative">
                            <div class="product-img-wrapper">
                                <img src="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                                    alt="{{ $product->name }}" class="product-img">
                            </div>

                            {{--  <img src="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                                class="card-img-top product-image" alt="{{ $product->name }}">  --}}

                            {{-- Badges --}}
                            <div class="position-absolute top-0 start-0 p-2">
                                @if ($product->created_at && $product->created_at->diffInDays(now()) < 7)
                                    <span class="badge bg-success">{{ __('products.new_badge') }}</span>
                                @endif
                            </div>

                            {{-- Wishlist Indicator (Top Right) --}}
                            @auth('customer')
                                <div class="position-absolute top-0 end-0 p-2">
                                    <a href="javascript:void(0)" onclick="addToWishlist(this)"
                                        data-product-id="{{ $product->id }}"
                                        class="wishlist-btn-top {{ in_array($product->id, $wishlistProductIds ?? []) ? 'active' : '' }}">
                                        <i
                                            class="{{ in_array($product->id, $wishlistProductIds ?? []) ? 'fas' : 'far' }} fa-heart"></i>
                                    </a>
                                </div>
                            @endauth

                            {{-- Quick View --}}
                            <div class="product-overlay">
                                <a href="{{ route('frontend.products.show', $product->id) }}"
                                    class="btn btn-light btn-sm rounded-pill mb-1 pd-1">
                                    <i class="fas fa-eye"></i> {{ __('products.quick_view') }}
                                </a>
                                @auth('customer')
                                    @php $isInWishlist = in_array($product->id, $wishlistProductIds ?? []); @endphp
                                    <button class="btn btn-light btn-sm rounded-pill add-to-wishlist"
                                        data-product-id="{{ $product->id }}" onclick="addToWishlist(this)">
                                        <i class="{{ $isInWishlist ? 'fas' : 'far' }} fa-heart"
                                            style="{{ $isInWishlist ? 'color: #dc3545;' : '' }}"></i>
                                        {{ $isInWishlist ? __('products.in_wishlist') : __('products.wishlist') }}
                                    </button>
                                @endauth
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            {{-- Product Name --}}
                            <h5 class="card-title fw-bold mb-2 text-truncate" title="{{ $product->name }}">
                                {{ $product->name }}
                            </h5>

                            {{-- Category --}}
                            @if ($product->category)
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-tag"></i> {{ $product->category->name }}
                                </p>
                            @endif

                            {{-- Description --}}
                            <p class="card-text text-muted small mb-3" style="min-height: 40px;">
                                {{ \Illuminate\Support\Str::limit($product->detail, 60) }}
                            </p>

                            {{-- Price --}}
                            <div class="mb-3">
                                <h4 class="text-primary fw-bold mb-0">
                                    ₹{{ number_format($product->price, 2) }}
                                </h4>
                            </div>

                            {{-- Color Selection Dropdown --}}
                            @if (!empty($colorIds))
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">
                                        <i class="fas fa-palette"></i> {{ __('products.select_color') }}:
                                    </label>
                                    <select class="form-select form-select-sm color-select"
                                        data-product-id="{{ $product->id }}">
                                        <option value="">{{ __('products.choose_color') }}</option>
                                        @foreach ($colorIds as $cid)
                                            @php $color = $colors->firstWhere('id', (int) $cid); @endphp
                                            @if ($color)
                                                <option value="{{ $color->id }}" data-hex="{{ $color->hex_code }}">
                                                    {{ $color->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    {{-- Color Preview --}}
                                    <div class="color-preview mt-2" id="colorPreview{{ $product->id }}"
                                        style="display: none;">
                                        <small class="text-muted">Selected: </small>
                                        <span class="color-indicator" id="colorIndicator{{ $product->id }}"></span>
                                        <small id="colorName{{ $product->id }}"></small>
                                    </div>
                                </div>
                            @endif

                            {{-- Size Selection Dropdown --}}
                            @if (!empty($sizeIds))
                                <div class="mb-3">
                                    <label class="form-label small text-muted mb-1">
                                        <i class="fas fa-ruler"></i> {{ __('products.select_size') }}:
                                    </label>
                                    <select class="form-select form-select-sm size-select"
                                        data-product-id="{{ $product->id }}">
                                        <option value="">{{ __('products.choose_size') }}</option>
                                        @foreach ($sizeIds as $sid)
                                            @php $size = $sizes->firstWhere('id', (int) $sid); @endphp
                                            @if ($size)
                                                <option value="{{ $size->id }}">
                                                    {{ $size->code ?? $size->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if (session()->has('customer_id'))
                                <div class="d-flex gap-2" id="product-actions-{{ $product->id }}">
                                    @if (in_array($product->id, $cartProductIds ?? []))
                                        <a href="{{ route('frontend.cart') }}"
                                            class="btn btn-outline-warning btn-sm flex-fill">
                                            <i class="fas fa-arrow-right"></i> {{ __('products.go_to_cart') }}
                                        </a>
                                    @else
                                        <button class="btn btn-outline-primary btn-sm flex-fill add-to-cart-btn"
                                            data-product-id="{{ $product->id }}"
                                            data-product-price="{{ $product->price }}" onclick="addToCart(this)">
                                            <i class="fas fa-shopping-cart"></i> {{ __('products.add_to_cart') }}
                                        </button>
                                    @endif
                                    <button class="btn btn-outline-success btn-sm flex-fill"
                                        data-product-id="{{ $product->id }}" data-product-price="{{ $product->price }}"
                                        onclick="buyNow(this)">
                                        <i class="fas fa-bolt"></i> {{ __('products.buy_now', ['price' => number_format($product->price)]) }}
                                    </button>
                                </div>
                            @else
                                <a href="{{ route('customer.login') }}" class="btn btn-primary w-100">
                                    {{ __('products.login_to_buy') }}
                                </a>
                            @endif
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
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .product-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        }

        .product-image-wrapper {
            height: 250px;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            /* IMPORTANT */
        }



        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .product-overlay {
            opacity: 1;
        }

        .color-dot {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            display: inline-block;
        }

        .color-dot:hover {
            transform: scale(1.2);
            border-color: #667eea;
        }

        .card-title {
            font-size: 1.1rem;
            line-height: 1.4;
        }

        .badge.bg-light {
            font-weight: 500;
            padding: 0.4em 0.6em;
        }

        .color-indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            display: inline-block;
            vertical-align: middle;
        }

        .color-preview {
            padding: 8px;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .form-select-sm {
            font-size: 0.875rem;
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
            transform: scale(1.1);
        }

        .wishlist-btn-top i {
            color: #ccc;
            font-size: 16px;
        }

        .wishlist-btn-top.active i {
            color: #dc3545;
        }
    </style>

    {{-- Scripts --}}
    <script>
        @php
            $appLang = [
                'please_select_color'  => __('products.please_select_color'),
                'please_select_size'   => __('products.please_select_size'),
                'added_to_cart'        => __('products.added_to_cart'),
                'something_went_wrong' => __('products.something_went_wrong'),
                'go_to_cart'           => __('products.go_to_cart'),
                'in_wishlist'          => __('products.in_wishlist'),
                'wishlist'             => __('products.wishlist'),
            ];
        @endphp
        window.AppLang = @json($appLang);

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize color select change handlers
            initializeColorSelects();
        });

        // Initialize color select dropdowns
        function initializeColorSelects() {
            document.querySelectorAll('.color-select').forEach(select => {
                select.addEventListener('change', function() {
                    const productId = this.dataset.productId;
                    const selectedOption = this.options[this.selectedIndex];
                    const colorHex = selectedOption.dataset.hex;
                    const colorName = selectedOption.text;

                    const preview = document.getElementById('colorPreview' + productId);
                    const indicator = document.getElementById('colorIndicator' + productId);
                    const nameSpan = document.getElementById('colorName' + productId);

                    if (this.value) {
                        preview.style.display = 'block';
                        indicator.style.backgroundColor = colorHex;
                        nameSpan.textContent = colorName;
                    } else {
                        preview.style.display = 'none';
                    }
                });
            });
        }

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
