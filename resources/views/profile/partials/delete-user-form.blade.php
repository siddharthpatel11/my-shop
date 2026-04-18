<section>
    <div class="mb-4">
        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle mb-3" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
            <i class="fas fa-exclamation-triangle fs-4"></i>
        </div>
        <h4 class="text-danger fw-bold mb-2">
            {{ __('Danger Zone: Delete Account') }}
        </h4>
        <p class="text-muted small mb-0 lh-lg">
            {{ __('Once your account is deleted, all of its resources, privileges, and data will be permanently wiped. There is no recovery.') }}
        </p>
    </div>

    <div class="alert bg-danger-subtle border-start border-danger border-4 shadow-sm rounded-3 py-3 px-4 mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle text-danger fs-4 me-3"></i>
            <div class="small text-danger-emphasis fw-medium">
                {{ __('Administrative Tip: Before deleting your account, ensure all duties have been transferred and you have downloaded necessary data.') }}
            </div>
        </div>
    </div>

    <!-- Delete Button triggers Modal -->
    <div class="pt-3 mt-4 border-top">
        <button type="button" class="btn btn-outline-danger px-4 py-2 fw-bold rounded-pill shadow-sm premium-hover w-100 w-sm-auto" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
            <i class="fas fa-user-slash me-2"></i>{{ __('Permanently Delete Account') }}
        </button>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger-subtle p-3 rounded-circle me-3 text-danger">
                            <i class="fas fa-fire fs-4"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark" id="confirmUserDeletionModalLabel">
                            {{ __('Final Warning') }}
                        </h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="modal-body px-4 pb-4">
                        <p class="text-dark fw-medium mb-3">
                            {{ __('Are you sure you want to completely erase your footprint from this system?') }}
                        </p>
                        <p class="text-muted small mb-4">
                            {{ __('This action cannot be undone. Please type your password below to confirm deletion.') }}
                        </p>

                        <div class="mb-0">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0 px-3" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-unlock-alt text-danger"></i></span>
                                <input id="password" name="password" type="password" class="form-control form-control-custom border-start-0 ps-0 @error('password', 'userDeletion') is-invalid @enderror" placeholder="{{ __('Verify Password') }}" style="border-radius: 0 0.75rem 0.75rem 0;">
                                @error('password', 'userDeletion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-0 p-3 mt-2 d-flex justify-content-between">
                        <button type="button" class="btn btn-light fw-bold px-4 rounded-pill text-secondary" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger fw-bold px-4 rounded-pill shadow-sm">
                            <i class="fas fa-trash-alt me-2"></i>{{ __('Erase Everything') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
