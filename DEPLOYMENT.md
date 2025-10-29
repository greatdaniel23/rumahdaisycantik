# Rumah Daisy Cantik - cPanel Deployment Guide

## 📁 Files to Upload to cPanel

Upload these files to your `public_html` folder:

### Essential Files:
- `index.html` - Main website file
- `styles.css` - Custom styling
- `.htaccess` - Server configuration
- `images/` folder - All your images including logo.png
- `Google Review/` folder - Review related files

### 🚫 Do NOT Upload:
- `node_modules/` folder
- `package.json` 
- `package-lock.json`
- `.gitignore`
- `deploy/` folder
- `review.txt`

## 📋 cPanel Upload Steps:

1. **Access File Manager**
   - Login to your cPanel
   - Open "File Manager"
   - Navigate to `public_html` folder

2. **Upload Files**
   - Upload `index.html` to the root of `public_html`
   - Upload `styles.css` to the root of `public_html`
   - Upload `.htaccess` to the root of `public_html`
   - Create `images` folder and upload your logo.png
   - Create `Google Review` folder for reviews

3. **Set Permissions**
   - Set file permissions to 644 for HTML/CSS files
   - Set folder permissions to 755 for directories

## 🌐 Domain Setup:

- Your website will be accessible at: `yourdomain.com`
- Make sure your domain is pointed to your hosting provider
- SSL certificate recommended for HTTPS

## ✅ Post-Upload Checklist:

- [ ] Test website loads correctly
- [ ] Check all images display properly
- [ ] Verify mobile responsiveness
- [ ] Test all navigation links
- [ ] Confirm YouTube video plays
- [ ] Check Google Maps embed works

## 🔧 Common Issues:

1. **Images not loading**: Check file paths and case sensitivity
2. **YouTube not working**: Ensure iframe is allowed
3. **CSS not loading**: Verify file path in HTML
4. **404 errors**: Check .htaccess file syntax

## 📞 Support:

If you need help with deployment, contact your hosting provider's support team.