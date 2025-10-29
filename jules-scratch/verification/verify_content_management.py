from playwright.sync_api import sync_playwright

def run(playwright):
    browser = playwright.chromium.launch()
    context = browser.new_context()
    page = context.new_page()

    # Go to index.html and take a screenshot
    page.goto("http://localhost:3001/index.html")
    page.wait_for_load_state('networkidle')
    page.screenshot(path="jules-scratch/verification/verification-index.png")

    # Go to admin.html and take a screenshot
    page.goto("http://localhost:3001/admin.html")
    page.wait_for_load_state('networkidle')
    page.screenshot(path="jules-scratch/verification/verification-admin.png")


    browser.close()

with sync_playwright() as playwright:
    run(playwright)
