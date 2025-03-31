import { test, expect } from '@playwright/test';

test.describe('Blog', () => {
  test('should have expected title on homepage', async ({ page }) => {
    // Go to the homepage
    await page.goto('/');
    
    // Check that the title in the html head is correct 
    await expect(page).toHaveTitle(/JYu's Blog/);
  });

  test('should check pages exist with expected URLs', async ({ page }) => {
    // Go to the blog page
    await page.goto('/blog');

    // Verify we're on the blog page
    await expect(page).toHaveURL(/\/blog(\/)?$/);
    
    // Go to a specific blog post path
    await page.goto('/blog/getting-started-with-laravel-and-vue');
    
    // Verify we're on the post detail page
    await expect(page).toHaveURL(/\/blog\/.+/);
  });

  test('should check category and tag URLs', async ({ page }) => {
    // Go to a category page
    await page.goto('/category/technology');
    
    // Verify we're on a category page
    await expect(page).toHaveURL(/\/category\/.+/);
    
    // Go to a tag page
    await page.goto('/tag/laravel');
    
    // Verify we're on a tag page
    await expect(page).toHaveURL(/\/tag\/.+/);
  });
});