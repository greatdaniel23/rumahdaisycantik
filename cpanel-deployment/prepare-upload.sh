#!/bin/bash

# Rumah Daisy Cantik - Quick Deployment Script
# Run this to prepare files for cPanel upload

echo "🚀 Preparing Rumah Daisy Cantik for cPanel deployment..."

# Create deployment folder
mkdir -p cpanel-upload

# Copy essential files
echo "📁 Copying essential files..."
cp index.html cpanel-upload/
cp styles.css cpanel-upload/
cp server.js cpanel-upload/
cp package.json cpanel-upload/
cp .htaccess cpanel-upload/

# Copy images folder
echo "🖼️ Copying images..."
cp -r images cpanel-upload/

echo "✅ Files prepared in 'cpanel-upload' folder"
echo ""
echo "📋 Next steps:"
echo "1. Check if your cPanel supports Node.js"
echo "2. Upload files from 'cpanel-upload' folder to public_html"
echo "3. If Node.js supported: run 'npm install express' on server"
echo "4. Configure Node.js app with startup file: server.js"
echo ""
echo "📖 See CPANEL_DEPLOYMENT_GUIDE.md for detailed instructions"