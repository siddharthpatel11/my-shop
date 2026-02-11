@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-{{ isset($page) ? 'edit' : 'plus' }} me-2"></i>
                            {{ isset($page) ? 'Edit Page' : 'Create New Page' }}
                        </h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ isset($page) ? route('pages.update', $page) : route('pages.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @if (isset($page))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Page Title *</label>
                                        <input type="text" name="title"
                                            class="form-control @error('title') is-invalid @enderror"
                                            value="{{ old('title', $page->title ?? '') }}" placeholder="Enter page title"
                                            required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Page Status</label>
                                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                                            <option value="active"
                                                {{ old('status', $page->status ?? 'active') == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="inactive"
                                                {{ old('status', $page->status ?? '') == 'inactive' ? 'selected' : '' }}>
                                                Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Page Content</label>
                                <textarea name="content" id="tiny-editor" rows="15" class="form-control @error('content') is-invalid @enderror"
                                    placeholder="Enter page content...">{{ old('content', $page->content ?? '') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Gallery Images</label>
                                <input type="file" name="gallery_images[]"
                                    class="form-control @error('gallery_images.*') is-invalid @enderror" multiple
                                    accept="image/*">
                                <div class="form-text">You can upload multiple images. Best for the Gallery page.</div>
                                @error('gallery_images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if (isset($page) && $page->gallery_images && count($page->gallery_images) > 0)
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Current Gallery Images</label>
                                    <div class="row g-2">
                                        @foreach ($page->gallery_images as $image)
                                            <div class="col-md-2 position-relative gallery-item-wrapper"
                                                id="image-{{ md5($image) }}">
                                                <div class="card h-100 shadow-sm border-0">
                                                    <img src="{{ asset('storage/' . $image) }}"
                                                        class="card-img-top rounded" alt="Gallery Image"
                                                        style="height: 100px; object-fit: cover;">
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1 rounded-circle"
                                                        onclick="deleteGalleryImage('{{ $image }}', '{{ $page->slug }}')"
                                                        title="Delete Image">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>{{ isset($page) ? 'Update' : 'Create' }} Page
                                </button>
                                <a href="{{ route('pages.index') }}" class="btn btn-secondary btn-lg ms-2">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#tiny-editor',
            license_key: 'gpl',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount code',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat code',
            height: 500,
            extended_valid_elements: 'script[src|async|defer|type|charset],style',
            valid_children: '+body[script|style],+div[script|style]',
            setup: function(editor) {
                editor.on('change', function() {
                    tinymce.triggerSave();
                });
            }
        });

        function deleteGalleryImage(imagePath, pageSlug) {
            Swal.fire({
                title: 'Delete image?',
                text: "Are you sure you want to delete this image?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/pages/${pageSlug}/delete-gallery-image`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                image_path: imagePath
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    text: 'Image deleted successfully',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    text: data.message || 'Error deleting image'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                text: 'Something went wrong'
                            });
                        });
                }
            });
        }
    </script>
@endpush
