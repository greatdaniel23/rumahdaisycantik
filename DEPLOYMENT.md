# Rumah Daisy Cantik - Database-Powered Website Deployment Guide

This guide provides instructions for deploying the Rumah Daisy Cantik website, which is now a dynamic database-powered website with MySQL backend and enhanced room management system.

## Prerequisites

- Web hosting with PHP 7.4+ and MySQL support
- MySQL database credentials
- cPanel or similar hosting control panel access
- FTP/SFTP access (optional but recommended)

## Deployment Steps

### Step 1: Prepare Your Files for Upload

1. **Build the frontend assets:**
   ```bash
   npm run build
   ```

2. **Prepare deployment package:**
   - Include all root HTML files (index.html, admin.html, login.html, etc.)
   - Include the entire `api/` directory with PHP backend
   - Include the `database/` directory with SQL schema
   - Include `database-api.js` and other JavaScript files
   - Include the `images/` directory and other assets
   - Include `setup.php` for database initialization

### Step 2: Database Setup

1. **Create MySQL Database:**
   - Log in to your hosting cPanel
   - Go to "MySQL Databases"
   - Create a new database (if not already created)
   - Note your database credentials:
     - Host: `localhost` 
     - Database: `u289291769_websiterdc`
     - Username: `u289291769_websiterdc`
     - Password: `Kanibal123!!!`

2. **Verify Database Configuration:**
   - Open `api/config/database.php`
   - Ensure database credentials match your hosting setup

### Step 3: Upload Application Files

1. **Upload via cPanel File Manager:**
   - Log in to your cPanel
   - Navigate to **"File Manager"**
   - Go to the `public_html` directory
   - Upload all files maintaining the directory structure:
     ```
     public_html/
     ├── .htaccess (root level for API routing)
     ├── index.html
     ├── admin.html
     ├── login.html
     ├── setup.php
     ├── test-api.php (for testing, delete after use)
     ├── database-api.js
     ├── api/
     │   ├── index.php
     │   ├── .htaccess (for internal API routing)
     │   ├── config/
     │   ├── middleware/
     │   ├── models/
     │   └── migrate.php
     ├── database/
     │   ├── setup.sql
     │   └── enhanced-setup.sql
     └── images/
     ```

2. **Set File Permissions:**
   - Ensure PHP files have 644 permissions
   - Ensure directories have 755 permissions
   - The `api/` directory should be executable

### Step 4: Initialize Database

1. **Run Database Setup:**
   - Navigate to `https://yourdomain.com/setup.php` in your browser
   - Review the setup information
   - Check the confirmation checkbox
   - Click "Start Database Setup"
   - Wait for completion confirmation

2. **Verify Setup:**
   - Check that all tables are created successfully
   - Verify sample data is imported
   - Test API health: `https://yourdomain.com/api/health`

3. **Run API Test (Optional but Recommended):**
   - Navigate to `https://yourdomain.com/test-api.php` for comprehensive testing
   - Review all test results and fix any ❌ errors
   - **Delete test-api.php after testing** for security

### Step 5: Configure Web Server

1. **Apache Configuration (.htaccess):**
   - Ensure both `.htaccess` files are uploaded:
     - Root `.htaccess` file for API routing
     - `api/.htaccess` file for internal API handling
   - Verify mod_rewrite is enabled on your hosting
   - Test API routing: `https://yourdomain.com/api/images`

2. **PHP Configuration:**
   - Ensure PHP 7.4+ is active
   - Verify PDO MySQL extension is enabled
   - Check that `allow_url_fopen` is enabled for external API calls

## Admin Panel Usage

The admin panel is now a **real-time database management system** that provides immediate updates to your website content.

### Accessing the Admin Panel

1. **Navigate to Admin Panel:**
   - Go to `https://yourdomain.com/admin.html`
   - Login with your credentials (default password should be changed)

2. **Real-time Management Features:**
   - **Images**: Add, edit, delete images with instant preview
   - **Room Types**: Manage different room categories and pricing
   - **Rooms**: Individual room management with detailed specifications
   - **Room Amenities**: Modal-based amenities management system
   - **Accommodations**: General accommodation packages
   - **Buttons**: Website buttons with styling options
   - **Popup**: Popup content management
   - **Parallax**: Background image settings
   - **Pages**: Page content and SEO metadata

### Content Management Workflow

1. **Individual Item Management:**
   - Each section has individual "Save Changes" buttons
   - Changes are immediately saved to the database
   - No need to download/upload files

2. **Adding New Content:**
   - Use "Add New..." buttons in each section
   - Fill in the details and save
   - Content appears on the website immediately

3. **Room Management:**
   - Create room types first (categories like "Deluxe Villa")
   - Add individual rooms linked to room types
   - Use "Manage Amenities" to add detailed room features
   - Set room status (Available, Occupied, Maintenance)

### Managing Images

**Enhanced Image Management:**

1. **Direct Image Management:**
   - Upload images to your server's `/images/` directory via cPanel
   - Use the Images section in admin panel to add image records
   - Set image type, category, alt text, and descriptions
   - Images are immediately available for use

