// This file is intended to be deployed as a serverless function.
// It fetches Google Reviews and returns them to the client.

// You will need to set your Google API key as an environment variable.
const API_KEY = process.env.GOOGLE_API_KEY;
const PLACE_ID = 'ChIJjZ5rME050i0RR9Hgjgo8HOo';

exports.handler = async function(event, context) {
    const url = `https://maps.googleapis.com/maps/api/place/details/json?place_id=${PLACE_ID}&fields=reviews,rating,user_ratings_total,name&key=${API_KEY}`;

    try {
        const fetch = (await import('node-fetch')).default;
        const response = await fetch(url);
        const data = await response.json();

        if (data.status === 'OK' && data.result) {
            return {
                statusCode: 200,
                body: JSON.stringify({ success: true, data: data.result })
            };
        } else {
            return {
                statusCode: 400,
                body: JSON.stringify({ success: false, error: data.error_message || 'Failed to fetch reviews' })
            };
        }
    } catch (error) {
        return {
            statusCode: 500,
            body: JSON.stringify({ success: false, error: 'Failed to connect to Google Maps API' })
        };
    }
};
