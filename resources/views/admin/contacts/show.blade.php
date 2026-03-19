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
                        <div class="d-flex justify-content-end gap-2">
                            @if($message->replied_at)
                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#replyModal">
                                    <i class="fas fa-check-circle me-1"></i> View Reply
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#replyModal">
                                    <i class="fas fa-reply me-1"></i> Reply
                                </button>
                            @endif
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

            <!-- Reply Modal -->
            <div class="modal fade text-start" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                        <div class="modal-header bg-white border-bottom py-3">
                            <h5 class="modal-title fw-bold text-success" id="replyModalLabel">
                                <i class="fas fa-reply me-2"></i> Reply to {{ $message->name }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        @if($message->replied_at)
                            <div class="modal-body p-4">
                                <div class="alert alert-info border-0 shadow-sm mb-3">
                                    <i class="fas fa-info-circle me-2"></i> You replied to this message on <strong>{{ $message->replied_at->format('M d, Y \a\t h:i A') }}</strong>
                                </div>
                                <div class="bg-light p-3 rounded-3 mb-3">
                                    <p class="mb-0 whitespace-pre-wrap small">{{ $message->reply_message }}</p>
                                </div>
                                @if($message->reply_image)
                                    <div class="mt-2">
                                        <p class="text-muted fw-bold small mb-2">Attached Image:</p>
                                        <img src="{{ asset('images/contacts/' . $message->reply_image) }}" alt="Reply Image" class="img-fluid rounded border shadow-sm" style="max-height: 150px">
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer border-top-0 py-3 bg-light d-flex justify-content-between" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                <form action="{{ route('admin.contacts.clear-reply', $message->id) }}" method="POST" class="d-inline delete-reply-form">
                                    @csrf
                                    <button type="button" class="btn btn-outline-danger rounded-pill px-4 delete-reply-btn">
                                        <i class="fas fa-trash-alt me-1"></i> Delete Reply
                                    </button>
                                </form>
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                            </div>
                        @else
                            <form action="{{ route('admin.contacts.reply', $message->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label for="reply_message" class="form-label fw-bold small text-muted">Your Reply Message *</label>
                                        <textarea name="reply_message" id="reply_message" rows="4" class="form-control" placeholder="Type your reply here..." required style="border-radius: 8px;"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="reply_image" class="form-label fw-bold small text-muted">Attach Image (Optional)</label>
                                        <input type="file" name="reply_image" id="reply_image" class="form-control" accept="image/*" style="border-radius: 8px;">
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0 py-3 bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                    <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success rounded-pill px-4">
                                        <i class="fas fa-paper-plane me-1"></i> Send Reply
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
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

    $(document).on('click', '.delete-reply-btn', function() {
        const form = $(this).closest('form');
        confirmDelete('Delete Reply?', 'This will permanently delete the admin response and remove it from the customer panel.').then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>
@endpush
