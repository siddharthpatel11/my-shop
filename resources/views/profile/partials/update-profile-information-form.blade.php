<section>
    <div class="mb-4">
        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
            <i class="fas fa-user-edit fs-4"></i>
        </div>
        <h4 class="text-dark fw-bold mb-2">
            {{ __('Profile Information') }}
        </h4>
        <p class="text-muted small mb-0 lh-lg">
            {{ __("Make changes to your basic account details and email address. Ensure your email is always up-to-date for important account alerts.") }}
        </p>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-4">
            <label for="name" class="form-label fw-bold small text-dark mb-2">{{ __('Full Name') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0 px-3" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-id-badge text-muted"></i></span>
                <input id="name" name="name" type="text" class="form-control form-control-custom border-start-0 ps-0 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" style="border-radius: 0 0.75rem 0.75rem 0;">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label for="email" class="form-label fw-bold small text-dark mb-2">{{ __('Email Address') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0 px-3" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-at text-muted"></i></span>
                <input id="email" name="email" type="email" class="form-control form-control-custom border-start-0 ps-0 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username" style="border-radius: 0 0.75rem 0.75rem 0;">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 bg-warning-subtle p-3 rounded-3 border border-warning border-opacity-25">
                    <p class="text-sm text-dark mb-2 fw-medium">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>{{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="btn btn-link btn-sm p-0 ms-2 fw-bold text-decoration-none">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success py-2 px-3 small rounded-3 border-0 shadow-sm mb-0">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center justify-content-between pt-3 mt-4 border-top">
            <button type="submit" class="btn btn-gradient px-4 py-2 fw-bold rounded-pill shadow-sm premium-hover w-100 w-sm-auto">
                <i class="fas fa-cloud-upload-alt me-2"></i>{{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small fw-bold animate__animated animate__zoomIn bg-success-subtle px-3 py-2 rounded-pill ms-3">
                    <i class="fas fa-check-circle me-1"></i>{{ __('Saved') }}
                </span>
            @endif
        </div>
    </form>
</section>
