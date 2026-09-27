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
