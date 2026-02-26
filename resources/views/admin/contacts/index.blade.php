@extends('layouts.app')

@section('title', 'Contact Messages')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-envelope me-2"></i> Contact Messages
                    </h5>
                </div>
                <div class="card-body">
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
                                    <td>{{ $loop->iteration + ($messages->currentPage() - 1) * $messages->perPage() }}</td>
                                    <td>{{ $message->name }}</td>
                                    <td>{{ $message->email }}</td>
                                    <td>{{ $message->number }}</td>
                                    <td>{{ $message->created_at->format('d M, Y h:i A') }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.contacts.show', $message->id) }}" class="btn btn-sm btn-light text-primary border shadow-sm" title="View Detail" style="border-radius: 8px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.contacts.destroy', $message->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-light text-danger border shadow-sm delete-btn" title="Delete" style="border-radius: 8px; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
</script>
@endpush
