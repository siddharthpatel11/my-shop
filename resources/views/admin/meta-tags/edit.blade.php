@extends('layouts.app')

@push('styles')
<style>
    .image-preview-container {
        width: 100%;
        max-width: 300px;
        height: 200px;
        border: 2px dashed #ddd;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        background-color: #f8f9fa;
    }
    .image-preview-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .image-preview-placeholder {
        text-align: center;
        color: #aba8a8;
    }
    .remove-image-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(220, 53, 69, 0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s;
        z-index: 10;
    }
    .remove-image-btn:hover {
        background-color: rgb(220, 53, 69);
    }
    .char-count {
        font-size: 0.8em;
        color: #6c757d;
    }
    .char-count.warning {
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Edit Meta Tags: {{ $name }} </h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.meta-tags.update', $identifier) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- SEO Info -->
                        <div class="col-md-6 mb-4">
                            <h5 class="mb-3"><i class="fas fa-search text-primary"></i> Search Engine Optimization (SEO)</h5>
                            
                            <div class="mb-3">
                                <label for="seo_title" class="form-label fw-bold">SEO Title</label>
                                <input type="text" name="seo_title" id="seo_title" class="form-control" value="{{ old('seo_title', $metaTag->seo_title) }}" oninput="updateCharCount(this, 'seo_title_count', 60)">
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Recommended length: 50-60 characters.</small>
                                    <span class="char-count" id="seo_title_count">0/60</span>
                                </div>
                                @error('seo_title') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="seo_description" class="form-label fw-bold">SEO Description</label>
                                <textarea name="seo_description" id="seo_description" class="form-control" rows="3" oninput="updateCharCount(this, 'seo_desc_count', 160)">{{ old('seo_description', $metaTag->seo_description) }}</textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Recommended length: 150-160 characters.</small>
                                    <span class="char-count" id="seo_desc_count">0/160</span>
                                </div>
                                @error('seo_description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="seo_key" class="form-label fw-bold">Keywords / Tags</label>
                                <input type="text" name="seo_key" id="seo_key" class="form-control" value="{{ old('seo_key', $metaTag->seo_key) }}">
                                <small class="text-muted">Separate keywords with commas (e.g. phones, smart phone).</small>
                                @error('seo_key') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="seo_canonical" class="form-label fw-bold">Canonical URL (Optional)</label>
                                <input type="url" name="seo_canonical" id="seo_canonical" class="form-control" value="{{ old('seo_canonical', $metaTag->seo_canonical) }}">
                                <small class="text-muted">Leave blank unless you specifically need to point to a different URL.</small>
                                @error('seo_canonical') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3 border p-3 rounded bg-light">
                                <label class="form-label fw-bold">SEO Preview Image</label>
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="image-preview-container" id="seo_preview_container">
                                            @if($metaTag->seo_image && \Illuminate\Support\Facades\File::exists(public_path('images/seo/' . $metaTag->seo_image)))
                                                <button type="button" class="remove-image-btn" aria-label="Remove Image" onclick="removePreview('seo')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <img src="{{ asset('images/seo/' . $metaTag->seo_image) }}" alt="SEO Image" id="seo_preview_img">
                                            @else
                                                <button type="button" class="remove-image-btn d-none" aria-label="Remove Image" onclick="removePreview('seo')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <div class="image-preview-placeholder" id="seo_placeholder">
                                                    <i class="fas fa-image fa-3x mb-2"></i><br>
                                                    <span>No Image Selected</span>
                                                </div>
                                                <img src="" alt="SEO Preview" id="seo_preview_img" class="d-none">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 align-self-center">
                                        <input type="file" name="seo_image" id="seo_image" class="form-control form-control-sm mb-2" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this, 'seo')">
                                        <small class="text-muted d-block">Optimum Size: 1200x630px. Max size: 2MB.</small>
                                        <input type="hidden" name="remove_seo_image" id="remove_seo_image" value="0">
                                    </div>
                                </div>
                                @error('seo_image') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Open Graph Info -->
                        <div class="col-md-6 mb-4 border-start">
                            <h5 class="mb-3"><i class="fas fa-share-alt text-info"></i> Open Graph (Social Sharing)</h5>
                            
                            <div class="alert alert-info py-2">
                                <small><i class="fas fa-info-circle me-1"></i> If OG fields are left empty, SEO fields will be used when sharing on social media.</small>
                            </div>

                            <div class="mb-3">
                                <label for="og_title" class="form-label fw-bold">OG Title</label>
                                <input type="text" name="og_title" id="og_title" class="form-control" value="{{ old('og_title', $metaTag->og_title) }}" oninput="updateCharCount(this, 'og_title_count', 90)">
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Recommended max 90 characters.</small>
                                    <span class="char-count" id="og_title_count">0/90</span>
                                </div>
                                @error('og_title') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="og_description" class="form-label fw-bold">OG Description</label>
                                <textarea name="og_description" id="og_description" class="form-control" rows="3" oninput="updateCharCount(this, 'og_desc_count', 200)">{{ old('og_description', $metaTag->og_description) }}</textarea>
                                <div class="d-flex justify-content-between mt-1">
                                    <small class="text-muted">Recommended max 200 characters.</small>
                                    <span class="char-count" id="og_desc_count">0/200</span>
                                </div>
                                @error('og_description') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="og_key" class="form-label fw-bold">OG Keywords</label>
                                <input type="text" name="og_key" id="og_key" class="form-control" value="{{ old('og_key', $metaTag->og_key) }}">
                                @error('og_key') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3 border p-3 rounded bg-light">
                                <label class="form-label fw-bold">OG Share Image (Facebook/Twitter/LinkedIn)</label>
                                <div class="d-flex">
                                    <div class="me-3">
                                        <div class="image-preview-container" id="og_preview_container">
                                            @if($metaTag->og_image && \Illuminate\Support\Facades\File::exists(public_path('images/seo/' . $metaTag->og_image)))
                                                <button type="button" class="remove-image-btn" aria-label="Remove Image" onclick="removePreview('og')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <img src="{{ asset('images/seo/' . $metaTag->og_image) }}" alt="OG Image" id="og_preview_img">
                                            @else
                                                <button type="button" class="remove-image-btn d-none" aria-label="Remove Image" onclick="removePreview('og')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <div class="image-preview-placeholder" id="og_placeholder">
                                                    <i class="fas fa-image fa-3x mb-2"></i><br>
                                                    <span>No Image Selected</span>
                                                </div>
                                                <img src="" alt="OG Preview" id="og_preview_img" class="d-none">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 align-self-center">
                                        <input type="file" name="og_image" id="og_image" class="form-control form-control-sm mb-2" accept="image/jpeg,image/png,image/jpg,image/gif" onchange="previewImage(this, 'og')">
                                        <small class="text-muted d-block">Optimum Size: 1200x630px. Max size: 2MB.</small>
                                        <input type="hidden" name="remove_og_image" id="remove_og_image" value="0">
                                    </div>
                                </div>
                                @error('og_image') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 border-top pt-3 d-flex justify-content-end">
                        <a href="{{ route('admin.meta-tags.index') }}" class="btn btn-light ms-2"><i class="fas fa-times me-1"></i> Cancel</a>
                        <button type="submit" class="btn btn-primary ms-2"><i class="fas fa-save me-1"></i> Save Meta Tags</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Image Preview and Removal Logic
    function previewImage(input, type) {
        const previewImg = document.getElementById(type + '_preview_img');
        const placeholder = document.getElementById(type + '_placeholder');
        const removeBtn = document.querySelector('#' + type + '_preview_container .remove-image-btn');
        const removeFlag = document.getElementById('remove_' + type + '_image');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('d-none');
                if(placeholder) placeholder.classList.add('d-none');
                removeBtn.classList.remove('d-none');
                removeFlag.value = "0"; // Reset remove flag when new image selected
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removePreview(type) {
        const input = document.getElementById(type + '_image');
        const previewImg = document.getElementById(type + '_preview_img');
        const placeholder = document.getElementById(type + '_placeholder');
        const removeBtn = document.querySelector('#' + type + '_preview_container .remove-image-btn');
        const removeFlag = document.getElementById('remove_' + type + '_image');

        // Reset file input
        input.value = '';
        
        // Hide image, show placeholder
        previewImg.src = '';
        previewImg.classList.add('d-none');
        if(!placeholder) {
            // Re-create placeholder if it was removed by blade
            const container = document.getElementById(type + '_preview_container');
            const placeholderHtml = `
                <div class="image-preview-placeholder" id="${type}_placeholder">
                    <i class="fas fa-image fa-3x mb-2"></i><br>
                    <span>No Image Selected</span>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', placeholderHtml);
        } else {
            placeholder.classList.remove('d-none');
        }
        
        // Hide remove button
        removeBtn.classList.add('d-none');
        
        // Set hidden flag to tell server to delete existing image
        removeFlag.value = "1";
    }

    // Character Counter
    function updateCharCount(input, counterId, max) {
        const count = input.value.length;
        const counter = document.getElementById(counterId);
        counter.textContent = count + '/' + max;
        
        if (count > max) {
            counter.classList.add('warning');
            counter.classList.add('fw-bold');
        } else {
            counter.classList.remove('warning');
            counter.classList.remove('fw-bold');
        }
    }

    // Initialize counters on load
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('seo_title')) updateCharCount(document.getElementById('seo_title'), 'seo_title_count', 60);
        if(document.getElementById('seo_description')) updateCharCount(document.getElementById('seo_description'), 'seo_desc_count', 160);
        if(document.getElementById('og_title')) updateCharCount(document.getElementById('og_title'), 'og_title_count', 90);
        if(document.getElementById('og_description')) updateCharCount(document.getElementById('og_description'), 'og_desc_count', 200);
    });
</script>
@endpush
