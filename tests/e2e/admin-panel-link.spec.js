import { test, expect } from '@playwright/test';

test.describe('Admin Panel Link on Welcome Page', () => {
  test('logged-out user clicking admin panel card should reach login page', async ({ page }) => {
    await page.goto('/');

    await expect(page.locator('h1')).toContainText('AI Concierge Demo');

    const adminCard = page.locator('a:has-text("Admin Panel")');
    await expect(adminCard).toBeVisible();
    await adminCard.click();

    await expect(page).toHaveURL(/\/login/);
    await expect(page.locator('input#email')).toBeVisible();
    await expect(page.locator('button:has-text("Log in")')).toBeVisible();
  });

  test('logged-in user clicking admin panel card should reach dashboard', async ({ page }) => {
    await page.goto('/login');
    await page.waitForSelector('input#email', { timeout: 10000 });
    await page.fill('input#email', 'admin@demo.local');
    await page.fill('input#password', 'demo123456');
    await page.click('button:has-text("Log in")');
    await page.waitForURL(/\/(admin\/)?dashboard/, { timeout: 15000 });

    await page.goto('/');

    const adminCard = page.locator('a:has-text("Admin Panel")');
    await expect(adminCard).toBeVisible();

    await Promise.all([
      page.waitForLoadState('networkidle', { timeout: 10000 }),
      adminCard.click()
    ]);

    await expect(page).toHaveURL(/\/(admin\/)?dashboard/, { timeout: 10000 });

    const bodyText = await page.textContent('body');
    expect(bodyText.length).toBeGreaterThan(100);
  });

  test('logged-in user should not encounter 404 or empty page', async ({ page }) => {
    await page.goto('/login');
    await page.waitForSelector('input#email', { timeout: 10000 });
    await page.fill('input#email', 'admin@demo.local');
    await page.fill('input#password', 'demo123456');
    await page.click('button:has-text("Log in")');
    await page.waitForURL(/\/(admin\/)?dashboard/, { timeout: 15000 });

    await page.goto('/');
    await page.click('a:has-text("Admin Panel")');
    await page.waitForLoadState('networkidle', { timeout: 10000 });

    const bodyText = await page.textContent('body');
    expect(bodyText).not.toContain('404');
    expect(bodyText).not.toContain('Not Found');
    expect(bodyText.length).toBeGreaterThan(100);

    await expect(page).toHaveURL(/\/(admin\/)?dashboard/, { timeout: 10000 });
  });
});
