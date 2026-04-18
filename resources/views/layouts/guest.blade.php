<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'My-Shop Admin') }} - Authentication</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Full Page Background */
        .ecommerce-bg {
            background-image: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* Overlay to make content readable */
        .bg-overlay {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(30, 58, 138, 0.7) 100%);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* The elegant center card */
        .premium-card {
            background-color: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            overflow: hidden;
        }

        /* Input styling inside the white card */
        .premium-card label {
            color: #475569 !important;
            /* Slate 600 */
            font-weight: 600;
            font-size: 0.875rem;
        }

        .premium-card input[type="text"],
        .premium-card input[type="email"],
        .premium-card input[type="password"] {
            background-color: #f8fafc !important;
            /* Slate 50 */
            border: 1px solid #e2e8f0 !important;
            /* Slate 200 */
            color: #0f172a !important;
            /* Slate 900 */
            border-radius: 0.5rem !important;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .premium-card input[type="text"]:focus,
        .premium-card input[type="email"]:focus,
        .premium-card input[type="password"]:focus {
            border-color: #2563eb !important;
            /* Blue 600 */
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
            background-color: #ffffff !important;
            outline: none;
        }

        /* Button Styling */
        .premium-card button,
        .premium-card .inline-flex.items-center.px-4.py-2.bg-gray-800 {
            background: #1e3a8a !important;
            /* Blue 900 */
            border: none !important;
            color: white !important;
            font-weight: 600 !important;
            border-radius: 0.5rem !important;
            padding: 0.875rem 1.5rem !important;
            transition: all 0.2s ease;
            width: 100%;
            justify-content: center;
            box-shadow: 0 4px 6px -1px rgba(30, 58, 138, 0.4);
        }

        .premium-card button:hover,
        .premium-card .inline-flex.items-center.px-4.py-2.bg-gray-800:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(30, 58, 138, 0.5);
            background: #1e40af !important;
            /* Blue 800 */
        }

        /* Links and text */
        .premium-card a:not(.no-override) {
            color: #2563eb !important;
            /* Blue 600 */
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .premium-card a:not(.no-override):hover {
            color: #1d4ed8 !important;
        }

        .premium-card input[type="checkbox"] {
            border-color: #cbd5e1 !important;
            color: #2563eb !important;
            border-radius: 0.25rem !important;
        }

        .premium-card .text-gray-600 {
            color: #64748b !important;
        }

        .premium-card .text-red-600 {
            color: #ef4444 !important;
        }

        .premium-card .text-green-600 {
            color: #10b981 !important;
        }

        /* Customizing title headings in views */
        .auth-heading {
            color: #0f172a;
            /* Slate 900 */
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .auth-subheading {
            color: #64748b;
            /* Slate 500 */
            font-size: 0.875rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* Illustration pattern placed on top of card */
        .card-header-art {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .store-icon-wrapper {
            background: white;
            padding: 1rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.15);
        }
    </style>
</head>

<body class="font-sans antialiased text-gray-900 ecommerce-bg selection:bg-blue-500 selection:text-white">

    <div class="min-h-screen w-full flex items-center justify-center p-4 sm:p-6 lg:p-8 bg-overlay">

        <!-- Global Brand Header outside the card -->
        <div class="fixed top-8 left-8 flex items-center gap-2 hidden md:flex">
            <div
                class="w-8 h-8 bg-white/20 backdrop-blur-md rounded-md flex items-center justify-center text-white border border-white/30">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
            </div>
            <span class="text-xl font-bold tracking-tight text-white drop-shadow-md">MyShop</span>
        </div>

        <!-- Authentic E-Commerce Card -->
        <div class="w-full max-w-md premium-card relative z-10 animate-[fade-in_0.5s_ease-out]">

            <!-- Card Header with Store Icon -->
            <div class="card-header-art relative">
                <!-- Subtle pattern dots -->
                <div class="absolute inset-0 opacity-10"
                    style="background-image: radial-gradient(#2563eb 1px, transparent 1px); background-size: 16px 16px;">
                </div>

                <div class="store-icon-wrapper relative z-10">
                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- Form Content Container -->
            <div class="p-8 sm:p-10">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer Text -->
        <div class="fixed bottom-6 text-center w-full text-white/60 text-sm font-medium">
            &copy; {{ date('Y') }} MyShop Enterprise. Manage your storefront effortlessly.
        </div>

    </div>

</body>

</html>
