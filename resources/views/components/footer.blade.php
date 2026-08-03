@php($homeRoute = app()->getLocale() === 'en' ? route('home.en') : route('home'))

<footer class="writer-footer">
    <p>© {{ date('Y') }} Virginia - {{ __('site.footer.claim') }}</p>

    <div>
        <a href="{{ $homeRoute }}#books">{{ __('site.nav.books') }}</a>
        <a href="{{ $homeRoute }}#newsletter">{{ __('site.nav.newsletter') }}</a>
        <a href="{{ $homeRoute }}#press">Press kit</a>
        <a href="{{ $homeRoute }}#blog">{{ __('site.nav.blog') }}</a>
        <a href="{{ $homeRoute }}#contact">{{ __('site.nav.contact') }}</a>
        <a href="https://www.instagram.com/" target="_blank" rel="noreferrer">Instagram</a>
    </div>
</footer>
