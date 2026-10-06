import { expect, test } from '@playwright/test';

for (const width of [390, 1280]) {
    test(`Stage 3 search and contact pages render at ${width}px`, async ({ page }) => {
        await page.setViewportSize({ width, height: 800 });
        await page.goto('/search?q=__unlikely_stage3_query__');
        await expect(page.getByRole('heading', { name: 'Search' })).toBeVisible();
        await expect(page.getByText('No approved results found.')).toBeVisible();
        await expect(page.getByRole('searchbox', { name: 'Search council pages' })).toHaveValue('__unlikely_stage3_query__');
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);

        await page.goto('/contact');
        await expect(page.getByRole('heading', { name: 'Contact the council' })).toBeVisible();
        await expect(page.getByRole('button', { name: 'Send enquiry' })).toBeVisible();
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    });
}

test('public locale selection persists and updates document language', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('html')).toHaveAttribute('lang', 'en');
    await page.getByLabel('Language').selectOption('sn');
    await expect(page.locator('html')).toHaveAttribute('lang', 'sn');
    await page.goto('/contact');
    await expect(page.locator('html')).toHaveAttribute('lang', 'sn');
    await page.getByLabel('Language').selectOption('en');
    await expect(page.locator('html')).toHaveAttribute('lang', 'en');
});

test('state-changing locale request rejects a missing CSRF token', async ({ request }) => {
    const response = await request.post('/locale', { data: { locale: 'sn' }, maxRedirects: 0 });
    expect(response.status()).toBe(419);
});
