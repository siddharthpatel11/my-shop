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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="inputPrice" class="form-label"><strong>Price:</strong></label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}"
                            class="form-control @error('price') is-invalid @enderror" id="inputPrice" placeholder="Price">
                        @error('price')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="inputStock" class="form-label"><strong>Stock:</strong></label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}"
                            class="form-control @error('stock') is-invalid @enderror" id="inputStock" placeholder="Stock">
                        @error('stock')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
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


                @if ($product->images->count() > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <label class="form-label"><strong>Existing Product Images:</strong></label>
                        </div>
                        @foreach ($product->images as $img)
                            <div class="col-md-4 col-lg-3 mb-3">
                                <div class="card shadow-sm border-0 h-100">
                                    <div class="card-img-wrapper text-center p-2 position-relative" style="background:#f8f9fa; min-height: 140px; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ asset('images/products/' . $img->image) }}"
                                            style="width:120px;height:120px;object-fit:contain;">
                                        
                                        <!-- Add More Images Button -->
                                        <div class="position-absolute bottom-0 end-0 p-1">
                                            <button type="button" class="btn btn-xs btn-success trigger-inline-file" title="Add more images for this variant">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                            <input type="file" name="existing_variant_files[{{ $img->id }}][]" class="d-none inline-image-input" accept="image/*" multiple data-img-id="{{ $img->id }}">
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        <!-- Inline Preview Container -->
                                        <div class="inline-preview-container d-flex flex-wrap gap-1 mb-2" id="inlinePreview{{ $img->id }}"></div>
                                        <div class="mb-2">
                                            <label class="small fw-bold">Color:</label>
                                            <select name="existing_image_data[{{ $img->id }}][color_id]"
                                                class="form-select form-select-sm color-selector">
                                                <option value="">No Color (General)</option>
                                                @foreach ($colors as $color)
                                                    @if (in_array($color->id, $selectedColors))
                                                        <option value="{{ $color->id }}"
                                                            {{ $img->color_id == $color->id ? 'selected' : '' }}>
                                                            {{ $color->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="small fw-bold">Size:</label>
                                            <select name="existing_image_data[{{ $img->id }}][size_id]"
                                                class="form-select form-select-sm size-selector">
                                                <option value="">No Size (General)</option>
                                                @foreach ($sizes as $size)
                                                    @if (in_array($size->id, $selectedSizes))
                                                        <option value="{{ $size->id }}"
                                                            {{ $img->size_id == $size->id ? 'selected' : '' }}>
                                                            {{ $size->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="small fw-bold">Price:</label>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text">₹</span>
                                                <input type="number" step="0.01"
                                                    name="existing_image_data[{{ $img->id }}][price]"
                                                    class="form-control" value="{{ $img->price }}"
                                                    placeholder="Price">
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="small fw-bold">Stock:</label>
                                            <input type="number" name="existing_image_data[{{ $img->id }}][stock]"
                                                class="form-control form-control-sm" value="{{ $img->stock ?? 0 }}"
                                                min="0">
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="remove_images[]" value="{{ $img->image }}"
                                                class="form-check-input" id="remove_idx_{{ $img->id }}">
                                            <label class="form-check-label text-danger small"
                                                for="remove_idx_{{ $img->id }}">Remove Image</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- ADD NEW IMAGES --}}
                <div class="mb-3">
                    <label class="form-label"><strong>Add New Images:</strong></label>

                    <div id="imageRepeater">
                        <div class="row mb-1 image-row align-items-center shadow-sm p-1 border rounded bg-light mx-0"
                            data-index="0">
                            <div class="col-md-4">
                                <label class="small fw-bold">Image(s):</label>
                                <div class="input-group input-group-sm">
                                    <button type="button" class="btn btn-success trigger-file-input" title="Browse Images">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                    <input type="file" name="image_data[0][files][]" class="form-control image-input"
                                        accept="image/*" multiple>
                                </div>
                                <div class="mt-2 preview-container d-flex flex-wrap gap-2" style="min-height: 50px; border: 1px dashed #ddd; border-radius: 5px; padding: 5px;"></div>
                            </div>

                            <div class="col-md-2 p-1">
                                <select name="image_data[0][color_id]" class="form-select form-select-sm color-selector">
                                    <option value="">No Color</option>
                                    @foreach ($colors as $color)
                                        @if (in_array($color->id, $selectedColors))
                                            <option value="{{ $color->id }}">
                                                {{ $color->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 p-1">
                                <select name="image_data[0][size_id]" class="form-select form-select-sm size-selector">
                                    <option value="">No Size</option>
                                    @foreach ($sizes as $size)
                                        @if (in_array($size->id, $selectedSizes))
                                            <option value="{{ $size->id }}">
                                                {{ $size->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 p-1">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text p-1">₹</span>
                                    <input type="number" step="0.01" name="image_data[0][price]"
                                        class="form-control" placeholder="Price">
                                </div>
                            </div>

                            <div class="col-md-1 p-1">
                                <input type="number" name="image_data[0][stock]" class="form-control form-control-sm"
                                    value="0" min="0" placeholder="Stk">
                            </div>

                            <div class="col-md-1 p-1 text-end">
                                <button type="button" class="btn btn-sm btn-danger remove-image">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="addImage" class="btn btn-secondary btn-sm mt-1">
                        <i class="fa fa-plus"></i> Add Another Image
                    </button>
                </div>

                <hr>
                <h4 class="mb-3 mt-4">SEO Meta Information</h4>
                <div class="mb-3">
                    <label for="inputSeoTitle" class="form-label"><strong>SEO Title:</strong></label>
                    <input type="text" name="seo_meta_title"
                        value="{{ old('seo_meta_title', $product->seo_meta_title) }}"
                        class="form-control @error('seo_meta_title') is-invalid @enderror" id="inputSeoTitle"
                        placeholder="SEO Title">
                </div>
                <div class="mb-3">
                    <label for="inputSeoDescription" class="form-label"><strong>SEO Description:</strong></label>
                    <textarea class="form-control @error('seo_meta_description') is-invalid @enderror" style="height:100px"
                        name="seo_meta_description" id="inputSeoDescription" placeholder="SEO Description">{{ old('seo_meta_description', $product->seo_meta_description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="inputSeoKey" class="form-label"><strong>SEO Keywords:</strong></label>
                    <input type="text" name="seo_meta_key" value="{{ old('seo_meta_key', $product->seo_meta_key) }}"
                        class="form-control @error('seo_meta_key') is-invalid @enderror" id="inputSeoKey"
                        placeholder="SEO Keywords">
                </div>
                <div class="mb-3">
                    <label for="inputSeoCanonical" class="form-label"><strong>SEO Canonical URL:</strong></label>
                    <input type="text" name="seo_canonical"
                        value="{{ old('seo_canonical', $product->seo_canonical) }}"
                        class="form-control @error('seo_canonical') is-invalid @enderror" id="inputSeoCanonical"
                        placeholder="Canonical URL">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>SEO Image:</strong></label>
                    <div class="row mb-2 seo-image-row">
                        <div class="col-md-5">
                            <input type="file" name="seo_meta_image"
                                class="form-control seo-image-input @error('seo_meta_image') is-invalid @enderror"
                                accept="image/*">
                            @if ($product->seo_meta_image)
                                <div class="mt-2 text-center" style="width:max-content;">
                                    <img src="{{ asset('images/products/' . $product->seo_meta_image) }}" alt="SEO Image"
                                        style="width:80px;height:80px;object-fit:cover; border-radius:5px; border:1px solid #ddd;">
                                    <div class="form-check mt-1 text-start">
                                        <input type="checkbox" name="remove_seo_image" value="1"
                                            class="form-check-input" id="removeExistingSeoImage">
                                        <label class="form-check-label" for="removeExistingSeoImage">Remove
                                            Existing</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-5">
                            <img class="seo-img-preview d-none"
                                style="width:80px;height:80px;object-fit:cover; border:1px solid #ddd;border-radius:5px;">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-seo-image d-none">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <hr>
                <h4 class="mb-3 mt-4">Open Graph (OG) Meta Information</h4>
                <div class="mb-3">
                    <label for="inputOgTitle" class="form-label"><strong>OG Title:</strong></label>
                    <input type="text" name="og_meta_title"
                        value="{{ old('og_meta_title', $product->og_meta_title) }}"
                        class="form-control @error('og_meta_title') is-invalid @enderror" id="inputOgTitle"
                        placeholder="OG Title">
                </div>
                <div class="mb-3">
                    <label for="inputOgDescription" class="form-label"><strong>OG Description:</strong></label>
                    <textarea class="form-control @error('og_meta_description') is-invalid @enderror" style="height:100px"
                        name="og_meta_description" id="inputOgDescription" placeholder="OG Description">{{ old('og_meta_description', $product->og_meta_description) }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="inputOgKey" class="form-label"><strong>OG Keywords:</strong></label>
                    <input type="text" name="og_meta_key" value="{{ old('og_meta_key', $product->og_meta_key) }}"
                        class="form-control @error('og_meta_key') is-invalid @enderror" id="inputOgKey"
                        placeholder="OG Keywords">
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>OG Image:</strong></label>
                    <div class="row mb-2 og-image-row">
                        <div class="col-md-5">
                            <input type="file" name="og_meta_image"
                                class="form-control og-image-input @error('og_meta_image') is-invalid @enderror"
                                accept="image/*">
                            @if ($product->og_meta_image)
                                <div class="mt-2 text-center" style="width:max-content;">
                                    <img src="{{ asset('images/products/' . $product->og_meta_image) }}" alt="OG Image"
                                        style="width:80px;height:80px;object-fit:cover; border-radius:5px; border:1px solid #ddd;">
                                    <div class="form-check mt-1 text-start">
                                        <input type="checkbox" name="remove_og_image" value="1"
                                            class="form-check-input" id="removeExistingOgImage">
                                        <label class="form-check-label" for="removeExistingOgImage">Remove
                                            Existing</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-5">
                            <img class="og-img-preview d-none"
                                style="width:80px;height:80px;object-fit:cover; border:1px solid #ddd;border-radius:5px;">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-og-image d-none">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="mb-4">

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
            // Initialize Select2
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


            // Update color options in all selectors when main color select changes
            $('#inputColor').on('change', function() {
                let selectedOptions = $(this).select2('data');
                let colorOptions = '<option value="">No Color (General)</option>';

                selectedOptions.forEach(function(opt) {
                    colorOptions += `<option value="${opt.id}">${opt.text}</option>`;
                });

                $('.color-selector').each(function() {
                    let currentVal = $(this).val();
                    $(this).html(colorOptions);
                    if ($(this).find(`option[value="${currentVal}"]`).length > 0) {
                        $(this).val(currentVal);
                    }
                });
            });

            // Update size options in all selectors when main size select changes
            $('#inputSize').on('change', function() {
                let selectedOptions = $(this).select2('data');
                let sizeOptions = '<option value="">No Size</option>';
                selectedOptions.forEach(opt => sizeOptions +=
                    `<option value="${opt.id}">${opt.text}</option>`);

                $('.size-selector').each(function() {
                    let currentVal = $(this).val();
                    $(this).html(sizeOptions);
                    if ($(this).find(`option[value="${currentVal}"]`).length > 0) $(this).val(
                        currentVal);
                });
            });

            // Update color options in all selectors when main color select changes
            $('#inputColor').on('change', function() {
                let selectedOptions = $(this).select2('data');
                let colorOptions = '<option value="">No Color</option>';
                selectedOptions.forEach(opt => colorOptions +=
                    `<option value="${opt.id}">${opt.text}</option>`);

                $('.color-selector').each(function() {
                    let currentVal = $(this).val();
                    $(this).html(colorOptions);
                    if ($(this).find(`option[value="${currentVal}"]`).length > 0) $(this).val(
                        currentVal);
                });
            });

            // Clone Row inside each row (Plus Button)
            $(document).on('click', '.clone-row', function() {
                $('#addImage').click();
            });

            // Add More Image (Smart Clone)
            let rowIndex = 1;
            $('#addImage').on('click', function() {
                let lastRow = $('.image-row').last();
                let newRow = lastRow.clone();
                let idx = rowIndex++;

                newRow.attr('data-index', idx);

                // Update names with unique index
                newRow.find('.image-input').attr('name', `image_data[${idx}][files][]`).val('');
                newRow.find('.color-selector').attr('name', `image_data[${idx}][color_id]`).val(lastRow
                    .find('.color-selector').val());
                newRow.find('.size-selector').attr('name', `image_data[${idx}][size_id]`).val(lastRow.find(
                    '.size-selector').val());
                newRow.find('input[placeholder="Price"]').attr('name', `image_data[${idx}][price]`).val(
                    lastRow.find('input[placeholder="Price"]').val());
                newRow.find('input[min="0"]').attr('name', `image_data[${idx}][stock]`).val(lastRow.find(
                    'input[min="0"]').val());

                // Clear previews
                newRow.find('.preview-container').html('');
                newRow.find('.remove-image').removeClass('d-none');

                $('#imageRepeater').append(newRow);
            });

            $(document).on('click', '.remove-image', function() {
                if ($('.image-row').length > 1) {
                    $(this).closest('.image-row').remove();
                }
            });

            // Trigger file input via (+) button
            $(document).on('click', '.trigger-file-input', function() {
                $(this).next('.image-input').click();
            });

            const rowFiles = {}; // Stores DataTransfer objects for each row index

            $(document).on('change', '.image-input', function() {
                let input = this;
                let files = input.files;
                if (!files || files.length === 0) return;

                let row = $(input).closest('.image-row');
                let idx = row.attr('data-index');

                if (!rowFiles[idx]) {
                    rowFiles[idx] = new DataTransfer();
                }

                // Add newly selected files to our persistent DataTransfer
                Array.from(files).forEach(file => {
                    rowFiles[idx].items.add(file);
                });

                // Update the actual input.files so the form sends everything
                input.files = rowFiles[idx].files;
                
                renderRowGallery(row, idx);
            });

            $(document).on('click', '.remove-thumbnail', function() {
                let thumb = $(this);
                let row = thumb.closest('.image-row');
                let idx = row.attr('data-index');
                let fileIdx = thumb.attr('data-file-index');

                if (rowFiles[idx]) {
                    let dt = new DataTransfer();
                    let files = rowFiles[idx].files;
                    for (let i = 0; i < files.length; i++) {
                        if (i != fileIdx) {
                            dt.items.add(files[i]);
                        }
                    }
                    rowFiles[idx] = dt;
                    row.find('.image-input')[0].files = dt.files;
                    renderRowGallery(row, idx);
                }
            });

            /* ================= INLINE IMAGE ADDITION (FOR EXISTING VARIANTS) ================= */

            $(document).on('click', '.trigger-inline-file', function() {
                $(this).next('.inline-image-input').click();
            });

            const inlineFiles = {}; // DataTransfer for each existing image ID

            $(document).on('change', '.inline-image-input', function() {
                let input = this;
                let files = input.files;
                if (!files || files.length === 0) return;

                let imgId = $(input).data('img-id');

                if (!inlineFiles[imgId]) {
                    inlineFiles[imgId] = new DataTransfer();
                }

                Array.from(files).forEach(file => {
                    inlineFiles[imgId].items.add(file);
                });

                input.files = inlineFiles[imgId].files;
                renderInlineGallery(imgId);
            });

            function renderInlineGallery(imgId) {
                let container = $(`#inlinePreview${imgId}`);
                container.html('');
                let files = inlineFiles[imgId].files;

                Array.from(files).forEach((file, fIdx) => {
                    let reader = new FileReader();
                    reader.onload = e => {
                        container.append(`
                            <div class="position-relative me-1 mb-1" style="cursor:pointer;" title="Remove" onclick="removeInlineThumb(${imgId}, ${fIdx})">
                                <img src="${e.target.result}" style="width:35px;height:35px;object-fit:cover;border:1px solid #28a745;border-radius:3px;">
                                <div class="position-absolute top-0 end-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:12px;height:12px;font-size:8px;margin-top:-3px;margin-right:-3px;">
                                    <i class="fa fa-times"></i>
                                </div>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(file);
                });
            }

            window.removeInlineThumb = function(imgId, fIdx) {
                if (inlineFiles[imgId]) {
                    let dt = new DataTransfer();
                    let files = inlineFiles[imgId].files;
                    for (let i = 0; i < files.length; i++) {
                        if (i != fIdx) dt.items.add(files[i]);
                    }
                    inlineFiles[imgId] = dt;
                    let input = document.querySelector(`.inline-image-input[data-img-id="${imgId}"]`);
                    if(input) input.files = dt.files;
                    renderInlineGallery(imgId);
                }
            };

            /* ================= PREVENT DUPLICATE VARIANTS ================= */

            function checkDuplicateVariant() {
                // Get all existing color-size combinations
                let existingVariants = [];
                $('.card-body').each(function() {
                    let c = $(this).find('.color-selector').val();
                    let s = $(this).find('.size-selector').val();
                    if (c || s) existingVariants.push(`${c}-${s}`);
                });

                // Check new rows
                $('.image-row').each(function() {
                    let row = $(this);
                    let c = row.find('.color-selector').val();
                    let s = row.find('.size-selector').val();
                    let combo = `${c}-${s}`;

                    if (existingVariants.includes(combo) && (c || s)) {
                        row.addClass('border-warning').css('background', '#fff3cd');
                        if (!row.find('.dup-warning').length) {
                            row.find('.col-md-4').append('<div class="dup-warning text-warning small fw-bold">Note: This variant matches an existing one above. Add images directly above for best results!</div>');
                        }
                    } else {
                        row.removeClass('border-warning').css('background', '');
                        row.find('.dup-warning').remove();
                    }
                });
            }

            $(document).on('change', '.color-selector, .size-selector', function() {
                checkDuplicateVariant();
            });

            // Initial check
            checkDuplicateVariant();

            function renderRowGallery(row, idx) {
                let container = row.find('.preview-container');
                container.html('');
                
                if (rowFiles[idx] && rowFiles[idx].files.length > 0) {
                    Array.from(rowFiles[idx].files).forEach((file, fIdx) => {
                        let reader = new FileReader();
                        reader.onload = e => {
                            container.append(`
                                <div class="position-relative remove-thumbnail me-2 mb-2" data-file-index="${fIdx}" style="cursor:pointer;" title="Click to remove">
                                    <img src="${e.target.result}" style="width:50px;height:50px;object-fit:cover;border:1px solid #ddd;border-radius:5px;">
                                    <div class="position-absolute top-0 end-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:15px;height:15px;font-size:10px;margin-top:-5px;margin-right:-5px;">
                                        <i class="fa fa-times"></i>
                                    </div>
                                </div>
                            `);
                        };
                        reader.readAsDataURL(file);
                    });
                }
            }

            function updateRemoveButtons() {
                if ($('.image-row').length > 1) {
                    $('.remove-image').removeClass('d-none');
                } else {
                    $('.remove-image').addClass('d-none');
                }
            }
            updateRemoveButtons();

            /* ================= REAL-TIME NAME VALIDATION ================= */

            let timeout = null;
            $('#inputName').on('keyup input', function() {
                clearTimeout(timeout);
                let name = $(this).val();
                let input = $(this);
                let btn = $('button[type="submit"]');

                if (name.length < 1) {
                    input.removeClass('is-invalid');
                    input.next('.form-text.text-danger').remove();
                    btn.prop('disabled', false);
                    return;
                }

                timeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('products.check-name') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            name: name,
                            id: "{{ $product->id }}"
                        },
                        success: function(response) {
                            input.next('.form-text.text-danger').remove();
                            if (response.exists) {
                                input.addClass('is-invalid');
                                input.after(
                                    `<div class="form-text text-danger">${response.message}</div>`
                                );
                                btn.prop('disabled', true);
                            } else {
                                input.removeClass('is-invalid');
                                btn.prop('disabled', false);
                            }
                        }
                    });
                }, 500);
            });

            /* ================= SEO AND OG IMAGE PREVIEW ================= */
            $(document).on('change', '.seo-image-input', function() {
                let preview = $(this).closest('.seo-image-row').find('.seo-img-preview');
                let removeBtn = $(this).closest('.seo-image-row').find('.remove-seo-image');

                if (this.files && this.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        preview.attr('src', e.target.result).removeClass('d-none');
                        removeBtn.removeClass('d-none');
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    preview.addClass('d-none').attr('src', '');
                    removeBtn.addClass('d-none');
                }
            });

            $(document).on('click', '.remove-seo-image', function() {
                let row = $(this).closest('.seo-image-row');
                row.find('.seo-image-input').val('');
                row.find('.seo-img-preview').addClass('d-none').attr('src', '');
                $(this).addClass('d-none');
            });

            $(document).on('change', '.og-image-input', function() {
                let preview = $(this).closest('.og-image-row').find('.og-img-preview');
                let removeBtn = $(this).closest('.og-image-row').find('.remove-og-image');

                if (this.files && this.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        preview.attr('src', e.target.result).removeClass('d-none');
                        removeBtn.removeClass('d-none');
                    };
                    reader.readAsDataURL(this.files[0]);
                } else {
                    preview.addClass('d-none').attr('src', '');
                    removeBtn.addClass('d-none');
                }
            });

            $(document).on('click', '.remove-og-image', function() {
                let row = $(this).closest('.og-image-row');
                row.find('.og-image-input').val('');
                row.find('.og-img-preview').addClass('d-none').attr('src', '');
                $(this).addClass('d-none');
            });

        });
    </script>
@endpush
