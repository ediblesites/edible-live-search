<?php
/**
 * Plugin Name: Edible Live Search
 * Plugin URI: https://github.com/edible-live-search
 * Description: A customizable live search widget with AJAX-powered autocomplete functionality, analytics tracking, and comprehensive admin configuration.
 * Version: 1.0.4
 * Author: Edible Team
 * Author URI: https://edible.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: edible-live-search
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('EDIBLE_LIVE_SEARCH_VERSION', '1.0.4');
define('EDIBLE_LIVE_SEARCH_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EDIBLE_LIVE_SEARCH_PLUGIN_URL', plugin_dir_url(__FILE__));
define('EDIBLE_LIVE_SEARCH_PLUGIN_FILE', __FILE__);

// Include required files
require_once EDIBLE_LIVE_SEARCH_PLUGIN_DIR . 'includes/utility.php';
require_once EDIBLE_LIVE_SEARCH_PLUGIN_DIR . 'includes/admin.php';
require_once EDIBLE_LIVE_SEARCH_PLUGIN_DIR . 'includes/ajax.php';
require_once EDIBLE_LIVE_SEARCH_PLUGIN_DIR . 'includes/analytics.php';
require_once EDIBLE_LIVE_SEARCH_PLUGIN_DIR . 'includes/shortcode.php';

/**
 * Initialize the plugin
 */
function edible_live_search_init() {
    // Load text domain for translations
    load_plugin_textdomain('edible-live-search', false, dirname(plugin_basename(__FILE__)) . '/languages');
    
    // Initialize admin functionality
    if (is_admin()) {
        edible_live_search_admin_init();
    }
    
    // Initialize frontend functionality
    edible_live_search_frontend_init();
}
add_action('init', 'edible_live_search_init');

/**
 * Plugin activation hook
 */
function edible_live_search_activate() {
    // Create database table
    edible_live_search_create_analytics_table();
    
    // Set default options
    edible_live_search_set_default_options();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'edible_live_search_activate');

/**
 * Plugin deactivation hook
 */
function edible_live_search_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'edible_live_search_deactivate');

/**
 * Initialize frontend functionality
 */
function edible_live_search_frontend_init() {
    // Enqueue HTMX
    add_action('wp_enqueue_scripts', 'edible_live_search_enqueue_scripts');
    
    // Register AJAX handlers
    add_action('wp_ajax_edible_live_search', 'edible_live_search_handler');
    add_action('wp_ajax_nopriv_edible_live_search', 'edible_live_search_handler');
    
    // Register shortcode
    add_shortcode('edible_live_search', 'edible_live_search_shortcode');
} 