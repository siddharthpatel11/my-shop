@extends('layouts.app')

@section('content')
    <div class="card mt-5">
        <h2 class="card-header">Add New Size</h2>
        <div class="card-body">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-primary btn-sm" href="{{ route('sizes.index') }}">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <form action="{{ route('sizes.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="inputName" class="form-label"><strong>Name:</strong></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="form-control @error('name') is-invalid @enderror" id="inputName"
                        placeholder="Size Name (e.g., Small, Medium, Large)">
                    @error('name')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{--  <div class="mb-3">
                    <label for="inputCode" class="form-label"><strong>Code:</strong></label>
                    <input type="text" name="code" value="{{ old('code') }}"
                        class="form-control @error('code') is-invalid @enderror" id="inputCode"
                        placeholder="Size Code (e.g., S, M, L, XL) - Optional">
                    @error('code')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>  --}}

                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-floppy-disk"></i> Submit
                </button>
            </form>

        </div>
    </div>
@endsection
