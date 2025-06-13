<?php
/**
 * Uninstall script for Edible Live Search
 * 
 * This file is executed when the plugin is deleted from WordPress admin
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user has permissions
if (!current_user_can('activate_plugins')) {
    return;
}

// Delete plugin options
delete_option('edible_live_search_options');

// Drop custom table
global $wpdb;
$table_name = $wpdb->prefix . 'edible_search_analytics';
$wpdb->query("DROP TABLE IF EXISTS $table_name");

// Clear any cached data
wp_cache_flush();

// Remove scheduled events
wp_clear_scheduled_hook('edible_live_search_cleanup');

// Delete any transients
delete_transient('edible_live_search_cache');
delete_transient('edible_live_search_analytics_summary');

// Clean up any custom CSS files if they exist
$upload_dir = wp_upload_dir();
$custom_css_file = $upload_dir['basedir'] . '/edible-live-search-custom.css';
if (file_exists($custom_css_file)) {
    unlink($custom_css_file);
}

// Log uninstall for debugging
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Edible Live Search plugin uninstalled and data cleaned up');
} 