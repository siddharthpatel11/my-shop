@extends('layouts.frontend.app')

@section('title', 'Customer Reset Password')

@section('content')
    <div class="min-vh-100 d-flex align-items-center bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5 col-lg-4">

                    <div class="card border-0 shadow-lg rounded-4">
                        <div class="card-body p-5">

                            <div class="text-center mb-4">
                                <div class="bg-gradient-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 80px; height: 80px;">
                                    <i class="fas fa-lock-open fa-2x text-white"></i>
                                </div>
                                <h3 class="fw-bold mb-1">Set New Password</h3>
                                <p class="text-muted">Enter your new password below.</p>
                            </div>

                            {{-- Error Messages --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('customer.password.update') }}">
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{ $email }}">

                                {{-- Password --}}
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-lock text-primary me-2"></i>New Password
                                    </label>
                                    <input type="password" name="password" class="form-control form-control-lg"
                                        placeholder="Enter new password" required>
                                </div>

                                {{-- Confirm Password --}}
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-check-circle text-primary me-2"></i>Confirm Password
                                    </label>
                                    <input type="password" name="password_confirmation" class="form-control form-control-lg"
                                        placeholder="Confirm new password" required>
                                </div>

                                {{-- Submit Button --}}
                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                    <i class="fas fa-save me-2"></i>Reset Password
                                </button>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
@endsection
