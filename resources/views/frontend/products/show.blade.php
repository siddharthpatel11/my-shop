@extends('layouts.frontend.app')

@section('title', $product->name)

@section('content')
    <div class="container my-5">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('frontend.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        {{-- Product Details --}}
        <div class="row g-4 mb-5">
            @php
                $images = $product->image ? explode(',', $product->image) : [];
                $sizeIds = $product->size_id ? explode(',', $product->size_id) : [];
                $colorIds = $product->color_id ? explode(',', $product->color_id) : [];
            @endphp

            {{-- Image Gallery --}}
            <div class="col-lg-6">
                <div class="sticky-top" style="top: 20px;">
                    {{-- Main Image --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="main-image-container">
                            <img id="mainImage" src="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                                class="card-img-top main-product-image" alt="{{ $product->name }}">
                            @if ($product->created_at && $product->created_at->diffInDays(now()) < 7)
                                <span class="badge bg-success position-absolute top-0 start-0 m-3">New</span>
                            @endif
                        </div>
                    </div>

                    {{-- Thumbnail Gallery --}}
                    @if (count($images) > 1)
                        <div class="row g-2">
                            @foreach ($images as $index => $image)
                                <div class="col-3">
                                    <img src="{{ asset('images/products/' . $image) }}"
                                        class="img-thumbnail thumbnail-image {{ $index === 0 ? 'active' : '' }}"
                                        onclick="changeMainImage('{{ asset('images/products/' . $image) }}', this)"
                                        style="cursor: pointer; height: 100px; object-fit: cover;"
                                        alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="col-lg-6">
                <div class="product-info">
                    {{-- Category Badge --}}
                    @if ($product->category)
                        <div class="mb-2">
                            <span class="badge bg-primary-subtle text-primary">
                                {{ $product->category->name }}
                            </span>
                        </div>
                    @endif

                    {{-- Product Name --}}
                    <h1 class="display-5 fw-bold mb-3">{{ $product->name }}</h1>

                    {{-- Price --}}
                    <div class="mb-4">
                        <h2 class="text-primary fw-bold mb-0">₹{{ number_format($product->price, 2) }}</h2>
                        <small class="text-muted">Inclusive of all taxes</small>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-2">Product Description</h5>
                        <p class="text-muted">{{ $product->detail }}</p>
                    </div>

                    <hr class="my-4">

                    {{-- Color Selection --}}
                    @if (!empty($colorIds))
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-flex align-items-center">
                                <i class="fas fa-palette me-2"></i> Select Color
                                <span class="ms-2 text-muted small" id="selectedColorName"></span>
                            </label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ($colorIds as $index => $cid)
                                    @php $color = $colors->firstWhere('id', (int) $cid); @endphp
                                    @if ($color && $color->hex_code)
                                        <div class="color-option {{ $index === 0 ? 'selected' : '' }}"
                                            data-color-id="{{ $color->id }}" data-color-name="{{ $color->name }}"
                                            onclick="selectColor(this)">
                                            <div class="color-swatch" style="background-color: {{ $color->hex_code }}">
                                            </div>
                                            <small class="color-label">{{ $color->name }}</small>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Size Selection --}}
                    @if (!empty($sizeIds))
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-flex align-items-center">
                                <i class="fas fa-ruler me-2"></i> Select Size
                                <span class="ms-2 text-muted small" id="selectedSizeName"></span>
                            </label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ($sizeIds as $index => $sid)
                                    @php $size = $sizes->firstWhere('id', (int) $sid); @endphp
                                    @if ($size)
                                        <div class="size-option {{ $index === 0 ? 'selected' : '' }}"
                                            data-size-id="{{ $size->id }}"
                                            data-size-name="{{ $size->code ?? $size->name }}" onclick="selectSize(this)">
                                            {{ $size->code ?? $size->name }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Quantity Selection --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-sort-numeric-up me-2"></i> Quantity
                        </label>
                        <div class="input-group" style="width: 150px;">
                            <button class="btn btn-outline-secondary" type="button" onclick="decrementQuantity()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" class="form-control text-center" id="quantity" value="1"
                                min="1" max="10">
                            <button class="btn btn-outline-secondary" type="button" onclick="incrementQuantity()">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-3 mb-4">
                        @auth('customer')
                            <button class="btn btn-primary btn-lg flex-fill" onclick="addToCart()">
                                <i class="fas fa-shopping-cart me-2"></i> Add to Cart
                            </button>
                            <button class="btn {{ $inWishlist ? 'btn-danger' : 'btn-outline-danger' }} btn-lg"
                                onclick="toggleWishlist()">
                                <i class="{{ $inWishlist ? 'fas' : 'far' }} fa-heart"></i>
                            </button>
                        @else
                            <a href="{{ route('customer.login') }}" class="btn btn-primary btn-lg flex-fill">
                                <i class="fas fa-sign-in-alt me-2"></i> Login to Buy
                            </a>
                        @endauth
                    </div>

                    {{-- Product Features --}}
                    <div class="card bg-light border-0">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-truck text-primary me-2"></i>
                                        <small>Free Shipping</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-undo text-primary me-2"></i>
                                        <small>Easy Returns</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-shield-alt text-primary me-2"></i>
                                        <small>Secure Payment</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-headset text-primary me-2"></i>
                                        <small>24/7 Support</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if ($relatedProducts->count() > 0)
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="fw-bold mb-4">Related Products</h3>
                </div>
            </div>

            <div class="row g-4">
                @foreach ($relatedProducts as $related)
                    @php
                        $relatedImages = $related->image ? explode(',', $related->image) : [];
                    @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="related-product-image-wrapper position-relative">
                                <img src="{{ asset('images/products/' . ($relatedImages[0] ?? 'no-image.png')) }}"
                                    class="card-img-top" alt="{{ $related->name }}">

                                {{-- Wishlist Indicator (Top Right) --}}
                                @auth('customer')
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <a href="javascript:void(0)" onclick="addToWishlistFromId({{ $related->id }}, this)"
                                            class="wishlist-btn-top {{ in_array($related->id, $wishlistProductIds ?? []) ? 'active' : '' }}">
                                            <i
                                                class="{{ in_array($related->id, $wishlistProductIds ?? []) ? 'fas' : 'far' }} fa-heart"></i>
                                        </a>
                                    </div>
                                @endauth
                            </div>
                            <div class="card-body">
                                <h6 class="card-title text-truncate">{{ $related->name }}</h6>
                                <p class="text-primary fw-bold mb-3">₹{{ number_format($related->price, 2) }}</p>
                                <a href="{{ route('frontend.products.show', $related->id) }}"
                                    class="btn btn-outline-primary btn-sm w-100">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Styles --}}
    <style>
        .main-image-container {
            position: relative;
            background: #f8f9fa;
            border-radius: 12px;
            overflow: hidden;
            height: 500px;
        }

        .main-product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .thumbnail-image {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .thumbnail-image.active,
        .thumbnail-image:hover {
            border-color: #667eea;
        }

        .color-option {
            text-align: center;
            cursor: pointer;
            padding: 8px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .color-option:hover {
            border-color: #667eea;
        }

        .color-option.selected {
            border-color: #667eea;
            background-color: #f8f9ff;
        }

        .color-swatch {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            margin: 0 auto 4px;
        }

        .color-label {
            display: block;
            font-size: 0.75rem;
        }

        .size-option {
            min-width: 50px;
            padding: 12px 16px;
            text-align: center;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .size-option:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }

        .size-option.selected {
            border-color: #667eea;
            background-color: #667eea;
            color: white;
        }

        .product-card {
            transition: transform 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }

        .related-product-image-wrapper {
            height: 200px;
            background: #f8f9fa;
            overflow: hidden;
        }

        .related-product-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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

        @media (max-width: 768px) {
            .main-image-container {
                height: 300px;
            }
        }
    </style>

    {{-- Scripts --}}
    <script>
        let selectedColor = null;
        let selectedSize = null;

        // Change main image
        function changeMainImage(imageSrc, element) {
            document.getElementById('mainImage').src = imageSrc;

            // Update active thumbnail
            document.querySelectorAll('.thumbnail-image').forEach(thumb => {
                thumb.classList.remove('active');
            });
            element.classList.add('active');
        }

        // Select color
        function selectColor(element) {
            document.querySelectorAll('.color-option').forEach(option => {
                option.classList.remove('selected');
            });
            element.classList.add('selected');
            selectedColor = element.dataset.colorId;
            document.getElementById('selectedColorName').textContent = `(${element.dataset.colorName})`;
        }

        // Select size
        function selectSize(element) {
            document.querySelectorAll('.size-option').forEach(option => {
                option.classList.remove('selected');
            });
            element.classList.add('selected');
            selectedSize = element.dataset.sizeId;
            document.getElementById('selectedSizeName').textContent = `(${element.dataset.sizeName})`;
        }

        // Quantity controls
        function incrementQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            const maxValue = parseInt(input.max);
            if (currentValue < maxValue) {
                input.value = currentValue + 1;
            }
        }

        function decrementQuantity() {
            const input = document.getElementById('quantity');
            const currentValue = parseInt(input.value);
            const minValue = parseInt(input.min);
            if (currentValue > minValue) {
                input.value = currentValue - 1;
            }
        }

        // Add to cart
        function addToCart() {
            const quantity = parseInt(document.getElementById('quantity').value);

            // Get selected color and size details
            const selectedColorElement = document.querySelector('.color-option.selected');
            const selectedSizeElement = document.querySelector('.size-option.selected');

            // Validate selections if color/size options exist
            const hasColorOptions = document.querySelectorAll('.color-option').length > 0;
            const hasSizeOptions = document.querySelectorAll('.size-option').length > 0;

            if (hasColorOptions && !selectedColor) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please select a color',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            if (hasSizeOptions && !selectedSize) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please select a size',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            // Get product image
            const mainImage = document.getElementById('mainImage').src;

            // Create cart item
            const cartItem = {
                productId: {{ $product->id }},
                name: "{{ $product->name }}",
                category: "{{ $product->category ? $product->category->name : '' }}",
                price: {{ $product->price }},
                quantity: quantity,
                image: mainImage,
                colorId: selectedColor,
                color: selectedColorElement ? selectedColorElement.dataset.colorName : null,
                colorHex: selectedColorElement ? selectedColorElement.querySelector('.color-swatch').style
                    .backgroundColor : null,
                sizeId: selectedSize,
                size: selectedSizeElement ? selectedSizeElement.dataset.sizeName : null,
                timestamp: new Date().getTime()
            };

            // Get existing cart from localStorage
            let cart = JSON.parse(localStorage.getItem('shoppingCart') || '[]');

            // Check if item already exists in cart (same product, color, size)
            const existingItemIndex = cart.findIndex(item =>
                item.productId === cartItem.productId &&
                item.colorId === cartItem.colorId &&
                item.sizeId === cartItem.sizeId
            );

            if (existingItemIndex > -1) {
                // Update quantity of existing item
                cart[existingItemIndex].quantity += quantity;
                if (cart[existingItemIndex].quantity > 10) {
                    cart[existingItemIndex].quantity = 10;
                }
            } else {
                // Add new item to cart
                cart.push(cartItem);
            }

            // Save to localStorage
            localStorage.setItem('shoppingCart', JSON.stringify(cart));

            // Show success notification
            showNotification('Product added to cart successfully!', 'success');

            // Optional: Redirect to cart page after short delay
            setTimeout(() => {
                window.location.href = "{{ route('frontend.cart') }}?added=true";
            }, 1000);
        }

        // Show notification function
        function showNotification(message, type = 'info') {
            Swal.fire({
                icon: type === 'info' ? 'info' : (type === 'success' ? 'success' : (type === 'warning' ? 'warning' :
                    'error')),
                text: message,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // Add/Remove from wishlist (for main product)
        function toggleWishlist() {
            const productId = {{ $product->id }};
            const btn = event.currentTarget;
            const icon = btn.querySelector('i');

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
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                            btn.classList.add('btn-danger', 'active');
                            btn.classList.remove('btn-outline-danger');
                        } else {
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                            btn.classList.remove('btn-danger', 'active');
                            btn.classList.add('btn-outline-danger');
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
                    showNotification('Something went wrong', 'error');
                });
        }

        // Initialize first selections
        document.addEventListener('DOMContentLoaded', function() {
            const firstColor = document.querySelector('.color-option.selected');
            if (firstColor) {
                selectedColor = firstColor.dataset.colorId;
                document.getElementById('selectedColorName').textContent = `(${firstColor.dataset.colorName})`;
            }

            const firstSize = document.querySelector('.size-option.selected');
            if (firstSize) {
                selectedSize = firstSize.dataset.sizeId;
                document.getElementById('selectedSizeName').textContent = `(${firstSize.dataset.sizeName})`;
            }
        });

        // Add to wishlist from ID (for related products)
        function addToWishlistFromId(productId, element) {
            const icon = element.querySelector('i');

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
                                icon.classList.remove('far');
                                icon.classList.add('fas');
                                element.classList.add('active');
                            } else {
                                icon.classList.remove('fas');
                                icon.classList.add('far');
                                element.classList.remove('active');
                            }

                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        } else {
                            showNotification(data.message || 'Error updating wishlist', 'error');
                        }
                    }
                })
        .catch(error => {
            console.error(error);
            showNotification('Something went wrong', 'error');
        });
        }
    </script>

    {{-- Font Awesome for icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
