import { expect, test } from '@playwright/test';

test('homepage shell has no horizontal overflow at supported widths', async ({ page }) => {
    for (const width of [320, 360, 390, 768, 1024, 1280, 1440]) {
        await page.setViewportSize({ width, height: 800 });
        await page.goto('/');
        await expect(page.getByRole('heading', { level: 1 })).toHaveText('Mutoko Rural District Council');
        const overflow = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth);
        expect(overflow, `horizontal overflow at ${width}px`).toBe(false);
    }
    await page.setViewportSize({ width: 320, height: 700 });
    await page.getByRole('button', { name: 'Menu' }).click();
    await expect(page.getByRole('navigation', { name: 'Primary navigation' })).toBeVisible();
});

test('admin redirects a guest to login', async ({ page }) => {
    await page.goto('/admin');
    await expect(page).toHaveURL(/\/admin\/login$/);
});

test('loads Theme 1 imagery at responsive widths', async ({ page }) => {
    for (const width of [320, 390, 768, 1024, 1440]) {
        await page.setViewportSize({ width, height: 900 });
        await page.goto('/');
        await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
        const heroLoaded = await page.locator('.hero-image').evaluate(
            image => image instanceof HTMLImageElement && image.complete && image.naturalWidth > 0
        );
        expect(heroLoaded, `hero image at ${width}px`).toBe(true);
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    }
});

test('public links resolve and keyboard navigation remains usable', async ({ page, request }) => {
    await page.setViewportSize({ width: 390, height: 800 });
    await page.goto('/');
    const hrefs = await page.locator('a[href]').evaluateAll(links =>
        links.map(link => link.getAttribute('href') || '')
    );
    for (const href of new Set(hrefs)) {
        if (href.startsWith('#')) {
            expect(await page.locator('[id="' + href.slice(1) + '"]').count(), href).toBeGreaterThan(0);
        } else if (href.startsWith('/#')) {
            expect(await page.locator('[id="' + href.slice(2) + '"]').count(), href).toBeGreaterThan(0);
        } else if (href.startsWith('/')) {
            expect((await request.get(href)).status(), href).toBe(200);
        }
    }
    expect(await page.getByRole('heading', { level: 1 }).count()).toBe(1);
    await page.keyboard.press('Tab');
    await expect(page.getByRole('link', { name: 'Skip to main content' })).toBeFocused();
    const focusOutline = await page.getByRole('link', { name: 'Skip to main content' }).evaluate(
        element => getComputedStyle(element).outlineStyle
    );
    expect(focusOutline).not.toBe('none');
    await page.keyboard.press('Enter');
    await expect(page.locator('main')).toBeFocused();
    const menu = page.getByRole('button', { name: 'Menu' });
    await menu.focus();
    await page.keyboard.press('Enter');
    await expect(page.getByRole('navigation', { name: 'Primary navigation' })).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(menu).toBeFocused();
    await expect(menu).toHaveAttribute('aria-expanded', 'false');
    expect(await page.locator('a[href^="tel:"], a[href^="mailto:"], a[href="https://facebook.com"]').count()).toBe(0);
    expect(await page.locator('header').count()).toBe(1);
    expect(await page.locator('main').count()).toBe(1);
    expect(await page.locator('footer').count()).toBe(1);
    expect(await page.locator('img:not([alt])').count()).toBe(0);
    await page.emulateMedia({ reducedMotion: 'reduce' });
    expect(await page.locator('html').evaluate(element => getComputedStyle(element).scrollBehavior)).toBe('auto');
});
