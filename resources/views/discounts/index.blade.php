@extends('layouts.app')

@section('content')

    <div class="card mt-5">
        <h2 class="card-header">Discount Management</h2>
        <div class="card-body">

            @session('success')
                <div class="alert alert-success">{{ $value }}</div>
            @endsession

            <div class="d-flex justify-content-between align-items-center mb-3">
                <a class="btn btn-success btn-sm" href="{{ route('discounts.create') }}">
                    <i class="fa fa-plus"></i> Create New Discount
                </a>
            </div>

            {{-- SEARCH --}}
            <form action="{{ route('discounts.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search by code..."
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
                        <a href="{{ route('discounts.index') }}" class="btn btn-secondary">
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
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Valid Period</th>
                        <th>Status</th>
                        <th width="280">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($discounts as $discount)
                        <tr>
                            <td>{{ ++$i }}</td>

                            {{-- CODE --}}
                            <td><code class="fs-6">{{ $discount->code }}</code></td>

                            {{-- TYPE --}}
                            <td>
                                <span class="badge bg-{{ $discount->type === 'percentage' ? 'info' : 'success' }}">
                                    {{ ucfirst($discount->type) }}
                                </span>
                            </td>

                            {{-- VALUE --}}
                            <td>
                                <strong>
                                    @if ($discount->type === 'percentage')
                                        {{ $discount->value }}%
                                    @else
                                        ₹{{ number_format($discount->value, 2) }}
                                    @endif
                                </strong>
                            </td>

                            {{-- VALID PERIOD --}}
                            <td>
                                @if ($discount->start_date || $discount->end_date)
                                    <small>
                                        {{ $discount->start_date ? $discount->start_date->format('M d, Y') : 'Start' }} -
                                        <br>
                                        {{ $discount->end_date ? $discount->end_date->format('M d, Y') : 'No End' }}
                                    </small>
                                @else
                                    <small class="text-muted">Always</small>
                                @endif
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <span
                                    class="badge bg-{{ $discount->status == 'active' ? 'success' : ($discount->status == 'inactive' ? 'secondary' : 'danger') }}">
                                    {{ ucfirst($discount->status) }}
                                </span>
                            </td>

                            {{-- ACTION --}}
                            <td>
                                <a class="btn btn-info btn-sm" href="{{ route('discounts.show', $discount->id) }}">
                                    <i class="fa-solid fa-list"></i> Show
                                </a>

                                <a class="btn btn-primary btn-sm" href="{{ route('discounts.edit', $discount->id) }}">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>

                                <form action="{{ route('discounts.toggle-status', $discount->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i
                                            class="fa-solid {{ $discount->status == 'active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this discount?')">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No discounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center">
                {!! $discounts->links() !!}
            </div>

        </div>
    </div>

@endsection
