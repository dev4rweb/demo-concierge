import { test, expect } from '@playwright/test';

test.describe('Admin Panel Link on Welcome Page', () => {
  test('logged-out user clicking admin panel card should reach login page', async ({ page }) => {
    // Navigate to welcome page
    await page.goto('/');
    
    // Wait for the page to load
    await expect(page.locator('h1')).toContainText('AI Concierge Demo');
    
    // Find and click the admin panel card
    const adminCard = page.locator('a:has-text("Админ-панель")');
    await expect(adminCard).toBeVisible();
    await adminCard.click();
    
    // Should be redirected to login page
    await expect(page).toHaveURL(/\/login/);
    // Check for login form presence (using id selector since TextInput doesn't forward name attribute)
    await expect(page.locator('input#email')).toBeVisible();
    await expect(page.locator('button:has-text("Log in")')).toBeVisible();
  });

  test('logged-in user clicking admin panel card should reach dashboard', async ({ page }) => {
    // Navigate to login page
    await page.goto('/login');
    
    // Wait for login form to load
    await page.waitForSelector('input#email', { timeout: 10000 });
    
    // Fill in login credentials
    await page.fill('input#email', 'admin@demo.local');
    await page.fill('input#password', 'demo123456');
    
    // Submit login form (Inertia form, so no full page navigation)
    await page.click('button:has-text("Log in")');
    
    // Wait for redirect to dashboard (Inertia navigation)
    await page.waitForURL(/\/admin\/dashboard/, { timeout: 15000 });
    
    // Go back to welcome page
    await page.goto('/');
    
    // Click admin panel card
    const adminCard = page.locator('a:has-text("Админ-панель")');
    await expect(adminCard).toBeVisible();
    
    await Promise.all([
      page.waitForLoadState('networkidle', { timeout: 10000 }),
      adminCard.click()
    ]);
    
    // Should go directly to dashboard (not stuck or redirected to non-existent page)
    await expect(page).toHaveURL(/\/admin\/dashboard/, { timeout: 10000 });
    
    // Verify we're on a valid page with content
    const bodyText = await page.textContent('body');
    expect(bodyText.length).toBeGreaterThan(100);
  });

  test('logged-in user should not encounter 404 or empty page', async ({ page }) => {
    // Login first
    await page.goto('/login');
    await page.waitForSelector('input#email', { timeout: 10000 });
    await page.fill('input#email', 'admin@demo.local');
    await page.fill('input#password', 'demo123456');
    
    // Submit login form (Inertia form)
    await page.click('button:has-text("Log in")');
    
    // Wait for redirect to dashboard (Inertia navigation)
    await page.waitForURL(/\/admin\/dashboard/, { timeout: 15000 });
    
    // Go to welcome page
    await page.goto('/');
    
    // Click admin panel card
    await page.click('a:has-text("Админ-панель")');
    
    // Wait a bit to ensure navigation completes
    await page.waitForLoadState('networkidle', { timeout: 10000 });
    
    // Should not see 404 or empty page
    const bodyText = await page.textContent('body');
    expect(bodyText).not.toContain('404');
    expect(bodyText).not.toContain('Not Found');
    expect(bodyText.length).toBeGreaterThan(100);
    
    // Should be on a valid page (dashboard)
    await expect(page).toHaveURL(/\/admin\/dashboard/, { timeout: 10000 });
  });
});
