@extends('layouts.app')

@section('content')

    <div class="card mt-5">
        <h2 class="card-header">Color Management</h2>
        <div class="card-body">

            @session('success')
                <div class="alert alert-success" role="alert"> {{ $value }} </div>
            @endsession

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a class="btn btn-success btn-sm" href="{{ route('colors.create') }}">
                        <i class="fa fa-plus"></i> Create New Color
                    </a>
                </div>
            </div>

            <!-- Search and Filter Form -->
            <form action="{{ route('colors.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control"
                            placeholder="Search by name or hex code..." value="{{ request('search') }}">
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
                        <a href="{{ route('colors.index') }}" class="btn btn-secondary">
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
                        <th>Hex Code</th>
                        <th width="100px">Preview</th>
                        <th width="100px">Status</th>
                        <th width="250px">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($colors as $color)
                        <tr>
                            <td align="center">{{ ++$i }}</td>
                            <td>{{ $color->name }}</td>
                            <td>{{ $color->hex_code ?? '-' }}</td>
                            <td align="center">
                                @if ($color->hex_code)
                                    <div
                                        style="width: 50px; height: 30px; background-color: {{ $color->hex_code }}; border: 1px solid #ddd; border-radius: 4px;">
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            <td align="center">
                                @if ($color->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                {{--  <a class="btn btn-info btn-sm" href="{{ route('colors.show', $color->id) }}">
                                    <i class="fa-solid fa-list"></i> Show
                                </a>  --}}

                                <a class="btn btn-primary btn-sm" href="{{ route('colors.edit', $color->id) }}">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>

                                <form action="{{ route('colors.toggle-status', $color->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i
                                            class="fa-solid {{ $color->status == 'active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('colors.destroy', $color->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this color?')">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">There are no colors.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div class="d-flex justify-content-center">
                {!! $colors->links() !!}
            </div>

        </div>
    </div>
@endsection
