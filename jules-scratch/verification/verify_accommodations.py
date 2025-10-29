
from playwright.sync_api import sync_playwright

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page()

        # Verify index.html
        page.goto("http://localhost:3001")
        page.screenshot(path="jules-scratch/verification/index_accommodations.png")

        # Verify admin.html
        page.goto("http://localhost:3001/admin.html")
        page.screenshot(path="jules-scratch/verification/admin_accommodations.png")

        browser.close()

run()
