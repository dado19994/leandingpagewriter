# Responsive QA Checklist

Test these viewports after layout changes:

- 1440 x 1000
- 1024 x 900
- 900 x 900
- 768 x 900
- 430 x 900
- 375 x 820

Pages to check:

- `/`
- `/?category=Scrittura#blog`
- `/libri/titolo-del-libro-1`
- `/blog/il-valore-delle-parole-lente`

What to verify:

- No horizontal scrolling.
- The `Libro in primo piano` block stays two-column on medium screens and stacks only on mobile.
- CTA labels stay readable and do not wrap awkwardly.
- The mobile menu opens, closes, and does not cover content after link selection.
- Newsletter form fits on one column on mobile.
- Modals fit within the viewport and can be closed with the close button, backdrop click, and Escape.

Run:

```bash
npm run responsive:check
```

If Playwright is installed, the script creates screenshots in `storage/app/`. Otherwise it prints the manual checklist.
