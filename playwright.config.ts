import { defineConfig } from '@playwright/test';
export default defineConfig({ testDir: 'tests/e2e', use: { baseURL: 'http://127.0.0.1:8000', launchOptions: { executablePath: process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE } }, webServer: { command: 'php artisan serve --host=127.0.0.1 --port=8000', url: 'http://127.0.0.1:8000', reuseExistingServer: !process.env.CI } });
