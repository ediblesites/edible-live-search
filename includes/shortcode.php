<?php
/**
 * Shortcode functions for Edible Live Search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main shortcode function
 */
function edible_live_search_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'placeholder' => edible_live_search_get_option('placeholder', __('Search...', 'edible-live-search')),
        'class' => 'edible-search-form',
        'color_scheme' => ''
    ), $atts, 'edible_live_search');
    
    // Generate and return search form
    return edible_live_search_get_form(array(
        'placeholder' => $atts['placeholder'],
        'class' => $atts['class'] . ' edible-search-color-' . $atts['color_scheme']
    ));
}

/**
 * Set temporary options for current request
 */
function edible_live_search_set_temp_options($options) {
    global $edible_live_search_temp_options;
    
    if (!isset($edible_live_search_temp_options)) {
        $edible_live_search_temp_options = array();
    }
    
    $edible_live_search_temp_options = wp_parse_args($options, $edible_live_search_temp_options);
}

/**
 * Get temporary options for current request
 */
function edible_live_search_get_temp_options() {
    global $edible_live_search_temp_options;
    
    return isset($edible_live_search_temp_options) ? $edible_live_search_temp_options : array();
}

/**
 * Clear temporary options
 */
function edible_live_search_clear_temp_options() {
    global $edible_live_search_temp_options;
    
    unset($edible_live_search_temp_options);
}

/**
 * Get option with temporary override support
 */
function edible_live_search_get_option_with_override($key, $default = null) {
    $temp_options = edible_live_search_get_temp_options();
    
    if (isset($temp_options[$key])) {
        return $temp_options[$key];
    }
    
    return edible_live_search_get_option($key, $default);
}

/**
 * Shortcode documentation
 */
function edible_live_search_shortcode_help() {
    $help = '<div class="edible-search-shortcode-help">';
    $help .= '<h3>' . __('Edible Live Search Shortcode Usage', 'edible-live-search') . '</h3>';
    $help .= '<p><code>[edible_live_search]</code> - ' . __('Basic search form', 'edible-live-search') . '</p>';
    $help .= '<h4>' . __('Available Parameters:', 'edible-live-search') . '</h4>';
    $help .= '<ul>';
    $help .= '<li><code>placeholder</code> - ' . __('Search input placeholder text (overrides admin setting)', 'edible-live-search') . '</li>';
    $help .= '<li><code>class</code> - ' . __('Additional CSS classes', 'edible-live-search') . '</li>';
    $help .= '<li><code>results_limit</code> - ' . __('Maximum number of results (1-20)', 'edible-live-search') . '</li>';
    $help .= '<li><code>min_characters</code> - ' . __('Minimum characters to trigger search', 'edible-live-search') . '</li>';
    $help .= '<li><code>search_delay</code> - ' . __('Delay in milliseconds before search', 'edible-live-search') . '</li>';
    $help .= '<li><code>post_types</code> - ' . __('Comma-separated list of post types', 'edible-live-search') . '</li>';
    $help .= '<li><code>color_scheme</code> - ' . __('Color scheme (default, dark, light, blue, green)', 'edible-live-search') . '</li>';
    $help .= '</ul>';
    $help .= '<h4>' . __('Examples:', 'edible-live-search') . '</h4>';
    $help .= '<p><code>[edible_live_search results_limit="10"]</code></p>';
    $help .= '<p><code>[edible_live_search post_types="post,page,product" color_scheme="dark"]</code></p>';
    $help .= '</div>';
    
    return $help;
}

/**
 * Add shortcode help to admin
 */
function edible_live_search_add_shortcode_help() {
    if (current_user_can('manage_options')) {
        add_action('admin_notices', function() {
            if (isset($_GET['page']) && $_GET['page'] === 'edible-live-search') {
                echo edible_live_search_shortcode_help();
            }
        });
    }
}
add_action('admin_init', 'edible_live_search_add_shortcode_help'); 