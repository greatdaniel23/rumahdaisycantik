const fs = require('fs');
const path = require('path');

console.log('🚀 Building Rumah Daisy Cantik for deployment...');

// Create build directory
const buildDir = './build';
if (fs.existsSync(buildDir)) {
    fs.rmSync(buildDir, { recursive: true });
}
fs.mkdirSync(buildDir);

// Copy files for deployment
const filesToCopy = [
    'index.html',
    'styles.css',
    'server.js',
    'package.json',
    '.htaccess'
];

// Copy individual files
filesToCopy.forEach(file => {
    if (fs.existsSync(file)) {
        fs.copyFileSync(file, path.join(buildDir, file));
        console.log(`✅ Copied: ${file}`);
    }
});

// Copy images directory
if (fs.existsSync('./images')) {
    copyDirectory('./images', path.join(buildDir, 'images'));
    console.log('✅ Copied: images/');
}

function copyDirectory(src, dest) {
    if (!fs.existsSync(dest)) {
        fs.mkdirSync(dest, { recursive: true });
    }
    
    const items = fs.readdirSync(src);
    items.forEach(item => {
        const srcPath = path.join(src, item);
        const destPath = path.join(dest, item);
        
        if (fs.statSync(srcPath).isDirectory()) {
            copyDirectory(srcPath, destPath);
        } else {
            fs.copyFileSync(srcPath, destPath);
        }
    });
}

console.log('');
console.log('🎉 Build complete!');
console.log('📁 Files ready in ./build folder');
console.log('');
console.log('📤 Upload ./build contents to your cPanel public_html');
console.log('🔧 If Node.js supported: run "npm install express" on server');
console.log('');