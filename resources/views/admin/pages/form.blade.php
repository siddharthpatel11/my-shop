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
                        <form action="{{ isset($page) ? route('pages.update', $page) : route('pages.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($page))
                                @method('PUT')
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-bold">Page Title *</label>
                                <input type="text" name="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    value="{{ old('title', $page->title ?? '') }}" placeholder="Enter page title" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Page Content</label>
                                <textarea name="content" id="tiny-editor" rows="15" class="form-control @error('content') is-invalid @enderror"
                                    placeholder="Enter page content...">{{ old('content', $page->content ?? '') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

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
    </script>
@endpush
