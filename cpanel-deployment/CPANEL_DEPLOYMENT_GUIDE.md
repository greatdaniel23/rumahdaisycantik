# Rumah Daisy Cantik - cPanel Deployment Guide

## 🎯 Deployment Options

Your website has Google Reviews integration that requires a Node.js server. You have two deployment options:

---

## Option 1: Node.js Hosting (Recommended)

**If your cPanel supports Node.js applications:**

### Files to Upload:
```
public_html/
├── index.html
├── styles.css
├── server.js
├── package.json
├── .htaccess
└── images/
    └── logo.png
```

### Setup Steps:
1. **Create Node.js App in cPanel**
   - Go to "Node.js Selector" in cPanel
   - Create new application
   - Set Node.js version to 18+ 
   - Application root: `public_html`
   - Application URL: your domain

2. **Upload Files**
   - Upload all files via File Manager
   - Install dependencies: `npm install express`

3. **Configure App**
   - Set startup file to `server.js`
   - Set application mode to `production`
   - Restart the application

### Server Configuration:
```javascript
// Update server.js port for cPanel
const PORT = process.env.PORT || 3001;
```

---

## Option 2: Static Hosting (Fallback)

**If your cPanel doesn't support Node.js:**

### Modified Deployment:
1. **Static Files Only**
   - Upload: `index.html`, `styles.css`, `images/`, `.htaccess`
   - Google Reviews will be disabled (fallback to static testimonials)

2. **Alternative Solutions**
   - Use third-party review widgets
   - Embed Google My Business reviews
   - Use static review content

---

## 🔧 Pre-Deployment Setup

### Update package.json for production:
```json
{
  "scripts": {
    "start": "node server.js",
    "dev": "node server.js"
  },
  "engines": {
    "node": ">=18.0.0"
  }
}
```

### Environment Variables:
- Set `NODE_ENV=production` in cPanel
- Keep Google API key secure

---

## 📁 Files NOT to Upload:
- `node_modules/` (will be installed on server)
- `deploy/`
- `DEPLOYMENT.md`
- `.gitignore`
- Test/debug files (already removed)

---

## ✅ Post-Deployment Checklist:

- [ ] Website loads correctly
- [ ] Google Reviews display properly
- [ ] Mobile responsiveness works
- [ ] All images load
- [ ] Contact form works (if applicable)
- [ ] SSL certificate is active

---

## 🆘 Troubleshooting:

### If Node.js isn't supported:
1. Contact your hosting provider about Node.js support
2. Consider upgrading to a hosting plan with Node.js
3. Use static deployment option as temporary solution

### Common Issues:
- **Reviews not loading**: Check API key and CORS settings
- **Server errors**: Check Node.js version compatibility
- **Images missing**: Verify file paths and permissions

---

## 📞 Need Help?

1. Check if your cPanel has "Node.js Selector" or "Node.js App"
2. Contact your hosting provider about Node.js support
3. Test deployment on a subdomain first