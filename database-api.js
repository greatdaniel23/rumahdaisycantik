/**
 * Database API Client for Admin Panel
 * Handles all communication with the PHP backend API
 */

class DatabaseAPI {
    constructor() {
        this.baseURL = '/api';
        this.sessionData = JSON.parse(sessionStorage.getItem('adminAuth')) || null;
        
        // Auto-refresh session data periodically
        setInterval(() => {
            this.sessionData = JSON.parse(sessionStorage.getItem('adminAuth')) || null;
        }, 30000); // Every 30 seconds
    }
    
    /**
     * Get authentication headers
     */
    getAuthHeaders() {
        const headers = {
            'Content-Type': 'application/json'
        };
        
        if (this.sessionData) {
            headers['X-Session-Data'] = JSON.stringify(this.sessionData);
        }
        
        return headers;
    }
    
    /**
     * Make API request with error handling
     */
    async makeRequest(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: this.getAuthHeaders(),
            ...options
        };
        
        try {
            const response = await fetch(url, config);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }
            
            return data;
        } catch (error) {
            console.error('API Request failed:', error);
            
            // Handle authentication errors
            if (error.message.includes('Authentication')) {
                this.handleAuthError();
            }
            
            throw error;
        }
    }
    
    /**
     * Handle authentication errors
     */
    handleAuthError() {
        sessionStorage.removeItem('adminAuth');
        if (window.location.pathname !== '/login.html') {
            window.location.href = '/login.html';
        }
    }
    
    /**
     * Test API connection and database health
     */
    async testConnection() {
        return await this.makeRequest('/health');
    }
    
    // ===================
    // IMAGES API
    // ===================
    
    async getImages(filters = {}) {
        let endpoint = '/images';
        const params = new URLSearchParams();
        
        if (filters.type) params.append('type', filters.type);
        if (filters.category) params.append('category', filters.category);
        
        if (params.toString()) {
            endpoint += '?' + params.toString();
        }
        
        return await this.makeRequest(endpoint);
    }
    
    async getImage(id) {
        return await this.makeRequest(`/images/${id}`);
    }
    
    async createImage(imageData) {
        return await this.makeRequest('/images', {
            method: 'POST',
            body: JSON.stringify(imageData)
        });
    }
    
    async updateImage(id, imageData) {
        return await this.makeRequest(`/images/${id}`, {
            method: 'PUT',
            body: JSON.stringify(imageData)
        });
    }
    
    async deleteImage(id) {
        return await this.makeRequest(`/images/${id}`, {
            method: 'DELETE'
        });
    }
    
    // ===================
    // ACCOMMODATIONS API
    // ===================
    
    async getAccommodations(type = null) {
        let endpoint = '/accommodations';
        if (type) {
            endpoint += `?type=${encodeURIComponent(type)}`;
        }
        return await this.makeRequest(endpoint);
    }
    
    async getAccommodation(id) {
        return await this.makeRequest(`/accommodations/${id}`);
    }
    
    async createAccommodation(accommodationData) {
        return await this.makeRequest('/accommodations', {
            method: 'POST',
            body: JSON.stringify(accommodationData)
        });
    }
    
    async updateAccommodation(id, accommodationData) {
        return await this.makeRequest(`/accommodations/${id}`, {
            method: 'PUT',
            body: JSON.stringify(accommodationData)
        });
    }
    
    async deleteAccommodation(id) {
        return await this.makeRequest(`/accommodations/${id}`, {
            method: 'DELETE'
        });
    }
    
    // ===================
    // POPUP API
    // ===================
    
    async getPopupItems() {
        return await this.makeRequest('/popup');
    }
    
    async getPopupItem(id) {
        return await this.makeRequest(`/popup/${id}`);
    }
    
    async createPopupItem(popupData) {
        return await this.makeRequest('/popup', {
            method: 'POST',
            body: JSON.stringify(popupData)
        });
    }
    
    async updatePopupItem(id, popupData) {
        return await this.makeRequest(`/popup/${id}`, {
            method: 'PUT',
            body: JSON.stringify(popupData)
        });
    }
    
    async deletePopupItem(id) {
        return await this.makeRequest(`/popup/${id}`, {
            method: 'DELETE'
        });
    }
    
    // ===================
    // PARALLAX API
    // ===================
    
    async getParallaxItems() {
        return await this.makeRequest('/parallax');
    }
    
    async getParallaxItem(id) {
        return await this.makeRequest(`/parallax/${id}`);
    }
    
    async createParallaxItem(parallaxData) {
        return await this.makeRequest('/parallax', {
            method: 'POST',
            body: JSON.stringify(parallaxData)
        });
    }
    
    async updateParallaxItem(id, parallaxData) {
        return await this.makeRequest(`/parallax/${id}`, {
            method: 'PUT',
            body: JSON.stringify(parallaxData)
        });
    }
    
    async deleteParallaxItem(id) {
        return await this.makeRequest(`/parallax/${id}`, {
            method: 'DELETE'
        });
    }
    
    // ===================
    // BUTTONS API
    // ===================
    
    async getButtons() {
        return await this.makeRequest('/buttons');
    }
    
    async getButton(id) {
        return await this.makeRequest(`/buttons/${id}`);
    }
    
    async createButton(buttonData) {
        return await this.makeRequest('/buttons', {
            method: 'POST',
            body: JSON.stringify(buttonData)
        });
    }
    
    async updateButton(id, buttonData) {
        return await this.makeRequest(`/buttons/${id}`, {
            method: 'PUT',
            body: JSON.stringify(buttonData)
        });
    }
    
    async deleteButton(id) {
        return await this.makeRequest(`/buttons/${id}`, {
            method: 'DELETE'
        });
    }
    
    // ===================
    // PAGES API
    // ===================
    
    async getPages() {
        return await this.makeRequest('/pages');
    }
    
    async getPage(id) {
        return await this.makeRequest(`/pages/${id}`);
    }
    
    async createPage(pageData) {
        return await this.makeRequest('/pages', {
            method: 'POST',
            body: JSON.stringify(pageData)
        });
    }
    
    async updatePage(id, pageData) {
        return await this.makeRequest(`/pages/${id}`, {
            method: 'PUT',
            body: JSON.stringify(pageData)
        });
    }
    
    async deletePage(id) {
        return await this.makeRequest(`/pages/${id}`, {
            method: 'DELETE'
        });
    }
    
    // ===================
    // ROOM TYPES API
    // ===================
    
    async getRoomTypes() {
        return await this.makeRequest('/room-types');
    }
    
    async getRoomType(id) {
        return await this.makeRequest(`/room-types/${id}`);
    }
    
    async createRoomType(roomTypeData) {
        return await this.makeRequest('/room-types', {
            method: 'POST',
            body: JSON.stringify(roomTypeData)
        });
    }
    
    async updateRoomType(id, roomTypeData) {
        return await this.makeRequest(`/room-types/${id}`, {
            method: 'PUT',
            body: JSON.stringify(roomTypeData)
        });
    }
    
    async deleteRoomType(id) {
        return await this.makeRequest(`/room-types/${id}`, {
            method: 'DELETE'
        });
    }
    
    // ===================
    // ROOMS API
    // ===================
    
    async getRooms(filters = {}) {
        let endpoint = '/rooms';
        const params = new URLSearchParams();
        
        if (filters.room_type_id) params.append('room_type_id', filters.room_type_id);
        if (filters.status) params.append('status', filters.status);
        
        if (params.toString()) {
            endpoint += '?' + params.toString();
        }
        
        return await this.makeRequest(endpoint);
    }
    
    async getRoom(id) {
        return await this.makeRequest(`/rooms/${id}`);
    }
    
    async createRoom(roomData) {
        return await this.makeRequest('/rooms', {
            method: 'POST',
            body: JSON.stringify(roomData)
        });
    }
    
    async updateRoom(id, roomData) {
        return await this.makeRequest(`/rooms/${id}`, {
            method: 'PUT',
            body: JSON.stringify(roomData)
        });
    }
    
    async deleteRoom(id) {
        return await this.makeRequest(`/rooms/${id}`, {
            method: 'DELETE'
        });
    }
    
    // ===================
    // ROOM AMENITIES API
    // ===================
    
    async getRoomAmenities(roomId) {
        return await this.makeRequest(`/room-amenities?room_id=${roomId}`);
    }
    
    async getRoomAmenity(id) {
        return await this.makeRequest(`/room-amenities/${id}`);
    }
    
    async createRoomAmenity(amenityData) {
        return await this.makeRequest('/room-amenities', {
            method: 'POST',
            body: JSON.stringify(amenityData)
        });
    }
    
    async updateRoomAmenities(roomId, amenities) {
        return await this.makeRequest('/room-amenities', {
            method: 'POST',
            body: JSON.stringify({
                room_id: roomId,
                amenities: amenities
            })
        });
    }
    
    async deleteRoomAmenity(id) {
        return await this.makeRequest(`/room-amenities/${id}`, {
            method: 'DELETE'
        });
    }
    
    // ===================
    // ROOM IMAGES API
    // ===================
    
    async getRoomImages(roomId) {
        return await this.makeRequest(`/room-images?room_id=${roomId}`);
    }
    
    async addImageToRoom(roomId, imageId, isPrimary = false, sortOrder = 0) {
        return await this.makeRequest('/room-images', {
            method: 'POST',
            body: JSON.stringify({
                room_id: roomId,
                image_id: imageId,
                is_primary: isPrimary,
                sort_order: sortOrder
            })
        });
    }
    
    async removeImageFromRoom(roomId, imageId) {
        return await this.makeRequest('/room-images', {
            method: 'DELETE',
            body: JSON.stringify({
                room_id: roomId,
                image_id: imageId
            })
        });
    }
    
    // ===================
    // UTILITY METHODS
    // ===================
    
    /**
     * Upload image file (if you have file upload endpoint)
     */
    async uploadImage(file, category = 'general') {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('category', category);
        
        const headers = {};
        if (this.sessionData) {
            headers['X-Session-Data'] = JSON.stringify(this.sessionData);
        }
        
        const response = await fetch(`${this.baseURL}/upload/image`, {
            method: 'POST',
            headers: headers,
            body: formData
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Upload failed');
        }
        
        return data;
    }
    
    /**
     * Batch operations
     */
    async batchUpdate(resource, updates) {
        return await this.makeRequest(`/${resource}/batch`, {
            method: 'PUT',
            body: JSON.stringify({ updates })
        });
    }
    
    /**
     * Search across content
     */
    async search(query, resources = ['images', 'accommodations', 'pages']) {
        const params = new URLSearchParams({
            q: query,
            resources: resources.join(',')
        });
        
        return await this.makeRequest(`/search?${params.toString()}`);
    }
}

