@extends('layouts.app')

@section('title', 'Manage Banners')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h2 class="mb-0">
            <i class="fas fa-images text-primary me-2"></i> Promotional Banners
        </h2>
        <p class="text-muted mt-2">Manage the promotional banners that appear on the storefront dashboard.</p>
    </div>
    <div class="col-md-6 text-md-end text-start mt-3 mt-md-0">
        <a href="{{ route('admin.banners.create') }}" class="btn btn-primary px-4 rounded-pill shadow-sm">
            <i class="fas fa-plus me-2"></i> Add New Banner
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Preview</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                        <tr>
                            <td class="ps-4" style="width: 150px;">
                                <div class="rounded p-2 text-center" style="background: {{ $banner->background_color }}; width: 120px; height: 60px;">
                                    @if($banner->image)
                                        <img src="{{ asset('images/banners/' . $banner->image) }}" alt="Preview" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                    @else
                                        <i class="fas fa-image fa-2x opacity-25 {{ $banner->text_color }}"></i>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-1 fw-bold">{{ $banner->title }}</h6>
                                <p class="mb-0 small text-muted">{{ $banner->subtitle ?? 'No subtitle' }}</p>
                                <div class="mt-1 small">
                                    <span class="badge bg-light text-dark shadow-sm">Order: {{ $banner->order }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch ms-2">
                                    <input class="form-check-input status-toggle" type="checkbox" role="switch"
                                        id="flexSwitchCheckDefault{{ $banner->id }}" data-id="{{ $banner->id }}"
                                        {{ $banner->status == 'active' ? 'checked' : '' }}>
                                    <label class="form-check-label ms-1" for="flexSwitchCheckDefault{{ $banner->id }}">
                                        <span id="status-label-{{ $banner->id }}" class="badge {{ $banner->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($banner->status) }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td class="text-center pe-4">
                                <div class="btn-group shadow-sm rounded-pill">
                                    <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline" id="delete-form-{{ $banner->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $banner->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                                <h5>No Banners Found</h5>
                                <p>You haven't created any promotional banners yet.</p>
                                <a href="{{ route('admin.banners.create') }}" class="btn btn-sm btn-outline-primary mt-2">Create First Banner</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($banners->hasPages())
        <div class="card-footer bg-white border-top custom-pagination">
            {{ $banners->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Status Toggle
    document.querySelectorAll('.status-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            let bannerId = this.getAttribute('data-id');
            let isChecked = this.checked;
            let label = document.getElementById('status-label-' + bannerId);

            fetch(`/admin/banners/${bannerId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    label.textContent = isChecked ? 'Active' : 'Inactive';
                    label.className = 'badge ' + (isChecked ? 'bg-success' : 'bg-secondary');
                    // Optional toast
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Status updated',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Delete Confirmation
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            let id = this.getAttribute('data-id');
            confirmDelete().then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            });
        });
    });
</script>
@endpush
