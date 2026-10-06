import { randomBytes } from 'node:crypto';
import { execFileSync } from 'node:child_process';
import { expect, test } from '@playwright/test';

const database = process.env.E2E_DB_DATABASE;
const email = `stage3-qa-${randomBytes(6).toString('hex')}@example.test`;
const password = randomBytes(24).toString('hex');
const office = `Stage 3 QA ${randomBytes(5).toString('hex')}`;
const disabledEmail = `stage3-disabled-${randomBytes(6).toString('hex')}@example.test`;
const limitedEmail = `stage3-limited-${randomBytes(6).toString('hex')}@example.test`;
const viewerEmail = `stage3-page-viewer-${randomBytes(6).toString('hex')}@example.test`;
const contactEmail = `qa-${randomBytes(5).toString('hex')}@example.test`;

function assertDisposableDatabase(): void {
    if (!database?.startsWith('mutoko_stage3_final_admin_') || process.env.DB_DATABASE !== database) {
        throw new Error('Admin E2E requires the matching named disposable MySQL database.');
    }
}

function artisan(code: string): void {
    assertDisposableDatabase();
    execFileSync('php', ['artisan', 'tinker', `--execute=${code}`], { env: process.env, stdio: 'pipe' });
}

function resetDisposableDatabase(): void {
    assertDisposableDatabase();
    execFileSync('php', ['artisan', 'migrate:fresh', '--seed', '--no-interaction'], { env: process.env, stdio: 'pipe' });
}

test.beforeAll(() => {
    resetDisposableDatabase();
    artisan(`$user = App\\Models\\User::factory()->create(["email" => "${email}", "password" => "${password}"]); $role = Spatie\\Permission\\Models\\Role::findByName("System Administrator"); $user->assignRole($role); Illuminate\\Support\\Facades\\DB::table("user_role_scopes")->insert(["user_id" => $user->id, "role_id" => $role->id, "scope_type" => "global", "created_at" => now(), "updated_at" => now()]); $disabled = App\\Models\\User::factory()->create(["email" => "${disabledEmail}", "password" => "${password}"]); $disabled->forceFill(["status" => "disabled", "disabled_at" => now()])->save(); $disabled->assignRole($role); Illuminate\\Support\\Facades\\DB::table("user_role_scopes")->insert(["user_id" => $disabled->id, "role_id" => $role->id, "scope_type" => "global", "created_at" => now(), "updated_at" => now()]); $limitedRole = Spatie\\Permission\\Models\\Role::firstOrCreate(["name" => "QA limited panel", "guard_name" => "web"]); $limitedRole->givePermissionTo("admin.access"); $limited = App\\Models\\User::factory()->create(["email" => "${limitedEmail}", "password" => "${password}"]); $limited->assignRole($limitedRole); Illuminate\\Support\\Facades\\DB::table("user_role_scopes")->insert(["user_id" => $limited->id, "role_id" => $limitedRole->id, "scope_type" => "global", "created_at" => now(), "updated_at" => now()]); $readerRole = Spatie\\Permission\\Models\\Role::firstOrCreate(["name" => "QA page reader", "guard_name" => "web"]); $readerRole->givePermissionTo(["admin.access", "pages.view"]); $reader = App\\Models\\User::factory()->create(["email" => "${viewerEmail}", "password" => "${password}"]); $reader->assignRole($readerRole); Illuminate\\Support\\Facades\\DB::table("user_role_scopes")->insert(["user_id" => $reader->id, "role_id" => $readerRole->id, "scope_type" => "global", "created_at" => now(), "updated_at" => now()]);`);
});

test.afterAll(() => {
    artisan('App\\Models\\Media::all()->each(fn ($media) => Illuminate\\Support\\Facades\\Storage::disk(config("cms.media_disk"))->delete($media->storage_path));');
    resetDisposableDatabase();
});