/**
 * Database API Instance
 * Global instance for use throughout the admin panel
 */
window.dbAPI = new DatabaseAPI();

/**
 * Notification Helper
 */
class NotificationHelper {
    static show(message, type = 'info', duration = 3000) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getIcon(type)}"></i>
                <span>${message}</span>
                <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        // Add styles if not present
        if (!document.querySelector('#notification-styles')) {
            const styles = document.createElement('style');
            styles.id = 'notification-styles';
            styles.textContent = `
                .notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 10000;
                    min-width: 300px;
                    max-width: 500px;
                    padding: 16px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    animation: slideInRight 0.3s ease-out;
                }
                .notification-info { background: #3b82f6; color: white; }
                .notification-success { background: #10b981; color: white; }
                .notification-warning { background: #f59e0b; color: white; }
                .notification-error { background: #ef4444; color: white; }
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }
                .notification-close {
                    background: none;
                    border: none;
                    color: inherit;
                    cursor: pointer;
                    margin-left: auto;
                    opacity: 0.8;
                }
                .notification-close:hover { opacity: 1; }
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `;
            document.head.appendChild(styles);
        }
        
        document.body.appendChild(notification);
        
        // Auto-remove after duration
        if (duration > 0) {
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, duration);
        }
        
        return notification;
    }
    
    static getIcon(type) {
        const icons = {
            info: 'info-circle',
            success: 'check-circle',
            warning: 'exclamation-triangle',
            error: 'exclamation-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    static success(message) {
        return this.show(message, 'success');
    }
    
    static error(message) {
        return this.show(message, 'error', 5000);
    }
    
    static warning(message) {
        return this.show(message, 'warning', 4000);
    }
    
    static info(message) {
        return this.show(message, 'info');
    }
}

