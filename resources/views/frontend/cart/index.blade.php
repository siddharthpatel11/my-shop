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
                        <div class="card border-0 shadow-sm mb-3 cart-item-card" data-cart-id="{{ $item->id }}">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-2 col-3 mb-3 mb-md-0">
                                        <img src="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                                            class="cart-item-image" alt="{{ $item->product->name }}">
                                    </div>

                                    <div class="col-md-4 col-9 mb-3 mb-md-0">
                                        <h6 class="fw-bold mb-2">{{ $item->product->name }}</h6>
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
                                            <div class="quantity-control">
                                                <button
                                                    onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" value="{{ $item->quantity }}" readonly>
                                                <button
                                                    onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-12">
                                        <div class="text-md-center">
                                            <small class="text-muted d-block">Subtotal</small>
                                            <div class="fw-bold text-primary mb-2">
                                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger"
                                                onclick="removeItem({{ $item->id }})">
                                                <i class="fas fa-trash-alt me-1"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Empty Cart Message --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted mb-3">Your cart is empty</h4>
                            <p class="text-muted mb-4">Add some products to get started!</p>
                            <a href="{{ route('frontend.products.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Cart Summary --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-body">
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

                        <button class="btn btn-primary w-100 mb-3" id="checkoutBtn" data-bs-toggle="modal"
                            data-bs-target="#addressModal" {{ $cartItems->count() == 0 ? 'disabled' : '' }}>
                            <i class="fas fa-lock me-2"></i> Proceed to Checkout
                        </button>

                        <a href="{{ route('frontend.products.index') }}" class="btn btn-outline-secondary w-100">
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
        .cart-item-card {
            transition: all 0.3s ease;
        }

        .cart-item-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
        }

        .cart-item-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
        }

        .quantity-control {
            display: inline-flex;
            align-items: center;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
        }

        .quantity-control button {
            border: none;
            background: #f8f9fa;
            padding: 8px 12px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .quantity-control button:hover {
            background: #e9ecef;
        }

        .quantity-control input {
            border: none;
            width: 50px;
            text-align: center;
            padding: 8px 4px;
        }

        .quantity-control input::-webkit-outer-spin-button,
        .quantity-control input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .color-indicator {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            display: inline-block;
        }

        .size-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-weight: 500;
        }

        .address-card {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .address-card:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .address-card.selected {
            border-color: #667eea;
            background: #f8f9ff;
        }

        @media (max-width: 768px) {
            .cart-item-image {
                width: 80px;
                height: 80px;
            }
        }
    </style>

    {{-- Scripts --}}
    <script>
        let selectedAddressId = null;

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
                        displayDiscounts(data.discounts);
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
        function displayDiscounts(discounts) {
            let html = '';

            discounts.forEach(discount => {
                const discountValue = discount.type === 'percentage' ?
                    `${discount.value}% OFF` :
                    `₹${parseFloat(discount.value).toFixed(2)} OFF`;

                const validPeriod = discount.start_date || discount.end_date ?
                    `<small class="text-muted d-block mt-1">
                        Valid: ${formatDate(discount.start_date)} - ${formatDate(discount.end_date)}
                    </small>` : '';

                html += `
                    <div class="discount-card" onclick="selectDiscount('${discount.code}')">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="discount-badge">${discount.code}</span>
                                    <span class="badge bg-${discount.type === 'percentage' ? 'info' : 'success'}">
                                        ${discountValue}
                                    </span>
                                </div>
                                ${validPeriod}
                            </div>
                            <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); selectDiscount('${discount.code}')">
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
            if (!confirm('Are you sure you want to remove the discount?')) {
                return;
            }

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
                        showNotification('Discount removed', 'info');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Show discount error
        function showDiscountError(message) {
            const errorDiv = document.getElementById('discountError');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 3000);
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
                window.location.href = `/checkout/review/${selectedAddressId}`;
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
                        window.location.href = `/checkout/review/${res.address.id}`;
                    }
                });
        }

        // Update quantity
        function updateQuantity(cartId, newQuantity) {
            if (newQuantity < 1) {
                showNotification('Minimum quantity is 1', 'warning');
                return;
            }

            if (newQuantity > 10) {
                showNotification('Maximum quantity is 10', 'warning');
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
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error updating quantity', 'danger');
                });
        }

        // Remove item from cart
        function removeItem(cartId) {
            if (!confirm('Are you sure you want to remove this item?')) {
                return;
            }

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
                        showNotification('Item removed from cart', 'info');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error removing item', 'danger');
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
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>

    {{-- Font Awesome for icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
