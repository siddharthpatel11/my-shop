@extends('layouts.frontend.app')

@section('title', $page->meta_title ?? $page->title)

@section('content')
    <div class="gallery-page">
        <!-- Banner -->
        @if ($page->banner_image_url)
            <div class="page-banner mb-5"
                style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ $page->banner_image_url }}') center/cover no-repeat; height: 400px; display: flex; align-items: center; justify-content: center;">
                <div class="text-center text-white">
                    <h1 class="display-3 fw-bold mb-3">{{ $page->title }}</h1>
                    @if ($page->content)
                        <p class="lead">{{ Str::limit(strip_tags($page->content), 150) }}</p>
                    @endif
                </div>
            </div>
        @endif

        <div class="container py-5">
            @if (!$page->banner_image_url)
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold mb-3">{{ $page->title }}</h1>
                    @if ($page->content)
                        <p class="lead text-muted">{{ Str::limit(strip_tags($page->content), 150) }}</p>
                    @endif
                </div>
            @endif

            <!-- Gallery Grid -->
            @if ($page->gallery_images && count($page->gallery_images) > 0)
                <div class="row g-4">
                    @foreach ($page->gallery_images as $index => $image)
                        <div class="col-md-6 col-lg-4">
                            <div class="gallery-item position-relative overflow-hidden rounded shadow-sm"
                                style="height: 300px;">
                                <img src="{{ asset('storage/' . $image) }}"
                                    alt="{{ $page->title }} - Image {{ $index + 1 }}"
                                    class="w-100 h-100 object-fit-cover gallery-img"
                                    style="cursor: pointer; transition: transform 0.3s;" data-bs-toggle="modal"
                                    data-bs-target="#imageModal{{ $index }}">
                                <div class="gallery-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                    style="background: rgba(0,0,0,0); transition: background 0.3s;">
                                    <i class="fas fa-search-plus text-white"
                                        style="font-size: 2rem; opacity: 0; transition: opacity 0.3s;"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Image Modal -->
                        <div class="modal fade" id="imageModal{{ $index }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content bg-transparent border-0">
                                    <div class="modal-body p-0 text-center">
                                        <button type="button"
                                            class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                                            data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>
                                        <img src="{{ asset('storage/' . $image) }}" alt="{{ $page->title }}"
                                            class="img-fluid rounded shadow-lg">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-images fa-4x text-muted mb-3"></i>
                    <p class="text-muted">No images available in this gallery.</p>
                </div>
            @endif

            <!-- Full Content (if exists) -->
            @if ($page->content)
                <div class="mt-5 pt-5 border-top">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="page-content-text" style="line-height: 1.8;">
                                {!! nl2br(e($page->content)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .gallery-img:hover {
                transform: scale(1.05);
            }

            .gallery-item:hover .gallery-overlay {
                background: rgba(0, 0, 0, 0.5) !important;
            }

            .gallery-item:hover .fa-search-plus {
                opacity: 1 !important;
            }

            .object-fit-cover {
                object-fit: cover;
            }
        </style>
    @endpush
@endsection
