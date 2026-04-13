@extends('layouts.frontend.app')

@section('title', 'Order Review')

@section('content')
    <div class="container my-5">

        {{-- Progress Steps --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-center align-items-center gap-3">
                    <div class="text-center">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            <i class="bi bi-check-lg"></i>
                        </div>
                        <div class="small mt-2 text-success fw-semibold">Cart</div>
                    </div>
                    <div class="border-top flex-grow-1" style="max-width: 100px; border-width: 2px !important;"></div>
                    <div class="text-center">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px;">
                            2
                        </div>
                        <div class="small mt-2 text-primary fw-semibold">Review</div>
                    </div>
                    <div class="border-top flex-grow-1 border-secondary" style="max-width: 100px;"></div>
                    <div class="text-center">
                        <div class="rounded-circle bg-light text-muted d-inline-flex align-items-center justify-content-center border"
                            style="width: 40px; height: 40px;">
                            3
                        </div>
                        <div class="small mt-2 text-muted">Payment</div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="fw-bold mb-4">Order Review</h2>

        <div class="row g-4">

            {{-- Left Column --}}
            <div class="col-lg-8">

                {{-- Delivery Address --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                Delivery Address
                            </h5>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#addressModal">
                                Change
                            </button>
                        </div>
                        <div class="bg-light rounded p-3" id="selectedAddressDisplay">
                            <p class="mb-0 text-dark">
                                @if ($address->full_address)
                                    {{ $address->full_address }},
                                @endif
                                {{ $address->city }}, {{ $address->district }},
                                {{ $address->state }}, {{ $address->country }}
                                @if ($address->pincode)
                                    - {{ $address->pincode }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Cart Items --}}
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-bag-check-fill text-primary me-2"></i>
                            Order Items ({{ $cartItems->count() }})
                        </h5>

                        @foreach ($cartItems as $item)
                            @php
                                $images = $item->product->image ? explode(',', $item->product->image) : [];
                                $firstImage = !empty($images) ? $images[0] : null;
                            @endphp
                            <div class="d-flex align-items-start gap-3 border-bottom pb-3 mb-3">
                                {{-- Product Image --}}
                                <div class="bg-light rounded overflow-hidden"
                                    style="width: 80px; height: 80px; flex-shrink: 0;">
                                    <a href="{{ route('frontend.products.show', $item->product->id) }}">
                                        @if ($firstImage)
                                            <img src="{{ asset('images/products/' . $firstImage) }}"
                                                alt="{{ $item->product->name }}" class="img-fluid"
                                                style="width: 80px; height: 80px; object-fit: cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                                <i class="bi bi-image fs-3"></i>
                                            </div>
                                        @endif
                                    </a>
                                </div>

                                <div class="flex-grow-1">
                                    <a href="{{ route('frontend.products.show', $item->product->id) }}"
                                        class="text-decoration-none">
                                        <h6 class="mb-1 text-dark fw-bold">{{ $item->product->name }}</h6>
                                    </a>
                                    <p class="text-muted small mb-2">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="text-muted small">Quantity:</span>
                                        <div class="input-group input-group-sm rounded shadow-sm overflow-hidden"
                                            style="width: 100px;">
                                            <button
                                                class="btn btn-primary d-flex align-items-center justify-content-center p-0"
                                                type="button" style="width: 32px; height: 32px;"
                                                onclick="updateReviewQuantity({{ $item->id }}, {{ $item->quantity }}, -1)"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <input type="text" class="form-control text-center bg-white border-0 fw-bold"
                                                value="{{ $item->quantity }}" readonly
                                                style="padding: 0; font-size: 0.9rem;">
                                            <button
                                                class="btn btn-primary d-flex align-items-center justify-content-center p-0"
                                                type="button" style="width: 32px; height: 32px;"
                                                onclick="updateReviewQuantity({{ $item->id }}, {{ $item->quantity }}, 1)"
                                                {{ $item->quantity >= 10 ? 'disabled' : '' }}>
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    </p>

                                    {{-- Color and Size --}}
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        @if ($item->color)
                                            <div class="d-flex align-items-center gap-1">
                                                <span class="color-indicator"
                                                    style="background-color: {{ $item->color->hex_code }}; width: 20px; height: 20px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.2); display: inline-block;"></span>
                                                <small class="text-muted">{{ $item->color->name }}</small>
                                            </div>
                                        @endif

                                        @if ($item->size)
                                            <div class="d-flex align-items-center gap-1">
                                                <small class="text-muted">Size:</small>
                                                <span
                                                    class="badge bg-light text-dark border">{{ $item->size->code ?? $item->size->name }}</span>
                                            </div>
                                        @endif
                                        
                                        @if (!empty($item->variant))
                                            <div class="d-flex align-items-center gap-1">
                                                <small class="text-muted">Variant:</small>
                                                <span
                                                    class="badge bg-light text-dark border border-secondary">{{ $item->variant }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-muted small">₹{{ number_format($item->price, 2) }} ×
                                            {{ $item->quantity }}</span>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <div class="fw-bold text-primary fs-6">
                                        ₹{{ number_format($item->price * $item->quantity, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

            </div>

            {{-- Right Column - Order Summary --}}
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Order Summary</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">₹{{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
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

                        {{-- Discount Display --}}
                        @if ($appliedDiscount)
                            <div class="bg-success bg-opacity-10 rounded p-3 mb-3 border border-success">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <small class="d-block fw-bold text-success">
                                            <i class="bi bi-tag-fill me-1"></i> {{ $appliedDiscount->code }}
                                        </small>
                                        <small class="text-muted">
                                            {{ $appliedDiscount->type === 'percentage' ? $appliedDiscount->value . '% OFF' : '₹' . number_format($appliedDiscount->value, 2) . ' OFF' }}
                                        </small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-link text-primary p-0" data-bs-toggle="modal"
                                            data-bs-target="#discountModal" title="Change discount">
                                            <i class="bi bi-pencil-square fs-5"></i>
                                        </button>
                                        <button class="btn btn-sm btn-link text-danger p-0" onclick="removeDiscount()"
                                            title="Remove discount">
                                            <i class="bi bi-x-circle fs-5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mb-3 text-success">
                                <span>Discount Savings</span>
                                <span class="fw-semibold">- ₹{{ number_format($discountAmount, 2) }}</span>
                            </div>
                        @else
                            <button class="btn btn-outline-primary w-100 mb-3" data-bs-toggle="modal"
                                data-bs-target="#discountModal" {{ $cartItems->count() == 0 ? 'disabled' : '' }}>
                                <i class="bi bi-tag-fill me-2"></i> Apply Discount Code
                            </button>
                        @endif

                        <hr>

                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Total Amount</span>
                            <span class="fw-bold fs-5 text-primary">
                                ₹{{ number_format($total, 2) }}
                            </span>
                        </div>

                        @if ($appliedDiscount)
                            <div class="alert alert-success py-2 px-3 mb-3">
                                <small class="d-block mb-0">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    You saved ₹{{ number_format($discountAmount, 2) }} with this discount!
                                </small>
                            </div>
                        @endif

                        <hr>

                        {{-- Payment Method Selection --}}
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Select Payment Method</h6>
                            <div class="form-check border rounded p-3 mb-2 cursor-pointer transition-all payment-option"
                                onclick="document.getElementById('cod').checked = true">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod"
                                    value="cod" checked>
                                <label class="form-check-label w-100 cursor-pointer" for="cod">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="bi bi-cash-stack me-2 text-success"></i>
                                            Cash on Delivery (COD)
                                        </span>
                                    </div>
                                </label>
                            </div>
                            <div class="form-check border rounded p-3 mb-2 cursor-pointer transition-all payment-option"
                                onclick="document.getElementById('razorpay').checked = true">
                                <input class="form-check-input" type="radio" name="payment_method" id="razorpay"
                                    value="razorpay">
                                <label class="form-check-label w-100 cursor-pointer" for="razorpay">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="bi bi-credit-card me-2 text-primary"></i>
                                            Online Payment (Razorpay)
                                        </span>
                                        <div class="razorpay-icons">
                                            <img src="https://razorpay.com/assets/razorpay-glyph.svg" height="20"
                                                alt="Razorpay">
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <hr>
                        <button class="btn btn-primary w-100 py-3 fw-semibold mb-2" onclick="placeOrder()">
                            <i class="bi bi-lock-fill me-2"></i>
                            Place Order
                        </button>

                        <a href="{{ route('frontend.cart') }}" class="btn btn-outline-secondary w-100 py-2">
                            <i class="bi bi-arrow-left me-2"></i>
                            Back to Cart
                        </a>

                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex align-items-center text-muted small mb-2">
                                <i class="bi bi-shield-check me-2 text-success"></i>
                                <span>Secure Payment</span>
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <i class="bi bi-truck me-2 text-info"></i>
                                <span>Free Delivery on this order</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>

    {{-- Address Change Modal --}}
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addressModalLabel">
                        <i class="bi bi-geo-alt-fill me-2"></i> Change Delivery Address
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{-- Saved Addresses --}}
                    <div id="savedAddresses" class="mb-4">
                        <h6 class="fw-bold mb-3">Select Address</h6>
                        <div id="addressList">
                            {{-- Addresses will be loaded here --}}
                        </div>
                        <button class="btn btn-outline-primary btn-sm mt-3" onclick="showNewAddressForm()">
                            <i class="bi bi-plus-circle me-1"></i> Add New Address
                        </button>
                    </div>

                    {{-- New Address Form --}}
                    <div id="newAddressForm" style="display: none;">
                        <h6 class="fw-bold mb-3">Add New Address</h6>
                        <form id="addressForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="country" class="form-label">Country <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="country" name="country" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="state" class="form-label">State <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="state" name="state" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="district" class="form-label">District <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="district" name="district" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="city" class="form-label">City <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="pincode" class="form-label">Pincode</labelsuu>
                                        <input type="text" class="form-control" id="pincode" name="pincode">
                                </div>
                                <div class="col-12">
                                    <label for="full_address" class="form-label">Full Address (House No., Street,
                                        Landmark)</label>
                                    <textarea class="form-control" id="full_address" name="full_address" rows="3"></textarea>
                                </div>
                            </div>
                        </form>
                        <button class="btn btn-outline-secondary btn-sm mt-3" onclick="showSavedAddresses()">
                            <i class="bi bi-arrow-left me-1"></i> Back to Saved Addresses
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveAndChangeAddress()">
                        <i class="bi bi-check-lg me-1"></i> Confirm Address
                    </button>
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
                        <i class="bi bi-tag-fill me-2"></i> Apply Discount
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

    {{-- Order Confirmation Modal --}}
    <div class="modal fade" id="orderConfirmationModal" tabindex="-1" aria-labelledby="orderConfirmationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Order Placed Successfully!</h4>
                    <p class="text-muted mb-4">
                        Your order has been placed successfully.<br>
                        Order Number: <strong id="orderNumber"></strong>
                    </p>
                    @if ($appliedDiscount)
                        <div class="alert alert-success mb-4">
                            <i class="bi bi-gift-fill me-2"></i>
                            You saved ₹{{ number_format($discountAmount, 2) }} on this order!
                        </div>
                    @endif
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
    </div>

    {{-- Scripts --}}
    <script>
        let selectedAddressId = {{ $address->id }};
        let currentAddresses = [];
        const cartItemId = new URLSearchParams(window.location.search).get('cart_item_id');

        // Load addresses when modal opens
        document.getElementById('addressModal').addEventListener('show.bs.modal', function() {
            loadAddresses();
        });

        // Load all addresses
        function loadAddresses() {
            fetch("{{ route('checkout.addresses') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.addresses.length > 0) {
                        currentAddresses = data.addresses;
                        displayAddresses(data.addresses);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Failed to load addresses', 'danger');
                });
        }

        // Display addresses
        function displayAddresses(addresses) {
            const addressList = document.getElementById('addressList');
            let html = '';

            addresses.forEach(address => {
                const isSelected = address.id === selectedAddressId;
                html += `
                    <div class="address-card mb-3 ${isSelected ? 'selected' : ''}"
                         onclick="selectAddress(${address.id})"
                         style="border: 2px solid ${isSelected ? '#667eea' : '#dee2e6'}; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.3s;">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="selectedAddress"
                                   id="address${address.id}" value="${address.id}"
                                   ${isSelected ? 'checked' : ''}>
                            <label class="form-check-label w-100" for="address${address.id}">
                                <strong>${address.city}</strong><br>
                                <small class="text-muted">
                                    ${address.full_address ? address.full_address + ', ' : ''}
                                    ${address.district}, ${address.state}, ${address.country}
                                    ${address.pincode ? ' - ' + address.pincode : ''}
                                </small>
                            </label>
                        </div>
                    </div>
                `;
            });

            addressList.innerHTML = html;
        }

        // Select address
        function selectAddress(addressId) {
            selectedAddressId = addressId;
            document.querySelectorAll('.address-card').forEach(card => {
                card.classList.remove('selected');
                card.style.borderColor = '#dee2e6';
            });
            event.currentTarget.classList.add('selected');
            event.currentTarget.style.borderColor = '#667eea';
            event.currentTarget.style.backgroundColor = '#f8f9ff';
        }

        // Show new address form
        function showNewAddressForm() {
            document.getElementById('savedAddresses').style.display = 'none';
            document.getElementById('newAddressForm').style.display = 'block';
            document.getElementById('addressForm').reset();
        }

        // Show saved addresses
        function showSavedAddresses() {
            document.getElementById('savedAddresses').style.display = 'block';
            document.getElementById('newAddressForm').style.display = 'none';
        }

        // Save and change address
        function saveAndChangeAddress() {
            // Check if new address form is shown
            if (document.getElementById('newAddressForm').style.display === 'block') {
                saveNewAddress();
            } else {
                changeToSelectedAddress();
            }
        }

        // Save new address
        function saveNewAddress() {
            const form = document.getElementById('addressForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            const addressData = Object.fromEntries(formData);

            fetch("{{ route('checkout.address.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(addressData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        selectedAddressId = data.address.id;
                        redirectToReview(data.address.id);
                    } else {
                        showNotification(data.message || 'Failed to save address', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error saving address', 'danger');
                });
        }

        // --- Quantity Update Functions ---

        function updateReviewQuantity(itemId, currentQty, change) {
            const newQty = currentQty + change;
            if (newQty < 1 || newQty > 10) return;

            // Show loading overlay
            Swal.fire({
                title: 'Updating quantity...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("{{ route('cart.update') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        id: itemId,
                        quantity: newQty
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to refresh all totals and discounts
                        window.location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: data.message || 'Failed to update quantity'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        text: 'Error connecting to server'
                    });
                });
        }

        // Change to selected address
        function changeToSelectedAddress() {
            if (!selectedAddressId) {
                showNotification('Please select an address', 'warning');
                return;
            }
            redirectToReview(selectedAddressId);
        }

        // Redirect to review page with new address
        function redirectToReview(addressId) {
            let url = `/checkout/review/${addressId}`;
            if (cartItemId) {
                url += `?cart_item_id=${cartItemId}`;
            }
            window.location.href = url;
        }

        // Place order
        function placeOrder() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            Swal.fire({
                title: 'Place Order?',
                text: `Are you sure you want to place this order using ${paymentMethod === 'cod' ? 'Cash on Delivery' : 'Online Payment'}?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, place order!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = document.querySelector('button[onclick="placeOrder()"]');
                    const originalBtnHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

                    const processData = {
                        address_id: selectedAddressId,
                        payment_method: paymentMethod
                    };
                    if (cartItemId) {
                        processData.cart_item_id = cartItemId;
                    }

                    fetch("{{ route('checkout.process') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json",
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify(processData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (paymentMethod === 'razorpay') {
                                    openRazorpayModal(data);
                                    btn.disabled = false;
                                    btn.innerHTML = originalBtnHtml;
                                } else {
                                    handleOrderSuccess(data.order);
                                }
                            } else {
                                btn.disabled = false;
                                btn.innerHTML = originalBtnHtml;
                                showNotification(data.message || 'Failed to place order', 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            btn.disabled = false;
                            btn.innerHTML = originalBtnHtml;
                            showNotification('Error processing order', 'danger');
                        });
                }
            });
        }

        function openRazorpayModal(data) {
            const options = {
                "key": data.razorpay_key,
                "amount": data.amount,
                "currency": data.currency,
                "name": "{{ $layoutSettings->site_title ?? 'MyShop' }}",
                "description": "Order Payment #" + data.order.order_number,
                "image": "{{ $layoutSettings->frontend_logo_url ?? '' }}",
                "order_id": data.razorpay_order_id,
                "handler": function(response) {
                    verifyPayment(response, data.order.id);
                },
                "prefill": {
                    "name": "{{ auth('customer')->user()->name }}",
                    "email": "{{ auth('customer')->user()->email }}",
                    "contact": ""
                },
                "theme": {
                    "color": "#667eea"
                }
            };
            const rzp = new Razorpay(options);
            rzp.on('payment.failed', function(response) {
                showNotification("Payment Failed: " + response.error.description, "danger");
            });
            rzp.open();
        }

        function verifyPayment(razorpayResponse, orderId) {
            fetch("{{ route('checkout.verify-payment') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        razorpay_order_id: razorpayResponse.razorpay_order_id,
                        razorpay_payment_id: razorpayResponse.razorpay_payment_id,
                        razorpay_signature: razorpayResponse.razorpay_signature
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const orderShowUrl = "{{ route('frontend.order.show', ':id') }}".replace(':id', orderId);
                        fetch(orderShowUrl)
                            .then(() => {
                                // Get the order data again or just show success
                                handleOrderSuccess({
                                    order_number: razorpayResponse.razorpay_order_id
                                }); // We need the actual order number
                                // Actually, let's just refresh to orders page or show modal
                                window.location.href = "{{ route('frontend.orders') }}";
                            });
                    } else {
                        showNotification(data.message || "Payment verification failed", "danger");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification("Error verifying payment", "danger");
                });
        }

        function handleOrderSuccess(order) {
            document.getElementById('orderNumber').textContent = order.order_number || 'Order Placed';
            const confirmModal = new bootstrap.Modal(document.getElementById('orderConfirmationModal'));
            confirmModal.show();

            setTimeout(() => {
                window.location.href = "{{ route('frontend.orders') }}";
            }, 3000);
        }

        // View order details
        function viewOrderDetails() {
            window.location.href = "{{ route('frontend.products.index') }}";
        }

        // Show notification
        function showNotification(message, type = 'info') {
            Swal.fire({
                icon: type === 'danger' ? 'error' : (type === 'success' ? 'success' : (type === 'warning' ?
                    'warning' : 'info')),
                text: message,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }

        // --- Discount Functions ---

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
                                <i class="bi bi-tag fs-1 text-muted mb-3"></i>
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
                            <i class="bi bi-lock-fill me-1"></i>
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
                        const modalEl = document.getElementById('discountModal');
                        const modal = bootstrap.Modal.getInstance(modalEl);
                        if (modal) modal.hide();
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
                position: 'top-end'
            });
        }
    </script>
    <style>
        .address-card:hover,
        .address-card.selected {
            background-color: #f8f9ff;
        }

        .payment-option:hover {
            border-color: #667eea !important;
            background-color: #f8f9ff;
        }

        .payment-option.active {
            border-color: #667eea !important;
            background-color: #f8f9ff;
        }

        .discount-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .discount-card:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .discount-card.locked {
            opacity: 0.8;
            cursor: not-allowed;
            background: #fdfdfd;
        }

        .discount-card.locked:hover {
            border-color: #dee2e6;
            background: #fdfdfd;
        }

        .discount-badge {
            background: #f0f2ff;
            color: #667eea;
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>

    {{-- Razorpay Script --}}
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    {{-- Bootstrap Icons CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endsection
