@extends('layouts.app')

@section('title', isset($tax) ? 'Edit Tax' : 'Create Tax')

@section('content')
    <div class="container-fluid py-4">

        {{-- Page Header --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1">
                            <i class="bi bi-{{ isset($tax) ? 'pencil-square' : 'plus-circle' }} text-primary me-2"></i>
                            {{ isset($tax) ? 'Edit Tax' : 'Create New Tax' }}
                        </h2>
                        <p class="text-muted mb-0">
                            {{ isset($tax) ? 'Update tax rate information' : 'Add a new tax rate for your products' }}
                        </p>
                    </div>
                    <a href="{{ route('taxes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Main Form --}}
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0 fw-semibold">
                            <i class="bi bi-file-text text-primary me-2"></i>Tax Details
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        <form action="{{ isset($tax) ? route('taxes.update', $tax) : route('taxes.store') }}" method="POST"
                            id="taxForm">
                            @csrf
                            @if (isset($tax))
                                @method('PUT')
                            @endif

                            {{-- Tax Name --}}
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold">
                                    <i class="bi bi-tag-fill text-primary me-1"></i>
                                    Tax Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name', $tax->name ?? '') }}"
                                    placeholder="e.g., GST, Sales Tax" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Enter a descriptive name for this tax (e.g. GST, Service Tax)
                                </div>
                            </div>

                            {{-- Tax Rate --}}
                            <div class="mb-4">
                                <label for="rate" class="form-label fw-semibold">
                                    <i class="bi bi-percent text-primary me-1"></i>
                                    Tax Rate <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <input type="number" class="form-control @error('rate') is-invalid @enderror"
                                        id="rate" name="rate" value="{{ old('rate', $tax->rate ?? '') }}"
                                        placeholder="18.00" step="0.01" min="0" max="100" required>
                                    <span class="input-group-text bg-primary text-white fw-bold">
                                        <i class="bi bi-percent"></i>
                                    </span>
                                </div>
                                @error('rate')
                                    <div class="text-danger small mt-1">
                                        <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Enter the percentage rate (e.g. 18 for 18% GST)
                                </div>

                                {{-- Tax Calculation Preview --}}
                                {{--  <div class="alert alert-info mt-3 mb-0" id="taxPreview" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calculator fs-4 me-3"></i>
                                        <div>
                                            <strong>Calculation Example:</strong><br>
                                            <span class="small">
                                                On ₹1,000 product: Tax = <strong>₹<span id="taxAmount">0</span></strong>,
                                                Total = <strong>₹<span id="totalAmount">0</span></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>  --}}
                            </div>

                            {{-- Status (Hidden - Always Active) --}}
                            <input type="hidden" name="status" value="active">

                            {{-- Form Actions --}}
                            <div class="d-flex gap-2 pt-4 border-top mt-4">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-check-circle me-2"></i>
                                    {{ isset($tax) ? 'Update Tax' : 'Create Tax' }}
                                </button>
                                <a href="{{ route('taxes.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                            </div>

                        </form>

                    </div>
                </div>
            </div>

            {{-- Sidebar Info --}}
            {{--  <div class="col-lg-4">


                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-lightbulb me-2"></i>Quick Guide
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-1-circle text-primary fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Name Your Tax</h6>
                                <p class="text-muted small mb-0">Choose a clear name like "GST" or "VAT"</p>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-2-circle text-primary fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Set the Rate</h6>
                                <p class="text-muted small mb-0">Enter percentage (0-100)</p>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-3-circle text-primary fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Save & Activate</h6>
                                <p class="text-muted small mb-0">Tax will be applied to new orders</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header bg-light border-bottom py-3">
                        <h6 class="mb-0 fw-semibold">
                            <i class="bi bi-info-circle text-primary me-2"></i>Tax Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="bi bi-calculator me-1"></i>How It Works
                            </h6>
                            <p class="small text-muted mb-0">
                                Tax is calculated as a percentage of the product price and added to the total at checkout.
                            </p>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-primary mb-2">
                                <i class="bi bi-lightning me-1"></i>Auto-Application
                            </h6>
                            <p class="small text-muted mb-0">
                                The first active tax will be automatically applied to all orders.
                            </p>
                        </div>

                        <div>
                            <h6 class="text-primary mb-2">
                                <i class="bi bi-shield-check me-1"></i>Status
                            </h6>
                            <p class="small text-muted mb-0">
                                New taxes are automatically set to active status.
                            </p>
                        </div>
                    </div>
                </div>


                <div class="card shadow-sm border-0 bg-light">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">
                            <i class="bi bi-bookmark text-primary me-2"></i>Common Tax Rates
                        </h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">GST (India)</span>
                            <span class="badge bg-primary">5%, 12%, 18%, 28%</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">VAT (EU)</span>
                            <span class="badge bg-primary">15-27%</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small">Sales Tax (US)</span>
                            <span class="badge bg-primary">0-10%</span>
                        </div>
                    </div>
                </div>

            </div>  --}}
        </div>

    </div>

    @push('scripts')
        <script>
            // Tax calculation preview
            const rateInput = document.getElementById('rate');
            const taxPreview = document.getElementById('taxPreview');
            const taxAmount = document.getElementById('taxAmount');
            const totalAmount = document.getElementById('totalAmount');

            rateInput.addEventListener('input', function() {
                const rate = parseFloat(this.value) || 0;

                if (rate > 0) {
                    const baseAmount = 1000;
                    const tax = (baseAmount * rate) / 100;
                    const total = baseAmount + tax;

                    taxAmount.textContent = tax.toFixed(2);
                    totalAmount.textContent = total.toFixed(2);
                    taxPreview.style.display = 'block';
                } else {
                    taxPreview.style.display = 'none';
                }
            });

            // Trigger on page load if editing
            @if (isset($tax))
                rateInput.dispatchEvent(new Event('input'));
            @endif

            // Form validation
            document.getElementById('taxForm').addEventListener('submit', function(e) {
                const name = document.getElementById('name').value.trim();
                const rate = parseFloat(document.getElementById('rate').value);

                if (!name) {
                    e.preventDefault();
                    alert('Please enter a tax name');
                    return false;
                }

                if (isNaN(rate) || rate < 0 || rate > 100) {
                    e.preventDefault();
                    alert('Please enter a valid tax rate between 0 and 100');
                    return false;
                }
            });
        </script>
    @endpush
@endsection