// Global notification helper
window.notify = NotificationHelper;

/**
 * Loading Indicator Helper
 */
class LoadingHelper {
    static show(message = 'Loading...') {
        const existing = document.querySelector('#loading-indicator');
        if (existing) {
            existing.remove();
        }
        
        const loader = document.createElement('div');
        loader.id = 'loading-indicator';
        loader.innerHTML = `
            <div class="loading-backdrop">
                <div class="loading-content">
                    <div class="loading-spinner"></div>
                    <div class="loading-message">${message}</div>
                </div>
            </div>
        `;
        
        // Add styles if not present
        if (!document.querySelector('#loading-styles')) {
            const styles = document.createElement('style');
            styles.id = 'loading-styles';
            styles.textContent = `
                .loading-backdrop {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10001;
                }
                .loading-content {
                    background: white;
                    padding: 32px;
                    border-radius: 12px;
                    text-align: center;
                    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
                }
                .loading-spinner {
                    width: 40px;
                    height: 40px;
                    border: 4px solid #e5e7eb;
                    border-top: 4px solid #3b82f6;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 16px;
                }
                .loading-message {
                    color: #374151;
                    font-weight: 500;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(styles);
        }
        
        document.body.appendChild(loader);
        return loader;
    }
    
    static hide() {
        const loader = document.querySelector('#loading-indicator');
        if (loader) {
            loader.remove();
        }
    }
}

// Global loading helper
window.loading = LoadingHelper;

console.log('✅ Database API client loaded successfully');