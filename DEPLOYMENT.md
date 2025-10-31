# Rumah Daisy Cantik - Static Website Deployment Guide

This guide provides instructions for deploying the Rumah Daisy Cantik website, which is a static website with a serverless function for Google Reviews.

## Deployment Steps

### Step 1: Prepare Your Files for Upload

1.  Run the build script locally to create the `build` directory:
    ```bash
    npm run build
    ```
2.  Compress the contents of the `build` directory into a single ZIP file (e.g., `rumah-daisy-cantik.zip`).

### Step 2: Upload Your Application Files to cPanel

1.  Log in to your cPanel and navigate to the **"File Manager"**.
2.  Navigate to the `public_html` directory.
3.  **Upload** the ZIP file you created in Step 1.
4.  **Extract** the contents of the ZIP file into the `public_html` directory.

### Step 3: Deploy the Serverless Function

The Google Reviews feature is powered by a serverless function. You will need to deploy the `reviews.js` file to a serverless function provider (e.g., Netlify Functions, Vercel Serverless Functions, AWS Lambda).

1.  Create a new serverless function on your chosen provider.
2.  Set the `GOOGLE_API_KEY` environment variable to your Google API key.
3.  Deploy the `reviews.js` file as the function code.
4.  Make sure the function is accessible at a public URL (e.g., `https://your-domain.com/.netlify/functions/reviews`).
5.  Update the `fetch` URL in `index.html` to point to your serverless function's URL.

## Admin Panel Usage

The admin panel is now an offline tool. It allows you to edit text content, manage accommodation packages, and configure site features.

To use it:

1.  Open the `admin.html` file in your local browser (it does not need to be running on a server).
2.  The panel will automatically load the content from the `content.json` file located in the same directory.
3.  Make your desired changes to the text, accommodations, or other settings.
4.  Click the **"Save and Download content.json"** button at the bottom of the page.
5.  Your browser will download the updated `content.json` file.
6.  Upload this new `content.json` file to your web hosting server (e.g., in the `public_html` directory), overwriting the old one. The changes will be live immediately.

### Managing Images

The admin panel does not have an image upload feature. To change an image, you must:

1.  Upload the new image file to your web hosting server (e.g., into an `images/` directory).
2.  In the admin panel, find the section corresponding to the image you want to change (e.g., an accommodation package or the parallax background).
3.  Update the "Image Source" or "Background Image URL" field with the correct path to your newly uploaded image (e.g., `/images/new-villa-image.jpg`).
4.  Save and download the `content.json` file as described above.

## Updating the Admin Password

The admin password is a SHA-256 hash stored in `login.html`. To change the password:

1.  Choose a new password.
2.  Use an online tool or a local script to generate the SHA-256 hash of your new password.
3.  Replace the `storedHash` value in `login.html` with your new hash.

## ✅ Post-Deployment Checklist

- [ ] Test that your website loads correctly at your domain.
- [ ] Verify that all images and content are loading correctly.
- [ ] Verify that the Google Reviews are loading correctly.

## 🔧 Common Issues

- **Images not loading**: Check file paths and case sensitivity.
- **CSS not loading**: Verify the file path in your HTML files.
- **404 errors**: Make sure all files were uploaded correctly.
- **Google Reviews not loading**: Check that your serverless function is deployed correctly and that the `GOOGLE_API_KEY` is set.
