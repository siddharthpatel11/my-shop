@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Discount Details</h2>
            <div>
                <a href="{{ route('discounts.edit', $discount) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('discounts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Discount Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Code:</strong>
                                <p><code class="fs-4">{{ $discount->code }}</code></p>
                            </div>
                            <div class="col-md-6">
                                <strong>Type:</strong>
                                <p>
                                    <span class="badge bg-{{ $discount->type === 'percentage' ? 'info' : 'success' }} fs-6">
                                        {{ ucfirst($discount->type) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Discount Value:</strong>
                                <p class="fs-3 text-primary fw-bold mb-0">
                                    @if ($discount->type === 'percentage')
                                        {{ $discount->value }}%
                                    @else
                                        ₹{{ number_format($discount->value, 2) }}
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Start Date:</strong>
                                <p>
                                    @if ($discount->start_date)
                                        {{ $discount->start_date->format('M d, Y h:i A') }}
                                    @else
                                        <span class="text-muted">Immediately</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <strong>End Date:</strong>
                                <p>
                                    @if ($discount->end_date)
                                        {{ $discount->end_date->format('M d, Y h:i A') }}
                                    @else
                                        <span class="text-muted">No expiration</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Current Status:</strong>
                            <p>
                                @if ($discount->status === 'active')
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check-circle"></i> Active
                                    </span>
                                @elseif($discount->status === 'inactive')
                                    <span class="badge bg-warning fs-6">
                                        <i class="fas fa-pause-circle"></i> Inactive
                                    </span>
                                @else
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-times-circle"></i> Deleted
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <strong>Validity:</strong>
                            <p>
                                @if ($discount->isValid())
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Valid Now
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle"></i> Not Valid
                                    </span>
                                @endif
                            </p>
                        </div>

                        @if ($discount->start_date && $discount->start_date->isFuture())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Discount will start in {{ $discount->start_date->diffForHumans() }}
                            </div>
                        @endif

                        @if ($discount->end_date && $discount->end_date->isPast())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Discount expired {{ $discount->end_date->diffForHumans() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
