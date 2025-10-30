from playwright.sync_api import sync_playwright, expect
import time

def run(playwright):
    browser = playwright.chromium.launch()
    page = browser.new_page()

    # --- Verification Steps ---

    # 1. Login Page
    page.goto("http://localhost:3001/login.html")
    expect(page.locator("#login-form")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/01_login_page.png")

    # 2. Attempt Login
    page.fill("#username", "admin")
    page.fill("#password", "password")
    page.click("button[type='submit']")

    # Wait for navigation to admin page to complete
    page.wait_for_url("http://localhost:3001/admin.html")
    expect(page).to_have_title("Admin Panel - Content Management")
    page.screenshot(path="jules-scratch/verification/02_admin_panel.png")

    # 3. Modify and Save Content
    # Enable popup and set image
    page.check("#popup-enabled")
    page.fill("#popup-title", "Special Offer!")
    page.fill("#popup-message", "Enjoy a complimentary breakfast for bookings made this week.")
    page.fill("#popup-image", "https://rumahdaisycantik.com/wp-content/uploads/1_bedroom-2_August-15.jpeg")

    # Change parallax background
    page.fill("#parallax-bg", "https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0") # A different image

    # Click save
    page.click("#save-all-btn")

    # Wait for the save confirmation message
    expect(page.locator("#save-message")).to_have_text("Content updated successfully!")
    time.sleep(1) # Give a moment for UI to potentially update

    # 4. Verify Index Page (with changes)
    page.goto("http://localhost:3001/")

    # Check for popup
    expect(page.locator("#popup-modal")).to_be_visible()
    expect(page.locator("#popup-title")).to_have_text("Special Offer!")
    expect(page.locator("#popup-image")).to_have_attribute("src", "https://rumahdaisycantik.com/wp-content/uploads/1_bedroom-2_August-15.jpeg")
    page.screenshot(path="jules-scratch/verification/03_index_with_popup.png")

    # Close popup to see parallax
    page.click("#close-popup-btn")
    expect(page.locator("#popup-modal")).to_be_hidden()

    # Check parallax background (this is tricky to verify with a screenshot alone, but we'll take one)
    # A more robust test would inspect the CSS property, which we'll do.
    parallax_element = page.locator('.parallax-bg')
    expect(parallax_element).to_have_css('background-image', 'url("https://images.unsplash.com/photo-1506748686214-e9df14d4d9d0")')
    page.screenshot(path="jules-scratch/verification/04_index_with_parallax.png")

    # 5. Logout and Verify
    page.goto("http://localhost:3001/logout")
    page.wait_for_url("http://localhost:3001/login.html")
    expect(page.locator("#login-form")).to_be_visible()

    # Try accessing admin.html directly
    page.goto("http://localhost:3001/admin.html")
    # Should be redirected back to login
    expect(page).to_have_url("http://localhost:3001/login.html")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
