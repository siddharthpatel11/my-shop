@extends('layouts.frontend.app')

@section('title', $product->seo_meta_title ?? $product->name)

@section('meta')
    @if ($product->seo_meta_title)
        <meta name="title" content="{{ $product->seo_meta_title }}">
    @endif
    @if ($product->seo_meta_description)
        <meta name="description" content="{{ $product->seo_meta_description }}">
    @endif
    @if ($product->seo_meta_key)
        <meta name="keywords" content="{{ $product->seo_meta_key }}">
    @endif
    @if ($product->seo_canonical)
        <link rel="canonical" href="{{ $product->seo_canonical }}">
    @endif
    @if ($product->seo_meta_image)
        <meta name="image" content="{{ asset('images/products/' . $product->seo_meta_image) }}">
    @endif

    @if ($product->og_meta_title)
        <meta property="og:title" content="{{ $product->og_meta_title }}">
    @endif
    @if ($product->og_meta_description)
        <meta property="og:description" content="{{ $product->og_meta_description }}">
    @endif
    @if ($product->og_meta_image)
        <meta property="og:image" content="{{ asset('images/products/' . $product->og_meta_image) }}">
    @endif
    @if ($product->og_meta_key)
        <meta property="og:keywords" content="{{ $product->og_meta_key }}">
    @endif
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:type" content="product">
@endsection

