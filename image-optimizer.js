/**
 * Advanced Image Optimization Utilities
 * Provides lazy loading, format detection, and responsive image solutions
 */

class ImageOptimizer {
    static supportsWebP = null;
    static supportsAVIF = null;
    static observer = null;

    /**
     * Initialize image optimization system
     */
    static async init() {
        await this.checkFormatSupport();
        this.initLazyLoading();
        this.initPerformanceMonitoring();
    }

    /**
     * Check browser support for modern image formats
     */
    static async checkFormatSupport() {
        if (this.supportsWebP === null) {
            this.supportsWebP = await this.canUseWebP();
        }
        if (this.supportsAVIF === null) {
            this.supportsAVIF = await this.canUseAVIF();
        }
    }

    static canUseWebP() {
        return new Promise(resolve => {
            const webP = new Image();
            webP.onload = webP.onerror = () => resolve(webP.height === 2);
            webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        });
    }

    static canUseAVIF() {
        return new Promise(resolve => {
            const avif = new Image();
            avif.onload = avif.onerror = () => resolve(avif.height === 2);
            avif.src = 'data:image/avif;base64,AAAAIGZ0eXBhdmlmAAAAAGF2aWZtaWYxbWlhZk1BMUIAAADybWV0YQAAAAAAAAAoaGRscgAAAAAAAAAAcGljdAAAAAAAAAAAAAAAAGxpYmF2aWYAAAAADnBpdG0AAAAAAAEAAAAeaWxvYwAAAABEAAABAAEAAAABAAABGgAAAB0AAAAoaWluZgAAAAAAAQAAABppbmZlAgAAAAABAABhdjAxQ29sb3IAAAAAamlwcnAAAABLaXBjbwAAABRpc3BlAAAAAAAAAAIAAAACAAAAEHBpeGkAAAAAAwgICAAAAAxhdjFDgQ0MAAAAABNjb2xybmNseAACAAIAAYAAAAAXaXBtYQAAAAAAAAABAAEEAQKDBAAAACVtZGF0EgAKCBgABogQEAwgMg8f8D///8WfhwB8+ErK42A=';
        });
    }

