<?php
/**
 * Utility functions for Edible Live Search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get plugin option with fallback to default
 */
function edible_live_search_get_option($key, $default = null) {
    $options = get_option('edible_live_search_options', array());
    
    if (isset($options[$key])) {
        return $options[$key];
    }
    
    // Return default if not set
    $defaults = edible_live_search_get_default_options();
    return isset($defaults[$key]) ? $defaults[$key] : $default;
}

/**
 * Get all plugin options
 */
function edible_live_search_get_options() {
    $options = get_option('edible_live_search_options', array());
    $defaults = edible_live_search_get_default_options();
    
    return wp_parse_args($options, $defaults);
}

/**
 * Get default options
 */
function edible_live_search_get_default_options() {
    return array(
        'post_types' => array('post', 'page'),
        'results_limit' => 5,
        'min_characters' => 3,
        'search_delay' => 300,
        'search_fields' => array('title', 'content'),
        'default_result_page_id' => 0,
        'enable_analytics' => true,
        'data_retention_days' => 90,
        'fallback_image_id' => 0,
        'color_scheme' => 'default',
        'placeholder' => __('Search...', 'edible-live-search'),
        'no_results_text' => __('No results found', 'edible-live-search'),
        'enable_caching' => false,
        'cache_duration' => 3600
    );
}

/**
 * Set default options
 */
function edible_live_search_set_default_options() {
    $existing_options = get_option('edible_live_search_options', array());
    $default_options = edible_live_search_get_default_options();
    
    $merged_options = wp_parse_args($existing_options, $default_options);
    update_option('edible_live_search_options', $merged_options);
}

/**
 * Sanitize text input
 */
function edible_live_search_sanitize_input($input) {
    return sanitize_text_field($input);
}

/**
 * Get thumbnail for post
 */
function edible_live_search_get_thumbnail($post_id, $size = array(60, 60)) {
    // First try to get company_logo custom field
    $company_logo = get_post_meta($post_id, 'company_logo', true);
    if (!empty($company_logo)) {
        $image = wp_get_attachment_image_src($company_logo, $size);
        if ($image) {
            return array(
                'url' => $image[0],
                'width' => $image[1],
                'height' => $image[2]
            );
        }
    }
    
    // Fallback to featured image
    if (has_post_thumbnail($post_id)) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
        if ($image) {
            return array(
                'url' => $image[0],
                'width' => $image[1],
                'height' => $image[2]
            );
        }
    }
    
    // Final fallback to admin-selected image
    $fallback_image_id = edible_live_search_get_option('fallback_image_id', 0);
    if ($fallback_image_id) {
        $image = wp_get_attachment_image_src($fallback_image_id, $size);
        if ($image) {
            return array(
                'url' => $image[0],
                'width' => $image[1],
                'height' => $image[2]
            );
        }
    }
    
    // Return default placeholder
    return array(
        'url' => EDIBLE_LIVE_SEARCH_PLUGIN_URL . 'public/images/placeholder.png',
        'width' => $size[0],
        'height' => $size[1]
    );
}

/**
 * Create analytics table
 */
function edible_live_search_create_analytics_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'edible_search_analytics';
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        search_term varchar(255) NOT NULL,
        results_count int(11) NOT NULL DEFAULT 0,
        search_timestamp datetime NOT NULL,
        PRIMARY KEY (id),
        KEY search_term (search_term),
        KEY search_timestamp (search_timestamp)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

/**
 * Get available post types
 */
function edible_live_search_get_available_post_types() {
    $post_types = get_post_types(array(
        'public' => true,
        'show_ui' => true
    ), 'objects');
    
    $available_types = array();
    foreach ($post_types as $post_type) {
        $available_types[$post_type->name] = $post_type->labels->name;
    }
    
    return $available_types;
}

/**
 * Get search fields options
 */
function edible_live_search_get_search_fields() {
    return array(
        'title' => __('Title', 'edible-live-search'),
        'content' => __('Content', 'edible-live-search'),
        'excerpt' => __('Excerpt', 'edible-live-search'),
        'custom_fields' => __('Custom Fields', 'edible-live-search')
    );
} 