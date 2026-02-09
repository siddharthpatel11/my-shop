@extends('layouts.frontend')

@section('title', $page->title . ' - ' . ($layoutSettings->frontend_app_name ?? 'MyShop'))

@section('content')
    {{-- Page Header --}}
    <div class="bg-primary text-white py-5 mb-5 shadow-sm">
        <div class="container text-center">
            <h1 class="display-4 fw-bold mb-0">{{ $page->title }}</h1>
        </div>
    </div>

    {{-- Page Content --}}
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <div class="page-content lead text-secondary" style="line-height: 1.8;">
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
