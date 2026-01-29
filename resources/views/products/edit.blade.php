@extends('layouts.app')

@php
    $selectedSizes = $product->size_id ? explode(',', $product->size_id) : [];
    $selectedColors = $product->color_id ? explode(',', $product->color_id) : [];
@endphp

@section('content')
    <div class="card mt-5">
        <h2 class="card-header">Edit Product</h2>
        <div class="card-body">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-primary btn-sm" href="{{ route('products.index') }}"><i class="fa fa-arrow-left"></i>
                    Back</a>
            </div>

            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="inputName" class="form-label"><strong>Name:</strong></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                        class="form-control @error('name') is-invalid @enderror" id="inputName" placeholder="Name">
                    @error('name')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="inputCategory" class="form-label"><strong>Category:</strong></label>
                    <select name="category_id" id="inputCategory"
                        class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="inputPrice" class="form-label"><strong>Price:</strong></label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}"
                        class="form-control @error('price') is-invalid @enderror" id="inputPrice" placeholder="Price">
                    @error('price')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="inputSize" class="form-label"><strong>Size:</strong></label>

                        <select name="size_id[]" id="inputSize"
                            class="form-select select2 @error('size_id') is-invalid @enderror" multiple>

                            @foreach ($sizes as $size)
                                <option value="{{ $size->id }}"
                                    {{ in_array($size->id, old('size_id', $selectedSizes)) ? 'selected' : '' }}>
                                    {{ $size->name }} {{ $size->code ? '(' . $size->code . ')' : '' }}
                                </option>
                            @endforeach
                        </select>

                        @error('size_id')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="col-md-6 mb-3">
                        <label for="inputColor" class="form-label"><strong>Color:</strong></label>

                        <select name="color_id[]" id="inputColor"
                            class="form-select select2 @error('color_id') is-invalid @enderror" multiple>

                            @foreach ($colors as $color)
                                <option value="{{ $color->id }}" data-color="{{ $color->hex_code }}"
                                    {{ in_array($color->id, old('color_id', $selectedColors)) ? 'selected' : '' }}>
                                    {{ $color->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('color_id')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="mb-3">
                    <label for="inputDetail" class="form-label"><strong>Detail:</strong></label>
                    <textarea class="form-control @error('detail') is-invalid @enderror" style="height:150px" name="detail"
                        id="inputDetail" placeholder="Detail">{{ old('detail', $product->detail) }}</textarea>
                    @error('detail')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                @php
                    $existingImages = $product->image ? explode(',', $product->image) : [];
                @endphp

                {{--  @if ($existingImages)
                    <div class="row mb-3">
                        @foreach ($existingImages as $img)
                            <div class="col-md-3 text-center mb-2">
                                <img src="{{ asset('images/products/' . $img) }}"
                                    style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd;border-radius:5px;">
                                <div class="form-check mt-1">
                                    <input type="checkbox" name="remove_images[]" value="{{ $img }}"
                                        class="form-check-input">
                                    <label class="form-check-label">Remove</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif  --}}


                @if ($existingImages)
                    <div class="row mb-3">
                        @foreach ($existingImages as $img)
                            <div class="col-md-3 text-center mb-2">
                                <img src="{{ asset('images/products/' . $img) }}"
                                    style="width:100px;height:100px;object-fit:cover;border:1px solid #ddd">
                                <div class="form-check mt-1">
                                    <input type="checkbox" name="remove_images[]" value="{{ $img }}"
                                        class="form-check-input">
                                    <label class="form-check-label">Remove</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- ADD NEW IMAGES --}}
                <div class="mb-3">
                    <label class="form-label"><strong>Add New Images:</strong></label>

                    <div id="imageRepeater">
                        <div class="row mb-2 image-row">
                            <div class="col-md-5">
                                <input type="file" name="images[]" class="form-control image-input" accept="image/*">
                            </div>
                            <div class="col-md-5">
                                <img class="img-preview d-none"
                                    style="width:80px;height:80px;object-fit:cover;border:1px solid #ddd">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger remove-image d-none">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addImage" class="btn btn-secondary btn-sm mt-2">
                        <i class="fa fa-plus"></i> Add Image
                    </button>
                </div>

                <input type="hidden" name="status" value="{{ old('status', $product->status) }}">

                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Update</button>
            </form>

        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            /* ========== SELECT2 ========== */

            $('#inputSize').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select size',
                width: '100%'
            });

            $('#inputColor').select2({
                theme: 'bootstrap-5',
                placeholder: 'Select color',
                width: '100%',
                templateResult: formatColor,
                templateSelection: formatColor
            });

            function formatColor(color) {
                if (!color.id) return color.text;

                let hex = $(color.element).data('color');
                if (!hex) return color.text;

                return $(`
            <span>
                <span style="
                    display:inline-block;
                    width:15px;
                    height:15px;
                    background:${hex};
                    border:1px solid #ddd;
                    border-radius:3px;
                    margin-right:6px;
                "></span>
                ${color.text}
            </span>
        `);
            }

            /* ========== IMAGE REPEATER ========== */

            $('#addImage').on('click', function() {
                let row = `
        <div class="row mb-2 image-row">
            <div class="col-md-5">
                <input type="file" name="images[]"
                    class="form-control image-input"
                    accept="image/*">
            </div>

            <div class="col-md-5">
                <img src="" class="img-preview d-none"
                    style="width:80px;height:80px;object-fit:cover;
                           border:1px solid #ddd;border-radius:5px;">
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-image">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>`;
                $('#imageRepeater').append(row);
            });

            $(document).on('click', '.remove-image', function() {
                $(this).closest('.image-row').remove();
            });

            $(document).on('change', '.image-input', function() {
                let preview = $(this).closest('.image-row').find('.img-preview');
                let removeBtn = $(this).closest('.image-row').find('.remove-image');

                if (this.files && this.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        preview.attr('src', e.target.result)
                            .removeClass('d-none');
                        removeBtn.removeClass('d-none');
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });

        });
    </script>
@endpush
