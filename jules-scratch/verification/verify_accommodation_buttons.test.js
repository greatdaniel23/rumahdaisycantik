const { test, expect } = require('@playwright/test');

test.describe('Accommodation Button Management', () => {
  const ADMIN_URL = 'http://localhost:3001/admin.html';
  const WEBSITE_URL = 'http://localhost:3001/';
  const LOGIN_URL = 'http://localhost:3001/login.html';
  const USERNAME = process.env.ADMIN_USERNAME || 'admin';
  const PASSWORD = process.env.ADMIN_PASSWORD || 'password';

  test('should allow admin to update accommodation button text and links', async ({ page }) => {
    // 1. Log in to the admin panel
    await page.goto(LOGIN_URL);
    await page.fill('#username', USERNAME);
    await page.fill('#password', PASSWORD);
    await page.click('button[type="submit"]');
    await expect(page).toHaveURL(ADMIN_URL);

    // Take a screenshot of the admin panel before changes
    await page.screenshot({ path: 'jules-scratch/verification/01_admin_before_acc_button_edit.png' });

    // 2. Locate the first accommodation package's button fields and update them
    const buttonTextField = page.locator('#pkg-btn-text-0');
    const buttonLinkField = page.locator('#pkg-btn-link-0');

    const newButtonText = 'Book This Villa';
    const newButtonLink = 'https://booking.com/villa-1';

    await buttonTextField.fill(newButtonText);
    await buttonLinkField.fill(newButtonLink);

    // Take a screenshot showing the edited fields
    await page.screenshot({ path: 'jules-scratch/verification/02_admin_during_acc_button_edit.png' });

    // 3. Save the changes
    await page.click('#save-all-btn');

    // Wait for the success message to appear
    await expect(page.locator('#save-message')).toContainText('Content updated successfully!', { timeout: 10000 });

    // Take a screenshot after saving
    await page.screenshot({ path: 'jules-scratch/verification/03_admin_after_acc_button_save.png' });

    // 4. Visit the main website to verify the changes
    await page.goto(WEBSITE_URL);

    // 5. Check the first accommodation card's button
    const accommodationContainer = page.locator('#accommodations-container');
    const firstPackageButton = accommodationContainer.locator('a').first();

    // Check if the text has been updated
    await expect(firstPackageButton).toContainText(newButtonText);

    // Check if the link (href attribute) has been updated
    await expect(firstPackageButton).toHaveAttribute('href', newButtonLink);

    // Take a final screenshot of the website with the updated button
    await page.screenshot({ path: 'jules-scratch/verification/04_website_with_updated_acc_button.png' });
  });
});
