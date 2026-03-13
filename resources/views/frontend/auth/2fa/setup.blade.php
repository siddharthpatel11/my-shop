@extends('layouts.frontend.app')

@section('title', 'Setup Google 2FA')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white p-4 text-center">
                    <h2 class="mb-0 fw-bold">Boost Your Security</h2>
                    <p class="mb-0 opacity-75">Enable Google Authenticator for your account</p>
                </div>
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-lg-5 text-center mb-4 mb-lg-0">
                            <div class="p-3 bg-white d-inline-block rounded-4 shadow-sm">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrCodeUrl) !!}
                            </div>
                            <div class="mt-3">
                                <span class="badge bg-light text-dark p-2 font-monospace">{{ $secret }}</span>
                            </div>
                        </div>
                        <div class="col-lg-7">
                            <h4 class="fw-bold mb-3">3 Simple Steps:</h4>
                            <ol class="ps-4 mb-4">
                                <li class="mb-2">Install <strong>Google Authenticator</strong> or <strong>Authy</strong> on your phone.</li>
                                <li class="mb-2">Scan the QR code or enter the secret key manually.</li>
                                <li class="mb-2">Enter the 6-digit code from your app below to verify.</li>
                            </ol>

                            <form action="{{ route('customer.2fa.enable') }}" method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="one_time_password" class="form-label fw-bold">Authentication Code</label>
                                    <input type="text" id="one_time_password" name="one_time_password" 
                                        class="form-control form-control-lg @error('one_time_password') is-invalid @enderror" 
                                        placeholder="000 000" maxlength="6" required autofocus autocomplete="off">
                                    @error('one_time_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold">
                                        Enable 2FA
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light p-4 text-center border-0">
                    <a href="{{ route('customer.profile') }}" class="text-decoration-none text-muted fw-bold">
                        <i class="fas fa-arrow-left me-1"></i> Back to Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }
    .btn-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        border: none;
        transition: transform 0.2s;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
    }
</style>
@endsection
