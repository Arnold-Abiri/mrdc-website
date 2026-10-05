import { defineConfig } from '@playwright/test';
export default defineConfig({ testDir: 'tests/e2e', use: { baseURL: 'http://127.0.0.1:18437', launchOptions: { executablePath: process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE } }, webServer: { command: 'php -S 127.0.0.1:18437 -t public', url: 'http://127.0.0.1:18437', reuseExistingServer: !process.env.CI } });
