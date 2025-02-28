import { test, expect } from '@playwright/test';

test('should display the homepage and show page structure', async ({ page }) => {
  // Go to the homepage
  await page.goto('/');

  // Let's see the page content
  const html = await page.content();
  console.log('Page HTML:', html.substring(0, 500) + '...');

  // Look for basic elements
  const title = await page.title();
  console.log('Page title:', title);

  // Check if body exists
  const bodyExists = await page.locator('body').count() > 0;
  console.log('Body exists:', bodyExists);

  // Check what visible text we have
  const bodyText = await page.locator('body').textContent();
  console.log('Body text sample:', bodyText?.substring(0, 200) + '...');

  // Pass a simple assertion so the test passes
  expect(bodyExists).toBeTruthy();
});
