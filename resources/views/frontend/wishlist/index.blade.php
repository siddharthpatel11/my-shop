@extends('layouts.frontend.app')

@section('title', 'My Wishlist')

@section('content')
    <div class="container my-5">
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                        <li class="breadcrumb-item active">My Wishlist</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h2 class="fw-bold mb-1"><i class="fas fa-heart text-danger me-2"></i> My Wishlist</h2>
                        <p class="text-muted mb-0">You have {{ $wishlistItems->count() }} items in your wishlist.</p>
                    </div>
                    <div class="bulk-actions d-flex gap-2">
                        <div class="form-check d-flex align-items-center me-2">
                            <input class="form-check-input me-2" type="checkbox" id="selectAllItems">
                            <label class="form-check-label small fw-bold" for="selectAllItems">Select All</label>
                        </div>
                        <button id="removeSelectedBtn" class="btn btn-outline-danger btn-sm rounded-pill"
                            style="display: none;">
                            <i class="fas fa-minus-circle me-1"></i> Remove Selected
                        </button>
                        <button onclick="clearWishlist()" class="btn btn-danger btn-sm rounded-pill">
                            <i class="fas fa-trash-alt me-1"></i> Clear All
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if ($wishlistItems->count() > 0)
            <div class="row g-4">
                @foreach ($wishlistItems as $item)
                    @php
                        $product = $item->product;
                        $images = $product->image ? explode(',', $product->image) : [];
                    @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6 wishlist-item-card" data-id="{{ $item->id }}">
                        <div class="card h-100 border-0 shadow-sm product-card position-relative">
                            <div class="form-check position-absolute top-0 start-0 m-3" style="z-index: 10;">
                                <input class="form-check-input item-checkbox" type="checkbox" value="{{ $item->id }}">
                            </div>
                            <div class="product-image-wrapper position-relative" style="height: 200px;">
                                <img src="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                                    class="card-img-top" alt="{{ $product->name }}"
                                    style="height: 100%; object-fit: contain; padding: 10px;">

                                <form action="{{ route('wishlist.remove', $item->id) }}" method="POST"
                                    class="position-absolute top-0 end-0 p-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-light btn-sm rounded-circle shadow-sm text-danger"
                                        title="Remove from wishlist">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-truncate mb-2" title="{{ $product->name }}">
                                    {{ $product->name }}
                                </h5>
                                <p class="text-primary fw-bold mb-3">₹{{ number_format($product->price, 2) }}</p>

                                <div class="mt-auto">
                                    <button class="btn btn-primary btn-sm w-100 mb-2" data-product-id="{{ $product->id }}"
                                        data-product-price="{{ $product->price }}" onclick="addToCart(this)">
                                        <i class="fas fa-shopping-cart me-1"></i> Add to Cart
                                    </button>
                                    <a href="{{ route('frontend.products.show', $product->id) }}"
                                        class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-eye me-1"></i> View Product
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 shadow-sm rounded bg-white mt-4">
                <div class="mb-4">
                    <i class="far fa-heart fa-5x text-muted opacity-25"></i>
                </div>
                <h3 class="fw-bold">Your wishlist is empty</h3>
                <p class="text-muted mb-4">Add items you love to your wishlist to browse them later.</p>
                <a href="{{ route('frontend.products.index') }}" class="btn btn-primary btn-lg px-5">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('selectAllItems');
            const itemCheckboxes = document.querySelectorAll('.item-checkbox');
            const removeSelectedBtn = document.getElementById('removeSelectedBtn');

            // Handle Select All
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    itemCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    toggleRemoveSelectedBtn();
                });
            }

            // Handle Individual Checkbox
            itemCheckboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    toggleRemoveSelectedBtn();
                    // Update Select All checkbox state
                    if (selectAll) {
                        const allChecked = Array.from(itemCheckboxes).every(c => c.checked);
                        selectAll.checked = allChecked;
                    }
                });
            });

            function toggleRemoveSelectedBtn() {
                const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
                if (removeSelectedBtn) {
                    removeSelectedBtn.style.display = checkedCount > 0 ? 'inline-block' : 'none';
                    removeSelectedBtn.innerHTML =
                        `<i class="fas fa-minus-circle me-1"></i> Remove Selected (${checkedCount})`;
                }
            }

            // Handle Multi-Remove
            if (removeSelectedBtn) {
                removeSelectedBtn.addEventListener('click', function() {
                    const selectedIds = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                        .map(cb => cb.value);

                    if (selectedIds.length === 0) return;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `You are about to remove ${selectedIds.length} items from your wishlist.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, remove them!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch("{{ route('wishlist.remove-multiple') }}", {
                                    method: "POST",
                                    headers: {
                                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                        "Accept": "application/json",
                                        "Content-Type": "application/json"
                                    },
                                    body: JSON.stringify({
                                        ids: selectedIds
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        showNotification(data.message, 'success');
                                        setTimeout(() => window.location.reload(), 1500);
                                    } else {
                                        showNotification(data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    console.error(error);
                                    showNotification('Something went wrong', 'error');
                                });
                        }
                    });
                });
            }
        });

        function clearWishlist() {
            Swal.fire({
                title: 'Clear entire wishlist?',
                text: "All items will be removed from your wishlist.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, clear all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ route('wishlist.clear') }}", {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json"
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showNotification(data.message, 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                showNotification(data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            showNotification('Something went wrong', 'error');
                        });
                }
            });
        }

        function addToCart(button) {
            const productId = button.dataset.productId;
            const price = button.dataset.productPrice;

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
                        color_id: null,
                        size_id: null
                    })
                })
                .then(async response => {
                    if (response.status === 401) {
                        window.location.href = "{{ route('customer.login') }}";
                        return;
                    }

                    const data = await response.json();

                    if (!response.ok) {
                        showNotification(data.message || 'Error adding to cart', 'error');
                        return;
                    }

                    if (data.success) {
                        window.location.href = "{{ route('frontend.cart') }}";
                    }
                })
                .catch(error => {
                    console.error(error);
                    showNotification('Something went wrong', 'error');
                });
        }

        function showNotification(message, type = 'success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: type,
                    text: message,
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                alert(message);
            }
        }
    </script>

    <style>
        .product-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
        }

        .breadcrumb-item a {
            text-decoration: none;
            color: #667eea;
        }
    </style>
@endsection
