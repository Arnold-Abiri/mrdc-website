import { expect, test } from '@playwright/test';

test('skip link, landmarks, and single page heading', async ({ page }) => {
    await page.goto('/en');
    const main = page.locator('main#main');
    await expect(main).toBeVisible();
    expect(await page.locator('h1').count()).toBe(1);
    await page.keyboard.press('Tab');
    const focused = page.locator(':focus');
    await expect(focused).toHaveText(/skip to main content/i);
});

test('language selector offers three text languages without flags', async ({ page }) => {
    await page.goto('/en');
    const selector = page.getByLabel('Language');
    await expect(selector).toBeVisible();
    const options = await selector.locator('option').allTextContents();
    expect(options).toEqual(expect.arrayContaining(['English', 'Shona', 'Ndebele']));
    const html = await page.content();
    expect(html).not.toMatch(/flag/i);
});

test('accessibility controls adjust font size and contrast with persistence', async ({ page }) => {
    await page.goto('/en');
    const group = page.getByRole('group', { name: 'Accessibility' });
    await expect(group).toBeVisible();
    await group.getByRole('button', { name: 'Increase font size' }).click();
    expect(await page.evaluate(() => document.documentElement.style.fontSize)).toBe('112.5%');
    await group.getByRole('button', { name: 'High contrast' }).click();
    await expect(page.locator('html.a11y-high-contrast')).toHaveCount(1);
    expect(await page.evaluate(() => window.localStorage.getItem('a11y-high-contrast'))).toBe('1');
    await page.reload();
    await expect(page.locator('html.a11y-high-contrast')).toHaveCount(1);
    await group.getByRole('button', { name: 'High contrast' }).click();
    await group.getByRole('button', { name: 'Reset font size' }).click();
});

test('contact form exposes labels and an error summary on invalid submission', async ({ page }) => {
    await page.goto('/en/contact');
    for (const label of ['Name', 'Email', 'Subject', 'Message']) {
        await expect(page.getByLabel(label, { exact: false }).first()).toBeVisible();
    }
    await page.locator('#contact-name').fill('QA');
    await page.evaluate(() => {
        const form = document.querySelector('form[action$="/contact"]');
        if (form instanceof HTMLFormElement) HTMLFormElement.prototype.submit.call(form);
    });
    await expect(page.getByRole('alert')).toBeVisible();
});

test('localized pages carry canonical and hreflang metadata', async ({ page }) => {
    await page.goto('/en/services');
    const canonical = await page.locator('link[rel="canonical"]').getAttribute('href');
    expect(canonical).toMatch(/\/en\/services$/);
    for (const locale of ['en', 'sn', 'nd', 'x-default']) {
        await expect(page.locator(`link[rel="alternate"][hreflang="${locale}"]`)).toHaveCount(1);
    }
});