    /**
     * Get optimized URL for different services
     */
    static getOptimizedUrl(originalUrl, width = 1200, quality = 80, format = 'auto') {
        if (!originalUrl) return '';

        // Unsplash optimization
        if (originalUrl.includes('unsplash.com')) {
            let params = `w=${width}&q=${quality}&auto=format&fit=crop`;
            if (format !== 'auto') params += `&fm=${format}`;
            return `${originalUrl.split('?')[0]}?${params}`;
        }
        
        // Pexels optimization
        if (originalUrl.includes('images.pexels.com')) {
            return `${originalUrl}?auto=compress&cs=tinysrgb&w=${width}&h=${Math.round(width * 0.6)}`;
        }
        
        // WordPress/custom domain optimization
        if (originalUrl.includes('rumahdaisycantik.com') || originalUrl.includes('wp-content')) {
            const separator = originalUrl.includes('?') ? '&' : '?';
            return `${originalUrl}${separator}w=${width}&quality=${quality}`;
        }

        // Cloudinary detection and optimization
        if (originalUrl.includes('cloudinary.com')) {
            return originalUrl.replace(/\/upload\//, `/upload/w_${width},q_${quality},f_auto/`);
        }

        return originalUrl;
    }

    /**
     * Create responsive image sizes
     */
    static createResponsiveSizes(baseUrl) {
        return {
            small: this.getOptimizedUrl(baseUrl, 400, 75),
            medium: this.getOptimizedUrl(baseUrl, 800, 80),
            large: this.getOptimizedUrl(baseUrl, 1200, 85),
            xlarge: this.getOptimizedUrl(baseUrl, 1600, 90)
        };
    }

    /**
     * Generate srcset attribute
     */
    static generateSrcSet(baseUrl) {
        const sizes = this.createResponsiveSizes(baseUrl);
        return `${sizes.small} 400w, ${sizes.medium} 800w, ${sizes.large} 1200w, ${sizes.xlarge} 1600w`;
    }

    /**
     * Create optimized picture element sources
     */
    static createPictureElement(baseUrl, alt, className = '') {
        const sources = [];
        
        // Add AVIF source if supported and service supports it
        if (this.supportsAVIF && (baseUrl.includes('unsplash.com') || baseUrl.includes('cloudinary.com'))) {
            const avifSizes = this.createResponsiveSizes(baseUrl);
            Object.keys(avifSizes).forEach(key => {
                const width = key === 'small' ? 400 : key === 'medium' ? 800 : key === 'large' ? 1200 : 1600;
                avifSizes[key] = this.getOptimizedUrl(baseUrl, width, 85, 'avif');
            });
            const avifSrcSet = Object.values(avifSizes).map((url, i) => 
                `${url} ${[400, 800, 1200, 1600][i]}w`).join(', ');
            sources.push(`<source srcset="${avifSrcSet}" type="image/avif">`);
        }

        // Add WebP source if supported and service supports it
        if (this.supportsWebP && (baseUrl.includes('unsplash.com') || baseUrl.includes('cloudinary.com'))) {
            const webpSizes = this.createResponsiveSizes(baseUrl);
            Object.keys(webpSizes).forEach(key => {
                const width = key === 'small' ? 400 : key === 'medium' ? 800 : key === 'large' ? 1200 : 1600;
                webpSizes[key] = this.getOptimizedUrl(baseUrl, width, 85, 'webp');
            });
            const webpSrcSet = Object.values(webpSizes).map((url, i) => 
                `${url} ${[400, 800, 1200, 1600][i]}w`).join(', ');
            sources.push(`<source srcset="${webpSrcSet}" type="image/webp">`);
        }

        return {
            sources: sources.join(''),
            fallbackSrc: this.getOptimizedUrl(baseUrl, 1200, 85),
            srcSet: this.generateSrcSet(baseUrl)
        };
    }

    /**
     * Initialize lazy loading with Intersection Observer
     */
    static initLazyLoading() {
        if (!('IntersectionObserver' in window)) {
            // Fallback for older browsers
            this.loadAllImages();
            return;
        }

        const options = {
            root: null,
            rootMargin: '50px',
            threshold: 0.1
        };

        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    this.loadImage(entry.target);
                    this.observer.unobserve(entry.target);
                }
            });
        }, options);

        // Observe all lazy images
        document.querySelectorAll('.lazy-image').forEach(img => {
            this.observer.observe(img);
        });
    }

    /**
     * Load a single image with optimization
     */
    static loadImage(img) {
        const originalSrc = img.dataset.src;
        const originalSrcSet = img.dataset.srcset;
        if (!originalSrc) return;

        // Show loading state
        img.classList.add('loading');

        // Preload the image
        const tempImage = new Image();
        
        tempImage.onload = () => {
            img.src = originalSrc;
            if (originalSrcSet) {
                img.srcset = originalSrcSet;
            }
            img.classList.add('loaded');
            img.classList.remove('loading');
            img.style.opacity = '1';
            
            // Hide placeholder with smooth transition
            const container = img.closest('.image-container');
            if (container) {
                const placeholder = container.querySelector('.image-placeholder');
                if (placeholder) {
                    placeholder.style.opacity = '0';
                    setTimeout(() => {
                        if (placeholder.parentNode) {
                            placeholder.remove();
                        }
                    }, 500);
                }
            }

            // Trigger custom event
            img.dispatchEvent(new CustomEvent('imageLoaded', { detail: { success: true } }));
        };
        
        tempImage.onerror = () => {
            img.src = this.generatePlaceholder(1200, 700, 'Image Not Available');
            img.style.opacity = '1';
            img.classList.add('loaded', 'error');
            img.classList.remove('loading');
            
            const container = img.closest('.image-container');
            if (container) {
                const placeholder = container.querySelector('.image-placeholder');
                if (placeholder) placeholder.remove();
            }

            // Trigger custom event
            img.dispatchEvent(new CustomEvent('imageLoaded', { detail: { success: false } }));
        };

        // Set loading timeout
        setTimeout(() => {
            if (!img.classList.contains('loaded')) {
                tempImage.onerror();
            }
        }, 10000); // 10 second timeout

        tempImage.src = originalSrc;
    }

    /**
     * Generate placeholder image URL
     */
    static generatePlaceholder(width, height, text = 'Loading...') {
        return `https://placehold.co/${width}x${height}/E0E0E0/666?text=${encodeURIComponent(text)}`;
    }

    /**
     * Fallback for browsers without IntersectionObserver
     */
    static loadAllImages() {
        document.querySelectorAll('.lazy-image').forEach(img => {
            this.loadImage(img);
        });
    }

    /**
     * Performance monitoring
     */
    static initPerformanceMonitoring() {
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                list.getEntries().forEach((entry) => {
                    if (entry.entryType === 'largest-contentful-paint') {
                        console.log('LCP:', entry.startTime);
                    }
                });
            });
            observer.observe({ entryTypes: ['largest-contentful-paint'] });
        }
    }

    /**
     * Utility to convert images to lazy loading
     */
    static convertToLazy(selector = 'img') {
        document.querySelectorAll(selector).forEach(img => {
            if (!img.classList.contains('lazy-image') && img.src) {
                img.dataset.src = img.src;
                img.src = this.generatePlaceholder(400, 300, 'Loading...');
                img.classList.add('lazy-image');
                if (this.observer) {
                    this.observer.observe(img);
                }
            }
        });
    }

    /**
     * Preload critical images
     */
    static preloadCritical(urls) {
        if (!Array.isArray(urls)) urls = [urls];
        
        urls.forEach(url => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = this.getOptimizedUrl(url, 1200, 90);
            document.head.appendChild(link);
        });
    }
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => ImageOptimizer.init());
} else {
    ImageOptimizer.init();
}