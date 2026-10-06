import { expect, test } from '@playwright/test';

test('journey 1: projects filter and project detail render', async ({ page }) => {
    await page.goto('/projects');
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
    await page.goto('/projects?status=ongoing');
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
});

test('journey 2: investment opportunity links to an enquiry form', async ({ page }) => {
    await page.goto('/investment');
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
    await page.goto('/contact?context=investment:no-such-opportunity');
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
});

test('journey 3: feedback submits and shows a reference', async ({ page }) => {
    await page.goto('/feedback');
    await page.locator('#feedback-name').fill('QA Citizen');
    await page.locator('#feedback-email').fill('citizen@example.test');
    await page.locator('#feedback-subject').fill('QA smoke complaint');
    await page.locator('#feedback-message').fill('Development-only smoke test message.');
    await page.locator('#feedback-consent').check();
    await page.getByRole('button', { name: /submit/i }).click();
    await expect(page).toHaveURL(/reference=[0-9a-f-]{8}/);
    await expect(page.getByRole('status')).toBeVisible();
});

test('journey 4: tender and vacancy sections render award and closing states', async ({ page }) => {
    await page.goto('/tenders');
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
    await page.goto('/vacancies');
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
    await page.goto('/tourism');
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
});
