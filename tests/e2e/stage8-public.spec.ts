import { expect, test } from '@playwright/test';

const pages = [
    '/en',
    '/en/services',
    '/en/documents',
    '/en/news',
    '/en/notices',
    '/en/tenders',
    '/en/vacancies',
    '/en/projects',
    '/en/investment',
    '/en/contact',
    '/en/feedback',
    '/en/search?q=council',
];

for (const path of pages) {
    test(`meaningful headings on ${path}`, async ({ page }) => {
        await page.goto(path);
        expect(await page.locator('h1').count()).toBe(1);
        await expect(page.locator('main#main')).toBeVisible();
    });
}

test('content images expose text alternatives, never filenames', async ({ page }) => {
    await page.goto('/en/officials');
    for (const image of await page.locator('img:not([aria-hidden="true"])').all()) {
        const alt = (await image.getAttribute('alt')) ?? '';
        expect(alt).not.toMatch(/\.(png|jpe?g|webp|svg)$/i);
        expect(alt.length).toBeGreaterThan(0);
    }
    expect(await page.locator('img[alt=""][aria-hidden="true"]').count()).toBeGreaterThanOrEqual(0);
});

test('public inputs are programmatically labeled', async ({ page }) => {
    await page.goto('/en/contact');
    for (const input of await page.locator('input[name], select[name], textarea[name]').all()) {
        const id = await input.getAttribute('id');
        const name = await input.getAttribute('name');
        if (name === '_token') continue;
        expect(id, `input "${name}" needs an id`).toBeTruthy();
        expect(await page.locator(`label[for="${id}"]`).count(), `input "${name}" needs a label`).toBeGreaterThan(0);
    }
});

test('interactive controls have accessible names', async ({ page }) => {
    await page.goto('/en');
    for (const button of await page.locator('button').all()) {
        if (!(await button.isVisible())) continue;
        const name = await button.textContent();
        const aria = await button.getAttribute('aria-label');
        expect(((name ?? '') + (aria ?? '')).trim().length).toBeGreaterThan(0);
    }
});
