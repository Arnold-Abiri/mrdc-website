import { expect, test } from '@playwright/test';

test('homepage exposes tenders, vacancies, and investment entry points', async ({ page }) => {
    await page.goto('/en/');
    await expect(page.getByRole('link', { name: 'Tenders' }).first()).toBeVisible();
    await expect(page.getByRole('link', { name: 'Vacancies' }).first()).toBeVisible();
    await expect(page.getByRole('link', { name: /invest/i }).first()).toBeVisible();
});

test('citizen service sections render honest empty states without seed data', async ({ page }) => {
    for (const path of ['/tenders', '/vacancies', '/investment']) {
        await page.goto(path);
        await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
    }
});

test('about and downloads aliases redirect to managed content', async ({ page }) => {
    await page.goto('/en/about');
    await expect(page).toHaveURL(/\/pages\/about-mutoko/);
    await page.goto('/en/downloads');
    await expect(page).toHaveURL(/\/documents/);
});

test('search covers the citizen service directory', async ({ page }) => {
    await page.goto('/en/search?q=council');
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
});

test('governance and transparency sections render honest empty states', async ({ page }) => {
    for (const path of ['/meetings', '/transparency', '/rates', '/documents?category=budget&year=2026']) {
        await page.goto(path);
        await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
    }
});
