@extends('layouts.app')

@php
    $sizeIds = $product->size_id ? explode(',', $product->size_id) : [];
    $colorIds = $product->color_id ? explode(',', $product->color_id) : [];
    $images = $product->image ? explode(',', $product->image) : [];
@endphp

@section('meta')
    @if($product->seo_meta_title)
        <meta name="title" content="{{ $product->seo_meta_title }}">
    @endif
    @if($product->seo_meta_description)
        <meta name="description" content="{{ $product->seo_meta_description }}">
    @endif
    @if($product->seo_meta_key)
        <meta name="keywords" content="{{ $product->seo_meta_key }}">
    @endif
    @if($product->seo_canonical)
        <link rel="canonical" href="{{ $product->seo_canonical }}">
    @endif
    @if($product->seo_meta_image)
        <meta name="image" content="{{ asset('images/products/' . $product->seo_meta_image) }}">
    @endif

    @if($product->og_meta_title)
        <meta property="og:title" content="{{ $product->og_meta_title }}">
    @endif
    @if($product->og_meta_description)
        <meta property="og:description" content="{{ $product->og_meta_description }}">
    @endif
    @if($product->og_meta_image)
        <meta property="og:image" content="{{ asset('images/products/' . $product->og_meta_image) }}">
    @endif
    @if($product->og_meta_key)
        <meta property="og:keywords" content="{{ $product->og_meta_key }}">
    @endif
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:type" content="product">
@endsection

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
                <div class="col-md-4 mb-2">
                    <strong>Stock:</strong>
                    <p class="text-muted">{{ $product->stock ?? 0 }}</p>
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
                <hr>

                <div class="col-12 mt-4 mb-3">
                    <h5 class="border-bottom pb-2">SEO Meta Data</h5>
                </div>
                <div class="col-md-6 mb-2">
                    <strong>SEO Title:</strong>
                    <p class="text-muted">{{ $product->seo_meta_title ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-2">
                    <strong>SEO Keywords:</strong>
                    <p class="text-muted">{{ $product->seo_meta_key ?? 'N/A' }}</p>
                </div>
                <div class="col-12 mb-2">
                    <strong>SEO Description:</strong>
                    <p class="text-muted">{{ $product->seo_meta_description ?? 'N/A' }}</p>
                </div>
                <div class="col-12 mb-2">
                    <strong>SEO Canonical URL:</strong>
                    <p class="text-muted">{{ $product->seo_canonical ?? 'N/A' }}</p>
                </div>
                @if ($product->seo_meta_image)
                    <div class="col-12 mb-3">
                        <strong>SEO Image:</strong><br>
                        <img src="{{ asset('images/products/' . $product->seo_meta_image) }}" alt="SEO Image"
                            class="mt-2" style="max-height:150px; border:1px solid #ddd; border-radius:6px;">
                    </div>
                @endif

                <hr>
                <div class="col-12 mt-3 mb-3">
                    <h5 class="border-bottom pb-2">Open Graph (OG) Meta Data</h5>
                </div>
                <div class="col-md-6 mb-2">
                    <strong>OG Title:</strong>
                    <p class="text-muted">{{ $product->og_meta_title ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-2">
                    <strong>OG Keywords:</strong>
                    <p class="text-muted">{{ $product->og_meta_key ?? 'N/A' }}</p>
                </div>
                <div class="col-12 mb-2">
                    <strong>OG Description:</strong>
                    <p class="text-muted">{{ $product->og_meta_description ?? 'N/A' }}</p>
                </div>
                @if ($product->og_meta_image)
                    <div class="col-12 mb-3">
                        <strong>OG Image:</strong><br>
                        <img src="{{ asset('images/products/' . $product->og_meta_image) }}" alt="OG Image" class="mt-2"
                            style="max-height:150px; border:1px solid #ddd; border-radius:6px;">
                    </div>
                @endif

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
