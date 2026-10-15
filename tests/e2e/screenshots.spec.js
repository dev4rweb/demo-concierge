import { test } from '@playwright/test';
import fs from 'fs';
import path from 'path';

const widths = [320, 768, 1440];
const themes = ['dark', 'light'];
const artifactDir = process.env.SCREENSHOT_DIR || '/opt/cursor/artifacts/screenshots';

async function setTheme(page, theme) {
  await page.addInitScript((value) => {
    localStorage.setItem('theme', value);
    document.documentElement.classList.remove('dark', 'light');
    document.documentElement.classList.add(value);
    document.documentElement.style.colorScheme = value;
  }, theme);
}

async function login(page) {
  await page.goto('/login');
  await page.fill('input#email', 'admin@demo.local');
  await page.fill('input#password', 'demo123456');
  await page.click('button:has-text("Log in")');
  await page.waitForURL(/\/(admin\/)?dashboard/, { timeout: 15000 });
}

async function shot(page, name) {
  fs.mkdirSync(artifactDir, { recursive: true });
  await page.screenshot({
    path: path.join(artifactDir, `${name}.png`),
    fullPage: true,
  });
}

for (const theme of themes) {
  for (const width of widths) {
    test.describe(`${theme} ${width}px`, () => {
      test.use({ viewport: { width, height: 900 } });

      test.beforeEach(async ({ page }) => {
        await setTheme(page, theme);
      });

      test(`welcome`, async ({ page }) => {
        await page.goto('/');
        await page.waitForSelector('h1');
        await shot(page, `welcome-${theme}-${width}`);
      });

      test(`login`, async ({ page }) => {
        await page.goto('/login');
        await page.waitForSelector('input#email');
        await shot(page, `login-${theme}-${width}`);
      });

      test(`howto-en`, async ({ page }) => {
        await page.goto('/how-to');
        await page.waitForSelector('h1');
        await shot(page, `howto-en-${theme}-${width}`);
      });

      test(`howto-ar`, async ({ page }) => {
        await page.goto('/ar/how-to');
        await page.waitForSelector('h1');
        await shot(page, `howto-ar-${theme}-${width}`);
      });

      test(`about-en`, async ({ page }) => {
        await page.goto('/about');
        await page.waitForSelector('h1');
        await shot(page, `about-en-${theme}-${width}`);
      });

      test(`about-ar`, async ({ page }) => {
        await page.goto('/ar/about');
        await page.waitForSelector('h1');
        await shot(page, `about-ar-${theme}-${width}`);
      });

      test(`admin pages`, async ({ page }) => {
        await login(page);
        await shot(page, `dashboard-${theme}-${width}`);

        const dialogLink = page.locator('a[href*="conversations"]').first();
        await dialogLink.click();
        await page.waitForURL(/conversations/);
        await shot(page, `dialog-${theme}-${width}`);

        await page.goto('/admin/leads');
        await page.waitForSelector('h2');
        await shot(page, `leads-${theme}-${width}`);

        await page.goto('/admin/knowledge-base');
        await page.waitForSelector('h2');
        await shot(page, `knowledge-base-${theme}-${width}`);
      });
    });
  }
}
