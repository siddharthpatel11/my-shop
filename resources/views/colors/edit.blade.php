@extends('products.layout')

@section('content')
    <div class="card mt-5">
        <h2 class="card-header">Edit Color</h2>
        <div class="card-body">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-primary btn-sm" href="{{ route('colors.index') }}">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>

            <form action="{{ route('colors.update', $color->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="inputName" class="form-label"><strong>Name:</strong></label>
                    <input type="text" name="name" value="{{ old('name', $color->name) }}"
                        class="form-control @error('name') is-invalid @enderror" id="inputName" placeholder="Color Name">
                    @error('name')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="inputHexCode" class="form-label"><strong>Hex Code:</strong></label>
                    <div class="input-group">
                        <input type="text" name="hex_code" value="{{ old('hex_code', $color->hex_code) }}"
                            class="form-control @error('hex_code') is-invalid @enderror" id="inputHexCode"
                            placeholder="#FF0000 (Optional)">
                        <input type="color" class="form-control form-control-color" id="colorPicker"
                            value="{{ old('hex_code', $color->hex_code ?? '#000000') }}" title="Choose color">
                    </div>
                    <small class="text-muted">Use color picker or enter hex code (e.g., #FF0000)</small>
                    @error('hex_code')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-floppy-disk"></i> Update
                </button>
            </form>

        </div>
    </div>

    <script>
        const colorPicker = document.getElementById('colorPicker');
        const hexInput = document.getElementById('inputHexCode');

        colorPicker.addEventListener('input', function() {
            hexInput.value = this.value.toUpperCase();
        });

        hexInput.addEventListener('input', function() {
            if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                colorPicker.value = this.value;
            }
        });
    </script>
@endsection
