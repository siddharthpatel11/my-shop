@extends('layouts.frontend.app')

@section('title', $page->meta_title ?? $page->title)

@section('content')
    <div class="contact-page">
        <!-- Banner -->
        @if ($page->banner_image_url)
            <div class="page-banner mb-5"
                style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ $page->banner_image_url }}') center/cover no-repeat; height: 400px; display: flex; align-items: center; justify-content: center;">
                <div class="text-center text-white">
                    <h1 class="display-3 fw-bold mb-3">{{ $page->title }}</h1>
                    @if ($page->content)
                        <p class="lead">Get in touch with us</p>
                    @endif
                </div>
            </div>
        @endif

        <div class="container py-5">
            @if (!$page->banner_image_url)
                <div class="text-center mb-5">
                    <h1 class="display-4 fw-bold mb-3">{{ $page->title }}</h1>
                    <p class="lead text-muted">We'd love to hear from you</p>
                </div>
            @endif

            <div class="row g-5">
                <!-- Contact Information -->
                <div class="col-lg-5">
                    <div class="contact-info">
                        <h3 class="mb-4">Get In Touch</h3>

                        @if ($page->content)
                            <div class="mb-4" style="line-height: 1.8;">
                                {!! nl2br(e($page->content)) !!}
                            </div>
                        @endif

                        <!-- Contact Details -->
                        <div class="contact-details">
                            @if (isset($layoutSettings) && $layoutSettings->contact_email)
                                <div class="d-flex align-items-start mb-4">
                                    <div class="me-3">
                                        <i class="fas fa-envelope fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Email</h5>
                                        <a href="mailto:{{ $layoutSettings->contact_email }}" class="text-decoration-none">
                                            {{ $layoutSettings->contact_email }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            @if (isset($layoutSettings) && $layoutSettings->contact_phone)
                                <div class="d-flex align-items-start mb-4">
                                    <div class="me-3">
                                        <i class="fas fa-phone fa-2x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Phone</h5>
                                        <a href="tel:{{ $layoutSettings->contact_phone }}" class="text-decoration-none">
                                            {{ $layoutSettings->contact_phone }}
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex align-items-start mb-4">
                                <div class="me-3">
                                    <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="mb-1">Address</h5>
                                    <p class="mb-0">
                                        @if (isset($layoutSettings) && $layoutSettings->contact_address)
                                            @if ($layoutSettings->address_link)
                                                <a href="{{ $layoutSettings->address_link }}" target="_blank"
                                                    class="text-decoration-none text-muted">
                                                    {{ $layoutSettings->contact_address }}
                                                </a>
                                            @else
                                                {{ $layoutSettings->contact_address }}
                                            @endif
                                        @elseif($page->custom_fields && isset($page->custom_fields['address']))
                                            {{ $page->custom_fields['address'] }}
                                        @else
                                            Your Business Address Here
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Links -->
                        @if (isset($layoutSettings) && $layoutSettings->social_links)
                            <div class="mt-4">
                                <h5 class="mb-3">Follow Us</h5>
                                <div class="d-flex gap-3">
                                    @if (isset($layoutSettings->social_links['facebook']))
                                        <a href="{{ $layoutSettings->social_links['facebook'] }}" target="_blank"
                                            class="btn btn-outline-primary btn-lg">
                                            <i class="fab fa-facebook"></i>
                                        </a>
                                    @endif
                                    @if (isset($layoutSettings->social_links['instagram']))
                                        <a href="{{ $layoutSettings->social_links['instagram'] }}" target="_blank"
                                            class="btn btn-outline-danger btn-lg">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    @endif
                                    @if (isset($layoutSettings->social_links['linkedin']))
                                        <a href="{{ $layoutSettings->social_links['linkedin'] }}" target="_blank"
                                            class="btn btn-outline-primary btn-lg">
                                            <i class="fab fa-linkedin"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            <h3 class="mb-4">Send Us a Message</h3>

                            @if (session('contact_success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('contact_success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('contact.submit') }}" method="POST" id="contactForm">
                                @csrf
                                <input type="hidden" name="page_id" value="{{ $page->id }}">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Name *</label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone</label>
                                        <input type="tel" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Subject *</label>
                                        <input type="text" name="subject"
                                            class="form-control @error('subject') is-invalid @enderror"
                                            value="{{ old('subject') }}" required>
                                        @error('subject')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label">Message *</label>
                                        <textarea name="message" rows="5" class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                                        @error('message')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary btn-lg px-5">
                                            <i class="fas fa-paper-plane me-2"></i>
                                            Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Map or Additional Images -->
            @if ((isset($layoutSettings) && $layoutSettings->map_html) || ($page->images && count($page->images) > 0))
                <div class="mt-5 pt-5 border-top">
                    @if (isset($layoutSettings) && $layoutSettings->map_html)
                        <div class="mb-5">
                            <h3 class="mb-4 text-center">Our Location</h3>
                            <div class="map-container rounded shadow-sm overflow-hidden" style="height: 450px;">
                                {!! $layoutSettings->map_html !!}
                            </div>
                            <style>
                                .map-container iframe {
                                    width: 100% !important;
                                    height: 450px !important;
                                    border: 0 !important;
                                }
                            </style>
                        </div>
                    @endif

                    @if ($page->gallery_images && count($page->gallery_images) > 0)
                        <h3 class="mb-4 text-center">Our Facilities</h3>
                        <div class="row g-4">
                            @foreach ($page->gallery_images as $image)
                                <div class="col-md-4">
                                    <img src="{{ $image }}" alt="{{ $page->title }}"
                                        class="img-fluid rounded shadow-sm">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .contact-details a {
                color: #333;
                transition: color 0.3s;
            }

            .contact-details a:hover {
                color: #667eea;
            }
        </style>
    @endpush
@endsection
