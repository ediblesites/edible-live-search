# Edible Live Search - WordPress Plugin Plan

## Overview
A WordPress plugin that provides a customizable live search widget with AJAX-powered autocomplete functionality. The search can be embedded on any page and displays results with thumbnails and titles in an attractive format.

## Core Features

### Search Functionality
- **Live Search**: Real-time search results as users type (with debouncing to prevent excessive requests)
- **AJAX Implementation**: Asynchronous search without page refreshes
- **Autocomplete Interface**: Dropdown-style results that appear below the search input
- **Minimum Character Threshold**: Configurable minimum characters before search triggers (default: 3)
- **Search Delay**: Configurable delay after typing stops before search executes (default: 300ms)

### Search Analytics
- **Search Tracking**: Record all search queries with results count and timestamp
- **Success/Failure Metrics**: Track searches that return results vs. empty results
- **Data Storage**: Analytics stored in custom database table for easy reporting
- **Admin Reporting**: Simple dashboard showing popular searches and failure rates

### Admin Configuration Panel
- **Post Type Selection**: Checkbox interface to choose which post types to include in search
- **Results Limit**: Number input to set maximum results displayed (default: 5, range: 1-20)
- **Default Search Result**: Dropdown to select a page to display when no search results are found (optional)
- **Thumbnail Settings**: 
  - Thumbnails always enabled and displayed
  - Fixed thumbnail size: 60x60 pixels (cropped)
  - Fallback image: Media ID specified in admin settings
  - Primary image source: Custom field `company_logo`
  - Secondary fallback: Featured image if company_logo not available
- **Search Fields**: Choose which fields to search (title, content, excerpt, custom fields)
- **Styling Options**: Basic color and layout customization

### Display Components
- **Search Input**: Clean, modern search box with placeholder text
- **Results Container**: Styled dropdown/overlay that appears below input
- **Result Items**: Each result shows:
  - Post thumbnail (from company_logo custom field or featured image)
  - Post title (linked to full post)
  - Optional excerpt or meta information

### Embedding Options
- **Shortcode**: `[edible_live_search]` with optional parameters
- **PHP Function**: For theme developers to embed programmatically

## Technical Process Flow

### HTMX Implementation Details

#### Frontend Structure
The search interface consists of a simple HTML form with HTMX attributes:
```html
<input type="text" 
       name="search_term"
       hx-get="/wp-admin/admin-ajax.php?action=edible_live_search" 
       hx-trigger="keyup changed delay:300ms, focus"
       hx-target="#edible-search-results"
       hx-indicator="#edible-loading"
       hx-vals='{"nonce": "<?php echo wp_create_nonce('edible_search'); ?>"}'>
<div id="edible-loading" class="htmx-indicator">Searching...</div>
<div id="edible-search-results"></div>
```

#### Request Flow
1. **User Input**: User types in search box
2. **HTMX Trigger**: After 300ms delay, HTMX automatically sends GET request to WordPress AJAX endpoint
3. **Server Processing**: WordPress processes the request via registered AJAX action
4. **Query Execution**: Server runs WP_Query with search parameters and post type filters
5. **Results Processing**: If no results found and default search result page is configured, display that page instead
6. **HTML Generation**: Server renders HTML fragment with search results or default result
7. **Response Handling**: HTMX receives HTML response and automatically inserts it into target div
8. **Analytics Logging**: Server logs search term, result count, and timestamp to database

#### Server-Side AJAX Handler
```php
// WordPress AJAX action registration
add_action('wp_ajax_edible_live_search', 'handle_edible_search');
add_action('wp_ajax_nopriv_edible_live_search', 'handle_edible_search');

function handle_edible_search() {
    // 1. Verify nonce for security
    // 2. Sanitize search input
    // 3. Check minimum character threshold
    // 4. Build WP_Query with admin settings
    // 5. Execute search query
    // 6. If no results and default page configured, get default page data
    // 7. Generate HTML for results or default result
    // 8. Log analytics data
    // 9. Return HTML fragment
}
```

#### Data Flow Architecture
- **Input Sanitization**: All user input sanitized via `sanitize_text_field()`
- **Query Building**: Dynamic WP_Query based on admin-selected post types
- **Image Resolution**: Check for `company_logo` custom field, fallback to featured image, then admin fallback
- **HTML Assembly**: Server-side rendering ensures consistent markup
- **Analytics Storage**: Each search logged to `wp_edible_search_analytics` table

