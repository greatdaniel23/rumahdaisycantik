/**
 * Universal Image Optimization Initializer
 * Automatically applies image optimization to any page
 */

document.addEventListener('DOMContentLoaded', async function() {
    // Initialize image optimization if available
    if (typeof ImageOptimizer !== 'undefined') {
        await ImageOptimizer.init();
        
        // Convert existing images to lazy loading
        convertExistingImages();
        
        // Set up dynamic content observers
        setupDynamicContentObserver();
        
        console.log('✅ Image optimization initialized');
    }
});

/**
 * Convert existing images to lazy loading format
 */
function convertExistingImages() {
    const images = document.querySelectorAll('img:not(.lazy-image):not([data-src])');
    
    images.forEach(img => {
        // Skip logos and critical images that should load immediately
        if (img.id.includes('logo') || img.classList.contains('critical')) {
            return;
        }
        
        // Skip if already processed
        if (img.dataset.optimized) return;
        
        const container = document.createElement('div');
        container.className = 'image-container';
        container.style.width = img.style.width || img.getAttribute('width') || '100%';
        container.style.height = img.style.height || img.getAttribute('height') || 'auto';
        
        // Create placeholder
        const placeholder = document.createElement('div');
        placeholder.className = 'image-placeholder';
        container.appendChild(placeholder);
        
        // Convert image to lazy loading
        const originalSrc = img.src;
        if (originalSrc && !originalSrc.includes('placehold.co')) {
            // Create optimized image
            const optimizedImg = img.cloneNode(true);
            optimizedImg.dataset.src = originalSrc;
            optimizedImg.src = '';
            optimizedImg.className = `${img.className} lazy-image`.trim();
            optimizedImg.style.opacity = '0';
            optimizedImg.loading = 'lazy';
            optimizedImg.decoding = 'async';
            
            // Add responsive attributes
            if (!optimizedImg.getAttribute('sizes')) {
                optimizedImg.setAttribute('sizes', '(max-width: 768px) 100vw, (max-width: 1200px) 50vw, 33vw');
            }
            
            // Generate optimized sources if possible
            if (typeof ImageOptimizer !== 'undefined') {
                const imageData = ImageOptimizer.createPictureElement(originalSrc, img.alt || '');
                if (imageData.sources) {
                    const picture = document.createElement('picture');
                    picture.innerHTML = imageData.sources;
                    picture.appendChild(optimizedImg);
                    container.appendChild(picture);
                } else {
                    container.appendChild(optimizedImg);
                }
                
                // Set up lazy loading observer
                if (ImageOptimizer.observer) {
                    ImageOptimizer.observer.observe(optimizedImg);
                }
            } else {
                container.appendChild(optimizedImg);
            }
            
            // Replace original image with container
            img.parentNode.replaceChild(container, img);
            
            // Mark as optimized
            optimizedImg.dataset.optimized = 'true';
        }
    });
}

/**
 * Observer for dynamically added content
 */
function setupDynamicContentObserver() {
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            mutation.addedNodes.forEach(node => {
                if (node.nodeType === Node.ELEMENT_NODE) {
                    // Check if the node itself is an image
                    if (node.tagName === 'IMG' && !node.classList.contains('lazy-image')) {
                        processNewImage(node);
                    }
                    
                    // Check for images in added content
                    const images = node.querySelectorAll ? node.querySelectorAll('img:not(.lazy-image)') : [];
                    images.forEach(processNewImage);
                }
            });
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}

/**
 * Process a newly added image
 */
function processNewImage(img) {
    if (img.dataset.optimized || img.id.includes('logo')) return;
    
    const originalSrc = img.src;
    if (originalSrc && !originalSrc.includes('placehold.co') && typeof ImageOptimizer !== 'undefined') {
        // Convert to lazy loading
        img.dataset.src = originalSrc;
        img.src = '';
        img.classList.add('lazy-image');
        img.style.opacity = '0';
        img.loading = 'lazy';
        img.decoding = 'async';
        
        // Set up observer
        if (ImageOptimizer.observer) {
            ImageOptimizer.observer.observe(img);
        }
        
        img.dataset.optimized = 'true';
    }
}

/**
 * Preload critical images
 */
function preloadCriticalImages() {
    // Preload hero/banner images
    const heroImages = document.querySelectorAll('[id*="hero"], [id*="banner"], [id*="welcome"], .critical');
    heroImages.forEach(img => {
        if (img.dataset.src || img.src) {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = img.dataset.src || img.src;
            document.head.appendChild(link);
        }
    });
}

// Initialize critical image preloading
preloadCriticalImages();