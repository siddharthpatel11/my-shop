@extends('layouts.frontend.app')

@section('title', 'Home')

@section('content')

    <style>
        /* ── COLOR TOKENS ── */
        :root {
            --p: #4f46e5;
            --p-dk: #4338ca;
            --p-lt: #818cf8;
            --s: #06b6d4;
            --s-dk: #0891b2;
            --acc: #f59e0b;
            --dk: #0f172a;
            --body: #4b5563;
            --mid: #9ca3af;
            --pale: #f9fafb;
            --bdr: #e5e7eb;
            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-bdr: rgba(255, 255, 255, 0.4);
        }

        /* ── ANNOUNCE BAR ── */
        .ann-bar {
            background: linear-gradient(90deg, var(--p) 0%, var(--s) 100%);
            color: #fff;
            text-align: center;
            padding: 10px 1rem;
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .02em;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.2);
        }

        .ann-bar strong {
            color: var(--acc);
            font-weight: 800;
        }

        /* ── HERO ── */
        .hero {
            background: var(--pale);
            min-height: 85vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding: 4rem 0;
        }

        .hero-mesh {
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 20% 30%, rgba(79, 70, 229, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(6, 182, 212, 0.08) 0%, transparent 50%);
        }

        .hero-grid {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(var(--bdr) 0.5px, transparent 0.5px);
            background-size: 30px 30px;
            opacity: 0.4;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--white);
            border: 1px solid var(--bdr);
            color: var(--p);
            font-size: .75rem;
            font-weight: 700;
            padding: .5rem 1.2rem;
            border-radius: 100px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
        }

        .hero-h1 {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2.8rem, 6vw, 5rem);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -.04em;
            color: var(--dk);
            margin-bottom: 1.5rem;
        }

        .hero-h1 span {
            background: linear-gradient(135deg, var(--p) 0%, var(--s) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-p {
            font-size: 1.1rem;
            color: var(--body);
            line-height: 1.6;
            max-width: 500px;
            margin-bottom: 3rem;
        }

        /* ── BUTTONS ── */
        .btn-premium {
            padding: 1rem 2.5rem;
            border-radius: 14px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Outfit', sans-serif;
        }

        .btn-p-solid {
            background: var(--dk);
            color: var(--white);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
        }

        .btn-p-solid:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.3);
            background: #000;
        }

        .btn-p-outline {
            border: 2px solid var(--bdr);
            color: var(--dk);
            margin-left: 1rem;
        }

        .btn-p-outline:hover {
            background: var(--white);
            border-color: var(--dk);
            transform: translateY(-3px);
        }

        /* Stats */
        .hero-stats {
            display: flex;
            gap: 2.5rem;
            margin-top: 2.5rem;
            flex-wrap: wrap;
        }

        .hs {}

        .hs-n {
            font-family: 'Outfit', sans-serif;
            font-size: 1.55rem;
            font-weight: 800;
            color: var(--dark);
            letter-spacing: -.04em;
            line-height: 1;
        }

        .hs-n.oc {
            color: var(--orange);
        }

        .hs-l {
            font-size: .73rem;
            color: var(--mid);
            margin-top: 3px;
            font-weight: 400;
        }

        /* Right image side */
        .hero-right {
            position: relative;
            padding: 2rem 1rem;
        }

        .hero-img-frame {
            position: relative;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(15, 23, 42, .14);
        }

        .hero-img-frame img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            display: block;
            transition: transform .6s ease;
        }

        .hero-img-frame:hover img {
            transform: scale(1.04);
        }

        /* Decorative ring */
        .hero-ring {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .ring1 {
            width: 360px;
            height: 360px;
            top: -60px;
            right: -60px;
            border: 1.5px solid rgba(37, 99, 235, .14);
        }

        .ring2 {
            width: 200px;
            height: 200px;
            bottom: 40px;
            left: -40px;
            border: 1.5px solid rgba(249, 115, 22, .2);
            background: radial-gradient(circle, rgba(249, 115, 22, .05), transparent);
        }

        /* Floating chips */
        .f-chip {
            position: absolute;
            z-index: 2;
            background: #fff;
            border-radius: 16px;
            padding: .8rem 1.1rem;
            box-shadow: 0 8px 32px rgba(15, 23, 42, .12);
            display: flex;
            align-items: center;
            gap: .65rem;
        }

        .f-chip.c1 {
            bottom: 2.5rem;
            left: -1.5rem;
        }

        .f-chip.c2 {
            top: 2.5rem;
            right: -1.5rem;
        }

        .chip-ic {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
            flex-shrink: 0;
        }

        .ic-blue {
            background: var(--blue-pale);
            color: var(--blue-mid);
        }

        .ic-orange {
            background: var(--orange-lt);
            color: var(--orange);
        }

        .chip-val {
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--dark);
            line-height: 1;
        }

        .chip-lbl {
            font-size: .63rem;
            color: var(--mid);
            font-weight: 500;
            margin-top: 2px;
        }

        /* ── MARQUEE ── */
        .mq-bar {
            background: var(--pale);
            border-top: 1px solid var(--bdr);
            border-bottom: 1px solid var(--bdr);
            padding: 1rem 0;
            overflow: hidden;
        }

        .mq-inner {
            display: flex;
            gap: 3rem;
            animation: mq 20s linear infinite;
            width: max-content;
        }

        @keyframes mq {
            to {
                transform: translateX(-50%);
            }
        }

        .mq-item {
            display: flex;
            align-items: center;
            gap: .65rem;
            white-space: nowrap;
            font-size: .82rem;
            font-weight: 500;
            color: var(--mid);
        }

        .mq-item .ic-b {
            color: var(--blue-mid);
        }

        .mq-item .ic-o {
            color: var(--orange);
        }

        /* ── SECTION HEADER ── */
        .sh-eyebrow {
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
            color: var(--blue-mid);
            margin-bottom: .4rem;
        }

        .sh-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(1.6rem, 3vw, 2.3rem);
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--dark);
            line-height: 1.15;
        }

        .sh-sub {
            font-size: .9rem;
            color: var(--mid);
            margin-top: .35rem;
        }

        /* ── CATEGORIES ── */
        .cat-sec {
            padding: 6rem 0;
            background: var(--white);
        }

        .cat-card {
            background: var(--pale);
            border: 1px solid var(--bdr);
            border-radius: 24px;
            padding: 2.5rem 1.5rem;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: all 0.4s ease;
        }

        .cat-card:hover {
            transform: translateY(-10px);
            background: var(--white);
            border-color: var(--p);
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.1);
        }

        .cat-icon-wrap {
            width: 70px;
            height: 70px;
            background: var(--white);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--p);
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .cat-card:hover .cat-icon-wrap {
            background: var(--p);
            color: var(--white);
            transform: rotate(10deg);
        }

        .cat-name {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dk);
        }

        /* ── PRODUCTS ── */
        .prod-sec {
            padding: 6rem 0;
            background: var(--pale);
        }

        .pc {
            background: var(--white);
            border: 1px solid var(--bdr);
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.4s ease;
            height: 100%;
        }

        .pc:hover {
            transform: translateY(-12px);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.12);
            border-color: var(--p-lt);
        }

        .pc-img {
            aspect-ratio: 1;
            overflow: hidden;
            position: relative;
            background: var(--pale);
        }

        .pc-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .pc:hover .pc-img img {
            transform: scale(1.1);
        }

        .pc-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.4rem 1rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            z-index: 10;
        }

        .b-new {
            background: var(--p);
            color: #fff;
        }

        .b-hot {
            background: var(--acc);
            color: var(--dk);
        }

        .pc-body {
            padding: 1.5rem;
        }

        .pc-cat {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--mid);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .pc-name {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dk);
            margin-bottom: 0.75rem;
            line-height: 1.3;
        }

        .pc-price {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--p);
            margin-bottom: 1.25rem;
        }

        .pc-price .cur {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--mid);
        }

        .btn-view {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 0.8rem;
            border-radius: 12px;
            background: var(--dk);
            color: var(--white);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-view:hover {
            background: var(--p);
            transform: scale(1.02);
        }

        /* ── FEATURES ── */
        .feat-sec {
            padding: 5rem 0;
            background: var(--white);
        }

        .feat-item {
            background: var(--pale);
            padding: 2rem;
            border-radius: 24px;
            border: 1px solid var(--bdr);
            transition: all 0.3s ease;
            height: 100%;
        }

        .feat-item:hover {
            background: var(--white);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transform: translateY(-5px);
        }

        .feat-ic {
            width: 60px;
            height: 60px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            background: var(--white);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            color: var(--p);
        }

        .feat-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dk);
            margin-bottom: 0.5rem;
        }

        .feat-sub {
            font-size: 0.9rem;
            color: var(--body);
            line-height: 1.5;
        }

        /* ── REVEAL ANIMATIONS ── */
        .rv {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .rv.on {
            opacity: 1;
            transform: translateY(0);
        }

        .d1 {
            transition-delay: 0.1s;
        }

        .d2 {
            transition-delay: 0.2s;
        }

        .d3 {
            transition-delay: 0.3s;
        }

        .d4 {
            transition-delay: 0.4s;
        }

        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap');
    </style>


    <!-- ── ANNOUNCE ── -->
    <div class="ann-bar">
        {!! __('home.announce') !!}
    </div>

    <!-- ── HERO ── -->
    <section class="hero">
        <div class="hero-mesh"></div>
        <div class="hero-grid"></div>

        <div class="container position-relative">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="rv d1">
                        <div class="hero-badge">
                            <i class="fas fa-sparkles"></i>
                            {{ __('home.badge_premium') }}
                        </div>
                        <h1 class="hero-h1">
                            {!! __('home.hero_title_1') !!}
                        </h1>
                        <p class="hero-p">
                            {{ __('home.hero_desc') }}
                        </p>
                        <div class="d-flex flex-wrap gap-3">
                            <a href="{{ route('frontend.products.index') }}" class="btn-premium btn-p-solid">
                                {{ __('home.shop_collection') }} <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                            <a href="#categories" class="btn-premium btn-p-outline">
                                {{ __('home.view_categories') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="rv d2 hero-right">
                        <!-- Decorative Rings -->
                        <div class="hero-ring ring1"></div>
                        <div class="hero-ring ring2"></div>

                        <div class="hero-img-frame">
                            <img src="{{ asset('images/hero-image.jpg') }}" alt="Premium Products"
                                onerror="this.src='https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&q=80&w=800'">
                        </div>

                        <!-- Floating Chips -->
                        <div class="f-chip c1">
                            <div class="chip-ic ic-blue">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <div class="chip-val">50k+</div>
                                <div class="chip-lbl">{{ __('home.happy_customers') }}</div>
                            </div>
                        </div>

                        <div class="f-chip c2">
                            <div class="chip-ic ic-orange">
                                <i class="fas fa-star text-warning"></i>
                            </div>
                            <div>
                                <div class="chip-val">4.9/5</div>
                                <div class="chip-lbl">{{ __('home.average_rating') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- ── FEATURES ── -->
    <section class="feat-sec">
        <div class="container">
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="rv d1 feat-item">
                        <div class="feat-ic"><i class="fas fa-truck-fast"></i></div>
                        <h4 class="feat-title">{{ __('home.free_shipping') }}</h4>
                        <p class="feat-sub">{{ __('home.free_shipping_sub') }}</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rv d2 feat-item">
                        <div class="feat-ic"><i class="fas fa-rotate-left"></i></div>
                        <h4 class="feat-title">{{ __('home.easy_returns') }}</h4>
                        <p class="feat-sub">{{ __('home.easy_returns_sub') }}</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rv d3 feat-item">
                        <div class="feat-ic"><i class="fas fa-shield-halved"></i></div>
                        <h4 class="feat-title">{{ __('home.secure_payment') }}</h4>
                        <p class="feat-sub">{{ __('home.secure_payment_sub') }}</p>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="rv d4 feat-item">
                        <div class="feat-ic"><i class="fas fa-headset"></i></div>
                        <h4 class="feat-title">{{ __('home.expert_support') }}</h4>
                        <p class="feat-sub">{{ __('home.expert_support_sub') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── CATEGORIES ── -->
    <section class="cat-sec" id="categories">
        <div class="container">
            <div class="text-center mb-5 rv d1">
                <span class="sh-eyebrow">{{ __('home.explore') }}</span>
                <h2 class="sh-title">{{ __('home.top_categories') }}</h2>
            </div>

            <div class="row g-4">
                @foreach ($categories->take(4) as $index => $cat)
                    <div class="col-6 col-md-3">
                        <div class="rv {{ 'd' . ($index + 1) }}">
                            <a href="{{ route('frontend.products.index', ['category' => $cat->id]) }}" class="cat-card">
                                <div class="cat-icon-wrap">
                                    <i class="{{ $cat->icon ?? 'fas fa-tag' }}"></i>
                                </div>
                                <h3 class="cat-name">{{ $cat->name }}</h3>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ── PRODUCTS ── -->
    <section class="prod-sec">
        <div class="container">
            <div class="text-center mb-5 rv d1">
                <span class="sh-eyebrow">{{ __('home.handpicked') }}</span>
                <h2 class="sh-title">{{ __('home.new_arrivals') }}</h2>
            </div>

            <div class="row g-4">
                @foreach ($latestProducts as $i => $product)
                    @php
                        $images = $product->image ? explode(',', $product->image) : [];
                        $firstImage = $images[0] ?? null;
                        $imageUrl = $firstImage
                            ? asset('images/products/' . $firstImage)
                            : 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=400&q=80';
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3 rv" style="transition-delay:{{ ($i % 4) * 0.08 }}s;">
                        <div class="pc">
                            <div class="pc-img">
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy">
                                @if ($i === 0)
                                    <span class="pc-badge b-hot">{{ __('home.badge_hot') }}</span>
                                @elseif($i < 3)
                                    <span class="pc-badge b-new">{{ __('home.badge_new') }}</span>
                                @endif
                            </div>
                            <div class="pc-body">
                                <p class="pc-cat">{{ $product->category->name ?? __('home.collection') }}</p>
                                <h3 class="pc-name">{{ $product->name }}</h3>
                                <div class="pc-price">
                                    <span class="cur">₹</span>{{ number_format($product->price, 0) }}
                                </div>
                                <a href="{{ route('frontend.products.show', $product->id) }}" class="btn-view">
                                    {{ __('home.view_details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ── CTA BAND ── -->
    <section class="cta-sec">
        <div class="container position-relative">
            <div class="rv d1">
                <span class="sh-eyebrow" style="color:rgba(255,255,255,0.6)">{{ __('home.exclusive_offer') }}</span>
                <h2 class="cta-h text-white">{!! __('home.join_club') !!}</h2>
                <p class="cta-p" style="color:rgba(255,255,255,0.6)">{{ __('home.join_club_sub') }}</p>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('frontend.products.index') }}" class="btn-premium btn-p-solid"
                        style="background:var(--white); color:var(--dk);">
                        {{ __('home.start_shopping') }} <i class="fas fa-shopping-bag ms-2"></i>
                    </a>
                    @guest('customer')
                        <a href="{{ route('customer.login') }}" class="btn-premium btn-p-outline"
                            style="border-color:var(--white); color:var(--white);">
                            {{ __('home.create_account') }}
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            const rv = document.querySelectorAll('.rv');
            const io = new IntersectionObserver(es => {
                es.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('on');
                        io.unobserve(e.target);
                    }
                });
            }, {
                threshold: .12
            });
            rv.forEach(el => io.observe(el));
        </script>
    @endpush

@endsection
