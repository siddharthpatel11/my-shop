<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Setup Google Authenticator') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="max-w-xl mx-auto text-center">
                        <h3 class="text-xl font-bold mb-4">Secure Your Account</h3>
                        <p class="mb-6">Two-factor authentication adds an extra layer of security to your account. To
                            enable it, follow these steps:</p>

                        <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                            <p class="font-bold mb-2">1. Scan the QR Code</p>
                            <p class="text-sm text-gray-600 mb-4">Open your Google Authenticator app and scan this code:
                            </p>
                            <div class="flex justify-center mb-4">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}"
                                    alt="QR Code">
                            </div>
                            <p class="text-xs text-gray-500">Alternatively, enter this secret key manually:
                                <br><strong>{{ $secret }}</strong></p>
                        </div>

                        <div class="mb-8">
                            <p class="font-bold mb-2">2. Enter Verification Code</p>
                            <p class="text-sm text-gray-600 mb-4">Enter the 6-digit code from the app to confirm setup:
                            </p>

                            <form method="POST" action="{{ route('admin.2fa.setup') }}" class="max-w-xs mx-auto">
                                @csrf
                                <div>
                                    <x-text-input id="one_time_password"
                                        class="block mt-1 w-full text-center text-2xl tracking-widest" type="text"
                                        name="one_time_password" required autofocus maxlength="6" />
                                    <x-input-error :messages="$errors->get('one_time_password')" class="mt-2" />
                                </div>

                                <div class="flex items-center justify-center mt-6">
                                    <x-primary-button>
                                        {{ __('Enable 2FA') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
