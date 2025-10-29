# Rumah Daisy Cantik - cPanel Upload Files

## ✅ Files Ready for Upload

These files are configured and ready for cPanel deployment:

### Core Website Files:
- ✅ `index.html` - Main website (with Google Reviews)
- ✅ `styles.css` - Website styling
- ✅ `server.js` - Node.js server (configured for cPanel)
- ✅ `package.json` - Dependencies (Express.js)
- ✅ `.htaccess` - Server configuration
- ✅ `images/` - All website images

### Configuration Updates Made:
- ✅ Server.js updated to use environment PORT
- ✅ Package.json configured with proper start script
- ✅ Node.js version requirement added (18+)

---

## 🎯 Upload Instructions

### Step 1: Check Node.js Support
Before uploading, verify your cPanel has:
- "Node.js Selector" or "Node.js App" feature
- Node.js version 18 or higher available

### Step 2: Upload to cPanel
1. **Login to cPanel File Manager**
2. **Navigate to public_html folder**
3. **Upload these files:**
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

### Step 3: Configure Node.js App
1. **Go to "Node.js Selector"**
2. **Create new application:**
   - Application root: `/public_html`
   - Application URL: your domain
   - Application startup file: `server.js`
   - Node.js version: 18+

3. **Install dependencies:**
   ```bash
   npm install express
   ```

4. **Start the application**

---

## 🔄 Alternative: Static Deployment

If Node.js is NOT supported, upload only:
- `index.html` (Google Reviews will show loading message)
- `styles.css`
- `images/`
- `.htaccess`

---

## ✅ Post-Upload Testing

1. Visit your website URL
2. Check if Google Reviews load properly
3. Test mobile responsiveness
4. Verify all images display correctly
5. Test contact information

---

## 🆘 Troubleshooting

**Google Reviews not loading?**
- Check if Node.js app is running
- Verify Google API key is working
- Check browser console for errors

**Node.js not supported?**
- Contact hosting provider about Node.js plans
- Consider alternative hosting (Vercel, Netlify, Heroku)
- Use static deployment as temporary solution

---

## 📞 Support

If you need help with deployment:
1. Check cPanel documentation for Node.js setup
2. Contact your hosting provider
3. Consider managed hosting services for easier deployment