test('administrator signs in and creates a draft managed contact', async ({ page }) => {
    await page.goto('/admin/public-contacts/create');
    await expect(page).toHaveURL(/\/admin\/login/);
    await page.locator('input[type="email"]').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/public-contacts\/create$/);
    await page.getByLabel('Office').fill(office);
    await page.getByLabel('Value').fill(contactEmail);
    await page.getByLabel('Type').selectOption('email');
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/public-contacts\/\d+\/edit$/);
    await expect(page.getByLabel('Office')).toHaveValue(office);
    await page.getByLabel('Office').fill(`${office} edited`);
    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/livewire') && response.request().method() === 'POST'),
        page.getByRole('button', { name: 'Save changes' }).click(),
    ]);
    await expect(page.getByLabel('Office')).toHaveValue(`${office} edited`);
    await page.goto('/admin/public-contacts');
    const row = page.getByRole('row').filter({ hasText: `${office} edited` });
    await expect(row).toContainText('draft');
    await row.getByRole('button', { name: 'Verify' }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(row).toContainText('publishable');
    await row.getByRole('button', { name: 'Publish', exact: true }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(row).toContainText('published');
    await page.goto('/');
    await expect(page.getByText(contactEmail)).toBeVisible();
    await page.goto('/admin/public-contacts');
    const publishedRow = page.getByRole('row').filter({ hasText: `${office} edited` });
    await publishedRow.getByRole('button', { name: 'Unpublish' }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(publishedRow).toContainText('unpublished');
    await page.goto('/');
    await expect(page.getByText(contactEmail)).toHaveCount(0);
});


test('disabled administrator cannot authenticate to Filament', async ({ page }) => {
    await page.goto('/admin');
    await expect(page).toHaveURL(/\/admin\/login$/);
    await page.locator('input[type="email"]').fill(disabledEmail);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/login$/);
    await page.goto('/admin/public-contacts');
    await expect(page).toHaveURL(/\/admin\/login$/);
});

test('administrator publishes and withdraws services, wards, officials, news, and notices', async ({ page }) => {
    test.setTimeout(180000);
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);

    const suffix = randomBytes(5).toString('hex');
    const imageTitle = `QA image ${suffix}`;
    await page.goto('/admin/media/create');
    await page.locator('input[type="file"]').setInputFiles({
        name: 'qa-image.png', mimeType: 'image/png',
        buffer: Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/pWQAAAAASUVORK5CYII=', 'base64'),
    });
    await expect(page.getByRole('alert')).toContainText('Upload complete');
    await page.locator('[id="form.title"]').fill(imageTitle);
    await page.locator('[id="form.alt_text"]').fill('Development-only image');
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/media\/\d+\/edit$/);
    const records = [
        { admin: 'services', public: 'services', name: `QA service ${suffix}`, slug: `qa-service-${suffix}`, fields: { Name: `QA service ${suffix}`, Slug: `qa-service-${suffix}`, Summary: 'Test only service summary' } },
        { admin: 'wards', public: 'wards', name: `QA ward ${suffix}`, slug: `qa-ward-${suffix}`, fields: { Name: `QA ward ${suffix}`, Slug: `qa-ward-${suffix}`, Description: 'Test only ward profile' } },
        { admin: 'officials', public: 'officials', name: `QA official ${suffix}`, slug: `qa-official-${suffix}`, fields: { Name: `QA official ${suffix}`, Title: 'Test only position', Slug: `qa-official-${suffix}` } },
        { admin: 'editorial/editorial-items', public: 'news', name: `QA news ${suffix}`, slug: `qa-news-${suffix}`, type: 'news', fields: { Title: `QA news ${suffix}`, Slug: `qa-news-${suffix}`, Body: 'Test only news body' } },
        { admin: 'editorial/editorial-items', public: 'notices', name: `QA notice ${suffix}`, slug: `qa-notice-${suffix}`, type: 'notice', fields: { Title: `QA notice ${suffix}`, Slug: `qa-notice-${suffix}`, Body: 'Test only notice body' } },
    ];
    for (const record of records) {
        await page.goto(`/admin/${record.admin}/create`);
        if ('type' in record) await page.getByLabel('Type').selectOption(record.type ?? 'news');
        for (const [label, value] of Object.entries(record.fields)) await page.locator(`[id="form.${label.toLowerCase()}"]`).fill(value);
        if (record.admin === 'services') {
            await page.getByRole('group', { name: 'Requirements' }).getByRole('textbox', { name: 'Value' }).fill('Test requirement');
            await page.getByRole('group', { name: 'Steps' }).getByRole('textbox', { name: 'Value' }).fill('Test step');
        }
        if (record.admin === 'officials' || record.public === 'news') {
            await page.locator(record.admin === 'officials' ? '[id="form.photo_media_id"]' : '[id="form.featured_media_id"]').click();
            await page.getByRole('listbox').getByRole('textbox', { name: 'Search' }).fill(imageTitle);
            await page.getByRole('option', { name: imageTitle }).click();
        }
        await page.getByRole('button', { name: 'Create', exact: true }).click();
        await expect(page).toHaveURL(new RegExp(`/admin/${record.admin}/\\d+/edit$`));
        await page.goto(`/admin/${record.admin}`);
        const row = page.getByRole('row').filter({ hasText: record.name });
        await expect(row).toContainText('draft');
        await row.getByRole('button', { name: 'Verify' }).click();
        await page.getByRole('button', { name: 'Confirm' }).click();
        await expect(row).toContainText('publishable');
        await row.getByRole('button', { name: 'Publish', exact: true }).click();
        await page.getByRole('button', { name: 'Confirm' }).click();
        await expect(row).toContainText('published');
        await page.goto(`/${record.public}/${record.slug}`);
        await expect(page.getByRole('heading', { level: 1 })).toContainText(record.name);
        if (record.admin === 'officials' || record.public === 'news') {
            await expect(page.locator('img[src*="/managed-media/"]')).toBeVisible();
        }
        await page.goto(`/admin/${record.admin}`);
        const publishedRow = page.getByRole('row').filter({ hasText: record.name });
        await publishedRow.getByRole('button', { name: 'Unpublish' }).click();
        await page.getByRole('button', { name: 'Confirm' }).click();
        await expect(publishedRow).toContainText('unpublished');
        const withdrawnResponse = await page.goto(`/${record.public}/${record.slug}`);
        expect(withdrawnResponse?.status()).toBe(404);
    }
});

