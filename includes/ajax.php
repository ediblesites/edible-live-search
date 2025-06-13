<?php
/**
 * AJAX functions for Edible Live Search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main AJAX handler for search requests
 */
function edible_live_search_handler() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'edible_search')) {
        wp_die('Security check failed');
    }
    
    // Get and sanitize search term
    $search_term = isset($_POST['search_term']) ? edible_live_search_sanitize_input($_POST['search_term']) : '';
    
    // If search term is empty, return empty content to clear results
    if (empty($search_term)) {
        wp_die();
    }
    
    // Check minimum characters
    $min_characters = edible_live_search_get_option('min_characters', 3);
    if (strlen($search_term) < $min_characters) {
        echo '<div class="edible-search-no-results">';
        echo '<p>' . sprintf(__('Please enter at least %d characters', 'edible-live-search'), $min_characters) . '</p>';
        echo '</div>';
        wp_die();
    }
    
    // Process search query
    $results = edible_live_search_process_query($search_term);
    
    // Log analytics if enabled and search term is 3+ characters
    if (edible_live_search_get_option('enable_analytics', true) && strlen($search_term) >= 3) {
        edible_live_search_log_query($search_term, count($results));
    }
    
    // Generate HTML results
    $html = edible_live_search_generate_results($results, $search_term);
    
    // Return HTML response
    echo $html;
    wp_die();
}

/**
 * Process search query
 */
function edible_live_search_process_query($search_term) {
    $options = edible_live_search_get_options();
    
    // Build query args
    $query_args = array(
        'post_type' => $options['post_types'],
        'post_status' => 'publish',
        'posts_per_page' => $options['results_limit'],
        's' => $search_term,
        'orderby' => 'relevance',
        'order' => 'DESC'
    );
    
    // Add search fields
    $search_fields = $options['search_fields'];
    if (!empty($search_fields)) {
        add_filter('posts_search', function($search, $wp_query) use ($search_fields) {
            if (!empty($search) && !empty($wp_query->query_vars['search_terms'])) {
                global $wpdb;
                
                $q = $wp_query->query_vars['search_terms'];
                $search = '';
                
                foreach ($q as $term) {
                    $search .= " AND (";
                    
                    if (in_array('title', $search_fields)) {
                        $search .= $wpdb->prepare("{$wpdb->posts}.post_title LIKE %s", '%' . $wpdb->esc_like($term) . '%');
                    }
                    
                    if (in_array('content', $search_fields)) {
                        if (in_array('title', $search_fields)) {
                            $search .= " OR ";
                        }
                        $search .= $wpdb->prepare("{$wpdb->posts}.post_content LIKE %s", '%' . $wpdb->esc_like($term) . '%');
                    }
                    
                    if (in_array('excerpt', $search_fields)) {
                        if (in_array('title', $search_fields) || in_array('content', $search_fields)) {
                            $search .= " OR ";
                        }
                        $search .= $wpdb->prepare("{$wpdb->posts}.post_excerpt LIKE %s", '%' . $wpdb->esc_like($term) . '%');
                    }
                    
                    if (in_array('custom_fields', $search_fields)) {
                        if (in_array('title', $search_fields) || in_array('content', $search_fields) || in_array('excerpt', $search_fields)) {
                            $search .= " OR ";
                        }
                        $search .= $wpdb->prepare("(SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE post_id = {$wpdb->posts}.ID AND meta_value LIKE %s)", '%' . $wpdb->esc_like($term) . '%');
                    }
                    
                    $search .= ")";
                }
                
                return $search;
            }
            return $search;
        }, 10, 2);
    }
    
    // Execute query
    $query = new WP_Query($query_args);
    
    return $query->posts;
}

/**
 * Generate HTML results
 */
function edible_live_search_generate_results($posts, $search_term) {
    if (empty($posts)) {
        $no_results_text = edible_live_search_get_option('no_results_text', __('No results found', 'edible-live-search'));
        return '<div class="edible-search-no-results">' . 
               '<p>' . esc_html($no_results_text) . '</p>' .
               '</div>';
    }
    
    $html = '<div class="edible-search-results">';
    
    foreach ($posts as $post) {
        $thumbnail = edible_live_search_get_thumbnail($post->ID);
        $excerpt = wp_trim_words($post->post_excerpt, 20, '...');
        
        $html .= '<div class="edible-search-result-item">';
        $html .= '<a href="' . get_permalink($post->ID) . '" class="edible-search-result-link">';
        $html .= '<div class="edible-search-result-thumbnail">';
        $html .= '<img src="' . esc_url($thumbnail['url']) . '" alt="' . esc_attr($post->post_title) . '" width="' . $thumbnail['width'] . '" height="' . $thumbnail['height'] . '">';
        $html .= '</div>';
        $html .= '<div class="edible-search-result-content">';
        $html .= '<h4 class="edible-search-result-title">' . esc_html($post->post_title) . '</h4>';
        $html .= '<p class="edible-search-result-excerpt">' . esc_html($excerpt) . '</p>';
        $html .= '</div>';
        $html .= '</a>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Get search form HTML
 */
function edible_live_search_get_form($atts = array()) {
    $defaults = array(
        'placeholder' => edible_live_search_get_option('placeholder', __('Search...', 'edible-live-search')),
        'class' => 'edible-search-form'
    );
    
    $atts = wp_parse_args($atts, $defaults);
    
    $nonce = wp_create_nonce('edible_search');
    $ajax_url = admin_url('admin-ajax.php');
    
    $html = '<div class="' . esc_attr($atts['class']) . '">';
    $html .= '<form class="edible-search-form-inner">';
    $html .= '<input type="text" 
                     name="search_term" 
                     class="edible-search-input" 
                     placeholder="' . esc_attr($atts['placeholder']) . '"
                     autocomplete="off"
                     hx-post="' . esc_url($ajax_url) . '"
                     hx-trigger="keyup changed delay:' . edible_live_search_get_option('search_delay', 300) . 'ms"
                     hx-target="#edible-search-results"
                     hx-indicator="#edible-loading"
                     hx-swap="innerHTML"
                     hx-vals=\'{"action": "edible_live_search", "nonce": "' . $nonce . '"}\'>';
    $html .= '<div id="edible-loading" class="htmx-indicator edible-loading">' . __('Searching...', 'edible-live-search') . '</div>';
    $html .= '<div id="edible-search-results" class="edible-search-results-container"></div>';
    $html .= '</form>';
    $html .= '</div>';
    
    return $html;
} 