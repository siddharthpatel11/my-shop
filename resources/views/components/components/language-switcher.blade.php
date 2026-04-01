@php
    $currentLocale = app()->getLocale();
    $languages = [
        'en' => ['label' => 'English', 'flag' => '🇬🇧'],
        'hi' => ['label' => 'हिंदी', 'flag' => '🇮🇳'],
        'gu' => ['label' => 'ગુજરાતી', 'flag' => '🇮🇳'],
        'sa' => ['label' => 'संस्कृतम्', 'flag' => '🕉️'],
        'bn' => ['label' => 'বাংলা', 'flag' => '🇧🇩'],
    ];
@endphp

<div class="dropdown">
    <button class="btn btn-sm btn-outline-light dropdown-toggle
                    d-flex align-items-center gap-2"
        type="button" data-bs-toggle="dropdown">
        {{ $languages[$currentLocale]['flag'] }}
        <span class="d-none d-md-inline">
            {{ $languages[$currentLocale]['label'] }}
        </span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        @foreach ($languages as $code => $lang)
            <li>
                <form method="POST" action="{{ route('language.switch') }}">
                    @csrf
                    <input type="hidden" name="locale" value="{{ $code }}">
                    <button type="submit"
                        class="dropdown-item d-flex align-items-center gap-2
                    {{ $currentLocale === $code ? 'active' : '' }}">
                        {{ $lang['flag'] }} {{ $lang['label'] }}
                        @if ($currentLocale === $code)
                            <i class="fas fa-check ms-auto text-success"></i>
                        @endif
                    </button>
                </form>
            </li>
        @endforeach
    </ul>
</div>
