import { expect, test } from '@playwright/test';
import { mkdir } from 'node:fs/promises';

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

test('captures Theme 1 responsive homepage', async ({ page }) => {
    await mkdir('artifacts/theme-1', { recursive: true });
    for (const width of [320, 390, 768, 1024, 1440]) {
        await page.setViewportSize({ width, height: 900 });
        await page.goto('/');
        await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
        await page.screenshot({ path: `artifacts/theme-1/home-polish-${width}.png`, fullPage: true });
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth)).toBe(true);
    }
});
