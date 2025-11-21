# Button Inventory - index.html

This document provides a comprehensive list of all buttons and interactive elements in the `index.html` file of the Rumah Daisy Cantik website.

## Header Buttons

### 1. **Main CTA Button (Book Now)**
- **Element ID**: `cta-book-now`
- **Text**: "Book Now" (configurable via content.json)
- **Default Link**: `https://daysicantik.alphadigitalagency.id/`
- **Classes**: `bg-purple-600 text-white px-5 py-2.5 rounded-full hover:bg-purple-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 text-sm sm:text-base font-medium`
- **Hover Effect**: Color change, shadow lift, slight upward movement
- **Configuration**: Managed via `content.json` buttons array
- **Usage**: Primary call-to-action for bookings

### 2. **Mobile Menu Toggle**
- **Element ID**: `mobile-menu-button`
- **Icon**: Font Awesome bars icon (`fas fa-bars fa-lg`)
- **Function**: Toggles mobile navigation menu visibility
- **Classes**: `text-gray-800 focus:outline-none`
- **JavaScript**: `mobileMenu.classList.toggle('hidden')`
- **Responsive**: Only visible on mobile (`md:hidden`)

## Hero Section Buttons

### 3. **Ask Us Button**
- **Element ID**: `hero-ask-us`
- **Text**: "Ask Us" (configurable via content.json)
- **Default Link**: `#` (configurable)
- **Classes**: `bg-white text-purple-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5`
- **Style**: White background with purple text
- **Hover Effect**: Light gray background, enhanced shadow, upward movement
- **Configuration**: Managed via `content.json` buttons array

### 4. **WhatsApp Button**
- **Element ID**: `hero-whatsapp`  
- **Text**: "WhatsApp" (configurable via content.json)
- **Default Link**: `https://wa.me/6282221193425`
- **Target**: `_blank` (opens in new tab)
- **Classes**: `bg-green-500 text-white px-8 py-3 rounded-full font-semibold hover:bg-green-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5`
- **Style**: Green WhatsApp branding
- **Hover Effect**: Darker green, enhanced shadow, upward movement
- **Configuration**: Managed via `content.json` buttons array

## Booking Form Buttons

### 5. **Search Availability Button**
- **Element ID**: `search-availability-btn`
- **Text**: "Search Availability" (configurable via content.json)
- **Type**: `button` (not submit to prevent form submission)
- **Classes**: `w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 font-semibold text-lg sm:col-span-2 lg:col-span-1`
- **Function**: Redirects to Airbnb booking with form data
- **JavaScript Function**: Custom Airbnb URL generation with dates and guests
- **Validation**: Checks for check-in and check-out dates before proceeding
- **Configuration**: Text managed via `content.json` buttons array

## Review Navigation Buttons

### 6. **Previous Review Button**
- **Element ID**: `prevReviewBtn`
- **Icon**: Font Awesome chevron-left (`fas fa-chevron-left`)
- **Classes**: `absolute -left-6 md:-left-16 top-1/2 transform -translate-y-1/2 bg-purple-600 hover:bg-purple-700 text-white rounded-full p-3 shadow-lg transition-all duration-300 z-10 hidden`
- **Function**: Navigate to previous review slide
- **Visibility**: Hidden by default, shown when multiple slides exist
- **Position**: Absolute positioning outside slider container

### 7. **Next Review Button**  
- **Element ID**: `nextReviewBtn`
- **Icon**: Font Awesome chevron-right (`fas fa-chevron-right`)
- **Classes**: `absolute -right-6 md:-right-16 top-1/2 transform -translate-y-1/2 bg-purple-600 hover:bg-purple-700 text-white rounded-full p-3 shadow-lg transition-all duration-300 z-10 hidden`
- **Function**: Navigate to next review slide
- **Visibility**: Hidden by default, shown when multiple slides exist
- **Position**: Absolute positioning outside slider container

### 8. **Review Dots (Dynamic)**
- **Container ID**: `reviewDots`
- **Classes**: `review-dot w-3 h-3 rounded-full transition-all duration-300 bg-purple-600` (active) / `bg-gray-300 hover:bg-purple-400` (inactive)
- **Function**: Direct navigation to specific review slide
- **Generation**: Dynamically created based on number of review slides
- **Click Handler**: `goToReviewSlide(i)` function

### 9. **Manual Review Navigation Buttons**
- **Previous**: `prevManualBtn` - Same styling as main review buttons
- **Next**: `nextManualBtn` - Same styling as main review buttons  
- **Dots**: `manualReviewDots` - Same functionality as main review dots
- **Usage**: Fallback navigation when Google Reviews API fails

### 10. **Read More/Less Buttons (Dynamic)**
- **Classes**: `read-more-btn text-purple-600 hover:text-purple-700 font-medium text-sm mt-2 transition-colors duration-200`
- **Function**: Toggle between truncated and full review text
- **Generation**: Dynamically created for reviews longer than 150 characters
- **Text Toggle**: "Read more" ↔ "Read less"

