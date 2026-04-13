@auth('customer')
    @forelse($cartItemsPreview as $item)
        <li class="mb-3">
            <div class="d-flex align-items-center">
                @php $images = $item->product->image ? explode(',', $item->product->image) : []; @endphp
                <a href="{{ route('frontend.products.show', $item->product->id) }}"
                    class="text-decoration-none d-flex align-items-center flex-grow-1 overflow-hidden">
                    <img src="{{ asset('images/products/' . ($images[0] ?? 'no-image.png')) }}"
                        alt="{{ $item->product->name }}"
                        style="width: 50px; height: 50px; object-fit: contain;"
                        class="me-3 rounded border bg-light">
                    <div class="flex-grow-1 overflow-hidden">
                        <div class="text-truncate fw-bold text-dark small mb-0">
                            {{ $item->product->name }}</div>
                        
                        <div class="d-flex flex-wrap gap-1 mb-1" style="font-size: 0.7rem;">
                            @if($item->color)
                                <span class="text-muted">Color: {{ $item->color->name }}</span>
                            @endif
                            @if($item->size)
                                <span class="text-muted">| Size: {{ $item->size->code ?? $item->size->name }}</span>
                            @endif
                            @if(!empty($item->variant))
                                <span class="text-muted">| {{ $item->variant }}</span>
                            @endif
                        </div>

                        <div class="text-primary fw-bold small">
                            {{ $item->quantity }} x ₹{{ number_format($item->price, 2) }}
                        </div>
                    </div>
                </a>
                <a href="{{ route('frontend.cart') }}?buy_item_id={{ $item->id }}"
                    class="btn btn-sm btn-primary ms-1 px-2" title="Buy This">
                    <i class="fas fa-bolt"></i>
                </a>
                <a href="{{ route('frontend.products.show', $item->product->id) }}"
                    class="btn btn-sm btn-outline-primary ms-1 px-2" title="View">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        </li>
    @empty
        <li class="text-center py-3 text-muted">
            <i class="fas fa-shopping-cart fa-2x mb-2 d-block opacity-25"></i>
            <small>Your cart is empty</small>
        </li>
    @endforelse

    @if ($cartCount > 0)
        <li>
            <hr class="dropdown-divider my-3">
        </li>
        <li>
            <div class="d-grid gap-2">
                <a class="btn btn-outline-primary btn-sm py-2"
                    href="{{ route('frontend.cart') }}">
                    View Shopping Cart
                </a>
                <a class="btn btn-primary btn-sm py-2"
                    href="{{ route('frontend.cart') }}?checkout=1">
                    Proceed to Checkout
                </a>
            </div>
        </li>
    @else
        <li>
            <hr class="dropdown-divider my-3">
        </li>
        <li>
            <a class="btn btn-outline-primary btn-sm w-100 py-2"
                href="{{ route('frontend.products.index') }}">
                Go to Shop
            </a>
        </li>
    @endif
@else
    <li class="text-center py-3">
        <p class="small text-muted mb-3">Please login to view your cart</p>
        <a href="{{ route('customer.login') }}" class="btn btn-primary btn-sm w-100">
            Login Now
        </a>
    </li>
@endauth
