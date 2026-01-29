@extends('layouts.app')

@section('content')

    <div class="card mt-5">
        <h2 class="card-header">Size Management</h2>
        <div class="card-body">

            @session('success')
                <div class="alert alert-success" role="alert"> {{ $value }} </div>
            @endsession

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a class="btn btn-success btn-sm" href="{{ route('sizes.create') }}">
                        <i class="fa fa-plus"></i> Create New Size
                    </a>
                </div>
            </div>

            <form action="{{ route('sizes.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or code..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="{{ route('sizes.index') }}" class="btn btn-secondary">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-striped mt-4">
                <thead>
                    <tr>
                        <th width="80px">No</th>
                        <th>Name</th>
                        {{--  <th>Code</th>  --}}
                        <th width="100px">Status</th>
                        <th width="250px">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($sizes as $size)
                        <tr>
                            <td align="center">{{ ++$i }}</td>
                            <td>{{ $size->name }}</td>
                            {{--  <td>{{ $size->code ?? '-' }}</td>  --}}
                            <td align="center">
                                @if ($size->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                {{--  <a class="btn btn-info btn-sm" href="{{ route('sizes.show', $size->id) }}">
                                    <i class="fa-solid fa-list"></i> Show
                                </a>  --}}

                                <a class="btn btn-primary btn-sm" href="{{ route('sizes.edit', $size->id) }}">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>

                                <form action="{{ route('sizes.toggle-status', $size->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i
                                            class="fa-solid {{ $size->status == 'active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('sizes.destroy', $size->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this size?')">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">There are no sizes.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div class="d-flex justify-content-center">
                {!! $sizes->links() !!}
            </div>

        </div>
    </div>
@endsection
