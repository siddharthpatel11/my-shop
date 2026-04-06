@extends('layouts.app')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            border-radius: 0.375rem;
        }
    </style>
@endpush

@section('content')
    <div class="card mt-5">
        <h2 class="card-header">Add New Product</h2>
        <div class="card-body">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-primary btn-sm" href="{{ route('products.index') }}"><i class="fa fa-arrow-left"></i>
                    Back</a>
            </div>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="inputName" class="form-label"><strong>Name:</strong></label>
                    <input type="text" name="name" value="{{ old('name') }}"
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
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}"
                            class="form-control @error('price') is-invalid @enderror" id="inputPrice" placeholder="Price">
                        @error('price')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="inputStock" class="form-label"><strong>Stock:</strong></label>
                        <input type="number" name="stock" value="{{ old('stock', 0) }}"
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
                                    {{ in_array($size->id, old('size_id', [])) ? 'selected' : '' }}>
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
                                    {{ in_array($color->id, old('color_id', [])) ? 'selected' : '' }}>
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
                        id="inputDetail" placeholder="Detail">{{ old('detail') }}</textarea>
                    @error('detail')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Product Images:</strong></label>

                    <div id="imageRepeater">
                        <div class="row mb-2 image-row align-items-end shadow-sm p-2 border rounded bg-light mx-0"
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
                                <div class="mt-2 preview-container d-flex flex-wrap gap-2"
                                    style="min-height: 50px; border: 1px dashed #ddd; border-radius: 5px; padding: 5px;">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="small fw-bold">Color:</label>
                                <select name="image_data[0][color_id]" class="form-select color-selector">
                                    <option value="">No Color</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="small fw-bold">Size:</label>
                                <select name="image_data[0][size_id]" class="form-select size-selector">
                                    <option value="">No Size</option>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="small fw-bold">Price (Optional):</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" name="image_data[0][price]"
                                        class="form-control" placeholder="Price">
                                </div>
                            </div>

                            <div class="col-md-1">
                                <label class="small fw-bold">Stock:</label>
                                <input type="number" name="image_data[0][stock]" class="form-control form-control-sm"
                                    value="0" min="0">
                            </div>

                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-danger remove-image">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" id="addImage" class="btn btn-sm btn-primary">
                            <i class="fa fa-plus"></i> Add More Image
                        </button>
                    </div>
                </div>

                <hr>
                <h4 class="mb-3 mt-4">SEO Meta Information</h4>
                <div class="mb-3">
                    <label for="inputSeoTitle" class="form-label"><strong>SEO Title:</strong></label>
                    <input type="text" name="seo_meta_title" value="{{ old('seo_meta_title') }}"
                        class="form-control @error('seo_meta_title') is-invalid @enderror" id="inputSeoTitle"
                        placeholder="SEO Title">
                </div>
                <div class="mb-3">
                    <label for="inputSeoDescription" class="form-label"><strong>SEO Description:</strong></label>
                    <textarea class="form-control @error('seo_meta_description') is-invalid @enderror" style="height:100px"
                        name="seo_meta_description" id="inputSeoDescription" placeholder="SEO Description">{{ old('seo_meta_description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="inputSeoKey" class="form-label"><strong>SEO Keywords:</strong></label>
                    <input type="text" name="seo_meta_key" value="{{ old('seo_meta_key') }}"
                        class="form-control @error('seo_meta_key') is-invalid @enderror" id="inputSeoKey"
                        placeholder="SEO Keywords">
                </div>
                <div class="mb-3">
                    <label for="inputSeoCanonical" class="form-label"><strong>SEO Canonical URL:</strong></label>
                    <input type="text" name="seo_canonical" value="{{ old('seo_canonical') }}"
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
                        </div>
                        <div class="col-md-5">
                            <img src="" class="seo-img-preview d-none"
                                style="width:80px;height:80px;object-fit:cover;border:1px solid #ddd;border-radius:5px;">
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
                    <input type="text" name="og_meta_title" value="{{ old('og_meta_title') }}"
                        class="form-control @error('og_meta_title') is-invalid @enderror" id="inputOgTitle"
                        placeholder="OG Title">
                </div>
                <div class="mb-3">
                    <label for="inputOgDescription" class="form-label"><strong>OG Description:</strong></label>
                    <textarea class="form-control @error('og_meta_description') is-invalid @enderror" style="height:100px"
                        name="og_meta_description" id="inputOgDescription" placeholder="OG Description">{{ old('og_meta_description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="inputOgKey" class="form-label"><strong>OG Keywords:</strong></label>
                    <input type="text" name="og_meta_key" value="{{ old('og_meta_key') }}"
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
                        </div>
                        <div class="col-md-5">
                            <img src="" class="og-img-preview d-none"
                                style="width:80px;height:80px;object-fit:cover;border:1px solid #ddd;border-radius:5px;">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger remove-og-image d-none">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="mb-4">

                <input type="hidden" name="status" value="active">

                <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Submit</button>
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
                return $(
                    `<span><span style="display:inline-block;width:15px;height:15px;background:${hex};border:1px solid #ddd;border-radius:3px;margin-right:6px;"></span>${color.text}</span>`
                );
            }

            // Sync selectors in repeater when main selects change
            $('#inputSize').on('change', function() {
                let sizeOptions = '<option value="">No Size</option>';
                $(this).select2('data').forEach(opt => sizeOptions +=
                    `<option value="${opt.id}">${opt.text}</option>`);
                $('.size-selector').each(function() {
                    let val = $(this).val();
                    $(this).html(sizeOptions).val($(this).find(`option[value="${val}"]`).length ?
                        val : '');
                });
            });

            $('#inputColor').on('change', function() {
                let colorOptions = '<option value="">No Color</option>';
                $(this).select2('data').forEach(opt => colorOptions +=
                    `<option value="${opt.id}">${opt.text}</option>`);
                $('.color-selector').each(function() {
                    let val = $(this).val();
                    $(this).html(colorOptions).val($(this).find(`option[value="${val}"]`).length ?
                        val : '');
                });
            });

            // Add More Image (Smart Clone)
            let rowIndex = 1;
            $('#addImage').on('click', function() {
                let lastRow = $('.image-row').last();
                let newRow = lastRow.clone();
                let idx = rowIndex++;

                newRow.attr('data-index', idx);

                // Update names with the new unique index
                newRow.find('.image-input').attr('name', `image_data[${idx}][files][]`).val('');
                newRow.find('.color-selector').attr('name', `image_data[${idx}][color_id]`).val(lastRow
                    .find('.color-selector').val());
                newRow.find('.size-selector').attr('name', `image_data[${idx}][size_id]`).val(lastRow.find(
                    '.size-selector').val());
                newRow.find('input[placeholder="Price"]').attr('name', `image_data[${idx}][price]`).val(
                    lastRow.find('input[placeholder="Price"]').val());
                newRow.find('input[min="0"]').attr('name', `image_data[${idx}][stock]`).val(lastRow.find(
                    'input[min="0"]').val());

                // Clear preview container
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
                let input = $(this);
                let name = input.val();
                let btn = $('button[type="submit"]');

                if (name.length < 1) {
                    input.removeClass('is-invalid').next('.form-text.text-danger').remove();
                    btn.prop('disabled', false);
                    return;
                }

                timeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('products.check-name') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            name: name
                        },
                        success: function(response) {
                            input.next('.form-text.text-danger').remove();
                            if (response.exists) {
                                input.addClass('is-invalid').after(
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

            /* ================= SEO/OG PREVIEWS ================= */
            function setupPreview(inputClass, previewClass, removeBtnClass) {
                $(document).on('change', inputClass, function() {
                    let row = $(this).closest('div').parent(); // Adjusted for structure
                    let preview = row.find(previewClass);
                    let removeBtn = row.find(removeBtnClass);
                    if (this.files && this.files[0]) {
                        let reader = new FileReader();
                        reader.onload = e => {
                            preview.attr('src', e.target.result).removeClass('d-none');
                            removeBtn.removeClass('d-none');
                        };
                        reader.readAsDataURL(this.files[0]);
                    }
                });
                $(document).on('click', removeBtnClass, function() {
                    let row = $(this).closest('div').parent();
                    row.find(inputClass).val('');
                    row.find(previewClass).addClass('d-none').attr('src', '');
                    $(this).addClass('d-none');
                });
            }
            setupPreview('.seo-image-input', '.seo-img-preview', '.remove-seo-image');
            setupPreview('.og-image-input', '.og-img-preview', '.remove-og-image');
        });
    </script>
@endpush
