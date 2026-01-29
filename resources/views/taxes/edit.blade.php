@extends('layouts.app')

@section('title', isset($tax) ? 'Edit Tax' : 'Create Tax')

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('taxes.index') }}">Taxes</a></li>
                    <li class="breadcrumb-item active">{{ isset($tax) ? 'Edit' : 'Create' }}</li>
                </ol>
            </nav>
            <h2 class="fw-bold">{{ isset($tax) ? 'Edit Tax' : 'Create New Tax' }}</h2>
        </div>

        <div class="row">
            <div class="col-lg-8 col-xl-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">

                        <form action="{{ isset($tax) ? route('taxes.update', $tax) : route('taxes.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($tax))
                                @method('PUT')
                            @endif

                            {{-- Tax Name --}}
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold">
                                    Tax Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $tax->name ?? '') }}"
                                    placeholder="e.g., GST, VAT, Sales Tax" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Enter a descriptive name for this tax</small>
                            </div>

                            {{-- Tax Rate --}}
                            <div class="mb-4">
                                <label for="rate" class="form-label fw-semibold">
                                    Tax Rate (%) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('rate') is-invalid @enderror"
                                        id="rate" name="rate" value="{{ old('rate', $tax->rate ?? '') }}"
                                        placeholder="18.00" step="0.01" min="0" max="100" required>
                                    <span class="input-group-text">%</span>
                                    @error('rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Enter tax rate percentage (e.g., 18 for 18% GST)</small>
                            </div>

                            {{-- Status --}}
                            <div class="mb-4">
                                <label for="status" class="form-label fw-semibold">
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status"
                                    name="status" required>
                                    <option value="active"
                                        {{ old('status', $tax->status ?? 'active') === 'active' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="inactive"
                                        {{ old('status', $tax->status ?? '') === 'inactive' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Only active taxes will be applied to orders</small>
                            </div>

                            {{-- Form Actions --}}
                            <div class="d-flex gap-2 pt-3 border-top">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-check-circle me-2"></i>
                                    {{ isset($tax) ? 'Update Tax' : 'Create Tax' }}
                                </button>
                                <a href="{{ route('taxes.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>

                        </form>

                    </div>
                </div>

                {{-- Info Card --}}
                <div class="card shadow-sm border-0 mt-4 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-info-circle text-primary me-2"></i>Tax Information
                        </h6>
                        <ul class="small text-muted mb-0">
                            <li class="mb-2"><strong>Tax Rate:</strong> Calculated as a percentage of the product price
                                (e.g., 18% GST on ₹1000 = ₹180)</li>
                            <li class="mb-2"><strong>Active Status:</strong> Only active taxes will be applied to customer
                                orders</li>
                            <li><strong>First Active Tax:</strong> The first active tax will be automatically applied to
                                checkout</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
