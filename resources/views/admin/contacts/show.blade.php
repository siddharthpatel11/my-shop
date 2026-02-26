@extends('layouts.app')

@section('title', 'Message Detail')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-envelope-open-text me-2"></i> Message Detail
                    </h5>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="text-muted small fw-bold text-uppercase">From</label>
                            <h5 class="mb-0">{{ $message->name }}</h5>
                            <a href="mailto:{{ $message->email }}" class="text-decoration-none small">
                                {{ $message->email }}
                            </a>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <label class="text-muted small fw-bold text-uppercase">Received At</label>
                            <p class="mb-0">{{ $message->created_at->format('F d, Y \a\t h:i A') }}</p>
                            <p class="small text-muted mb-0">({{ $message->created_at->diffForHumans() }})</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="text-muted small fw-bold text-uppercase">Phone Number</label>
                        <p class="mb-0">
                            @if($message->number)
                                <a href="tel:{{ $message->number }}" class="text-decoration-none">
                                    {{ $message->number }}
                                </a>
                            @else
                                <span class="text-muted italic">Not provided</span>
                            @endif
                        </p>
                    </div>

                    <div class="message-content p-4 bg-light rounded-3">
                        <label class="text-muted small fw-bold text-uppercase mb-2 d-block">Message Body</label>
                        <p class="mb-0 whitespace-pre-wrap">{{ $message->message }}</p>
                    </div>
                </div>
                <div class="card-footer bg-white py-3">
                    <div class="d-flex justify-content-end">
                        <form action="{{ route('admin.contacts.destroy', $message->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-danger delete-btn">
                                <i class="fas fa-trash me-1"></i> Delete Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .whitespace-pre-wrap {
        white-space: pre-wrap;
    }
    .message-content {
        line-height: 1.6;
        min-height: 200px;
    }
</style>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.delete-btn', function() {
        const form = $(this).closest('form');
        confirmDelete('Delete Message?', 'This action cannot be undone.').then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
