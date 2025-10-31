
from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch()
    page = browser.new_page()

    # Check for redirect
    page.goto("http://localhost:3002/admin.html")
    expect(page).to_have_url("http://localhost:3002/login.html")

    # Log in
    page.fill("#username", "admin")
    page.fill("#password", "password")
    page.click("button[type=submit]")

    # Wait for navigation and check URL
    page.wait_for_url("http://localhost:3002/admin.html")
    expect(page).to_have_url("http://localhost:3002/admin.html")

    # Take screenshot
    page.screenshot(path="jules-scratch/verification/build_admin_panel.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
