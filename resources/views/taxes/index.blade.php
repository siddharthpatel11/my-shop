@extends('layouts.app')

@section('title', 'Tax Management')

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1">Tax Management</h2>
                <p class="text-muted mb-0">Manage tax rates for your products</p>
            </div>
            <a href="{{ route('taxes.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Add New Tax
            </a>
        </div>

        {{-- SweetAlert handles session notifications globally --}}

        {{-- Taxes Table --}}
        <div class="card shadow-sm border-0">
            <div class="card-body">

                @if ($taxes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tax Name</th>
                                    <th>Rate (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($taxes as $tax)
                                    <tr>
                                        <td>{{ $tax->id }}</td>
                                        <td>
                                            <strong>{{ $tax->name }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark fs-6">
                                                {{ number_format($tax->rate, 2) }}%
                                            </span>
                                        </td>
                                        {{--  <td>
                                            <div class="form-check form-switch">
                                                <span
                                                    class="badge {{ $tax->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ ucfirst($tax->status) }}
                                                </span>
                                                </label>
                                            </div>
                                        </td>  --}}
                                        {{--  <td class="text-end">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('taxes.edit', $tax) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('taxes.destroy', $tax) }}" method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this tax?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>  --}}
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $taxes->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-calculator display-1 text-muted"></i>
                        <h5 class="mt-3 text-muted">No Taxes Found</h5>
                        <p class="text-muted">Start by adding your first tax rate</p>
                        <a href="{{ route('taxes.create') }}" class="btn btn-primary mt-2">
                            <i class="bi bi-plus-circle me-2"></i>Add New Tax
                        </a>
                    </div>
                @endif

            </div>
        </div>

    </div>

    @push('scripts')
        <script>
            function toggleStatus(taxId) {
                const checkbox = document.getElementById(`status${taxId}`);
                const originalState = checkbox.checked;

                fetch(`/taxes/${taxId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload page to update badge color
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: 'Failed to update status'
                            });
                            checkbox.checked = !originalState;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            text: 'An error occurred'
                        });
                        checkbox.checked = !originalState;
                    });
            }
        </script>
    @endpush
@endsection
