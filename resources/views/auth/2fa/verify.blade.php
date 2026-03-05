<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Two-Factor Authentication') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <h3 class="text-lg font-bold mb-4">Verification Required</h3>
                    <p class="mb-6">Please enter the OTP from your Google Authenticator app to continue.</p>

                    <form method="POST" action="{{ route('admin.2fa.verify') }}" class="max-w-sm mx-auto">
                        @csrf

                        <div class="mb-5 p-4 bg-light rounded-4 border shadow-sm text-center">
                            <p class="text-muted small mb-3 uppercase tracking-wider font-bold">Scan QR Code or Use
                                Manual Key</p>
                            <div class="d-flex justify-content-center mb-4">
                                <div class="bg-white p-3 rounded-3 shadow-sm border">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($qrCodeUrl) }}"
                                        alt="QR Code">
                                </div>
                            </div>
                            <div class="bg-white py-2 px-3 rounded-pill border d-inline-block">
                                <code class="text-primary fw-bold"
                                    style="font-size: 1.1rem; letter-spacing: 1px;">{{ $secret }}</code>
                            </div>
                        </div>

                        <div class="mb-4" style="max-width:300px; margin:auto;">
                            <label for="one_time_password" class="form-label fw-bold">
                                {{ __('Enter 6-digit OTP') }}
                            </label>

                            <input id="one_time_password" type="text"
                                class="form-control text-center @error('one_time_password') is-invalid @enderror"
                                name="one_time_password" required autofocus maxlength="6"
                                style="font-size:20px; letter-spacing:8px; height:45px;">

                            @error('one_time_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle me-2"></i> {{ __('Verify OTP') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
