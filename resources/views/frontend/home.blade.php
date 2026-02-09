@extends('layouts.frontend.app')

@section('title', 'Home')

@section('content')
    <div class="container my-5 text-center">
        <h1 class="fw-bold">Welcome to MyShop</h1>
        <p class="text-muted">Browse our latest products</p>

        <a href="{{ route('frontend.products.index') }}" class="btn btn-primary mt-3">
            View Products
        </a>
    </div>
@endsection
