@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Edit Page: {{ $page->title }}
                        </h4>
                    </div>

                    <div class="card-body">
                        @include('admin.pages.form', [
                            'page' => $page,
                            'action' => route('pages.update', $page),
                            'method' => 'PUT',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