@section('content')
    <div class="container my-5">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">{{ __('products.home') }}</a></li>
                <li class="breadcrumb-item"><a
                        href="{{ route('frontend.products.index') }}">{{ __('products.products') }}</a></li>
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        {{-- Product Details --}}
        <div class="row g-4 mb-5">
            @php
                $allImages = $product->images;
                $sizeIds = $product->size_id ? explode(',', $product->size_id) : [];
                $colorIds = $product->color_id ? explode(',', $product->color_id) : [];

                // Filter images by color or show all if no color selected/general images exist
                // Initially, we show either general images (color_id null) or images of the first selected color
                $initialColorId = $colorIds[0] ?? null;
                $displayImages = $allImages->filter(
                    fn($img) => $img->color_id == $initialColorId || $img->color_id === null,
                );
                if ($displayImages->isEmpty() && !$allImages->isEmpty()) {
                    $displayImages = collect([$allImages->first()]);
                }
            @endphp

            {{-- Image Gallery --}}
            <div class="col-lg-6">
                <div class="sticky-top" style="top: 20px;">
                    {{-- Main Image --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="main-image-container">
                            <div id="mainImageBadge" class="main-image-badge"></div>
                            <img id="mainImage"
                                src="{{ asset('images/products/' . ($displayImages->first()->image ?? 'no-image.png')) }}"
                                class="card-img-top main-product-image" alt="{{ $product->name }}">
                            @if ($product->created_at && $product->created_at->diffInDays(now()) < 7)
                                <span
                                    class="badge bg-success position-absolute top-0 start-0 m-3">{{ __('products.new_badge') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Thumbnail Gallery --}}
                    <div class="row g-2 mb-3" id="thumbnailGallery">
                        @foreach ($displayImages as $index => $img)
                            <div class="col-3">
                                <img src="{{ asset('images/products/' . $img->image) }}"
                                    class="img-thumbnail thumbnail-image {{ $index === 0 ? 'active' : '' }}"
                                    onclick="changeMainImage('{{ asset('images/products/' . $img->image) }}', this)"
                                    style="cursor: pointer; height: 100px; object-fit: contain;"
                                    alt="{{ $product->name }}">
                            </div>
                        @endforeach
                    </div>

                    {{-- Stock Status Display (Moved below image/thumbnails) --}}
                    <div id="stockStatusDisplay" class="mt-2">
                        @php $initialStock = (int)($displayImages->first()->stock ?? $product->stock); @endphp
                        @if ($initialStock <= 0)
                            <div class="text-danger fw-bold">
                                <i class="fas fa-times-circle me-1"></i> Out Of Stock
                            </div>
                        @elseif($initialStock <= 5)
                            <div class="stock-counter-wrapper">
                                <div class="stock-count-text">
                                    <i class="fas fa-fire me-2 text-danger"></i>
                                    Hurry! Only {{ $initialStock }} left in stock
                                </div>
                                <div class="stock-progress">
                                    <div class="stock-progress-bar" style="width: {{ ($initialStock / 5) * 100 }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
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
                    <h1 class="display-5 fw-extrabold mb-3 product-title-animated">{{ $product->name }}</h1>

                    {{-- Price --}}
                    <div class="mb-4 d-flex align-items-center gap-3">
                        <h2 class="price-gradient fw-bold mb-0" id="productPriceDisplay">
                            ₹{{ number_format($product->price, 2) }}</h2>
                        <span
                            class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill shadow-sm">
                            <i class="fas fa-tag me-1"></i> Special Price
                        </span>
                    </div>
                    <div class="mb-4">
                        <small class="text-muted"><i class="fas fa-info-circle me-1"></i>
                            {{ __('products.inclusive_taxes') }}</small>
                    </div>

                    {{-- Description --}}
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-2">{{ __('products.product_description') }}</h5>
                        <p class="text-muted">{{ $product->detail }}</p>
                    </div>

                    <hr class="my-4">

                    {{-- Color Selection --}}
                    @if (!empty($colorIds))
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-flex align-items-center">
                                <i class="fas fa-palette me-2"></i> {{ __('products.select_color') }}
                                <span class="ms-2 text-muted small" id="selectedColorName"></span>
                            </label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ($colorIds as $index => $cid)
                                    @php
                                        $color = $colors->firstWhere('id', (int) $cid);
                                        // Find first image for this color to show as thumbnail
                                        $colorImage = $allImages->firstWhere('color_id', (int) $cid);
                                        // Calculate total stock for this color across all images
                                        $colorStock = $allImages->where('color_id', (int) $cid)->sum('stock');
                                        // Fallback to product main if no specific color image
                                        $imageUrl = $colorImage
                                            ? asset('images/products/' . $colorImage->image)
                                            : asset('images/products/no-image.png');
                                    @endphp
                                    @if ($color)
                                        <div class="color-option {{ $index === 0 ? 'selected' : '' }} {{ $colorStock <= 0 ? 'out-of-stock' : '' }}"
                                            data-color-id="{{ $color->id }}" data-color-name="{{ $color->name }}"
                                            data-is-out-of-stock="{{ $colorStock <= 0 ? '1' : '0' }}"
                                            onclick="handleColorSelection(this)">
                                            @if ($colorImage)
                                                <div class="color-image-thumb">
                                                    <img src="{{ $imageUrl }}" alt="{{ $color->name }}">
                                                </div>
                                            @else
                                                <div class="color-swatch" style="background-color: {{ $color->hex_code }}">
                                                </div>
                                            @endif
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
                                <i class="fas fa-ruler me-2"></i> {{ __('products.select_size') }}
                                <span class="ms-2 text-muted small" id="selectedSizeName"></span>
                            </label>
                            <div class="d-flex gap-2 flex-wrap">
                                @foreach ($sizeIds as $index => $sid)
                                    @php $size = $sizes->firstWhere('id', (int) $sid); @endphp
                                    @if ($size)
                                        <div class="size-option {{ $index === 0 ? 'selected' : '' }}"
                                            data-size-id="{{ $size->id }}"
                                            data-size-name="{{ $size->code ?? $size->name }}"
                                            onclick="handleSizeSelection(this)">
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
                            <i class="fas fa-sort-numeric-up me-2"></i> {{ __('products.quantity') }}
                        </label>
                        <div class="input-group" style="width: 150px;">
                            <button class="btn btn-light border-0 shadow-sm quantity-btn" type="button"
                                onclick="decrementQuantity()" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                <i class="fas fa-minus text-muted"></i>
                            </button>
                            <input type="number"
                                class="form-control text-center quantity-input border-0 bg-light fw-bold" id="quantity"
                                value="{{ $product->stock <= 0 ? '0' : '1' }}"
                                min="{{ $product->stock <= 0 ? '0' : '1' }}" max="10"
                                {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            <button class="btn btn-light border-0 shadow-sm quantity-btn" type="button"
                                onclick="incrementQuantity()" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                <i class="fas fa-plus text-muted"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Stock Status Display removed from here --}}

                    {{-- Action Buttons --}}
                    <div class="d-flex gap-3 mb-4 flex-wrap" id="main-product-actions">
                        @auth('customer')
                            @if ($product->stock > 0)
                                @if (in_array($product->id, $cartProductIds ?? []))
                                    <a href="{{ route('frontend.cart') }}" class="btn action-btn btn-go-cart flex-fill">
                                        <div class="btn-content">
                                            <i class="fas fa-arrow-right me-2"></i> {{ __('products.go_to_cart') }}
                                        </div>
                                    </a>
                                @else
                                    <button class="btn action-btn btn-add-cart flex-fill" id="addToCartBtn"
                                        onclick="addToCart()">
                                        <div class="btn-content">
                                            <i class="fas fa-shopping-cart me-2"></i> {{ __('products.add_to_cart') }}
                                        </div>
                                    </button>
                                @endif
                                <button id="buyNowBtn" class="btn action-btn btn-buy-now flex-fill" onclick="buyNow()">
                                    <div class="btn-content">
                                        <i class="fas fa-bolt text-warning me-2"></i>
                                        <span
                                            id="buyNowText">{{ __('products.buy_now', ['price' => number_format($product->price)]) }}</span>
                                    </div>
                                    <div class="btn-glow"></div>
                                </button>
                            @else
                                <button class="btn btn-secondary action-btn flex-fill disabled" style="opacity: 0.6;">
                                    <div class="btn-content">
                                        <i class="fas fa-ban me-2"></i> Currently Unavailable
                                    </div>
                                </button>
                            @endif
                            <button class="btn action-btn btn-wishlist {{ $inWishlist ? 'active' : '' }}" title="Wishlist"
                                onclick="toggleWishlist()">
                                <i class="{{ $inWishlist ? 'fas text-danger' : 'far text-muted' }} fa-heart fa-lg"></i>
                            </button>
                        @else
                            <a href="{{ route('customer.login') }}" class="btn action-btn btn-add-cart flex-fill">
                                <div class="btn-content">
                                    <i class="fas fa-sign-in-alt me-2"></i> {{ __('products.login_to_buy') }}
                                </div>
                            </a>
                        @endauth
                    </div>

                    {{-- Product Features --}}
                    <div class="card premium-feature-card border-0 shadow-sm mt-4">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="feature-item p-2 rounded d-flex align-items-center">
                                        <div class="feature-icon-wrapper me-3">
                                            <i class="fas fa-truck text-primary"></i>
                                        </div>
                                        <span class="fw-medium text-dark small">{{ __('products.free_shipping') }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="feature-item p-2 rounded d-flex align-items-center">
                                        <div class="feature-icon-wrapper me-3">
                                            <i class="fas fa-undo text-primary"></i>
                                        </div>
                                        <span class="fw-medium text-dark small">{{ __('products.easy_returns') }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="feature-item p-2 rounded d-flex align-items-center">
                                        <div class="feature-icon-wrapper me-3">
                                            <i class="fas fa-shield-alt text-primary"></i>
                                        </div>
                                        <span class="fw-medium text-dark small">{{ __('products.secure_payment') }}</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="feature-item p-2 rounded d-flex align-items-center">
                                        <div class="feature-icon-wrapper me-3">
                                            <i class="fas fa-headset text-primary"></i>
                                        </div>
                                        <span class="fw-medium text-dark small">{{ __('products.support') }}</span>
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
                    <h3 class="fw-bold mb-4">{{ __('products.related_products') }}</h3>
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
                                    {{ __('products.view_details') }}
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
        .fw-extrabold {
            font-weight: 800;
        }

        .product-title-animated {
            background: linear-gradient(to right, #1e293b, #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .price-gradient {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2.2rem;
            letter-spacing: -0.5px;
        }

        .main-image-container {
            position: relative;
            background: radial-gradient(circle at center, #ffffff 0%, #f1f5f9 100%);
            border-radius: 20px;
            overflow: hidden;
            height: auto;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .main-product-image {
            width: auto;
            height: auto;
            max-height: 520px;
            object-fit: contain;
            padding: 20px;
            background: transparent;
            filter: drop-shadow(0 20px 30px rgba(0, 0, 0, 0.1));
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .main-image-container:hover .main-product-image {
            transform: scale(1.05) translateY(-5px);
        }

        .thumbnail-image {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border: 2px solid transparent;
            width: 100%;
            height: 90px;
            object-fit: contain;
            background: #ffffff;
            border-radius: 12px;
            padding: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            cursor: pointer;
        }

        .thumbnail-image.active,
        .thumbnail-image:hover {
            border-color: #6366f1;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(99, 102, 241, 0.15);
        }

        .color-option {
            text-align: center;
            cursor: pointer;
            padding: 6px;
            border: 2px solid transparent;
            background: #ffffff;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            min-width: 60px;
        }

        .color-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.08);
            border-color: #e2e8f0;
        }

        .color-option.selected {
            border-color: #6366f1;
            background-color: #eef2ff;
            box-shadow: 0 0 0 1px #6366f1, 0 4px 10px rgba(99, 102, 241, 0.15);
        }

        .color-swatch {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            margin: 0 auto 6px;
            transition: transform 0.2s ease;
        }

        .color-option:hover .color-swatch {
            transform: scale(1.1);
        }

        .color-image-thumb {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            margin: 0 auto 6px;
            background: #fff;
        }

        .color-image-thumb img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .color-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 600;
            color: #475569;
        }

        .color-option.out-of-stock {
            position: relative;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .size-option {
            min-width: 55px;
            padding: 10px 16px;
            text-align: center;
            border: 2px solid transparent;
            background: #ffffff;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            color: #475569;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .size-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.08);
            color: #1e293b;
        }

        .size-option.selected {
            border-color: #6366f1;
            background-color: #6366f1;
            color: white;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
        }

        /* Quantity Input */
        .quantity-input {
            width: 60px !important;
            font-size: 1.1rem;
        }

        .quantity-btn {
            width: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .quantity-btn:hover:not(:disabled) {
            background: #e2e8f0 !important;
            color: #1e293b;
        }

        /* Modern Custom Action Buttons */
        .action-btn {
            position: relative;
            overflow: hidden;
            border: none;
            border-radius: 14px;
            padding: 14px 24px;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: 0.3px;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
        }

        .btn-add-cart {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #4338ca;
            box-shadow: 0 4px 10px rgba(199, 210, 254, 0.5);
        }

        .btn-add-cart:hover {
            background: linear-gradient(135deg, #c7d2fe 0%, #a5b4fc 100%);
            color: #3730a3;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(165, 180, 252, 0.6);
        }

        .btn-go-cart {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #b45309;
            box-shadow: 0 4px 10px rgba(253, 230, 138, 0.5);
        }

        .btn-buy-now {
            background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
            color: white;
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-buy-now:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.5);
            color: white;
        }

        .btn-glow {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 60%);
            opacity: 0;
            transform: scale(0.5);
            transition: opacity 0.4s, transform 0.6s;
            z-index: 0;
            pointer-events: none;
        }

        .btn-buy-now:hover .btn-glow {
            opacity: 1;
            transform: scale(1);
            animation: rotateGlow 4s linear infinite;
        }

        @keyframes rotateGlow {
            0% {
                transform: scale(1) rotate(0deg);
            }

            100% {
                transform: scale(1) rotate(360deg);
            }
        }

        .btn-wishlist {
            background: #ffffff;
            border: 2px solid #f1f5f9;
            color: #94a3b8;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
            width: 60px;
            padding: 0;
            border-radius: 14px;
        }

        .btn-wishlist:hover {
            border-color: #ffe4e6;
            background: #fff1f2;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(225, 29, 72, 0.1);
        }

        .btn-wishlist.active {
            background: #fff1f2;
            border-color: #fecdd3;
            animation: heartbeat 1.5s ease-in-out infinite both;
        }

        @keyframes heartbeat {
            0% {
                transform: scale(1);
            }

            14% {
                transform: scale(1.1);
            }

            28% {
                transform: scale(1);
            }

            42% {
                transform: scale(1.1);
            }

            70% {
                transform: scale(1);
            }
        }

        /* Premium Stock Counter Box */
        .stock-counter-wrapper {
            margin-top: 12px;
            padding: 10px 16px;
            background: linear-gradient(135deg, #fff5f5 0%, #ffeded 100%);
            border-radius: 12px;
            border: 1px solid #fed7d7;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(245, 101, 101, 0.08);
        }

        .stock-count-text {
            color: #c53030;
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
        }

        .stock-progress {
            height: 6px;
            background: #fed7d7;
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
            width: 100%;
        }

        .stock-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #fc8181 0%, #e53e3e 100%);
            transition: width 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* Features Section */
        .premium-feature-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
            border: 1px solid rgba(0, 0, 0, 0.02) !important;
        }

        .feature-item {
            background: #f8fafc;
            transition: background 0.3s;
        }

        .feature-item:hover {
            background: #f1f5f9;
        }

        .feature-icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #eef2ff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #f1f5f9 !important;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .related-product-image-wrapper {
            height: 220px;
            background: radial-gradient(circle at center, #ffffff 0%, #f8fafc 100%);
            overflow: hidden;
            padding: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .related-product-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.5s ease;
        }

        .product-card:hover .related-product-image-wrapper img {
            transform: scale(1.08);
        }

        /* Wishlist Button Top Right */
        .wishlist-btn-top {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(4px);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .wishlist-btn-top:hover {
            transform: scale(1.15);
            background: white;
            box-shadow: 0 6px 15px rgba(220, 53, 69, 0.15);
        }

        .wishlist-btn-top i {
            color: #cbd5e1;
            font-size: 16px;
            transition: color 0.3s;
        }

        .wishlist-btn-top:hover i {
            color: #fca5a5;
        }

        .wishlist-btn-top.active i {
            color: #e11d48;
        }

        /* Main Image Floating Badge */
        .main-image-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(255, 255, 255, 0.95);
            padding: 8px 16px;
            border-radius: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            font-weight: 800;
            font-size: 0.85rem;
            z-index: 10;
            display: none;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 1);
        }

        .main-image-badge.out-of-stock {
            color: #be123c;
            background: rgba(255, 228, 230, 0.95);
            border-color: #fecdd3;
        }

        .main-image-badge.low-stock {
            color: #b45309;
            background: rgba(254, 243, 199, 0.95);
            border-color: #fde68a;
        }

        @media (max-width: 768px) {
            .main-image-container {
                height: 400px;
            }

            .action-btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .btn-wishlist {
                width: 100%;
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

        // Product images JSON for JS filtering (ensure sequential array)
        const productImages = {!! $allImages->values()->toJson() !!};
        const assetUrl = "{{ asset('images/products') }}"; // Internal path without trailing slash
        const basePrice = "{{ $product->price }}";
        const baseStock = "{{ $product->stock }}";

        // Select color handler
        function handleColorSelection(element) {
            // Ensure we have the div element even if child was clicked
            const el = element.closest('.color-option');
            if (!el) return;

            console.log('Color selection triggered:', el.dataset.colorName, 'ID:', el.dataset.colorId);

            // UI Update: Selected state
            document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('selected'));
            el.classList.add('selected');

            const colorId = el.dataset.colorId;
            selectedColor = colorId;

            // Name display
            const nameEl = document.getElementById('selectedColorName');
            if (nameEl) nameEl.textContent = `(${el.dataset.colorName})`;

            // Filtering logic
            let filtered = productImages.filter(img => String(img.color_id || '') === String(colorId || ''));

            // Fallback to size-specific or general images if specific color images not found
            if (filtered.length === 0) {
                if (selectedSize) {
                    filtered = productImages.filter(img => String(img.size_id || '') === String(selectedSize || ''));
                }
                if (filtered.length === 0) {
                    filtered = productImages.filter(img => !img.color_id && !img.size_id);
                }
            }

            // DOM Updates
            updateGalleryUI(filtered);
        }

        // Select size handler
        function handleSizeSelection(element) {
            const el = element.closest('.size-option');
            if (!el) return;

            console.log('Size selection triggered:', el.dataset.sizeName, 'ID:', el.dataset.sizeId);

            // UI Update
            document.querySelectorAll('.size-option').forEach(option => option.classList.remove('selected'));
            el.classList.add('selected');

            const sizeId = el.dataset.sizeId;
            selectedSize = sizeId;

            const nameEl = document.getElementById('selectedSizeName');
            if (nameEl) nameEl.textContent = `(${el.dataset.sizeName})`;

            // Filtering logic
            let filtered = productImages.filter(img => String(img.size_id || '') === String(sizeId || ''));

            // Fallback to color-specific or general images if specific size images not found
            if (filtered.length === 0) {
                if (selectedColor) {
                    filtered = productImages.filter(img => String(img.color_id || '') === String(selectedColor || ''));
                }
                if (filtered.length === 0) {
                    filtered = productImages.filter(img => !img.size_id && !img.color_id);
                }
            }

            // DOM Updates
            updateGalleryUI(filtered);
        }

        // Shared function to update Gallery UI
        function updateGalleryUI(filtered) {
            if (filtered.length > 0) {
                const mainImg = document.getElementById('mainImage');
                if (mainImg) {
                    mainImg.src = `${assetUrl}/${filtered[0].image}`;
                }

                const gallery = document.getElementById('thumbnailGallery');
                if (gallery) {
                    gallery.innerHTML = '';
                    filtered.forEach((img, idx) => {
                        const imgUrl = `${assetUrl}/${img.image}`;
                        const col = document.createElement('div');
                        col.className = 'col-3';
                        col.innerHTML = `
                            <img src="${imgUrl}"
                                class="img-thumbnail thumbnail-image ${idx === 0 ? 'active' : ''}"
                                onclick="changeMainImage('${imgUrl}', this)"
                                style="cursor: pointer; height: 100px; object-fit: contain;"
                                alt="{{ $product->name }}">
                        `;
                        gallery.appendChild(col);
                    });
                }
            }
            updateVariantInfo();
        }

        // Update Price and Stock based on current selection
        function updateVariantInfo() {
            // Find the best matching image record
            let match = productImages.find(img => {
                const colorMatch = String(img.color_id || '') === String(selectedColor || '');
                const sizeMatch = String(img.size_id || '') === String(selectedSize || '');
                return colorMatch && sizeMatch;
            });

            // Fallback 1: Match only color (Color images usually carry the price/stock)
            if (!match && selectedColor) {
                match = productImages.find(img => String(img.color_id || '') === String(selectedColor || ''));
            }

            // Fallback 2: Match only size
            if (!match && selectedSize) {
                match = productImages.find(img => String(img.size_id || '') === String(selectedSize || ''));
            }

            const priceDisplay = document.getElementById('productPriceDisplay');
            const stockDisplay = document.getElementById('stockStatusDisplay');
            const mainBadge = document.getElementById('mainImageBadge');
            const buyNowBtn = document.getElementById('buyNowBtn');
            const buyNowText = document.getElementById('buyNowText');
            const addToCartBtn = document.getElementById('addToCartBtn');

            if (match) {
                const price = match.price ? match.price : basePrice;
                const formattedPrice =
                    `₹${parseFloat(price).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                // Update Price Display
                if (priceDisplay) priceDisplay.textContent = formattedPrice;
                // Update Buy Now Button
                if (buyNowText) buyNowText.textContent = `Buy Now at ${formattedPrice}`;

                // Update Stock and Badge
                const stock = parseInt(match.stock || 0);
                if (mainBadge) {
                    if (stock <= 0) {
                        mainBadge.innerHTML = '<i class="fas fa-times-circle me-1"></i> Out of Stock';
                        mainBadge.className = 'main-image-badge out-of-stock';
                        mainBadge.style.display = 'block';
                    } else if (stock <= 5) {
                        mainBadge.innerHTML = `<i class="fas fa-fire me-1"></i> Only ${stock} Left`;
                        mainBadge.className = 'main-image-badge low-stock';
                        mainBadge.style.display = 'block';
                    } else {
                        mainBadge.style.display = 'none';
                    }
                }

                if (stockDisplay) {
                    if (stock <= 0) {
                        stockDisplay.innerHTML = `
                            <div class="text-danger fw-bold">
                                <i class="fas fa-times-circle me-1"></i> Out Of Stock
                            </div>`;
                        if (addToCartBtn) addToCartBtn.disabled = true;
                        if (buyNowBtn) buyNowBtn.disabled = true;
                    } else if (stock <= 5) {
                        const progressWidth = (stock / 5) * 100;
                        stockDisplay.innerHTML = `
                            <div class="stock-counter-wrapper">
                                <div class="stock-count-text">
                                    <i class="fas fa-fire me-2 text-danger"></i>
                                    Hurry! Only ${stock} left in stock
                                </div>
                                <div class="stock-progress">
                                    <div class="stock-progress-bar" style="width: ${progressWidth}%"></div>
                                </div>
                            </div>`;
                        if (addToCartBtn) addToCartBtn.disabled = false;
                        if (buyNowBtn) buyNowBtn.disabled = false;
                    } else {
                        stockDisplay.innerHTML = '';
                        if (addToCartBtn) addToCartBtn.disabled = false;
                        if (buyNowBtn) buyNowBtn.disabled = false;
                    }
                }
            } else {
                if (mainBadge) mainBadge.style.display = 'none';
                const formattedBasePrice =
                    `₹${parseFloat(basePrice).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                if (priceDisplay) priceDisplay.textContent = formattedBasePrice;
                if (buyNowText) buyNowText.textContent = `Buy Now at ${formattedBasePrice}`;
                if (addToCartBtn) addToCartBtn.disabled = false;
                if (buyNowBtn) buyNowBtn.disabled = false;
            }
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
        function addToCart(mode = 'increment', callback = null) {
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
                    text: '{{ __('products.please_select_color') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            if (hasSizeOptions && !selectedSize) {
                Swal.fire({
                    icon: 'warning',
                    text: '{{ __('products.please_select_size') }}',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            // Server-side add to cart
            fetch("{{ route('cart.add') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        product_id: {{ $product->id }},
                        quantity: quantity,
                        price: {{ $product->price }},
                        color_id: selectedColor,
                        size_id: selectedSize,
                        mode: mode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (callback) {
                            callback(data);
                        } else {
                            showNotification('{{ __('products.added_to_cart') }}', 'success');
                            // Dynamic button switch
                            const addToCartBtn = document.getElementById('addToCartBtn');
                            if (addToCartBtn) {
                                const goCartHtml = `
                                <a href="{{ route('frontend.cart') }}" class="btn btn-warning btn-lg flex-fill">
                                    <i class="fas fa-arrow-right me-2"></i> {{ __('products.go_to_cart') }}
                                </a>
                            `;
                                addToCartBtn.outerHTML = goCartHtml;
                            }
                        }
                    } else {
                        showNotification(data.message || '{{ __('products.something_went_wrong') }}', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error connecting to server', 'error');
                });
        }

        // Buy Now function
        function buyNow() {
            addToCart('replace', function(data) {
                if (data && data.cart_item_id) {
                    // Redirect to cart with specific item ID
                    window.location.href = "{{ route('frontend.cart') }}?buy_item_id=" + data.cart_item_id;
                } else {
                    // Fallback to general checkout if ID is missing
                    window.location.href = "{{ route('frontend.cart') }}?checkout=1";
                }
            });
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
                // Trigger initial selection logic
                handleColorSelection(firstColor);
            }

            const firstSize = document.querySelector('.size-option.selected');
            if (firstSize) {
                selectedSize = firstSize.dataset.sizeId;
                const sizeNameEl = document.getElementById('selectedSizeName');
                if (sizeNameEl) {
                    sizeNameEl.textContent = `(${firstSize.dataset.sizeName})`;
                }
                firstSize.classList.add('selected');
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