2. **Room Image Galleries:**
   - Link multiple images to individual rooms
   - Set primary images for room displays
   - Organize images with sort order

3. **Image Optimization:**
   - The system supports WebP and AVIF formats
   - Lazy loading is built-in for better performance
   - Responsive image support included

## Security Configuration

### Updating the Admin Password

**Method 1: Via Database (Recommended)**
1. Access your MySQL database via phpMyAdmin or similar tool
2. Go to the `admin_users` table
3. Update the `password_hash` field with a new bcrypt hash
4. Use an online bcrypt generator or PHP script:
   ```php
   echo password_hash('your_new_password', PASSWORD_DEFAULT);
   ```

**Method 2: Via login.html (Legacy)**
1. The system still supports SHA-256 hashes in `login.html`
2. Generate SHA-256 hash of your new password
3. Replace the `storedHash` value in `login.html`

### API Security

1. **Session-based Authentication:**
   - 30-minute session timeout
   - Automatic logout on inactivity
   - IP address and user agent logging

2. **Database Security:**
   - All queries use prepared statements (SQL injection protection)
   - Input validation and sanitization
   - CORS headers properly configured

3. **File Permissions:**
   - Ensure `api/config/database.php` is not publicly accessible
   - Set appropriate file permissions (644 for files, 755 for directories)

## ✅ Post-Deployment Checklist

### Frontend Testing
- [ ] Test that your website loads correctly at your domain
- [ ] Verify that all images and content are loading correctly
- [ ] Test responsive design on mobile and desktop
- [ ] Verify image optimization is working (WebP/AVIF support)

### Database & API Testing
- [ ] Database setup completed successfully via `setup.php`
- [ ] API health check returns "healthy": `https://yourdomain.com/api/health`
- [ ] Test API endpoints: `/api/images`, `/api/rooms`, `/api/room-types`
- [ ] If 404 errors occur, test direct access: `https://yourdomain.com/api/index.php`
- [ ] Verify both `.htaccess` files are properly uploaded and configured
- [ ] Verify database connection and query performance

### Admin Panel Testing
- [ ] Admin panel loads correctly: `https://yourdomain.com/admin.html`
- [ ] Login functionality works with your credentials
- [ ] Test creating, editing, and deleting content in each section
- [ ] Verify real-time updates appear on the frontend immediately
- [ ] Test room management features (room types, rooms, amenities)

### Security Testing
- [ ] Verify admin panel requires authentication
- [ ] Test session timeout (30 minutes)
- [ ] Ensure database credentials are secure
- [ ] Check that API endpoints require authentication

## 🔧 Troubleshooting

### Database Issues
- **"Database connection failed"**: 
  - Verify database credentials in `api/config/database.php`
  - Ensure MySQL service is running
  - Check database permissions

- **"Table doesn't exist"**: 
  - Run the database setup via `setup.php`
  - Check if SQL files were uploaded correctly
  - Verify MySQL version compatibility (5.7+ recommended)

### API Issues
- **404 errors on API calls**: 
  - Ensure **both** `.htaccess` files are uploaded:
    - Root `.htaccess` (routes `/api/*` requests)
    - `api/.htaccess` (handles internal routing)
  - Verify mod_rewrite is enabled on your server
  - Check Apache configuration
  - Test direct access: `https://yourdomain.com/api/index.php?health=1`

- **"Authentication required"**: 
  - Clear browser cache and session storage
  - Re-login to the admin panel
  - Check session timeout settings

### Admin Panel Issues
- **"Failed to load content"**: 
  - Check browser console for JavaScript errors
  - Verify API endpoints are accessible
  - Test database connection

- **Changes not saving**: 
  - Check network tab for failed API requests
  - Verify database write permissions
  - Check PHP error logs

### Performance Issues
- **Slow page loading**: 
  - Optimize database queries
  - Enable database query caching
  - Consider CDN for images
  - Verify image optimization is working

### File Upload Issues
- **PHP file permissions**: 
  - Set files to 644, directories to 755
  - Ensure web server can read PHP files
  - Check PHP version (7.4+ required)

## 📊 Monitoring & Maintenance

### Regular Maintenance
- **Database Backups**: Set up automated MySQL backups
- **Content Backups**: The system creates automatic content.json backups
- **Activity Monitoring**: Check the `activity_log` table for admin changes
- **Performance Monitoring**: Monitor database query performance

### Scaling Considerations
- **Database Optimization**: Add indexes for frequently queried fields
- **Caching**: Implement Redis or Memcached for better performance
- **CDN**: Use a CDN for static assets and images
- **Load Balancing**: Consider load balancing for high traffic

## 🚀 Advanced Features

### Custom Development
- **API Extensions**: Add custom endpoints in `api/index.php`
- **Frontend Integration**: Use `database-api.js` for custom frontend features
- **Booking System**: The room management system is ready for booking integration
- **Multi-language**: Extend the database schema for multi-language support

### Integration Options
- **Payment Gateway**: Integrate with booking and payment systems
- **Email Notifications**: Add email notifications for bookings
- **Analytics**: Integrate with Google Analytics or similar tools
- **Social Media**: Connect with social media APIs for reviews and bookings