## Footer Buttons

### 11. **Subscribe Send Button**
- **Element ID**: `footer-subscribe-send`
- **Text**: "Send" (configurable via content.json)
- **Classes**: `bg-purple-600 px-4 rounded-r-lg hover:bg-purple-700 transition-colors`
- **Form Integration**: Part of email subscription form
- **Style**: Right-rounded to connect with email input
- **Configuration**: Managed via `content.json` buttons array

## Popup Modal Buttons

### 12. **Popup Close Button**
- **Element ID**: `close-popup-btn`
- **Icon**: Font Awesome times icon (`fas fa-times`)
- **Classes**: `absolute -top-4 -right-4 bg-purple-600 text-white rounded-full h-10 w-10 flex items-center justify-center shadow-lg hover:bg-purple-700 transition-colors`
- **Function**: Closes promotional popup modal
- **Position**: Absolute positioning outside modal container
- **Style**: Circular close button with hover effect

### 13. **Popup CTA Button**
- **Element ID**: `popup-cta-button`
- **Text**: Configurable via `content.json` popup section
- **Link**: Configurable via `content.json` popup section
- **Classes**: `bg-purple-600 text-white px-8 py-3 rounded-full hover:bg-purple-700 transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 font-semibold`
- **Default Text**: "Learn More"
- **Visibility**: Hidden if no buttonText/buttonLink in content.json
- **Configuration**: Managed via `content.json` popup object

## Dynamic Accommodation Buttons

### 14. **Accommodation Action Buttons (Generated)**
- **Text**: Varies per accommodation (e.g., "Book This Villa", "View Details")
- **Links**: Configurable per accommodation in content.json
- **Classes**: `text-purple-600 font-medium hover:underline`
- **Icon**: Font Awesome arrow-right (`fas fa-arrow-right ml-1 text-sm`)
- **Generation**: Created dynamically from `content.accommodations` array
- **Style**: Purple text with underline hover effect

## Button Configuration System

### **Content.json Integration**
All configurable buttons are managed through the `buttons` array in `content.json`:

```json
{
  "buttons": [
    {
      "id": "cta-book-now",
      "text": "Reserve Now!",
      "link": "https://booking.rumahdaisycantik.com"
    },
    {
      "id": "hero-ask-us", 
      "text": "Ask Us",
      "link": "https://wa.me/6282221193425"
    }
    // ... more buttons
  ]
}
```

### **JavaScript Button Application**
```javascript
content.buttons.forEach(button => {
    const btnElement = document.getElementById(button.id);
    if (btnElement) {
        btnElement.textContent = button.text;
        if (btnElement.tagName === 'A' && button.link) {
            btnElement.href = button.link;
        }
    }
});
```

## Button Categories by Function

### **Navigation Buttons**
- Mobile menu toggle
- Review slider navigation (prev/next/dots)
- Manual review navigation

### **Call-to-Action Buttons** 
- Main CTA (Book Now)
- Hero buttons (Ask Us, WhatsApp)  
- Search availability
- Popup CTA
- Accommodation actions

### **Form Buttons**
- Search availability (with validation)
- Subscribe send button

### **Utility Buttons**
- Popup close button
- Read more/less toggles

## Button States & Interactions

### **Default State**
- Base colors and styling as defined in classes
- Proper contrast ratios for accessibility

### **Hover State**
- Color changes (usually darker variants)
- Shadow enhancements
- Transform effects (scale, translate)
- Smooth transitions (300ms duration)

### **Active/Focus State**
- Focus outlines for keyboard navigation
- Active states for click feedback

### **Disabled State**
- Some buttons hidden when not applicable (navigation buttons)
- Loading states for dynamic content

## Responsive Behavior

### **Mobile Adaptations**
- Button sizing adjusts via responsive classes (`text-sm sm:text-base`)
- Mobile menu button only visible on small screens
- Grid spanning adjustments for search button (`sm:col-span-2 lg:col-span-1`)

### **Desktop Enhancements** 
- Enhanced hover effects
- Better positioning for navigation buttons
- Larger click targets

## Accessibility Features

### **Keyboard Navigation**
- All buttons accessible via Tab key
- Focus indicators for keyboard users
- Proper ARIA labels where needed

### **Screen Reader Support**
- Descriptive button text
- Alt text for icon-only buttons
- Semantic button elements vs div-based buttons

## Performance Considerations

### **Event Handling**
- Event delegation for dynamically created buttons
- Debounced resize handlers for slider buttons
- Efficient click handlers with minimal DOM manipulation

### **Animation Performance**
- CSS transforms instead of layout changes
- Hardware acceleration via `transform` and `opacity`
- Smooth 300ms transition timing

---

**Last Updated**: November 20, 2025  
**File Version**: index.html (current)  
**Total Button Count**: 14+ (including dynamic elements)
**Configuration**: Centralized via content.json for easy management