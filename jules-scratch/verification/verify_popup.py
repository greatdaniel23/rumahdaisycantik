from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch()
    page = browser.new_page()
    page.goto("http://localhost:3001")

    # Wait for the popup to appear and take a screenshot
    popup = page.locator("#popup-modal")
    expect(popup).to_be_visible()
    page.screenshot(path="jules-scratch/verification/01_popup_visible.png")

    # Test the 'X' close button
    close_button = page.locator("#close-popup-btn")
    close_button.click()
    expect(popup).to_be_hidden()
    page.screenshot(path="jules-scratch/verification/02_popup_hidden_after_x.png")

    # Re-enable the popup for the next test
    page.evaluate('document.getElementById("popup-modal").classList.remove("hidden")')
    expect(popup).to_be_visible()

    # Test clicking the overlay to close
    popup.click(position={'x': 10, 'y': 10}) # Click outside the modal content
    expect(popup).to_be_hidden()
    page.screenshot(path="jules-scratch/verification/03_popup_hidden_after_overlay_click.png")

    browser.close()

with sync_playwright() as playwright:
    run(playwright)
