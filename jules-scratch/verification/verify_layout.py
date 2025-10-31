import asyncio
from playwright.async_api import async_playwright
import os

async def main():
    async with async_playwright() as p:
        browser = await p.chromium.launch()
        page = await browser.new_page()

        # Get the absolute path to the HTML file
        file_path = os.path.abspath('about.html')

        # Navigate to the local HTML file
        await page.goto(f'file://{file_path}')

        # Wait for the content to be loaded
        await page.wait_for_selector('#content-container')

        # Take a screenshot
        await page.screenshot(path='jules-scratch/verification/about-page.png')

        await browser.close()

if __name__ == '__main__':
    asyncio.run(main())
