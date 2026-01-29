@extends('layouts.app')

@section('content')

    <div class="card mt-5">
        <h2 class="card-header">Laravel CRUD</h2>
        <div class="card-body">

            @session('success')
                <div class="alert alert-success">{{ $value }}</div>
            @endsession

            <div class="d-flex justify-content-between align-items-center mb-3">
                <a class="btn btn-success btn-sm" href="{{ route('products.create') }}">
                    <i class="fa fa-plus"></i> Create New Product
                </a>
            </div>

            {{-- SEARCH --}}
            <form action="{{ route('products.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or details..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                            <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <button class="btn btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- TABLE --}}
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="70">No</th>
                        <th width="90">Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Status</th>
                        <th width="280">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($products as $product)
                        @php
                            $images = $product->image ? explode(',', $product->image) : [];
                            $sizeIds = $product->size_id ? explode(',', $product->size_id) : [];
                            $colorIds = $product->color_id ? explode(',', $product->color_id) : [];
                        @endphp

                        <tr>
                            <td>{{ ++$i }}</td>

                            {{-- IMAGE --}}
                            <td>
                                @if (!empty($images))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach ($images as $img)
                                            <img src="{{ asset('images/products/' . $img) }}"
                                                style="width:40px;height:40px;
                            object-fit:cover;
                            border:1px solid #ddd;
                            border-radius:4px;">
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>


                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td>{{ number_format($product->price, 2) }}</td>

                            {{-- SIZE --}}
                            <td>
                                @forelse ($sizeIds as $sid)
                                    {{ optional($sizes->firstWhere('id', $sid))->name }}@if (!$loop->last)
                                        ,
                                    @endif
                                @empty
                                    -
                                @endforelse
                            </td>

                            {{-- COLOR --}}
                            <td>
                                @forelse ($colorIds as $cid)
                                    @php $color = $colors->firstWhere('id', $cid); @endphp
                                    @if ($color)
                                        <span class="badge" style="background:{{ $color->hex_code }};color:#fff;">
                                            {{ $color->name }}
                                        </span>
                                    @endif
                                @empty
                                    -
                                @endforelse
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <span
                                    class="badge bg-{{ $product->status == 'active' ? 'success' : ($product->status == 'inactive' ? 'secondary' : 'danger') }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>

                            {{-- ACTION --}}
                            <td>
                                <a class="btn btn-info btn-sm" href="{{ route('products.show', $product->id) }}">
                                    <i class="fa-solid fa-list"></i> Show
                                </a>

                                <a class="btn btn-primary btn-sm" href="{{ route('products.edit', $product->id) }}">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>

                                <form action="{{ route('products.toggle-status', $product->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i
                                            class="fa-solid {{ $product->status == 'active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this product?')">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {!! $products->links() !!}
            </div>

        </div>
    </div>

@endsection
