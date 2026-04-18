<section>
    <div class="mb-4">
        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
            <i class="fas fa-key fs-4"></i>
        </div>
        <h4 class="text-dark fw-bold mb-2">
            {{ __('Update Security Password') }}
        </h4>
        <p class="text-muted small mb-0 lh-lg">
            {{ __('Ensure your account is using a long, alphanumeric password with symbols to stay secure. A strong password is the first line of defense.') }}
        </p>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-4">
            <label for="update_password_current_password" class="form-label fw-bold small text-dark mb-2">{{ __('Current Password') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-0 px-3" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-lock text-muted"></i></span>
                <input id="update_password_current_password" name="current_password" type="password" class="form-control form-control-custom border-start-0 ps-0 @error('current_password', 'updatePassword') is-invalid @enderror" autocomplete="current-password" placeholder="Enter your current password" style="border-radius: 0 0.75rem 0.75rem 0;">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="update_password_password" class="form-label fw-bold small text-dark mb-2">{{ __('New Password') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 px-3" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-lock-open text-muted"></i></span>
                    <input id="update_password_password" name="password" type="password" class="form-control form-control-custom border-start-0 ps-0 @error('password', 'updatePassword') is-invalid @enderror" autocomplete="new-password" placeholder="New strong password" style="border-radius: 0 0.75rem 0.75rem 0;">
                    @error('password', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <label for="update_password_password_confirmation" class="form-label fw-bold small text-dark mb-2">{{ __('Confirm Password') }}</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 px-3" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-check-double text-muted"></i></span>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control form-control-custom border-start-0 ps-0 @error('password_confirmation', 'updatePassword') is-invalid @enderror" autocomplete="new-password" placeholder="Confirm your new password" style="border-radius: 0 0.75rem 0.75rem 0;">
                    @error('password_confirmation', 'updatePassword')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between pt-3 mt-4 border-top">
            <button type="submit" class="btn btn-gradient px-4 py-2 fw-bold rounded-pill shadow-sm premium-hover w-100 w-sm-auto">
                <i class="fas fa-shield-alt me-2"></i>{{ __('Update Password') }}
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success small fw-bold animate__animated animate__zoomIn bg-success-subtle px-3 py-2 rounded-pill ms-3">
                    <i class="fas fa-check-circle me-1"></i>{{ __('Secured') }}
                </span>
            @endif
        </div>
    </form>
</section>
