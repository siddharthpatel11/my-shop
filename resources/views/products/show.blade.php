@extends('layouts.app')

@php
    $sizeIds = $product->size_id ? explode(',', $product->size_id) : [];
    $colorIds = $product->color_id ? explode(',', $product->color_id) : [];
    $images = $product->image ? explode(',', $product->image) : [];
@endphp

@section('content')
    <div class="card mt-5">
        <h2 class="card-header">Show Product</h2>
        <div class="card-body">

            <div class="d-flex justify-content-end mb-3">
                <a class="btn btn-primary btn-sm" href="{{ route('products.index') }}">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <div class="row">

                {{-- PRODUCT IMAGES --}}
                @if (!empty($images))
                    <div class="col-12 mb-4">
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            @foreach ($images as $img)
                                <img src="{{ asset('images/products/' . $img) }}" alt="{{ $product->name }}"
                                    style="width:200px;height:200px;
                                        object-fit:cover;
                                        border:1px solid #ddd;
                                        border-radius:6px;">
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- NAME --}}
                <div class="col-md-6 mb-2">
                    <strong>Name:</strong>
                    <p class="text-muted">{{ $product->name }}</p>
                </div>

                {{-- CATEGORY --}}
                <div class="col-md-6 mb-2">
                    <strong>Category:</strong>
                    <p class="text-muted">{{ $product->category->name ?? 'N/A' }}</p>
                </div>

                {{-- PRICE --}}
                <div class="col-md-4 mb-2">
                    <strong>Price:</strong>
                    <p class="text-muted">{{ number_format($product->price, 2) }}</p>
                </div>

                {{-- SIZE --}}
                <div class="col-md-4 mb-2">
                    <strong>Size:</strong>
                    <p class="text-muted">
                        @forelse ($sizeIds as $sid)
                            @php $size = $sizes->firstWhere('id', $sid); @endphp
                            @if ($size)
                                {{ $size->name }}
                                @if ($size->code)
                                    ({{ $size->code }})
                                @endif
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endif
                        @empty
                            N/A
                        @endforelse
                    </p>
                </div>

                {{-- COLOR --}}
                <div class="col-md-4 mb-2">
                    <strong>Color:</strong>
                    <p class="text-muted">
                        @forelse ($colorIds as $cid)
                            @php $color = $colors->firstWhere('id', $cid); @endphp
                            @if ($color)
                                <span class="badge"
                                    style="background:{{ $color->hex_code }};
                                         color:#fff;
                                         margin-right:6px;">
                                    {{ $color->name }}
                                </span>
                            @endif
                        @empty
                            N/A
                        @endforelse
                    </p>
                </div>

                {{-- STATUS --}}
                <div class="col-12 mb-2">
                    <strong>Status:</strong>
                    <p>
                        <span
                            class="badge bg-{{ $product->status == 'active' ? 'success' : ($product->status == 'inactive' ? 'secondary' : 'danger') }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </p>
                </div>

                {{-- DETAILS --}}
                <div class="col-12 mb-2">
                    <strong>Details:</strong>
                    <p class="text-muted">{{ $product->detail }}</p>
                </div>

                {{-- CREATED --}}
                {{--  <div class="col-md-6 mb-2">
                    <strong>Created At:</strong>
                    <p class="text-muted">{{ $product->created_at->format('d M Y, h:i A') }}</p>
                </div>  --}}

                {{-- UPDATED --}}
                <div class="col-md-6 mb-2">
                    <strong>Last Updated:</strong>
                    <p class="text-muted">{{ $product->updated_at->format('d M Y, h:i A') }}</p>
                </div>

            </div>
        </div>
    </div>
@endsection
