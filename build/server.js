const express = require('express');
const https = require('https');
const path = require('path');
const multer = require('multer');
const fs = require('fs');
const session = require('express-session');
const bodyParser = require('body-parser');
require('dotenv').config();
const app = express();
const PORT = process.env.PORT || 3001;

// --- Middleware Setup ---
app.use(bodyParser.urlencoded({ extended: true }));
app.use(bodyParser.json());
app.use(session({
    secret: process.env.SESSION_SECRET || 'a_default_secret_key',
    resave: false,
    saveUninitialized: true,
    cookie: { secure: false } // Set to true if using HTTPS
}));

// CORS middleware
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    next();
});


// --- Authentication & Routing ---

app.post('/login', (req, res) => {
    const { username, password } = req.body;
    if (username === process.env.ADMIN_USERNAME && password === process.env.ADMIN_PASSWORD) {
        req.session.user = 'admin';
        res.json({ success: true });
    } else {
        res.status(401).json({ success: false, error: 'Invalid credentials' });
    }
});

app.get('/logout', (req, res) => {
    req.session.destroy(err => {
        if (err) {
            return res.redirect('/admin.html');
        }
        res.clearCookie('connect.sid');
        res.redirect('/login.html');
    });
});

const checkAuth = (req, res, next) => {
    if (req.session.user === 'admin') {
        next();
    } else {
        // For API requests, send an error. For page requests, redirect.
        if (req.path.startsWith('/api/')) {
            return res.status(401).json({ success: false, error: 'Unauthorized' });
        }
        res.redirect('/login.html');
    }
};

// Protect admin page - this MUST be defined before app.use(express.static)
app.get('/admin.html', checkAuth, (req, res) => {
    res.sendFile(path.join(__dirname, 'admin.html'));
});


// --- File Upload Configuration ---
const storage = multer.diskStorage({
    destination: (req, file, cb) => cb(null, 'images/'),
    filename: (req, file, cb) => cb(null, file.originalname)
});
const imageFileFilter = (req, file, cb) => {
    if (!file.originalname.match(/\.(jpg|jpeg|png|gif)$/i)) {
        return cb(new Error('Only image files are allowed!'), false);
    }
    cb(null, true);
};
const upload = multer({ storage: storage, fileFilter: imageFileFilter });


// --- Protected API Endpoints ---
app.post('/api/upload-image', checkAuth, (req, res) => {
    upload.single('image')(req, res, function (err) {
        if (err) {
            return res.status(400).json({ success: false, error: err.message });
        }
        if (!req.file) {
            return res.status(400).json({ success: false, error: 'No file uploaded.' });
        }
        res.json({ success: true, message: 'Image uploaded successfully!', file: req.file });
    });
});

app.get('/api/images', checkAuth, (req, res) => {
    const imageDir = path.join(__dirname, 'images');
    fs.readdir(imageDir, (err, files) => {
        if (err) {
            return res.status(500).json({ success: false, error: 'Could not read image directory.' });
        }
        res.json({ success: true, images: files.filter(f => /\.(jpg|jpeg|png|gif)$/i.test(f)) });
    });
});

app.post('/api/content', checkAuth, (req, res) => {
    const contentPath = path.join(__dirname, 'content.json');
    fs.writeFile(contentPath, JSON.stringify(req.body, null, 2), 'utf8', (err) => {
        if (err) {
            return res.status(500).json({ success: false, error: 'Could not write content file.' });
        }
        res.json({ success: true, message: 'Content updated successfully!' });
    });
});


// --- Public API Endpoints ---
app.get('/api/content', (req, res) => {
    const contentPath = path.join(__dirname, 'content.json');
    fs.readFile(contentPath, 'utf8', (err, data) => {
        if (err) {
            return res.status(500).json({ success: false, error: 'Could not read content file.' });
        }
        res.json({ success: true, data: JSON.parse(data) });
    });
});

app.get('/api/reviews', (req, res) => {
    const API_KEY = process.env.API_KEY;
    const PLACE_ID = 'ChIJjZ5rME050i0RR9Hgjgo8HOo';
    const url = `https://maps.googleapis.com/maps/api/place/details/json?place_id=${PLACE_ID}&fields=reviews,rating,user_ratings_total,name&key=${API_KEY}`;

    https.get(url, (apiRes) => {
        let data = '';
        apiRes.on('data', (chunk) => { data += chunk; });
        apiRes.on('end', () => {
            try {
                const jsonData = JSON.parse(data);
                if (jsonData.status === 'OK' && jsonData.result) {
                    res.json({ success: true, data: jsonData.result });
                } else {
                    res.status(400).json({ success: false, error: jsonData.error_message || 'Failed to fetch reviews' });
                }
            } catch (error) {
                res.status(500).json({ success: false, error: 'Failed to parse API response' });
            }
        });
    }).on('error', (err) => {
        res.status(500).json({ success: false, error: 'Failed to connect to Google Maps API' });
    });
});

// --- Static File Server ---
// This serves static files like index.html, login.html, styles.css, etc.
// It's a catch-all and MUST be placed after all specific routes.
app.use(express.static('.'));


// --- Server Start ---
app.listen(PORT, () => {
    console.log(`🚀 Server running at http://localhost:${PORT}`);
    console.log(` நிர்வாகி உள்நுழைவு (Admin Login): http://localhost:${PORT}/login.html`);
});
