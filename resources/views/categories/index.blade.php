@extends('layouts.app')

@section('content')

    <div class="card mt-5">
        <h2 class="card-header">Category Management</h2>
        <div class="card-body">

            @session('success')
                <div class="alert alert-success" role="alert"> {{ $value }} </div>
            @endsession

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a class="btn btn-success btn-sm" href="{{ route('categories.create') }}">
                        <i class="fa fa-plus"></i> Create New Category
                    </a>
                </div>
            </div>

            <form action="{{ route('categories.index') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Search by name..."
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
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
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
                        {{--  <th>Description</th>  --}}
                        <th width="100px">Status</th>
                        <th width="250px">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td align="center">{{ ++$i }}</td>
                            <td>{{ $category->name }}</td>
                            {{--  <td>{{ Str::limit($category->description, 50) ?? '-' }}</td>  --}}
                            <td align="center">
                                @if ($category->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                {{--  <a class="btn btn-info btn-sm" href="{{ route('categories.show', $category->id) }}">
                                    <i class="fa-solid fa-list"></i> Show
                                </a>  --}}

                                <a class="btn btn-primary btn-sm" href="{{ route('categories.edit', $category->id) }}">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>

                                <form action="{{ route('categories.toggle-status', $category->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i
                                            class="fa-solid {{ $category->status == 'active' ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">There are no categories.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div class="d-flex justify-content-center">
                {!! $categories->links() !!}
            </div>

        </div>
    </div>
@endsection
