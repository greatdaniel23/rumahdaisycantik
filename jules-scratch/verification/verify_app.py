from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch()
    page = browser.new_page()

    # Verify index.html
    page.goto("http://localhost:3001")
    page.screenshot(path="jules-scratch/verification/verification-index.png")

    # Verify admin.html
    page.goto("http://localhost:3001/admin.html")
    page.screenshot(path="jules-scratch/verification/verification-admin.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
