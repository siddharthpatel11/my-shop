@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Create New Discount</h2>
            <a href="{{ route('discounts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('discounts.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Discount Code <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code') }}" style="text-transform: uppercase;" required>
                            <small class="text-muted">Will be converted to uppercase automatically</small>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Discount Type <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type"
                                required>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>
                                    Percentage
                                </option>
                                <option value="amount" {{ old('type') == 'amount' ? 'selected' : '' }}>
                                    Fixed Amount
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="value" class="form-label">
                                Discount Value <span class="text-danger">*</span>
                                <span id="value-unit">(in %)</span>
                            </label>
                            <input type="number" step="0.01" min="0"
                                class="form-control @error('value') is-invalid @enderror" id="value" name="value"
                                value="{{ old('value') }}" required>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status"
                                required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date</label>
                            <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror"
                                id="start_date" name="start_date" value="{{ old('start_date') }}">
                            <small class="text-muted">Leave empty to start immediately</small>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date</label>
                            <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror"
                                id="end_date" name="end_date" value="{{ old('end_date') }}">
                            <small class="text-muted">Leave empty for no expiration</small>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Discount
                        </button>
                        <a href="{{ route('discounts.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Update value label based on discount type
            $('#type').change(function() {
                const valueUnit = $(this).val() === 'percentage' ? '(in %)' : '(in $)';
                $('#value-unit').text(valueUnit);
            });

            // Convert code to uppercase
            $('#code').on('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
@endpush
