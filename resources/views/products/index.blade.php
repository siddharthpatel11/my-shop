@extends('layouts.app')

@section('content')

    <div class="card mt-5">
        <h2 class="card-header">Laravel CRUD</h2>
        <div class="card-body">

            <!-- Session messages handled globally -->

            <div class="d-flex justify-content-between align-items-center mb-3">
                <a class="btn btn-success btn-sm" href="{{ route('products.create') }}">
                    <i class="fa fa-plus"></i> Create New Product
                </a>
            </div>

            {{-- SEARCH + IMPORT/EXPORT ROW --}}
            <form action="{{ route('products.index') }}" method="GET" class="mb-3" id="searchForm">
                <div class="row g-3 align-items-end">
                    {{-- Search --}}
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or details..."
                            value="{{ request('search') }}">
                    </div>

                    {{-- Status --}}
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                            <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>

                    {{-- Search / Reset --}}
                    <div class="col-md-3">
                        <button class="btn btn-primary" type="submit">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                    </div>

                    {{-- Import / Export --}}
                    <div class="col-md-3 d-flex gap-2 justify-content-end">

                        {{-- Export CSV --}}
                        <a href="{{ route('products.export', array_merge(request()->only('search', 'status'), ['format' => 'csv'])) }}"
                            class="btn btn-outline-success btn-sm" title="Export to CSV">
                            <i class="fa-solid fa-file-csv"></i> Export CSV
                        </a>

                        {{-- Export Excel --}}
                        <a href="{{ route('products.export', array_merge(request()->only('search', 'status'), ['format' => 'excel'])) }}"
                            class="btn btn-outline-success btn-sm" title="Export to Excel">
                            <i class="fa-solid fa-file-excel"></i> Export Excel
                        </a>

                        {{-- Import (opens modal) --}}
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#importModal" title="Import from CSV / Excel">
                            <i class="fa-solid fa-file-import"></i> Import
                        </button>
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
                                                style="width:40px;height:40px;object-fit:cover;border:1px solid #ddd;border-radius:4px;">
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
                                @php $sizeIds = array_filter(array_map('trim', $sizeIds)); @endphp
                                @if (!empty($sizeIds))
                                    @foreach ($sizeIds as $sid)
                                        @php $size = $sizes->firstWhere('id', (int)$sid); @endphp
                                        @if ($size)
                                            <span class="badge bg-secondary">{{ $size->name }}</span>
                                        @endif
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>

                            {{-- COLOR --}}
                            <td>
                                @php $colorIds = array_filter(array_map('trim', $colorIds)); @endphp
                                @if (!empty($colorIds))
                                    @foreach ($colorIds as $cid)
                                        @php $color = $colors->firstWhere('id', (int)$cid); @endphp
                                        @if ($color)
                                            <span class="badge"
                                                style="background:{{ $color->hex_code ?? '#6c757d' }};color:#fff;">
                                                {{ $color->name }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    -
                                @endif
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
                                    style="display:inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i
                                            class="fa-solid {{ $product->status == 'active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="event.preventDefault(); confirmDelete('Are you sure?', 'You want to delete this product?').then((result) => { if (result.isConfirmed) { this.closest('form').submit(); } })">
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


    {{-- ════════════════════════════════════════════
         IMPORT MODAL
    ════════════════════════════════════════════ --}}
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">
                        <i class="fa-solid fa-file-import"></i> Import Products
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        {{-- Template download hint --}}
                        <div class="alert alert-info py-2 small">
                            <i class="fa-solid fa-circle-info"></i>
                            Need the correct format?
                            <a href="{{ route('products.import.template') }}" class="alert-link">
                                Download CSV template
                            </a>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Select File <span class="text-muted">(CSV or Excel .xlsx/.xls)</span>
                            </label>
                            <input type="file" name="import_file" class="form-control" accept=".csv,.xlsx,.xls,.txt"
                                required>
                        </div>

                        <div class="small text-muted">
                            <strong>Required columns:</strong> name, detail, category, price<br>
                            <strong>Optional columns:</strong> sizes, colors, status
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-upload"></i> Import
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

@endsection
