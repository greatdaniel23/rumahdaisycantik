const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();

  const screenshotsDir = 'jules-scratch/verification';

  // --- Step 1: Verify Admin Page ---
  console.log('Navigating to admin page...');
  await page.goto('http://localhost:3001/admin.html');
  await page.screenshot({ path: `${screenshotsDir}/01_admin_panel_after_cleanup.png`, fullPage: true });
  console.log('Admin page screenshot captured.');

  // Check that no "villa" images are present
  const villaImageCount = await page.locator('text=villa-').count();
  if (villaImageCount > 0) {
    console.error('Error: Found obsolete "villa" images on the admin page.');
  } else {
    console.log('Verified: Obsolete "villa" images are not present on the admin page.');
  }


  // --- Step 2: Verify Main Website ---
  console.log('Navigating to the main website...');
  await page.goto('http://localhost:3001/');
  await page.screenshot({ path: `${screenshotsDir}/02_main_site_after_cleanup.png`, fullPage: true });
  console.log('Main website screenshot captured.');


  await browser.close();
  console.log('Verification script finished.');
})();
