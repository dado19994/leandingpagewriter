@props([
    'title' => 'Virginia | Blog & Scrittura',
    'description' => 'Blog e sito vetrina di Virginia: romanzi, racconti, eventi, appunti di scrittura e collaborazioni editoriali.',
    'image' => null,
    'type' => 'website',
    'schema' => null,
    'hideChrome' => false,
    'bodyClass' => '',
    'canonical' => null,
    'alternate' => null,
])

<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $description }}">
    <meta name="author" content="Virginia">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:type" content="{{ $type }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $image ?? asset('images/virginia.jpg') }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">
    @if ($alternate)
        <link rel="alternate" hreflang="{{ app()->getLocale() }}" href="{{ $canonical ?? url()->current() }}">
        <link rel="alternate" hreflang="{{ app()->getLocale() === 'en' ? 'it' : 'en' }}" href="{{ $alternate }}">
        <link rel="alternate" hreflang="x-default" href="{{ app()->getLocale() === 'it' ? ($canonical ?? url()->current()) : $alternate }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image ?? asset('images/virginia.jpg') }}">
    <title>{{ $title }}</title>
    <script src="{{ asset('theme-init.js') }}"></script>

    @if ($schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="{{ $bodyClass }}">
    @unless ($hideChrome)
        <x-navbar :language-route="$alternate" />
    @endunless

    <main>
        {{ $slot }}
    </main>

    @unless ($hideChrome)
        <x-footer />
    @endunless
</body>
</html>
