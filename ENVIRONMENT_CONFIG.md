# Environment Configuration for Admin Authentication

## Overview
The admin authentication system now supports environment-based configuration using `.env` files, making it more secure and configurable.

## How It Works

### 1. Environment Variables (.env)
```bash
# Admin credentials
ADMIN_USERNAME=admin
ADMIN_PASSWORD=password
```

### 2. Configuration Generation
The system generates a `config.json` file from the `.env` file:
```json
{
  "admin": {
    "username": "admin", 
    "password": "password"
  }
}
```

### 3. Runtime Loading
The login.html now loads credentials from `config.json` at runtime instead of using hardcoded values.

## Setup Instructions

### Step 1: Create .env file
```bash
# Copy the example file
cp .env.example .env

# Edit with your credentials
ADMIN_USERNAME=your_admin_username  
ADMIN_PASSWORD=your_secure_password
```

### Step 2: Generate Configuration
```bash
# Generate config.json from .env
npm run config

# Or as part of the build process
npm run build
```

### Step 3: Deploy
- Deploy the generated `config.json` with your website
- Keep `.env` file secure and never deploy it to production

## Security Considerations

### What's Secure:
✅ `.env` file is git-ignored and stays on server
✅ Passwords are hashed using SHA256 before comparison
✅ Session-based authentication with timeout
✅ Config can be generated from environment variables

### What's Not Fully Secure:
⚠️ `config.json` contains plaintext passwords (client-side limitation)
⚠️ Client-side JavaScript authentication can be bypassed by advanced users
⚠️ No server-side validation

### Recommendations for Production:
1. **Use HTTPS** - Always serve over encrypted connection
2. **Server-side Authentication** - Implement proper backend authentication
3. **Rotate Passwords** - Regularly change admin credentials
4. **Access Logs** - Monitor admin panel access
5. **IP Restrictions** - Limit admin access to specific IPs if possible

## File Structure
```
├── .env                    # Environment variables (git-ignored)
├── .env.example           # Template for environment variables
├── config.json           # Generated config (git-ignored)
├── generate-config.js     # Script to generate config from .env
├── login.html            # Updated to load from config.json
├── admin.html            # Protected with session authentication
└── build/
    ├── config.json       # Generated config for build
    ├── login.html        # Build version
    └── admin.html        # Build version
```

## Scripts Available

```bash
# Generate config.json from .env
npm run config

# Generate config and build project  
npm run build

# Development server
npm run dev-live
```

## Changing Credentials

### Method 1: Update .env file
```bash
# Edit .env file
ADMIN_USERNAME=newadmin
ADMIN_PASSWORD=newsecurepassword123

# Regenerate config
npm run config
```

### Method 2: Direct config.json edit
```json
{
  "admin": {
    "username": "newadmin",
    "password": "newsecurepassword123"  
  }
}
```

## Troubleshooting

### Config not loading?
- Check if `config.json` exists in the same directory as `login.html`
- Verify the JSON format is valid
- Check browser console for fetch errors

### Authentication still using old password?
- Clear browser cache and sessionStorage
- Regenerate config.json: `npm run config`
- Check if build directory has updated files

### Build process fails?
- Ensure Node.js is installed
- Run `npm install` to install dependencies
- Check if `.env` file exists and is readable

## Migration from Hardcoded System

If upgrading from the previous hardcoded system:
1. Create `.env` file with desired credentials
2. Run `npm run config` to generate configuration
3. Update any hardcoded references in custom code
4. Test login functionality thoroughly
5. Update build/deployment processes to include config generation

## Future Improvements

For better security, consider implementing:
- Server-side authentication API
- JWT tokens instead of session storage
- Rate limiting for login attempts
- Two-factor authentication
- Audit logging for admin actions