@extends('layouts.app')

@section('title', 'Edit Banner')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-white border-bottom border-light p-4 rounded-top-4 d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-primary"><i class="fas fa-edit me-2"></i>Edit Banner / Ad</h4>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Title (e.g. SUMMER SALE) <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control form-control-lg @error('title') is-invalid @enderror" value="{{ old('title', $banner->title) }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Subtitle (e.g. Mega Deals on Coolers)</label>
                            <input type="text" name="subtitle" class="form-control form-control-lg @error('subtitle') is-invalid @enderror" value="{{ old('subtitle', $banner->subtitle) }}">
                            @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Link (URL to redirect)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $banner->link) }}" placeholder="https://...">
                            </div>
                            @error('link')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Background Gradient / Color</label>
                            <select name="background_color" class="form-select form-select-lg @error('background_color') is-invalid @enderror">
                                <option value="linear-gradient(135deg, #fceabb 0%, #f8b500 100%)" {{ $banner->background_color == 'linear-gradient(135deg, #fceabb 0%, #f8b500 100%)' ? 'selected' : '' }}>Yellow / Gold (Summer)</option>
                                <option value="linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)" {{ $banner->background_color == 'linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%)' ? 'selected' : '' }}>Light Blue (Tech)</option>
                                <option value="linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)" {{ $banner->background_color == 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)' ? 'selected' : '' }}>Pink (Fashion/Beauty)</option>
                                <option value="linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%)" {{ $banner->background_color == 'linear-gradient(135deg, #d4fc79 0%, #96e6a1 100%)' ? 'selected' : '' }}>Mint Green (Fresh)</option>
                                <option value="linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%)" {{ $banner->background_color == 'linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%)' ? 'selected' : '' }}>Purple (Premium)</option>
                                <option value="#f1f3f6" {{ $banner->background_color == '#f1f3f6' ? 'selected' : '' }}>Solid Light Gray (Standard)</option>
                            </select>
                            @error('background_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Text Color</label>
                            <select name="text_color" class="form-select form-select-lg @error('text_color') is-invalid @enderror">
                                <option value="text-dark" {{ $banner->text_color == 'text-dark' ? 'selected' : '' }}>Dark Text</option>
                                <option value="text-white" {{ $banner->text_color == 'text-white' ? 'selected' : '' }}>White Text</option>
                            </select>
                            @error('text_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Banner Image</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*" id="bannerImage">
                            <div class="form-text">Leave blank to keep existing image. Transparent PNG looks best.</div>
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 d-flex justify-content-center align-items-center">
                            <div id="imagePreview" class="border rounded p-2 text-center" style="height: 100px; width: 100px; background: #eee;">
                                @if($banner->image)
                                    <img src="{{ asset('images/banners/' . $banner->image) }}" alt="Preview" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                @else
                                    <span class="text-muted small align-middle">No Image</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Order / Sequence</label>
                            <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $banner->order) }}">
                            <div class="form-text">Lower number means it will appear first.</div>
                            @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="active" {{ $banner->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $banner->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm py-2 fw-bold text-uppercase">
                            Update Banner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('bannerImage').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" style="max-height: 100%; max-width: 100%; object-fit: contain;">`;
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