#### Security Layer
- **Nonce Verification**: CSRF protection on every request
- **Input Validation**: Server-side validation of all parameters
- **Capability Checks**: Admin settings protected by user capabilities
- **SQL Safety**: All database queries use prepared statements or WP_Query

## Technical Architecture

### Frontend Components
- **HTMX**: Declarative AJAX handling via HTML attributes (eliminates custom JavaScript)
- **CSS**: Responsive styling with customizable variables
- **HTML Structure**: Semantic markup with HTMX attributes for interactivity

### Backend Components
- **AJAX Handler**: WordPress AJAX hooks returning HTML fragments for HTMX
- **Search Query**: Custom WP_Query with relevant parameters
- **HTML Generation**: Server-side rendering of search results
- **Security**: Nonce verification and input sanitization
- **Caching**: Optional result caching for performance

### Database Considerations
- **Custom Analytics Table**: Single table for search tracking (`wp_edible_search_analytics`)
- **Post Data**: Utilizes existing WordPress post structure
- **Options Storage**: Plugin settings stored in wp_options with namespace `edible_live_search_`
- **Performance**: Optimized queries with proper indexing considerations

## User Experience Flow

### Search Process
1. User types in search box with HTMX attributes
2. HTMX automatically sends request after delay/minimum characters
3. Server processes search with admin-defined parameters
4. Results returned as HTML and automatically inserted by HTMX
5. User can click result to navigate to full post
6. Built-in loading indicators handled by HTMX

### Responsive Design
- **Mobile-First**: Touch-friendly interface
- **Loading States**: HTMX-powered loading indicators
- **Screen Reader Support**: Proper ARIA labels and semantic HTML

## Admin Interface Structure

### Settings Page Location
- **Main Menu**: "Edible Search" in WordPress admin sidebar
- **Settings Tabs**: 
  - General Settings
  - Appearance
  - Analytics
  - Advanced Options

### Configuration Sections

#### General Settings
- Post types to include in search
- Maximum number of results
- Minimum characters to trigger search
- Search delay timing
- Fields to search within
- Default search result page (dropdown of available pages)

#### Appearance Settings
- Color scheme options
- Result layout style
- Animation preferences
- Fallback image media ID selection

#### Analytics Settings
- **Enable/disable search tracking**
- **Data retention period** (30, 60, 90 days, or indefinite)
- **Popular searches report** (top 20 most searched terms)
- **Failed searches report** (searches returning zero results)
- **Search volume statistics** (daily/weekly/monthly totals)
- **Export analytics data** (CSV download)

#### Advanced Options
- Caching settings
- Custom CSS input
- Exclude specific posts/categories
- Search result ordering
- Performance optimization toggles

## Installation and Setup

### Plugin Installation
- **WordPress Repository**: Standard plugin installation
- **Manual Upload**: ZIP file upload option
- **Activation**: Single-click activation with setup wizard

### Initial Configuration
- **Setup Wizard**: Guided configuration for first-time users
- **Default Settings**: Sensible defaults that work out-of-the-box
- **Preview Mode**: Live preview of search functionality in admin

### Embedding Instructions
- **Documentation**: Clear instructions for each embedding method
- **Code Examples**: Copy-paste ready shortcodes and PHP snippets
- **Visual Guide**: Screenshots showing placement options

## Performance Considerations

### Optimization Features
- **Query Optimization**: Efficient database queries with proper LIMIT clauses
- **Result Caching**: Optional caching of search results for repeated queries
- **Asset Loading**: Conditional loading of CSS/JS only when needed
- **Debouncing**: Prevents excessive AJAX requests during typing

### Scalability
- **Large Site Support**: Handles sites with thousands of posts
- **Load Balancing**: Compatible with caching plugins and CDNs
- **Database Efficiency**: Minimal database impact with optimized queries

## Security and Compatibility

### Security Measures
- **Input Sanitization**: All user inputs properly sanitized
- **Nonce Verification**: CSRF protection for AJAX requests
- **Capability Checks**: Admin-only access to configuration
- **SQL Injection Prevention**: Prepared statements and WP_Query usage

## Future Enhancement Possibilities
- **Post Type Indicators**: Show post type badges in search results
- **Advanced Filtering**: Category, tag, and custom field filters
- **Enhanced Analytics**: User session tracking and click-through rates
- **Result Caching**: Optional caching of search results for repeated queries
- **Custom Post Field Display**: Show additional custom field values in results
- **Multi-language Support**: WPML and Polylang integration
- **API Integration**: External search service integration options
- **Default Result Customization**: Visual indicators for default search results, multiple fallback options