@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-file-alt me-2"></i>Manage Pages</h2>
            <a href="{{ route('pages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Create New Page
            </a>
        </div>

        {{-- SweetAlert handles session notifications globally --}}

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pages as $page)
                                <tr>
                                    <td>
                                        <strong>{{ $page->title }}</strong>
                                        <div class="small text-muted">Slug: {{ $page->slug }}</div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" type="checkbox"
                                                data-id="{{ $page->slug }}"
                                                {{ $page->status === 'active' ? 'checked' : '' }} style="cursor: pointer;">
                                            <span
                                                class="badge bg-{{ $page->status === 'active' ? 'success' : 'secondary' }} status-badge">
                                                {{ ucfirst($page->status ?? 'Active') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ $page->url }}" target="_blank" class="btn btn-sm btn-info"
                                                title="View Page">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('pages.edit', $page) }}" class="btn btn-sm btn-warning"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('pages.destroy', $page) }}" method="POST"
                                                class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No pages found. Create your first page!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($pages->hasPages())
                    <div class="mt-3">
                        {{ $pages->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Confirm delete
            document.querySelectorAll('.delete-form').forEach(form => {
                form.querySelector('button[type="submit"]').type = 'button';
                form.querySelector('button[type="button"]').addEventListener('click', function(e) {
                    confirmDelete('Are you sure?', 'You want to delete this page?').then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Status toggle
            document.querySelectorAll('.status-toggle').forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const slug = this.dataset.id;
                    const badge = this.parentElement.querySelector('.status-badge');

                    fetch(`/pages/${slug}/toggle-status`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(
                                    1);
                                badge.className =
                                    `badge status-badge bg-${data.status === 'active' ? 'success' : 'secondary'}`;
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    text: 'Error updating status'
                                });
                                this.checked = !this.checked;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                text: 'Something went wrong'
                            });
                            this.checked = !this.checked;
                        });
                });
            });
        </script>
    @endpush
@endsection
