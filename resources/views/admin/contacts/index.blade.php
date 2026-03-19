@extends('layouts.app')

@section('title', 'Contact Messages')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold">
                    <i class="fas fa-envelope me-2"></i>Contact Messages
                </h2>
                <p class="text-muted">View and manage all customer contact messages and enquiries.</p>
            </div>
        </div>

        <div class="row">
            {{-- Filters --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.contacts.index') }}" method="GET" class="row g-3">
                        <div class="col-md-5">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control"
                                placeholder="Search by name, email, or phone number..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="reply_status" class="form-label">Reply Status</label>
                            <select name="reply_status" id="reply_status" class="form-select">
                                <option value="all" {{ request('reply_status') === 'all' ? 'selected' : '' }}>All
                                    Messages</option>
                                <option value="pending"
                                    {{ request('reply_status', 'pending') === 'pending' ? 'selected' : '' }}>Pending Reply
                                </option>
                                <option value="replied" {{ request('reply_status') === 'replied' ? 'selected' : '' }}>
                                    Replied</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                        </div>
                    </form>

                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i> Clear Filters
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Date</th>
                                    <th width="150" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $message)
                                    <tr>
                                        <td>{{ $loop->iteration + ($messages->currentPage() - 1) * $messages->perPage() }}
                                        </td>
                                        <td>{{ $message->name }}</td>
                                        <td>{{ $message->email }}</td>
                                        <td>{{ $message->number }}</td>
                                        <td>{{ $message->created_at->format('d M, Y h:i A') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                @if ($message->replied_at)
                                                    <button type="button"
                                                        class="btn btn-sm btn-light text-success border shadow-sm"
                                                        title="Replied"
                                                        style="border-radius: 8px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#replyModal{{ $message->id }}">
                                                        <i class="fas fa-check-circle"></i>
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        class="btn btn-sm btn-light text-warning border shadow-sm"
                                                        title="Reply"
                                                        style="border-radius: 8px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#replyModal{{ $message->id }}">
                                                        <i class="fas fa-reply"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('admin.contacts.show', $message->id) }}"
                                                    class="btn btn-sm btn-light text-primary border shadow-sm"
                                                    title="View Detail"
                                                    style="border-radius: 8px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('admin.contacts.destroy', $message->id) }}"
                                                    method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button"
                                                        class="btn btn-sm btn-light text-danger border shadow-sm delete-btn"
                                                        title="Delete"
                                                        style="border-radius: 8px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- Reply Modal -->
                                            <div class="modal fade text-start" id="replyModal{{ $message->id }}"
                                                tabindex="-1" aria-labelledby="replyModalLabel{{ $message->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg"
                                                        style="border-radius: 12px;">
                                                        <div class="modal-header bg-white border-bottom py-3">
                                                            <h5 class="modal-title fw-bold text-success"
                                                                id="replyModalLabel{{ $message->id }}">
                                                                <i class="fas fa-reply me-2"></i> Reply to
                                                                {{ $message->name }}
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>

                                                        @if ($message->replied_at)
                                                            <div class="modal-body p-4">
                                                                <div class="alert alert-info border-0 shadow-sm mb-3">
                                                                    <i class="fas fa-info-circle me-2"></i> You replied to
                                                                    this message on
                                                                    <strong>{{ $message->replied_at->format('M d, Y') }}</strong>
                                                                </div>
                                                                <div class="bg-light p-3 rounded-3 mb-3">
                                                                    <p class="mb-0 whitespace-pre-wrap small">
                                                                        {{ $message->reply_message }}</p>
                                                                </div>
                                                                @if ($message->reply_image)
                                                                    <div class="mt-2">
                                                                        <p class="text-muted fw-bold small mb-2">Attached
                                                                            Image:</p>
                                                                        <img src="{{ asset('images/contacts/' . $message->reply_image) }}"
                                                                            alt="Reply Image"
                                                                            class="img-fluid rounded border shadow-sm"
                                                                            style="max-height: 150px">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer border-top-0 py-3 bg-light d-flex justify-content-between"
                                                                style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                                                <form
                                                                    action="{{ route('admin.contacts.clear-reply', $message->id) }}"
                                                                    method="POST" class="d-inline delete-reply-form">
                                                                    @csrf
                                                                    <button type="button"
                                                                        class="btn btn-outline-danger rounded-pill px-4 delete-reply-btn">
                                                                        <i class="fas fa-trash-alt me-1"></i> Delete Reply
                                                                    </button>
                                                                </form>
                                                                <button type="button"
                                                                    class="btn btn-secondary rounded-pill px-4"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        @else
                                                            <form
                                                                action="{{ route('admin.contacts.reply', $message->id) }}"
                                                                method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="modal-body p-4">
                                                                    <div class="bg-light p-3 rounded-3 mb-4 border">
                                                                        <p class="mb-0 small text-muted"><strong>Message
                                                                                from {{ $message->name }}:</strong></p>
                                                                        <p class="mb-0 whitespace-pre-wrap mt-1 text-dark"
                                                                            style="font-size: 0.9rem;">
                                                                            {{ Str::limit($message->message, 150) }}</p>
                                                                    </div>

                                                                    <div class="mb-3">
                                                                        <label for="reply_message_{{ $message->id }}"
                                                                            class="form-label fw-bold small text-muted">Your
                                                                            Reply Message *</label>
                                                                        <textarea name="reply_message" id="reply_message_{{ $message->id }}" rows="4" class="form-control"
                                                                            placeholder="Type your reply here..." required style="border-radius: 8px;"></textarea>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="reply_image_{{ $message->id }}"
                                                                            class="form-label fw-bold small text-muted">Attach
                                                                            Image (Optional)</label>
                                                                        <input type="file" name="reply_image"
                                                                            id="reply_image_{{ $message->id }}"
                                                                            class="form-control" accept="image/*"
                                                                            style="border-radius: 8px;">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer border-top-0 py-3 bg-light"
                                                                    style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                                                    <button type="button"
                                                                        class="btn btn-light border rounded-pill px-4"
                                                                        data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit"
                                                                        class="btn btn-success rounded-pill px-4">
                                                                        <i class="fas fa-paper-plane me-1"></i> Send Reply
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                            No messages found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $messages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
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
            confirmDelete('Delete Reply?',
                'This will permanently delete the admin response and remove it from the customer panel.').then((
                result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
@endpush
