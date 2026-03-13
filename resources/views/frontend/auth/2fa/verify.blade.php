@extends('layouts.frontend.app')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="container py-5 my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-dark text-white p-4 text-center">
                    <div class="mb-3">
                        <i class="fas fa-shield-alt fa-3x text-primary"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">Security Verification</h3>
                    <p class="mb-0 opacity-75">Open your 2FA app to get the code</p>
                </div>
                <div class="card-body p-4 p-lg-5">
                    @if(session('error'))
                        <div class="alert alert-danger mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('customer.2fa.post-verify') }}" method="POST">
                        @csrf
                        <div class="mb-4 text-center">
                            <label for="one_time_password" class="form-label d-block mb-3 fs-5">Enter 6-digit Code</label>
                            <input type="text" id="one_time_password" name="one_time_password" 
                                class="form-control form-control-lg text-center fw-bold fs-3 tracking-widest @error('one_time_password') is-invalid @enderror" 
                                placeholder="000000" maxlength="6" required autofocus autocomplete="off"
                                style="letter-spacing: 0.5rem;">
                            @error('one_time_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3">
                                Verify & Continue
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <form action="{{ route('customer.logout') }}" method="POST" id="logout-form">
                                @csrf
                                <button type="submit" class="btn btn-link text-muted text-decoration-none fw-bold">
                                    <i class="fas fa-sign-out-alt me-1"></i> Log out
                                </button>
                            </form>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-widest {
        letter-spacing: 0.5rem;
    }
    .card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
    }
    .btn-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        border: none;
        box-shadow: 0 4px 14px 0 rgba(0, 118, 255, 0.39);
    }
    .btn-primary:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 20px rgba(0, 118, 255, 0.23);
    }
</style>
@endsection
