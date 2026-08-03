@props(['languageRoute' => null])

@php
    $homeRoute = app()->getLocale() === 'en' ? route('home.en') : route('home');
    $languageRoute ??= app()->getLocale() === 'en' ? route('home') : route('home.en');
@endphp

<nav class="writer-navbar">
    <a href="{{ $homeRoute }}" class="writer-logo">
        Virginia<span>.</span>
    </a>

    <button
        type="button"
        class="writer-menu-toggle"
        aria-label="{{ __('site.nav.open_menu') }}"
        data-open-label="{{ __('site.nav.open_menu') }}"
        data-close-label="{{ __('site.nav.close_menu') }}"
        aria-expanded="false"
        aria-controls="writer-nav-links"
    >
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="writer-nav-links" id="writer-nav-links">
        <a href="{{ $homeRoute }}#about">{{ __('site.nav.about') }}</a>
        <a href="{{ $homeRoute }}#book-focus">{{ __('site.nav.book') }}</a>
        <a href="{{ $homeRoute }}#books">{{ __('site.nav.books') }}</a>
        <a href="{{ $homeRoute }}#events">{{ __('site.nav.events') }}</a>
        <a href="{{ $homeRoute }}#blog">{{ __('site.nav.blog') }}</a>
        <a href="{{ $homeRoute }}#newsletter">{{ __('site.nav.newsletter') }}</a>
        <a href="{{ $homeRoute }}#contact">{{ __('site.nav.contact') }}</a>
        <a href="{{ $languageRoute }}" class="writer-language-link">{{ __('site.nav.language') }}</a>
    </div>

    <button type="button" class="writer-theme-toggle" data-theme-toggle
        data-light-label="{{ __('site.nav.light') }}" data-dark-label="{{ __('site.nav.dark') }}"
        aria-label="{{ __('site.nav.theme') }}" title="{{ __('site.nav.theme') }}">
        <span class="theme-icon theme-icon-sun" aria-hidden="true"></span>
        <span class="theme-icon theme-icon-moon" aria-hidden="true"></span>
    </button>
</nav>
