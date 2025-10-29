const express = require('express');
const https = require('https');
const path = require('path');
const multer = require('multer');
const fs = require('fs');
require('dotenv').config();
const app = express();
const PORT = process.env.PORT || 3001;

// Serve static files from the current directory
app.use(express.static('.'));
app.use(express.json());

// CORS middleware
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    next();
});

// Multer configuration for image uploads
const storage = multer.diskStorage({
    destination: function (req, file, cb) {
        cb(null, 'images/');
    },
    filename: function (req, file, cb) {
        cb(null, file.originalname);
    }
});

const imageFileFilter = (req, file, cb) => {
    if (!file.originalname.match(/\.(jpg|jpeg|png|gif)$/i)) {
        return cb(new Error('Only image files are allowed!'), false);
    }
    cb(null, true);
};

const upload = multer({ storage: storage, fileFilter: imageFileFilter });

// API endpoint for image uploads
app.post('/api/upload-image', (req, res) => {
    const uploadSingle = upload.single('image');
    uploadSingle(req, res, function (err) {
        if (err) {
            return res.status(400).json({ success: false, error: err.message });
        }
        if (!req.file) {
            return res.status(400).json({ success: false, error: 'No file uploaded.' });
        }
        res.json({ success: true, message: 'Image uploaded successfully!', file: req.file });
    });
});

// API endpoint to get the list of images
app.get('/api/images', (req, res) => {
    const imageDir = path.join(__dirname, 'images');
    fs.readdir(imageDir, (err, files) => {
        if (err) {
            console.error('Error reading image directory:', err);
            return res.status(500).json({ success: false, error: 'Could not read image directory.' });
        }
        const imageFiles = files.filter(file => /\.(jpg|jpeg|png|gif)$/i.test(file));
        res.json({ success: true, images: imageFiles });
    });
});

// API endpoint to get content
app.get('/api/content', (req, res) => {
    const contentPath = path.join(__dirname, 'content.json');
    fs.readFile(contentPath, 'utf8', (err, data) => {
        if (err) {
            console.error('Error reading content file:', err);
            return res.status(500).json({ success: false, error: 'Could not read content file.' });
        }
        res.json({ success: true, data: JSON.parse(data) });
    });
});

// API endpoint to update content
app.post('/api/content', (req, res) => {
    const contentPath = path.join(__dirname, 'content.json');
    const newData = req.body;
    fs.writeFile(contentPath, JSON.stringify(newData, null, 2), 'utf8', (err) => {
        if (err) {
            console.error('Error writing content file:', err);
            return res.status(500).json({ success: false, error: 'Could not write content file.' });
        }
        res.json({ success: true, message: 'Content updated successfully!' });
    });
});

// API endpoint to get Google Places reviews (text reviews only)
app.get('/api/reviews', (req, res) => {
    const API_KEY = process.env.API_KEY;
    const PLACE_ID = 'ChIJjZ5rME050i0RR9Hgjgo8HOo'; // Rumah Daisy Cantik

    const url = `https://maps.googleapis.com/maps/api/place/details/json?place_id=${PLACE_ID}&fields=reviews,rating,user_ratings_total,name&key=${API_KEY}`;

    console.log('🔍 Fetching reviews from Google Maps API...');

    https.get(url, (apiRes) => {
        let data = '';

        apiRes.on('data', (chunk) => {
            data += chunk;
        });

        apiRes.on('end', () => {
            try {
                const jsonData = JSON.parse(data);

                if (jsonData.status === 'OK' && jsonData.result) {
                    // Filter reviews to only include those with text content
                    const allReviews = jsonData.result.reviews || [];
                    const textReviews = allReviews.filter(review => {
                        return review.text &&
                               review.text.trim().length > 10 && // At least 10 characters
                               review.text.trim() !== 'No comment provided' &&
                               !review.text.trim().match(/^[\s\.\-_]*$/); // Not just spaces or punctuation
                    });

                    // Sort by rating (5 stars first) then by recency
                    textReviews.sort((a, b) => {
                        if (a.rating !== b.rating) {
                            return b.rating - a.rating; // Higher rating first
                        }
                        return new Date(b.time * 1000) - new Date(a.time * 1000); // More recent first
                    });

                    console.log('✅ Successfully fetched reviews');
                    console.log(`📊 Rating: ${jsonData.result.rating}/5`);
                    console.log(`📝 Total Reviews: ${allReviews.length} total, ${textReviews.length} with text`);
                    console.log(`👥 Total User Reviews: ${jsonData.result.user_ratings_total || 0}`);

                    // Log review quality info
                    textReviews.forEach((review, index) => {
                        console.log(`   ${index + 1}. ${review.author_name} - ${review.rating}⭐ (${review.text.length} chars)`);
                    });

                    res.json({
                        success: true,
                        data: {
                            name: jsonData.result.name,
                            rating: jsonData.result.rating,
                            user_ratings_total: jsonData.result.user_ratings_total,
                            total_reviews: allReviews.length,
                            text_reviews_count: textReviews.length,
                            reviews: textReviews
                        }
                    });
                } else {
                    console.log('❌ API Error:', jsonData.error_message || 'Unknown error');
                    res.status(400).json({
                        success: false,
                        error: jsonData.error_message || 'Failed to fetch reviews',
                        status: jsonData.status
                    });
                }
            } catch (error) {
                console.error('❌ JSON Parse Error:', error.message);
                res.status(500).json({
                    success: false,
                    error: 'Failed to parse API response'
                });
            }
        });

    }).on('error', (err) => {
        console.error('❌ Request Error:', err.message);
        res.status(500).json({
            success: false,
            error: 'Failed to connect to Google Maps API'
        });
    });
});

// Start server
app.listen(PORT, () => {
    console.log(`🚀 Server running at http://localhost:${PORT}`);
    console.log(`📱 Website: http://localhost:${PORT}/review.html`);
    console.log(`🔌 API endpoint: http://localhost:${PORT}/api/reviews`);
    console.log('🖼️ Image upload endpoint: http://localhost:${PORT}/api/upload-image');
    console.log('📸 Image list endpoint: http://localhost:${PORT}/api/images');
    console.log('📝 Content endpoints: http://localhost:${PORT}/api/content');
    console.log('');
    console.log('🏨 Ready to fetch Rumah Daisy Cantik reviews!');
});