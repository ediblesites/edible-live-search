# Edible Live Search

A lightweight, HTMX-powered live search plugin for WordPress with analytics tracking and comprehensive admin configuration.

## Features

### 🔍 Live Search
- **Real-time results** as you type (with configurable delay)
- **HTMX-powered** - No custom JavaScript required
- **Keyboard navigation** - Arrow keys, Enter to select
- **Smooth animations** - Ease-in dropdown appearance
- **Auto-trigger** - Results reappear when returning to page

### 🎨 Customizable
- **Configurable placeholder text** - Set via admin or shortcode
- **Custom "no results" message** - Fully configurable text
- **Responsive design** - Works on desktop and mobile
- **Direct CSS access** - Style it your way
- **Loupe icon** - Built-in search icon

### 📊 Analytics
- **Search tracking** - Monitor popular searches
- **Failed searches** - Identify content gaps
- **Privacy-compliant** - No personal data collection
- **Data retention** - Configurable cleanup

### ⚙️ Admin Controls
- **Post type selection** - Choose what to search
- **Search fields** - Title, content, excerpt, custom fields
- **Results limit** - 1-20 results
- **Minimum characters** - 1-10 character threshold
- **Search delay** - 100-2000ms debouncing

## Installation

1. Upload the plugin files to `/wp-content/plugins/edible-live-search/`
2. Activate the plugin through the WordPress admin
3. Configure settings at **Settings > Live Search**
4. Add the shortcode to your pages

## Usage

### Shortcode
```php
[edible_live_search]
[edible_live_search placeholder="Search posts..."]
[edible_live_search class="my-custom-class"]
```

### Admin Configuration
Navigate to **Settings > Live Search** to configure all search options.

## Technical Details

### Architecture
- **HTMX** for AJAX interactions (no custom JS)
- **Conditional loading** - Assets only load when search form present
- **Privacy-first** - No IP or user agent tracking
- **Internal plugin** - Streamlined for internal use

### Performance
- **Conditional asset loading** - HTMX only when needed
- **Efficient queries** - Optimized WordPress queries
- **Minimal CSS** - Essential styles only
- **No external dependencies** - Self-contained

## Keyboard Navigation

- **↑ Arrow Up** - Previous result
- **↓ Arrow Down** - Next result
- **Enter** - Navigate to selected result
- **Escape** - No action (ignored)

## Analytics

View search analytics at **Settings > Live Search Analytics**:
- **Today's searches** - Current day statistics
- **Weekly totals** - 7-day search volume
- **Success rate** - Percentage of successful searches
- **Popular searches** - Most common search terms
- **Failed searches** - Searches with no results

## Browser Support

- **Modern browsers** - Chrome, Firefox, Safari, Edge
- **HTMX 1.9.10** - From CDN
- **Responsive design** - Mobile-first approach
- **Accessibility** - Keyboard navigation support

## Security

- **Nonce verification** - CSRF protection
- **Input sanitization** - All user input sanitized
- **Capability checks** - Admin-only settings
- **SQL injection prevention** - WordPress query functions

## Version History

### 1.0.5
- Removed browser autocomplete
- Fixed escape key behavior
- Configurable "no results" text

### 1.0.4
- Added keyboard navigation
- Auto-trigger search on page return
- Smooth dropdown animations
- Loupe icon in search box

### 1.0.3
- Simplified for internal use
- Removed widget functionality
- Removed color schemes
- Streamlined codebase

## Support

This is an internal plugin designed for specific use cases. For modifications or support, refer to the codebase directly.

## License

GPL v2 or later
