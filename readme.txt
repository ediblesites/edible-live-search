=== Edible Live Search ===
Contributors: edibleteam
Tags: search, ajax, live search, autocomplete, analytics, widget, shortcode
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A customizable live search widget with AJAX-powered autocomplete functionality, analytics tracking, and comprehensive admin configuration.

== Description ==

Edible Live Search provides a powerful, customizable search experience for your WordPress site with real-time results as users type. Built with HTMX for modern, lightweight AJAX functionality without heavy JavaScript dependencies.

= Key Features =

* **Live Search**: Real-time search results as users type with configurable delay
* **HTMX Integration**: Modern AJAX implementation without heavy JavaScript
* **Analytics Tracking**: Comprehensive search analytics with popular searches and failure tracking
* **Multiple Embedding Options**: Shortcode, PHP function, and widget support
* **Customizable Appearance**: Multiple color schemes and custom CSS support
* **Responsive Design**: Mobile-first design with touch-friendly interface
* **Accessibility**: WCAG compliant with keyboard navigation and screen reader support
* **Performance Optimized**: Efficient queries with optional caching
* **Security**: Nonce verification and input sanitization

= Embedding Options =

**Shortcode:**
`[edible_live_search]`

**PHP Function:**
`<?php edible_live_search(); ?>`

**Widget:**
Add "Edible Live Search" widget to any widget area

= Configuration =

* Choose which post types to include in search
* Set minimum characters before search triggers
* Configure search delay timing
* Select search fields (title, content, excerpt, custom fields)
* Choose color schemes and customize appearance
* Enable/disable analytics tracking
* Set data retention policies

= Analytics Features =

* Track all search queries with results count
* Monitor popular searches and failed searches
* Export analytics data to CSV
* View daily, weekly, and monthly statistics
* Success rate tracking

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/edible-live-search` directory, or install through WordPress admin
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Edible Search' in the admin menu to configure settings
4. Use the shortcode `[edible_live_search]` or add the widget to display the search

== Frequently Asked Questions ==

= What is HTMX? =

HTMX is a modern JavaScript library that allows you to access AJAX, CSS Transitions, WebSockets and Server Sent Events directly in HTML, without writing JavaScript. This makes the search functionality lightweight and performant.

= Can I customize the appearance? =

Yes! The plugin includes multiple color schemes and supports custom CSS. You can also override styles using the custom CSS field in the admin settings.

= Which post types are supported? =

By default, the plugin searches posts and pages. You can configure it to search any public post type including custom post types, products, etc.

= Is the search secure? =

Yes, all search requests are protected with WordPress nonces and all user input is properly sanitized to prevent security vulnerabilities.

= Can I track search analytics? =

Yes! The plugin includes comprehensive analytics tracking. You can view popular searches, failed searches, and export data to CSV.

= Is it mobile-friendly? =

Absolutely! The search interface is built with a mobile-first approach and includes touch-friendly interactions.

== Screenshots ==

1. Search interface with live results
2. Admin settings page with tabbed interface
3. Analytics dashboard showing search statistics
4. Widget configuration options

== Changelog ==

= 1.0.0 =
* Initial release
* Live search functionality with HTMX
* Analytics tracking system
* Multiple embedding options
* Admin configuration interface
* Responsive design
* Accessibility features

== Upgrade Notice ==

= 1.0.0 =
Initial release of Edible Live Search plugin.

== Usage Examples ==

= Basic Shortcode =
`[edible_live_search]`

= Shortcode with Parameters =
`[edible_live_search placeholder="Search posts..." results_limit="10" color_scheme="dark"]`

= PHP Function =
```php
<?php
// Basic usage
edible_live_search();

// With parameters
edible_live_search(array(
    'placeholder' => 'Search...',
    'results_limit' => 8,
    'post_types' => array('post', 'page', 'product'),
    'color_scheme' => 'blue'
));
?>
```

= Widget =
1. Go to Appearance > Widgets
2. Add "Edible Live Search" widget to desired widget area
3. Configure widget settings
4. Save

== Support ==

For support, feature requests, or bug reports, please visit our GitHub repository or contact us through our website.

== Credits ==

Built with HTMX for modern AJAX functionality.
Designed with accessibility and performance in mind. 