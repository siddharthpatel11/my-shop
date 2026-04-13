@extends('layouts.frontend.app')

@section('title', 'Shopping Cart')

@section('content')
    <div class="container my-5">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('frontend.products.index') }}">Products</a></li>
                <li class="breadcrumb-item active">Shopping Cart</li>
            </ol>
        </nav>

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">
                    <i class="fas fa-shopping-cart text-primary me-2"></i> Shopping Cart
                </h2>
            </div>
        </div>

        <div class="row g-4">
            {{-- Cart Items --}}
            <div class="col-lg-8">
                @if ($cartItems->count() > 0)
                    @foreach ($cartItems as $item)
                        @php
                            $images = $item->product->image ? explode(',', $item->product->image) : [];
                        @endphp
                        <div class="card border-0 premium-shadow mb-4 cart-item-card" data-cart-id="{{ $item->id }}">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-2 col-3 mb-3 mb-md-0 text-center">
                                        <a href="{{ route('frontend.products.show', $item->product->id) }}"
                                            class="cart-image-wrapper">
                                            <img src="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                                                class="cart-item-image" alt="{{ $item->product->name }}">
                                        </a>
                                    </div>

                                    <div class="col-md-4 col-9 mb-3 mb-md-0">
                                        <a href="{{ route('frontend.products.show', $item->product->id) }}"
                                            class="text-decoration-none">
                                            <h6 class="fw-bold mb-1 text-dark">{{ $item->product->name }}</h6>
                                        </a>
                                        <div class="mb-2">
                                            <a href="{{ route('frontend.products.show', $item->product->id) }}"
                                                class="text-primary small text-decoration-none">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </a>
                                        </div>
                                        <p class="text-muted small mb-2">
                                            {{ $item->product->category->name ?? '' }}
                                        </p>

                                        <div class="d-flex align-items-center gap-3 flex-wrap">
                                            @if ($item->color)
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="text-muted">Color:</small>
                                                    <span class="color-indicator"
                                                        style="background-color: {{ $item->color->hex_code }}"></span>
                                                    <small>{{ $item->color->name }}</small>
                                                </div>
                                            @endif

                                            @if ($item->size)
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="text-muted">Size:</small>
                                                    <span
                                                        class="size-badge">{{ $item->size->code ?? $item->size->name }}</span>
                                                </div>
                                            @endif

                                            @if (!empty($item->variant))
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <small class="text-muted">Variant:</small>
                                                    <span class="size-badge">{{ $item->variant }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                                        <div class="text-md-center">
                                            <small class="text-muted d-block">Price</small>
                                            <span class="fw-bold text-primary">₹{{ number_format($item->price, 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-6 mb-3 mb-md-0">
                                        <div class="text-md-center">
                                            <small class="text-muted d-block mb-2">Quantity</small>
                                            <div
                                                class="quantity-control {{ $item->product->stock <= 0 ? 'opacity-50' : '' }}">
                                                <button class="btn-qty"
                                                    onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                    {{ $item->quantity <= 1 || $item->product->stock <= 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="input-qty"
                                                    value="{{ $item->product->stock <= 0 ? 0 : $item->quantity }}"
                                                    readonly>
                                                <button class="btn-qty"
                                                    onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                    {{ $item->quantity >= $item->product->stock || $item->product->stock <= 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                            @if ($item->product->stock > 0 && $item->product->stock <= 5)
                                                <div class="text-danger small mt-1 fw-bold">
                                                    Only {{ $item->product->stock }} left
                                                </div>
                                            @elseif($item->product->stock <= 0)
                                                <div class="text-danger small mt-1 fw-bold">
                                                    Out of Stock
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-12 text-md-center">
                                        <div class="text-md-center">
                                            <small class="text-muted d-block">Subtotal</small>
                                            <div class="fw-bold text-primary mb-2">
                                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                                            </div>
                                            <div class="d-grid gap-2 mt-2">
                                                <button class="btn btn-sm btn-outline-danger text-nowrap btn-remove-item"
                                                    onclick="removeItem({{ $item->id }})">
                                                    <i class="fas fa-trash-alt me-1"></i> Remove
                                                </button>
                                                <button class="btn btn-sm btn-primary text-nowrap btn-buy-this"
                                                    onclick="checkoutSingleItem({{ $item->id }})"
                                                    {{ $item->product->stock <= 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-bolt me-1 text-warning"></i> Buy This
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Empty Cart Message --}}
                    <div class="card border-0 premium-shadow">
                        <div class="card-body text-center py-5 empty-cart-container">
                            <div class="empty-cart-icon mb-4 mx-auto">
                                <i class="fas fa-shopping-cart fa-3x text-primary"></i>
                            </div>
                            <h4 class="fw-bold mb-3 text-dark">Your cart is empty</h4>
                            <p class="text-muted mb-4">Looks like you haven't added any products to your cart yet.</p>
                            <a href="{{ route('frontend.products.index') }}" class="btn btn-premium-checkout px-4 py-2">
                                <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Cart Summary --}}
            <div class="col-lg-4">
                <div class="card border-0 premium-shadow sticky-top summary-card" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Order Summary</h5>

                        @php
                            $totalItems = $cartItems->sum('quantity');
                        @endphp

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">
                                Subtotal ({{ $totalItems }} items)
                            </span>
                            <span class="fw-semibold">
                                ₹{{ number_format($subtotal, 2) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Delivery Charges</span>
                            <span class="fw-semibold text-success">FREE</span>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">
                                Tax (GST {{ $taxPercent }}%)
                            </span>
                            <span class="fw-semibold">
                                ₹{{ number_format($taxAmount, 2) }}
                            </span>
                        </div>

                        {{-- Show Subtotal + Tax before discount --}}
                        @if ($appliedDiscount)
                            <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                <span class="text-muted">Subtotal + Tax</span>
                                <span class="fw-semibold">₹{{ number_format($subtotal + $taxAmount, 2) }}</span>
                            </div>
                        @endif

                        {{--  Discount Section  --}}
                        @if ($appliedDiscount)
                            <div class="alert alert-success py-2 px-3 mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="d-block fw-bold">
                                            <i class="fas fa-tag me-1"></i> {{ $appliedDiscount->code }}
                                        </small>
                                        <small class="text-muted">
                                            {{ $appliedDiscount->type === 'percentage' ? $appliedDiscount->value . '%' : '₹' . number_format($appliedDiscount->value, 2) }}
                                            discount
                                        </small>
                                    </div>
                                    <button class="btn btn-sm btn-link text-danger p-0" onclick="removeDiscount()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-3 text-success">
                                <span>Discount Applied</span>
                                <span class="fw-semibold">- ₹{{ number_format($discountAmount, 2) }}</span>
                            </div>
                        @else
                            <button class="btn btn-outline-primary w-100 mb-3" data-bs-toggle="modal"
                                data-bs-target="#discountModal" {{ $cartItems->count() == 0 ? 'disabled' : '' }}>
                                <i class="fas fa-tag me-2"></i> Apply Discount Code
                            </button>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Total</span>
                            <span class="fw-bold fs-5 text-primary">
                                ₹{{ number_format($total, 2) }}
                            </span>
                        </div>

                        <button class="btn btn-premium-checkout w-100 mb-3" id="checkoutBtn" onclick="checkoutAllItems()"
                            {{ $cartItems->count() == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-lock me-2"></i> Proceed to Checkout
                        </button>

                        <a href="{{ route('frontend.products.index') }}"
                            class="btn btn-outline-secondary w-100 btn-continue-shopping">
                            <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Discount Modal --}}
    <div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="discountModalLabel">
                        <i class="fas fa-tag me-2"></i> Apply Discount
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Manual Code Entry --}}
                    <div class="mb-4">
                        <label for="discountCodeInput" class="form-label fw-bold">Enter Discount Code</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="discountCodeInput"
                                placeholder="Enter discount code" style="text-transform: uppercase;">
                            <button class="btn btn-primary" type="button" onclick="applyDiscountCode()">
                                Apply
                            </button>
                        </div>
                        <div id="discountError" class="text-danger small mt-2" style="display: none;"></div>
                    </div>

                    <hr>

                    {{-- Available Discounts --}}
                    <div>
                        <h6 class="fw-bold mb-3">Available Discounts</h6>
                        <div id="availableDiscounts">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted small mt-2">Loading discounts...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Address Modal --}}
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addressModalLabel">
                        <i class="fas fa-map-marker-alt me-2"></i> Delivery Address
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <button type="button" id="backToAddressesBtn" class="btn btn-link mb-3" style="display:none;"
                        onclick="showSavedAddresses()">
                        ← Back to Saved Addresses
                    </button>
                    {{-- Saved Addresses --}}
                    <div id="savedAddresses" class="mb-4" style="display: none;">
                        <h6 class="fw-bold mb-3">Saved Addresses</h6>
                        <div id="addressList"></div>
                        <button class="btn btn-outline-primary btn-sm mt-2" onclick="showNewAddressForm()">
                            <i class="fas fa-plus me-1"></i> Add New Address
                        </button>
                    </div>

                    {{-- Address Form --}}
                    <form id="addressForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="country" class="form-label">Country <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="country" name="country" required>
                            </div>
                            <div class="col-md-6">
                                <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="state" name="state" required>
                            </div>
                            <div class="col-md-6">
                                <label for="district" class="form-label">District <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="district" name="district" required>
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            {{--  <div class="col-md-6">
                                <label for="area" class="form-label">Area <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="area" name="area" required>
                            </div>  --}}
                            <div class="col-md-6">
                                <label for="pincode" class="form-label">Pincode</label>
                                <input type="text" class="form-control" id="pincode" name="pincode">
                            </div>
                            <div class="col-12">
                                <label for="full_address" class="form-label">Full Address (House No., Street,
                                    Landmark)</label>
                                <textarea class="form-control" id="full_address" name="full_address" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveAddressAndCheckout()">
                        <i class="fas fa-check me-1"></i> Save & Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Order Confirmation Modal --}}
    {{--  <div class="modal fade" id="orderConfirmationModal" tabindex="-1" aria-labelledby="orderConfirmationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Order Placed Successfully!</h4>
                    <p class="text-muted mb-4">
                        Your order has been placed successfully.<br>
                        Order Number: <strong id="orderNumber"></strong>
                    </p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('frontend.products.index') }}" class="btn btn-outline-primary">
                            Continue Shopping
                        </a>
                        <button class="btn btn-primary" onclick="viewOrderDetails()">
                            View Order Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>  --}}

    {{-- Styles --}}
    <style>
        .premium-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
            border-radius: 16px;
        }

        .cart-item-card {
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.02) !important;
        }

        .cart-item-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.08) !important;
            border-color: #f1f5f9 !important;
        }

        .cart-image-wrapper {
            display: block;
            background: #f8fafc;
            border-radius: 12px;
            padding: 10px;
            overflow: hidden;
            transition: background 0.3s;
        }

        .cart-item-card:hover .cart-image-wrapper {
            background: #f1f5f9;
        }

        .cart-item-image {
            width: 100%;
            height: 100px;
            object-fit: contain;
            mix-blend-mode: multiply;
            transition: transform 0.4s;
        }

        .cart-item-card:hover .cart-item-image {
            transform: scale(1.05);
        }

        /* Quantity Control */
        .quantity-control {
            display: inline-flex;
            align-items: center;
            background: #f8fafc;
            border-radius: 30px;
            padding: 4px;
            border: 1px solid #e2e8f0;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: border-color 0.3s;
        }

        .quantity-control:hover {
            border-color: #cbd5e1;
        }

        .btn-qty {
            border: none;
            background: #ffffff;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            color: #64748b;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-qty:hover:not(:disabled) {
            background: #6366f1;
            color: #ffffff;
            transform: scale(1.05);
        }

        .btn-qty:active:not(:disabled) {
            transform: scale(0.95);
        }

        .btn-qty:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            box-shadow: none;
            background: transparent;
        }

        .input-qty {
            border: none;
            background: transparent;
            width: 40px;
            text-align: center;
            font-weight: 700;
            color: #1e293b;
        }

        .input-qty:focus {
            outline: none;
        }

        .input-qty::-webkit-outer-spin-button,
        .input-qty::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .color-indicator {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            display: inline-block;
        }

        .size-badge {
            display: inline-block;
            padding: 4px 10px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* Order Summary Card */
        .summary-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #f1f5f9;
        }

        .btn-premium-checkout {
            background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 20px;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-premium-checkout:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.5);
            color: white;
        }

        .btn-premium-checkout:disabled {
            opacity: 0.6;
            background: #94a3b8;
            box-shadow: none;
        }

        .btn-continue-shopping {
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-remove-item {
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-remove-item:hover {
            background: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }

        .btn-buy-this {
            border-radius: 8px;
            background: #eef2ff;
            color: #4f46e5;
            border-color: #eef2ff;
            font-weight: 600;
        }

        .btn-buy-this:hover {
            background: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        /* Empty Cart */
        .empty-cart-container {
            background: radial-gradient(circle at center, #ffffff 0%, #f8fafc 100%);
            border-radius: 16px;
        }

        .empty-cart-icon {
            width: 100px;
            height: 100px;
            background: #eef2ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .address-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fff;
        }

        .address-card:hover {
            border-color: #818cf8;
            background: #f8fafc;
        }

        .address-card.selected {
            border-color: #6366f1;
            background: #eef2ff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
        }

        .discount-card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #fff;
        }

        .discount-card:hover {
            border-color: #818cf8;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
        }

        .discount-card.locked {
            opacity: 0.7;
            cursor: not-allowed;
            background: #f8fafc;
        }

        .discount-card.locked:hover {
            border-color: #e2e8f0;
            transform: none;
            box-shadow: none;
        }

        .discount-badge {
            background: #eef2ff;
            color: #4f46e5;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 6px;
            font-family: inherit;
        }

        @media (max-width: 768px) {
            .cart-item-image {
                height: 80px;
            }
        }
    </style>

    {{-- Scripts --}}
    <script>
        let selectedAddressId = null;
        let singleItemCheckoutId = null;

        // Load saved addresses when modal opens
        document.getElementById('addressModal').addEventListener('show.bs.modal', function() {
            loadSavedAddresses();
        });

        // Load valid discounts when modal opens
        document.getElementById('discountModal').addEventListener('show.bs.modal', function() {
            loadValidDiscounts();
        });

        // Load available discounts
        function loadValidDiscounts() {
            fetch("{{ route('cart.discounts') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.discounts.length > 0) {
                        displayDiscounts(data.discounts, data.subtotalWithTax);
                    } else {
                        document.getElementById('availableDiscounts').innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-tag fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No active discounts available at the moment.</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('availableDiscounts').innerHTML = `
                        <div class="alert alert-danger">Error loading discounts</div>
                    `;
                });
        }

        // Display available discounts
        function displayDiscounts(discounts, subtotalWithTax) {
            let html = '';

            discounts.forEach(discount => {
                const discountValue = discount.type === 'percentage' ?
                    `${discount.value}% OFF` :
                    `₹${parseFloat(discount.value).toFixed(2)} OFF`;

                const validPeriod = discount.start_date || discount.end_date ?
                    `<small class="text-muted d-block mt-1">
                        Valid: ${formatDate(discount.start_date)} - ${formatDate(discount.end_date)}
                    </small>` : '';

                const minAmount = parseFloat(discount.min_amount) || 0;
                const isLocked = subtotalWithTax < minAmount;
                const difference = minAmount - subtotalWithTax;

                let lockedMessage = '';
                if (isLocked) {
                    lockedMessage = `
                        <div class="mt-2 text-danger small">
                            <i class="fas fa-lock me-1"></i>
                            Minimum purchase of ₹${minAmount.toFixed(2)} required to apply this discount.<br>
                            <strong>Shop for ₹${difference.toFixed(2)} more to unlock.</strong>
                        </div>
                    `;
                }

                html += `
                    <div class="discount-card ${isLocked ? 'locked' : ''}" ${!isLocked ? `onclick="selectDiscount('${discount.code}')"` : ''}>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="discount-badge">${discount.code}</span>
                                    <span class="badge bg-${discount.type === 'percentage' ? 'info' : 'success'}">
                                        ${discountValue}
                                    </span>
                                </div>
                                ${validPeriod}
                                ${lockedMessage}
                            </div>
                            <button class="btn btn-sm btn-primary" ${isLocked ? 'disabled' : ''} onclick="event.stopPropagation(); selectDiscount('${discount.code}')">
                                Apply
                            </button>
                        </div>
                    </div>
                `;
            });

            document.getElementById('availableDiscounts').innerHTML = html;
        }

        // Format date
        function formatDate(dateString) {
            if (!dateString) return 'Anytime';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-IN', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // Select and apply discount
        function selectDiscount(code) {
            document.getElementById('discountCodeInput').value = code;
            applyDiscountCode();
        }

        // Apply discount code
        function applyDiscountCode() {
            const code = document.getElementById('discountCodeInput').value.trim();

            if (!code) {
                showDiscountError('Please enter a discount code');
                return;
            }

            fetch("{{ route('cart.apply-discount') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        discount_code: code
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal and reload page
                        bootstrap.Modal.getInstance(document.getElementById('discountModal')).hide();
                        showNotification('Discount applied successfully!', 'success');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        showDiscountError(data.message || 'Invalid discount code');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showDiscountError('Error applying discount');
                });
        }

        // Remove discount
        function removeDiscount() {
            Swal.fire({
                title: 'Remove Discount?',
                text: "Are you sure you want to remove the discount?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('cart.remove-discount') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'info',
                                    text: 'Discount removed',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                setTimeout(() => location.reload(), 1000);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                }
            });
        }

        // Show discount error
        function showDiscountError(message) {
            Swal.fire({
                icon: 'error',
                text: message,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'bottom-end'
            });
        }


        // Load saved addresses
        function loadSavedAddresses() {
            fetch("{{ route('checkout.addresses') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.addresses.length > 0) {
                        displaySavedAddresses(data.addresses);
                    } else {
                        document.getElementById('savedAddresses').style.display = 'none';
                        document.getElementById('addressForm').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function displaySavedAddresses(addresses) {
            const addressList = document.getElementById('addressList');
            let html = '';

            addresses.forEach(address => {
                html += `
            <div class="address-card mb-2 ${address.is_default ? 'selected' : ''}"
                 onclick="selectAddress(${address.id}, this)">
                <div class="form-check">
                    <input class="form-check-input" type="radio"
                           name="selectedAddress"
                           ${address.is_default ? 'checked' : ''}>
                    <label class="form-check-label w-100">
                        <strong>${address.city}</strong><br>
                        <small class="text-muted">
                            ${address.formatted_address}
                        </small>
                    </label>
                </div>
            </div>
        `;
            });

            addressList.innerHTML = html;
            document.getElementById('savedAddresses').style.display = 'block';
            document.getElementById('addressForm').style.display = 'none';
            document.getElementById('backToAddressesBtn').style.display = 'none';


            // auto-select default/first
            if (addresses.length > 0) {
                const def = addresses.find(a => a.is_default);
                selectedAddressId = def ? def.id : addresses[0].id;
            }
        }


        // Select address
        function selectAddress(addressId, el) {
            selectedAddressId = addressId;

            document.querySelectorAll('.address-card').forEach(card => {
                card.classList.remove('selected');
                const radio = card.querySelector('input[type="radio"]');
                if (radio) radio.checked = false;
            });

            el.classList.add('selected');
            const radio = el.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }

        // Auto-trigger checkout if parameter is present
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const buyItemId = urlParams.get('buy_item_id');

            if (buyItemId) {
                checkoutSingleItem(buyItemId);
            } else if (urlParams.get('checkout') === '1') {
                checkoutAllItems();
            }
        });

        function checkoutAllItems() {
            singleItemCheckoutId = null;
            const addressModal = new bootstrap.Modal(document.getElementById('addressModal'));
            addressModal.show();
        }

        function checkoutSingleItem(id) {
            singleItemCheckoutId = id;
            const addressModal = new bootstrap.Modal(document.getElementById('addressModal'));
            addressModal.show();
        }

        // Show new address form
        function showNewAddressForm() {
            document.getElementById('savedAddresses').style.display = 'none';
            document.getElementById('addressForm').style.display = 'block';
            document.getElementById('backToAddressesBtn').style.display = 'inline-block';

            document.getElementById('addressForm').reset();
            selectedAddressId = null;
        }

        function showSavedAddresses() {
            document.getElementById('addressForm').style.display = 'none';
            document.getElementById('savedAddresses').style.display = 'block';
            document.getElementById('backToAddressesBtn').style.display = 'none';
        }

        // Save address and checkout
        function saveAddressAndCheckout() {

            // Existing address selected
            if (selectedAddressId) {
                let url = `/checkout/review/${selectedAddressId}`;
                if (typeof singleItemCheckoutId !== 'undefined' && singleItemCheckoutId) {
                    url += `?cart_item_id=${singleItemCheckoutId}`;
                }
                window.location.href = url;
                return;
            }

            const form = document.getElementById('addressForm');

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const addressData = Object.fromEntries(new FormData(form));

            fetch("{{ route('checkout.address.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(addressData)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        let url = `/checkout/review/${res.address.id}`;
                        if (typeof singleItemCheckoutId !== 'undefined' && singleItemCheckoutId) {
                            url += `?cart_item_id=${singleItemCheckoutId}`;
                        }
                        window.location.href = url;
                    }
                });
        }

        // Update quantity
        function updateQuantity(cartId, newQuantity) {
            if (newQuantity < 1) {
                showNotification('Minimum quantity is 1', 'warning');
                return;
            }

            fetch("{{ route('cart.update') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        id: cartId,
                        quantity: newQuantity
                    })
                })
                .then(async response => {
                    const data = await response.json();
                    if (response.ok && data.success) {
                        location.reload();
                    } else {
                        throw new Error(data.message || 'Error updating quantity');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        text: error.message || 'Error updating quantity',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                });
        }

        // Remove item from cart
        function removeItem(cartId) {
            Swal.fire({
                title: 'Remove Item?',
                text: "Are you sure you want to remove this item from your cart?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/cart/remove/${cartId}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    text: 'Item removed from cart',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                setTimeout(() => location.reload(), 1000);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('Error removing item', 'error');
                        });
                }
            });
        }

        // Apply promo code
        function applyPromoCode() {
            const promoCode = document.getElementById('promoCode').value.trim();

            if (!promoCode) {
                showNotification('Please enter a promo code', 'warning');
                return;
            }

            showNotification('Promo code validation would happen here', 'info');
        }

        // Show notification
        function showNotification(message, type = 'info') {
            Swal.fire({
                icon: type === 'danger' ? 'error' : (type === 'info' ? 'info' : (type === 'success' ? 'success' :
                    (type === 'warning' ? 'warning' : 'info'))),
                text: message,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }
    </script>
@endsection
