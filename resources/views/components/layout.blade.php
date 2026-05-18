@props([
    'title' => 'Virginia | Blog & Scrittura',
    'description' => 'Blog e sito vetrina di Virginia: romanzi, racconti, eventi, appunti di scrittura e collaborazioni editoriali.',
    'image' => null,
    'type' => 'website',
    'schema' => null,
    'hideChrome' => false,
    'bodyClass' => '',
    'canonical' => null,
])

<!doctype html>
<html lang="it">
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
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $image ?? asset('images/virginia.jpg') }}">
    <title>{{ $title }}</title>

    @if ($schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="{{ $bodyClass }}">
    @unless ($hideChrome)
        <x-navbar />
    @endunless

    <main>
        {{ $slot }}
    </main>

    @unless ($hideChrome)
        <x-footer />
    @endunless
</body>
</html>
