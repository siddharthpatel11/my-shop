@extends('layouts.frontend.app')

@section('title', 'Our Products')

@section('content')
    {{-- Hero Section --}}
    <div class="bg-gradient-primary mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-30 fw-bold text-white mb-1">Our Products</h1>
                    <p class="lead text-white-50">Discover our amazing collection of quality products</p>
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
                                    <span class="badge bg-success">New</span>
                                @endif
                            </div>

                            {{-- Quick View --}}
                            <div class="product-overlay">
                                <a href="{{ route('frontend.products.show', $product->id) }}"
                                    class="btn btn-light btn-sm rounded-pill">
                                    <i class="fas fa-eye"></i> Quick View
                                </a>
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
                                        <i class="fas fa-palette"></i> Select Color:
                                    </label>
                                    <select class="form-select form-select-sm color-select"
                                        data-product-id="{{ $product->id }}">
                                        <option value="">Choose Color</option>
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
                                        <i class="fas fa-ruler"></i> Select Size:
                                    </label>
                                    <select class="form-select form-select-sm size-select"
                                        data-product-id="{{ $product->id }}">
                                        <option value="">Choose Size</option>
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
                            {{--
@php
    dump(session()->all());
@endphp  --}}
                            @if (session()->has('customer_id'))
                                <button class="btn btn-primary w-100" data-product-id="{{ $product->id }}"
                                    data-product-price="{{ $product->price }}" onclick="addToCart(this)">
                                    Add to Cart
                                </button>
                            @else
                                <a href="{{ route('customer.login') }}" class="btn btn-primary">
                                    Login to Add to Cart
                                </a>
                            @endif




                            {{--  Action Buttons --}}
                            {{--  <div class="mt-auto">
                                <a href="{{ route('frontend.products.show', $product->id) }}"
                                    class="btn btn-primary w-100 add-to-cart-btn" data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}" data-product-price="{{ $product->price }}"
                                    data-product-image="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                                    data-product-category="{{ $product->category ? $product->category->name : '' }}"
                                    onclick="handleAddToCart(event, {{ $product->id }})">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </a>
                            </div>  --}}
                        </div>
                    </div>
                </div>

            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">No products available</h4>
                        <p class="text-muted">Check back later for new products!</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- No Results Message --}}
        <div class="row" id="noResultsMessage" style="display: none;">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No products found</h4>
                    <p class="text-muted">Try adjusting your filters</p>
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
    </style>

    {{-- Scripts --}}
    <script>
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

        function addToCart(button) {

            const productId = button.dataset.productId;
            const price = button.dataset.productPrice;

            const colorSelect = document.querySelector(`.color-select[data-product-id="${productId}"]`);
            const sizeSelect = document.querySelector(`.size-select[data-product-id="${productId}"]`);

            let colorId = colorSelect ? colorSelect.value : null;
            let sizeId = sizeSelect ? sizeSelect.value : null;

            if (colorSelect && !colorId) {
                alert('Please select a color');
                return;
            }

            if (sizeSelect && !sizeId) {
                alert('Please select a size');
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
                        size_id: sizeId
                    })
                })
                .then(async response => {
                    if (response.status === 401) {
                        window.location.href = "{{ route('customer.login') }}";
                        return;
                    }

                    const data = await response.json();

                    if (!response.ok) {
                        alert(data.message || 'Error adding to cart');
                        return;
                    }

                    if (data.success) {
                        window.location.href = "{{ route('frontend.cart') }}";
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('Something went wrong');
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
            const notification = document.createElement('div');
            notification.className = `toast align-items-center text-bg-${type} border-0 show`;
            notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    width: 320px;
    `;

            notification.innerHTML = `
    <div class="d-flex">
        <div class="toast-body">
            ${product ? `
                                                        <div class="d-flex align-items-center gap-2 mb-2">
                                                            <img src="${product.image}" style="width:50px;height:50px;object-fit:contain;">
                                                            <div>
                                                                <strong>${product.name}</strong><br>
                                                                <small>
                                                                    ${product.color ? `Color: ${product.color}` : ''}
                                                                    ${product.size ? ` | Size: ${product.size}` : ''}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        ` : ''}
            ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto"></button>
    </div>
    `;

            document.body.appendChild(notification);

            setTimeout(() => notification.remove(), 3500);
        }
    </script>
@endsection
