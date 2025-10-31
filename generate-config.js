const fs = require('fs');
const path = require('path');

// Function to read .env file and parse it
function loadEnvFile(filePath) {
    try {
        const envContent = fs.readFileSync(filePath, 'utf8');
        const envVars = {};
        
        envContent.split('\n').forEach(line => {
            line = line.trim();
            if (line && !line.startsWith('#')) {
                const [key, ...valueParts] = line.split('=');
                if (key && valueParts.length > 0) {
                    envVars[key.trim()] = valueParts.join('=').trim();
                }
            }
        });
        
        return envVars;
    } catch (error) {
        console.warn('No .env file found, using defaults');
        return {};
    }
}

// Main function to generate config.json from .env
function generateConfig() {
    // Try to load from .env file
    const envVars = loadEnvFile('.env');
    
    // Create config object with defaults
    const config = {
        admin: {
            username: envVars.ADMIN_USERNAME || 'admin',
            password: envVars.ADMIN_PASSWORD || 'password'
        },
        api: {
            googleApiKey: envVars.GOOGLE_API_KEY || ''
        }
    };
    
    // Write config.json
    fs.writeFileSync('config.json', JSON.stringify(config, null, 2));
    
    // Also write to build directory if it exists
    if (fs.existsSync('build')) {
        fs.writeFileSync('build/config.json', JSON.stringify(config, null, 2));
    }
    
    console.log('✅ config.json generated successfully');
    console.log('Environment variables found:', envVars);
    console.log('Configuration:', JSON.stringify(config, null, 2));
}

// Run the script
generateConfig();