test('administrator creates, restores, verifies, publishes, and withdraws a CMS page', async ({ page }) => {
    test.setTimeout(90000);
    const suffix = randomBytes(5).toString('hex');
    const title = `QA CMS ${suffix}`;
    const slug = `qa-cms-${suffix}`;
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
    await page.goto('/admin/pages/create');
    await page.locator('[id="form.title"]').fill(title);
    await page.locator('[id="form.slug"]').fill(slug);
    await page.locator('[id="form.summary"]').fill('Development-only CMS summary');
    await page.getByLabel('Type').selectOption('paragraph');
    await page.getByLabel('Text').fill('Development-only CMS body');
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/pages\/\d+\/edit$/);
    const editUrl = page.url();
    await page.locator('[id="form.summary"]').fill('Edited development-only summary');
    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/livewire') && response.request().method() === 'POST'),
        page.getByRole('button', { name: 'Save changes' }).click(),
    ]);
    await page.reload();
    await expect(page.locator('[id="form.summary"]')).toHaveValue('Edited development-only summary');
    await page.getByRole('button', { name: 'Restore revision' }).click();
    const restoreModal = page.getByRole('dialog');
    await expect(restoreModal.locator('.fi-modal-window')).toBeVisible();
    await restoreModal.getByLabel('Previous revision').selectOption({ label: 'Revision 1' });
    const [restoreRequest] = await Promise.all([
        page.waitForRequest(request => request.url().includes('/livewire') && request.method() === 'POST'),
        restoreModal.getByRole('button', { name: 'Submit' }).click(),
    ]);
    await expect(restoreModal.locator('.fi-modal-window')).toBeHidden();
    await expect(page.locator('[id="form.summary"]')).toHaveValue('Development-only CMS summary');
    await page.goto('/admin/pages');
    const row = page.getByRole('row').filter({ hasText: title });
    await expect(row).toContainText('draft');
    await row.getByRole('button', { name: 'Mark publishable' }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(row).toContainText('publishable');
    await row.getByRole('button', { name: 'Publish', exact: true }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(row).toContainText('published');
    await page.goto(`/pages/${slug}`);
    await expect(page.getByRole('heading', { level: 1 })).toContainText(title);
    await expect(page.getByText('Development-only CMS summary')).toBeVisible();
    await page.goto('/admin/pages');
    const publishedRow = page.getByRole('row').filter({ hasText: title });
    await publishedRow.getByRole('button', { name: 'Unpublish' }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(publishedRow).toContainText('unpublished');
    const response = await page.goto(`/pages/${slug}`);
    expect(response?.status()).toBe(404);
    artisan(`$page = App\\Models\\Page::where("slug", "${slug}")->firstOrFail(); $event = Illuminate\\Support\\Facades\\DB::table("audit_events")->where("action", "pages.restored")->where("subject_id", (string) $page->id)->first(); if (!$event || !$event->actor_id || !$event->created_at || $page->revisions()->count() < 5) throw new Exception("Restore audit or history missing");`);
    await page.context().clearCookies();
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(viewerEmail);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
    expect((await page.goto(editUrl))?.status()).toBe(403);
    await page.goto('/admin/pages');
    const forbiddenStatus = await page.evaluate(async ({ url, body }) => {
        const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
        const response = await fetch(url, {
            method: 'POST', credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Livewire': 'true' },
            body: JSON.stringify(body),
        });
        return response.status;
    }, { url: restoreRequest.url(), body: restoreRequest.postDataJSON() });
    expect([403, 404]).toContain(forbiddenStatus);
});

test('administrator uploads a PDF and publishes then withdraws a document', async ({ page }) => {
    test.setTimeout(180000);
    const suffix = randomBytes(5).toString('hex');
    const mediaTitle = `QA PDF ${suffix}`;
    const title = `QA document ${suffix}`;
    const slug = `qa-document-${suffix}`;
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
    await page.goto('/admin/media/create');
    await page.locator('input[type="file"]').setInputFiles({
        name: 'qa-document.pdf', mimeType: 'application/pdf',
        buffer: Buffer.from('%PDF-1.4\n1 0 obj\n<<>>\nendobj\n%%EOF'),
    });
    await expect(page.getByRole('alert')).toContainText('Upload complete');
    await page.locator('[id="form.title"]').fill(mediaTitle);
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/media\/\d+\/edit$/);
    await page.goto('/admin/documents/create');
    await page.locator('[id="form.title"]').fill(title);
    await page.locator('[id="form.slug"]').fill(slug);
    await page.getByLabel('Category').selectOption('other');
    await page.getByLabel('Media').click();
    await page.getByRole('listbox').getByRole('textbox', { name: 'Search' }).fill(mediaTitle);
    await page.getByRole('option', { name: mediaTitle }).click();
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/documents\/\d+\/edit$/);
    await page.goto('/admin/documents');
    const row = page.getByRole('row').filter({ hasText: title });
    await row.getByRole('button', { name: 'Verify' }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(row).toContainText('publishable');
    await row.getByRole('button', { name: 'Publish', exact: true }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(row).toContainText('published');
    const download = await page.request.get(`/documents/${slug}/download`);
    expect(download.status()).toBe(200);
    expect(download.headers()['content-type']).toContain('application/pdf');
    expect((await download.body()).toString()).toContain('%PDF-1.4');
    await page.goto('/admin/media/create');
    await page.locator('input[type="file"]').setInputFiles({
        name: 'replacement.pdf', mimeType: 'application/pdf',
        buffer: Buffer.from('%PDF-1.5\n1 0 obj\n<</Title (Replacement)>>\nendobj\n%%EOF'),
    });
    await expect(page.getByRole('alert')).toContainText('Upload complete');
    const replacementTitle = `${mediaTitle} replacement`;
    await page.locator('[id="form.title"]').fill(replacementTitle);
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/media\/\d+\/edit$/);
    await page.goto('/admin/documents');
    await page.getByRole('row').filter({ hasText: title }).getByRole('link', { name: title }).click();
    await page.getByLabel('Media').click();
    await page.getByRole('listbox').getByRole('textbox', { name: 'Search' }).fill(replacementTitle);
    await page.getByRole('option', { name: replacementTitle }).click();
    await page.getByRole('button', { name: 'Save changes' }).click();
    await page.reload();
    await expect(page.getByLabel('Media')).toContainText(replacementTitle);
    expect((await page.request.get(`/documents/${slug}/download`)).status()).toBe(404);
    await page.goto('/admin/documents');
    const replacementRow = page.getByRole('row').filter({ hasText: title });
    await expect(replacementRow).toContainText('draft');
    await replacementRow.getByRole('button', { name: 'Verify' }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(replacementRow).toContainText('publishable');
    await replacementRow.getByRole('button', { name: 'Publish', exact: true }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(replacementRow).toContainText('published');
    await expect.poll(async () => (await page.request.get(`/documents/${slug}/download`)).status(), { timeout: 15000 }).toBe(200);
    const replacedDownload = await page.request.get(`/documents/${slug}/download`);
    expect(replacedDownload.status()).toBe(200);
    expect((await replacedDownload.body()).toString()).toContain('Replacement');
    await page.goto('/admin/media/create');
    await page.locator('input[type="file"]').setInputFiles({ name: 'invalid.pdf', mimeType: 'application/pdf', buffer: Buffer.from('<?php echo 1;') });
    await page.locator('[id="form.title"]').fill('Invalid replacement');
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/media\/create$/);
    expect((await (await page.request.get(`/documents/${slug}/download`)).body()).toString()).toContain('Replacement');
    await page.goto('/admin/documents');
    const publishedRow = page.getByRole('row').filter({ hasText: title });
    await publishedRow.getByRole('button', { name: 'Unpublish' }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(publishedRow).toContainText('unpublished');
    expect((await page.request.get(`/documents/${slug}/download`)).status()).toBe(404);
    await page.goto('/admin/documents');
    await page.getByRole('row').filter({ hasText: title }).getByRole('link', { name: title }).click();
    await page.locator('[id="form.description"]').fill('Updated development-only document');
    await page.getByRole('button', { name: 'Save changes' }).click();
    await expect(page.locator('[id="form.description"]')).toHaveValue('Updated development-only document');
});

test('administrator creates and publishes an authoritative department profile', async ({ page }) => {
    test.setTimeout(90000);
    const suffix = randomBytes(5).toString('hex');
    const name = `QA department ${suffix}`;
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
    await page.goto('/admin/departments/create');
    await page.locator('[id="form.name"]').fill(name);
    await page.locator('[id="form.code"]').fill(`QA${suffix}`);
    await page.locator('[id="form.public_name"]').fill(name);
    await page.locator('[id="form.public_summary"]').fill('Development-only department summary');
    await page.getByRole('group', { name: 'Responsibilities' }).getByRole('textbox', { name: 'Value' }).fill('Development-only responsibility');
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/departments\/\d+$/);
    const id = page.url().match(/\/departments\/(\d+)$/)?.[1];
    expect(id).toBeTruthy();
    await page.goto('/admin/departments');
    const row = page.getByRole('row').filter({ hasText: name });
    await row.getByRole('button', { name: /verify public/i }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(row).toContainText('publishable');
    await row.getByRole('button', { name: 'Publish public', exact: true }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(row).toContainText('published');
    await page.goto(`/departments/${id}`);
    await expect(page.getByRole('heading', { level: 1 })).toContainText(name);
    await page.goto('/admin/departments');
    const publishedRow = page.getByRole('row').filter({ hasText: name });
    await publishedRow.getByRole('button', { name: 'Unpublish public', exact: true }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(publishedRow).toContainText('unpublished');
    expect((await page.goto(`/departments/${id}`))?.status()).toBe(404);
});

test('resident enquiry is routed, assigned, noted, and moved through staff workflow', async ({ page }) => {
    test.setTimeout(150000);
    const suffix = randomBytes(5).toString('hex');
    const departmentName = `QA routing ${suffix}`;
    const staffName = `QA staff ${suffix}`;
    const secondStaffName = `QA second staff ${suffix}`;
    const subject = `QA enquiry ${suffix}`;
    const otherSubject = `QA other enquiry ${suffix}`;
    artisan(`$department = App\\Models\\Department::factory()->create(["name" => "${departmentName}", "code" => "E${suffix}", "status" => "active"]); $role = Spatie\\Permission\\Models\\Role::firstOrCreate(["name" => "QA handler ${suffix}", "guard_name" => "web"]); $role->givePermissionTo(["admin.access", "enquiries.view", "enquiries.update"]); foreach ([["${staffName}", "qa-staff-${suffix}@example.test"], ["${secondStaffName}", "qa-second-staff-${suffix}@example.test"]] as [$name, $address]) { $staff = App\\Models\\User::factory()->create(["name" => $name, "email" => $address, "password" => "${password}", "department_id" => $department->id]); $staff->assignRole($role); Illuminate\\Support\\Facades\\DB::table("user_role_scopes")->insert(["user_id" => $staff->id, "role_id" => $role->id, "scope_type" => "department", "department_id" => $department->id, "created_at" => now(), "updated_at" => now()]); } $other = App\\Models\\Department::factory()->create(["name" => "QA other department ${suffix}", "code" => "O${suffix}", "status" => "active"]); App\\Models\\Enquiry::factory()->create(["department_id" => $other->id, "subject" => "${otherSubject}"]);`);
    await page.goto('/contact');
    await page.getByLabel('Name').fill('QA resident');
    await page.getByLabel('Email').fill(`resident-${suffix}@example.test`);
    await page.getByLabel('Subject').fill(subject);
    await page.getByLabel('Message').fill('Development test enquiry only.');
    await page.getByRole('button', { name: 'Send enquiry' }).click();
    await expect(page.getByRole('status')).toContainText('received');
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
    await page.goto('/admin/enquiries');
    await page.getByRole('row').filter({ hasText: subject }).getByRole('link').first().click();
    await expect(page.getByText(subject)).toBeVisible();
    await page.getByRole('button', { name: 'Route to department' }).click();
    await page.getByRole('dialog').getByRole('combobox', { name: /Department/ }).selectOption({ label: departmentName });
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await expect(page.getByRole('dialog')).toBeHidden();
    await page.goto('/admin/enquiries');
    await page.getByRole('row').filter({ hasText: subject }).getByRole('link').first().click();
    await expect(page.getByText(departmentName)).toBeVisible();
    await page.getByRole('button', { name: 'Assign staff' }).click();
    await page.getByRole('dialog').getByRole('combobox', { name: /Staff member/ }).selectOption({ label: staffName });
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await expect(page.getByRole('dialog')).toBeHidden();
    await page.goto('/admin/enquiries');
    await page.getByRole('row').filter({ hasText: subject }).getByRole('link').first().click();
    await expect(page.getByText(staffName)).toBeVisible();
    await page.getByRole('button', { name: 'Assign staff' }).click();
    await page.getByRole('dialog').getByRole('combobox', { name: /Staff member/ }).selectOption({ label: secondStaffName });
    const [reassignmentRequest] = await Promise.all([
        page.waitForRequest(request => request.url().includes('/livewire') && request.method() === 'POST'),
        page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click(),
    ]);
    await expect(page.getByRole('dialog')).toBeHidden();
    await page.reload();
    await expect(page.getByText(secondStaffName)).toBeVisible();
    artisan(`$enquiry = App\\Models\\Enquiry::where("subject", "${subject}")->firstOrFail(); $audit = Illuminate\\Support\\Facades\\DB::table("audit_events")->where("action", "enquiries.assigned")->where("subject_id", (string) $enquiry->id)->orderByDesc("id")->first(); if (!$audit || !$audit->actor_id || !str_contains($audit->metadata, "from_user_id") || !str_contains($audit->metadata, "to_user_id") || $enquiry->status !== "new") throw new Exception("Reassignment audit or state missing");`);
    await page.getByRole('button', { name: 'Assign staff' }).click();
    await page.getByRole('dialog').getByRole('combobox', { name: /Staff member/ }).evaluate(element => {
        const option = document.createElement('option');
        option.value = '999999';
        option.textContent = 'Invalid assignee';
        element.append(option);
        (element as HTMLSelectElement).value = '999999';
        element.dispatchEvent(new Event('change', { bubbles: true }));
    });
    const invalidAssigneeResponse = await Promise.all([
        page.waitForResponse(response => response.url().includes('/livewire') && response.request().method() === 'POST'),
        page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click(),
    ]).then(([response]) => response);
    expect([404, 422]).toContain(invalidAssigneeResponse.status());
    await page.reload();
    await expect(page.getByText(secondStaffName)).toBeVisible();
    await page.getByRole('button', { name: 'Add internal note' }).click();
    await page.getByRole('dialog').getByRole('textbox', { name: /Note/ }).fill('QA internal note only');
    await page.getByRole('dialog').getByRole('button', { name: 'Submit' }).click();
    await expect(page.getByRole('dialog')).toBeHidden();
    await page.goto('/admin/enquiries');
    await page.getByRole('row').filter({ hasText: subject }).getByRole('link').first().click();
    await expect(page.getByText('QA internal note only')).toBeVisible();
    await page.getByRole('button', { name: 'Mark in progress' }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(page.getByRole('dialog')).toBeHidden();
    await page.goto('/admin/enquiries');
    await expect(async () => {
        await page.reload();
        await expect(page.getByRole('row').filter({ hasText: subject })).toContainText('in_progress');
    }).toPass({ timeout: 15000 });
    const ownUrl = await page.getByRole('row').filter({ hasText: subject }).getByRole('link').first().getAttribute('href');
    const otherUrl = await page.getByRole('row').filter({ hasText: otherSubject }).getByRole('link').first().getAttribute('href');
    expect(ownUrl).toBeTruthy();
    expect(otherUrl).toBeTruthy();
    await page.context().clearCookies();
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(`qa-staff-${suffix}@example.test`);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
    expect((await page.goto(ownUrl!))?.status()).toBe(200);
    await expect(page.getByRole('button', { name: 'Assign staff' })).toHaveCount(0);
    const forbiddenAssignment = await page.evaluate(async ({ url, body }) => {
        const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content ?? '';
        const response = await fetch(url, {
            method: 'POST', credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'X-Livewire': 'true' },
            body: JSON.stringify(body),
        });
        return response.status;
    }, { url: reassignmentRequest.url(), body: reassignmentRequest.postDataJSON() });
    expect([403, 404]).toContain(forbiddenAssignment);
    expect([403, 404]).toContain((await page.goto(otherUrl!))?.status());
});

test('media form rejects an executable upload and keeps its file required', async ({ page }) => {
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
    await page.goto('/admin/media/create');
    await page.locator('input[type="file"]').setInputFiles({
        name: 'payload.php', mimeType: 'application/x-php', buffer: Buffer.from('<?php echo 1;'),
    });
    await page.locator('[id="form.title"]').fill('Rejected executable');
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/media\/create$/);
    await expect(page.getByText('The file field is required.')).toBeVisible();
});

test('administrator edits media metadata and archives a published image', async ({ page }) => {
    test.setTimeout(90000);
    const suffix = randomBytes(5).toString('hex');
    const title = `QA archive image ${suffix}`;
    const slug = `qa-media-official-${suffix}`;
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
    await page.goto('/admin/media/create');
    await page.locator('input[type="file"]').setInputFiles({
        name: 'archive-image.png', mimeType: 'image/png',
        buffer: Buffer.from('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/pWQAAAAASUVORK5CYII=', 'base64'),
    });
    await expect(page.getByRole('alert')).toContainText('Upload complete');
    await page.locator('[id="form.title"]').fill(title);
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/media\/\d+\/edit$/);
    const mediaId = page.url().match(/\/media\/(\d+)\/edit$/)?.[1];
    expect(mediaId).toBeTruthy();
    await page.locator('[id="form.alt_text"]').fill('Updated public image description');
    await page.locator('[id="form.caption"]').fill('Updated council image caption');
    await page.getByRole('button', { name: 'Save changes' }).click();
    await page.reload();
    await expect(page.locator('[id="form.alt_text"]')).toHaveValue('Updated public image description');
    await expect(page.locator('[id="form.caption"]')).toHaveValue('Updated council image caption');
    artisan(`$official = new App\\Models\\Official(["slug" => "${slug}", "name" => "QA media official ${suffix}", "title" => "QA title", "photo_media_id" => ${mediaId}, "display_order" => 0]); $official->forceFill(["status" => "published", "verification_status" => "publishable", "published_at" => now()])->save();`);
    expect((await page.request.get(`/managed-media/${mediaId}`)).status()).toBe(200);
    await page.goto('/admin/media');
    const row = page.getByRole('row').filter({ hasText: title });
    await row.getByRole('button', { name: 'Archive' }).click();
    await page.getByRole('button', { name: 'Confirm' }).click();
    await expect(row).toContainText('archived');
    expect((await page.request.get(`/managed-media/${mediaId}`)).status()).toBe(404);
    expect((await page.goto(`/officials/${slug}`))?.status()).toBe(404);
    artisan(`$media = App\\Models\\Media::findOrFail(${mediaId}); if (!Illuminate\\Support\\Facades\\DB::table("audit_events")->where("action", "media.archived")->where("subject_id", (string) $media->id)->whereNotNull("actor_id")->exists()) throw new Exception("Missing media archive audit");`);
});

test('notice expiry and publication boundaries are enforced by public responses', async ({ page }) => {
    const suffix = randomBytes(5).toString('hex');
    const states = [
        { label: 'active', published: 'now()', expiry: 'today()' },
        { label: 'future', published: 'now()->addDay()', expiry: 'today()->addDays(2)' },
        { label: 'expired', published: 'now()->subDays(2)', expiry: 'today()->subDay()' },
    ];
    for (const state of states) {
        const slug = `qa-${state.label}-notice-${suffix}`;
        artisan(`$notice = new App\\Models\\EditorialItem(["type" => "notice", "slug" => "${slug}", "title" => "QA ${state.label} notice ${suffix}", "body" => "QA notice body", "display_order" => 0, "expires_at" => ${state.expiry}]); $notice->forceFill(["status" => "published", "verification_status" => "publishable", "published_at" => ${state.published}])->save();`);
    }
    await page.goto('/notices');
    await expect(page.getByText(`QA active notice ${suffix}`)).toBeVisible();
    await expect(page.getByText(`QA future notice ${suffix}`)).toHaveCount(0);
    await expect(page.getByText(`QA expired notice ${suffix}`)).toHaveCount(0);
    expect((await page.request.get(`/notices/qa-active-notice-${suffix}`)).status()).toBe(200);
    expect((await page.request.get(`/notices/qa-future-notice-${suffix}`)).status()).toBe(404);
    expect((await page.request.get(`/notices/qa-expired-notice-${suffix}`)).status()).toBe(404);
});

test('page form rejects required-field, invalid state, and duplicate slug manipulation', async ({ page }) => {
    test.setTimeout(90000);
    const suffix = randomBytes(5).toString('hex');
    const slug = `qa-validation-${suffix}`;
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(email);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
    await page.goto('/admin/pages/create');
    await page.locator('[id="form.slug"]').fill(slug);
    await page.getByLabel('Type').selectOption('paragraph');
    await page.getByLabel('Text').fill('QA validation body');
    await page.locator('[id="form.title"]').evaluate(element => element.removeAttribute('required'));
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/pages\/create$/);
    await expect(page.getByText(/title.*required/i)).toBeVisible();
    await page.locator('[id="form.title"]').fill(`QA validation ${suffix}`);
    await page.getByLabel('Type').evaluate((element) => {
        const option = document.createElement('option');
        option.value = 'invalid_state';
        option.textContent = 'Invalid state';
        element.append(option);
        (element as HTMLSelectElement).value = 'invalid_state';
        element.dispatchEvent(new Event('change', { bubbles: true }));
    });
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/pages\/create$/);
    await page.getByLabel('Type').selectOption('paragraph');
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/pages\/\d+\/edit$/);
    expect((await page.goto('/admin/pages/not-an-id/edit'))?.status()).toBe(404);
    await page.goto('/admin/pages/create');
    await page.locator('[id="form.title"]').fill('Duplicate slug attempt');
    await page.locator('[id="form.slug"]').fill(slug);
    await page.getByLabel('Type').selectOption('paragraph');
    await page.getByLabel('Text').fill('QA duplicate slug body');
    await page.getByRole('button', { name: 'Create', exact: true }).click();
    await expect(page).toHaveURL(/\/admin\/pages\/create$/);
    await expect(page.getByText('The slug has already been taken.')).toBeVisible();
});


test('panel user without contact permission cannot open the resource URL', async ({ page }) => {
    await page.goto('/admin/login');
    await page.locator('input[type="email"]').fill(limitedEmail);
    await page.locator('input[type="password"]').fill(password);
    await page.getByRole('button', { name: /sign in/i }).click();
    await expect(page).toHaveURL(/\/admin\/?$/);
    for (const path of [
        '/admin/pages', '/admin/media', '/admin/documents', '/admin/departments',
        '/admin/services', '/admin/editorial/editorial-items', '/admin/wards',
        '/admin/officials', '/admin/public-contacts', '/admin/enquiries',
    ]) {
        const response = await page.goto(path);
        expect(response?.status(), path).toBe(403);
    }
});
