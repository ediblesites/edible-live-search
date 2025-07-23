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
    // Ensure scripts and styles are loaded
    wp_enqueue_script(
        'htmx',
        'https://unpkg.com/htmx.org@1.9.10',
        array(),
        '1.9.10',
        true
    );
    
    wp_enqueue_style(
        'edible-live-search',
        EDIBLE_LIVE_SEARCH_PLUGIN_URL . 'public/css/search-style.css',
        array(),
        EDIBLE_LIVE_SEARCH_VERSION
    );
    
    wp_enqueue_script(
        'edible-live-search-keyboard',
        EDIBLE_LIVE_SEARCH_PLUGIN_URL . 'public/js/search-keyboard.js',
        array('htmx'),
        EDIBLE_LIVE_SEARCH_VERSION,
        true
    );
    
    // Localize script for AJAX URL and nonce
    wp_localize_script('htmx', 'edible_live_search_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('edible_search'),
        'action' => 'edible_live_search'
    ));
    
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'placeholder' => edible_live_search_get_option('placeholder', __('Search...', 'edible-live-search')),
        'class' => 'edible-search-form'
    ), $atts, 'edible_live_search');
    
    // Generate and return search form
    return edible_live_search_get_form(array(
        'placeholder' => $atts['placeholder'],
        'class' => $atts['class']
    ));
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
    $help .= '</ul>';
    $help .= '<h4>' . __('Examples:', 'edible-live-search') . '</h4>';
    $help .= '<p><code>[edible_live_search]</code></p>';
    $help .= '<p><code>[edible_live_search placeholder="Search posts..."]</code></p>';
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