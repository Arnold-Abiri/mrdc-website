import { createHash, randomBytes } from 'node:crypto';
import { execFileSync } from 'node:child_process';
import { expect, test } from '@playwright/test';

const database = process.env.E2E_DB_DATABASE;
const email = `stage7-qa-${randomBytes(6).toString('hex')}@example.test`;
const password = randomBytes(24).toString('hex');

function assertDisposableDatabase(): void {
    if (!database?.startsWith('mutoko_stage3_final_admin_') || process.env.DB_DATABASE !== database) {
        throw new Error('Admin E2E requires the matching named disposable MySQL database.');
    }
}

function artisan(code: string): void {
    assertDisposableDatabase();
    execFileSync('php', ['artisan', 'tinker', `--execute=${code}`], { env: process.env, stdio: 'pipe' });
}

test.beforeAll(() => {
    assertDisposableDatabase();
    execFileSync('php', ['artisan', 'migrate:fresh', '--seed', '--no-interaction'], { env: process.env, stdio: 'pipe' });
    artisan(`$user = App\\Models\\User::factory()->create(["email" => "${email}", "password" => "${password}"]); $role = Spatie\\Permission\\Models\\Role::findByName("System Administrator"); $user->assignRole($role); Illuminate\\Support\\Facades\\DB::table("user_role_scopes")->insert(["user_id" => $user->id, "role_id" => $role->id, "scope_type" => "global", "created_at" => now(), "updated_at" => now()]);`);
});

test.beforeEach(() => {
    const key = 'livewire-rate-limiter:' + createHash('sha1').update('Filament\\Auth\\Pages\\Login|authenticate|127.0.0.1').digest('hex');
    artisan(`Illuminate\\Support\\Facades\\RateLimiter::clear("${key}");`);
});

async function signIn(page: import('@playwright/test').Page): Promise<void> {
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
}

test('journey 1: manager opens M&E dashboard and changes period', async ({ page }) => {
    await signIn(page);
    await page.goto('/admin/analytics-dashboard');
    await expect(page.getByRole('heading', { name: 'Monitoring & Evaluation' })).toBeVisible();
    await page.locator('select[name="period"]').selectOption('last_7');
    await page.getByRole('button', { name: 'Apply' }).click();
    await expect(page).toHaveURL(/period=last_7/);
    await expect(page.getByText('Visitor-days (approx)')).toBeVisible();
});

test('journey 2: manager filters the audit report', async ({ page }) => {
    await signIn(page);
    await page.goto('/admin/audit-report');
    await expect(page.getByRole('heading', { name: 'Audit report' })).toBeVisible();
    await page.locator('input[name="action"]').fill('auth.login');
    await page.getByRole('button', { name: 'Filter' }).click();
    await expect(page).toHaveURL(/action=auth\.login/);
});

test('journey 3: operations user views system health', async ({ page }) => {
    await signIn(page);
    await page.goto('/admin/system-health');
    await expect(page.getByRole('heading', { name: 'System health' })).toBeVisible();
    await expect(page.getByText('Healthy').first()).toBeVisible();
});
