const fs = require('fs');
const path = require('path');

/**
 * Universal Image Optimization HTML Injector
 * Adds image optimization scripts and styles to HTML files
 */

function injectImageOptimization(htmlFilePath) {
    console.log(`📝 Adding image optimization to ${htmlFilePath}...`);
    
    let html = fs.readFileSync(htmlFilePath, 'utf8');
    
    // Check if already optimized
    if (html.includes('image-optimizer.js') || html.includes('image-optimization.css')) {
        console.log(`✅ ${htmlFilePath} already optimized`);
        return;
    }
    
    // Find head section
    const headEndIndex = html.indexOf('</head>');
    if (headEndIndex === -1) {
        console.log(`❌ No </head> tag found in ${htmlFilePath}`);
        return;
    }
    
    // Prepare optimization injection
    const optimizationCode = `
    <!-- Image Optimization -->
    <link rel="stylesheet" href="image-optimization.css">
    <script src="image-optimizer.js" defer></script>
    <script src="image-optimization-init.js" defer></script>
`;
    
    // Inject before </head>
    const beforeHead = html.substring(0, headEndIndex);
    const afterHead = html.substring(headEndIndex);
    
    const optimizedHtml = beforeHead + optimizationCode + afterHead;
    
    // Write back to file
    fs.writeFileSync(htmlFilePath, optimizedHtml, 'utf8');
    console.log(`✅ ${htmlFilePath} optimized successfully`);
}

/**
 * Process all HTML files in a directory
 */
function optimizeAllHtmlFiles(directory = './') {
    const files = fs.readdirSync(directory);
    
    files.forEach(file => {
        if (file.endsWith('.html') && !file.includes('admin') && !file.includes('login')) {
            const filePath = path.join(directory, file);
            injectImageOptimization(filePath);
        }
    });
    
    // Also process build directory if it exists
    const buildDir = path.join(directory, 'build');
    if (fs.existsSync(buildDir)) {
        console.log('📁 Processing build directory...');
        const buildFiles = fs.readdirSync(buildDir);
        
        buildFiles.forEach(file => {
            if (file.endsWith('.html') && !file.includes('admin') && !file.includes('login')) {
                const filePath = path.join(buildDir, file);
                injectImageOptimization(filePath);
            }
        });
    }
}

// Run optimization if called directly
if (require.main === module) {
    console.log('🎨 Starting universal image optimization...');
    optimizeAllHtmlFiles();
    console.log('🎉 Universal image optimization complete!');
}

module.exports = { injectImageOptimization, optimizeAllHtmlFiles };