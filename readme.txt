=== Edible Live Search ===
Contributors: edibleteam
Tags: search, ajax, live search, autocomplete, analytics, widget, shortcode
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A customizable live search widget with AJAX-powered autocomplete functionality, analytics tracking, and comprehensive admin configuration. Features include default search result pages for improved user experience when no results are found.

== Description ==

Edible Live Search provides a powerful, customizable search experience for your WordPress site with real-time results as users type. Built with HTMX for modern, lightweight AJAX functionality without heavy JavaScript dependencies.

= Key Features =

* **Live Search**: Real-time search results as users type with configurable delay
* **HTMX Integration**: Modern AJAX implementation without heavy JavaScript
* **Default Search Results**: Configure a page to display when no search results are found
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

= 1.0.7 =
* Code refactoring: Created reusable template function for search result items
* Eliminated code duplication between regular results and default search results
* Improved maintainability and consistency of search result display

= 1.0.6 =
* Added default search result feature - configure a page to display when no search results are found
* Improved user experience by providing helpful fallback content instead of empty results
* Admin can select any published page as the default search result
* Default result appears formatted identically to regular search results

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
```