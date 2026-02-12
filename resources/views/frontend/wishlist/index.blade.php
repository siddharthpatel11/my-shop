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
                <h2 class="fw-bold"><i class="fas fa-heart text-danger me-2"></i> My Wishlist</h2>
                <p class="text-muted">You have {{ $wishlistItems->count() }} items in your wishlist.</p>
            </div>
        </div>

        @if ($wishlistItems->count() > 0)
            <div class="row g-4">
                @foreach ($wishlistItems as $item)
                    @php
                        $product = $item->product;
                        $images = $product->image ? explode(',', $product->image) : [];
                    @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="card h-100 border-0 shadow-sm product-card">
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
