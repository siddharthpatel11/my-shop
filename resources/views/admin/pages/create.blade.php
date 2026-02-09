@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-plus me-2"></i>Create New Page
                        </h4>
                    </div>

                    <div class="card-body">
                        @include('admin.pages.form', [
                            'page' => null,
                            'action' => route('pages.store'),
                            'method' => 'POST',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
