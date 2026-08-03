(() => {
    let storedTheme = null;

    try {
        storedTheme = window.localStorage.getItem('writer-theme');
    } catch {
        storedTheme = null;
    }

    const prefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false;
    const theme = ['light', 'dark'].includes(storedTheme) ? storedTheme : (prefersDark ? 'dark' : 'light');

    document.documentElement.dataset.theme = theme;
})();
