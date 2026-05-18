const baseUrl = process.env.RESPONSIVE_BASE_URL || 'http://127.0.0.1:8011';

const viewports = [
    { name: 'desktop', width: 1440, height: 1000 },
    { name: 'laptop', width: 1024, height: 900 },
    { name: 'tablet', width: 900, height: 900 },
    { name: 'tablet-small', width: 768, height: 900 },
    { name: 'mobile-large', width: 430, height: 900 },
    { name: 'mobile-small', width: 375, height: 820 },
];

const paths = ['/', '/?category=Scrittura#blog', '/libri/titolo-del-libro-1'];

try {
    const { chromium } = await import('playwright');
    const browser = await chromium.launch();
    const failures = [];
    const screenshots = [];

    for (const viewport of viewports) {
        const page = await browser.newPage({ viewport });

        for (const path of paths) {
            const url = `${baseUrl}${path}`;
            const response = await page.goto(url, { waitUntil: 'networkidle' });

            if (!response?.ok()) {
                failures.push(`${viewport.name} ${url} returned ${response?.status()}`);
                continue;
            }

            const overflow = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth + 1);
            if (overflow) {
                failures.push(`${viewport.name} ${url} has horizontal overflow`);
            }

            const screenshotPath = `storage/app/responsive-${viewport.name}-${path.replace(/[^a-z0-9]+/gi, '-') || 'home'}.png`;
            await page.screenshot({
                path: screenshotPath,
                fullPage: true,
            });
            screenshots.push({ viewport: viewport.name, path, screenshotPath });
        }

        await page.close();
    }

    await browser.close();

    if (failures.length) {
        console.error(failures.join('\n'));
        process.exit(1);
    }

    const report = `<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Responsive Report</title>
    <style>
        body { font-family: system-ui, sans-serif; margin: 0; background: #f7f1e8; color: #261f1a; }
        main { width: min(1280px, calc(100% - 32px)); margin: 32px auto; }
        h1 { font-family: Georgia, serif; font-size: clamp(2rem, 5vw, 4rem); }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; }
        article { background: #fffdf8; border: 1px solid rgba(38,31,26,.14); padding: 14px; border-radius: 8px; }
        img { width: 100%; display: block; border-radius: 6px; border: 1px solid rgba(38,31,26,.12); }
        p { color: #6f6259; }
    </style>
</head>
<body>
<main>
    <h1>Responsive Report</h1>
    <div class="grid">
        ${screenshots.map((item) => `<article><h2>${item.viewport}</h2><p>${item.path}</p><img src="${item.screenshotPath.replace('storage/app/', '')}" alt="${item.viewport} ${item.path}"></article>`).join('')}
    </div>
</main>
</body>
</html>`;
    await import('node:fs/promises').then((fs) => fs.writeFile('storage/app/responsive-report.html', report));

    console.log('Responsive check passed. Screenshots and responsive-report.html saved in storage/app/.');
} catch (error) {
    if (error?.code !== 'ERR_MODULE_NOT_FOUND' && !String(error?.message || '').includes('Cannot find package')) {
        console.error('Responsive check failed while running Playwright:');
        console.error(error);
        process.exit(1);
    }

    console.log('Playwright is not installed, so automated screenshots were skipped.');
    console.log(`Manual checklist base URL: ${baseUrl}`);
    for (const viewport of viewports) {
        console.log(`- ${viewport.name}: ${viewport.width}x${viewport.height}`);
    }
    console.log('Install Playwright later to enable screenshots: npm i -D playwright && npx playwright install chromium');
}
