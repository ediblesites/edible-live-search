<?php
/**
 * Analytics functions for Edible Live Search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Log search query to analytics table
 */
function edible_live_search_log_query($search_term, $results_count) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'edible_search_analytics';
    
    $data = array(
        'search_term' => $search_term,
        'results_count' => $results_count,
        'search_timestamp' => current_time('mysql')
    );
    
    $wpdb->insert($table_name, $data);
    
    return $wpdb->insert_id;
}

/**
 * Get analytics data
 */
function edible_live_search_get_analytics($args = array()) {
    global $wpdb;
    
    $defaults = array(
        'days' => 30,
        'limit' => 20,
        'type' => 'popular' // popular, failed, volume
    );
    
    $args = wp_parse_args($args, $defaults);
    $table_name = $wpdb->prefix . 'edible_search_analytics';
    
    switch ($args['type']) {
        case 'popular':
            return edible_live_search_get_popular_searches($args);
        case 'failed':
            return edible_live_search_get_failed_searches($args);
        case 'volume':
            return edible_live_search_get_search_volume($args);
        default:
            return array();
    }
}

/**
 * Get popular searches
 */
function edible_live_search_get_popular_searches($args) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'edible_search_analytics';
    $days = intval($args['days']);
    $limit = intval($args['limit']);
    
    $sql = $wpdb->prepare("
        SELECT search_term, COUNT(*) as search_count, AVG(results_count) as avg_results
        FROM $table_name
        WHERE search_timestamp >= DATE_SUB(NOW(), INTERVAL %d DAY)
        GROUP BY search_term
        ORDER BY search_count DESC
        LIMIT %d
    ", $days, $limit);
    
    return $wpdb->get_results($sql);
}

/**
 * Get failed searches (zero results)
 */
function edible_live_search_get_failed_searches($args) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'edible_search_analytics';
    $days = intval($args['days']);
    $limit = intval($args['limit']);
    
    $sql = $wpdb->prepare("
        SELECT search_term, COUNT(*) as search_count
        FROM $table_name
        WHERE search_timestamp >= DATE_SUB(NOW(), INTERVAL %d DAY)
        AND results_count = 0
        GROUP BY search_term
        ORDER BY search_count DESC
        LIMIT %d
    ", $days, $limit);
    
    return $wpdb->get_results($sql);
}

/**
 * Get search volume statistics
 */
function edible_live_search_get_search_volume($args) {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'edible_search_analytics';
    $days = intval($args['days']);
    
    $sql = $wpdb->prepare("
        SELECT 
            DATE(search_timestamp) as search_date,
            COUNT(*) as total_searches,
            COUNT(CASE WHEN results_count > 0 THEN 1 END) as successful_searches,
            COUNT(CASE WHEN results_count = 0 THEN 1 END) as failed_searches
        FROM $table_name
        WHERE search_timestamp >= DATE_SUB(NOW(), INTERVAL %d DAY)
        GROUP BY DATE(search_timestamp)
        ORDER BY search_date DESC
    ", $days);
    
    return $wpdb->get_results($sql);
}

/**
 * Export analytics data to CSV
 */
function edible_live_search_export_analytics($args = array()) {
    global $wpdb;
    
    $defaults = array(
        'days' => 30,
        'format' => 'csv'
    );
    
    $args = wp_parse_args($args, $defaults);
    $table_name = $wpdb->prefix . 'edible_search_analytics';
    $days = intval($args['days']);
    
    $sql = $wpdb->prepare("
        SELECT search_term, results_count, search_timestamp
        FROM $table_name
        WHERE search_timestamp >= DATE_SUB(NOW(), INTERVAL %d DAY)
        ORDER BY search_timestamp DESC
    ", $days);
    
    $results = $wpdb->get_results($sql);
    
    if ($args['format'] === 'csv') {
        return edible_live_search_generate_csv($results);
    }
    
    return $results;
}

/**
 * Generate CSV from analytics data
 */
function edible_live_search_generate_csv($data) {
    $filename = 'edible-search-analytics-' . date('Y-m-d') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Add headers (removed user_ip)
    fputcsv($output, array('Search Term', 'Results Count', 'Timestamp'));
    
    // Add data (removed user_ip)
    foreach ($data as $row) {
        fputcsv($output, array(
            $row->search_term,
            $row->results_count,
            $row->search_timestamp
        ));
    }
    
    fclose($output);
    exit;
}

/**
 * Clean up old analytics data
 */
function edible_live_search_cleanup_analytics() {
    global $wpdb;
    
    $retention_days = edible_live_search_get_option('data_retention_days', 90);
    $table_name = $wpdb->prefix . 'edible_search_analytics';
    
    $sql = $wpdb->prepare("
        DELETE FROM $table_name
        WHERE search_timestamp < DATE_SUB(NOW(), INTERVAL %d DAY)
    ", $retention_days);
    
    return $wpdb->query($sql);
}

/**
 * Get analytics summary
 */
function edible_live_search_get_analytics_summary() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'edible_search_analytics';
    
    $summary = array();
    
    // Total searches today
    $sql = "SELECT COUNT(*) as count FROM $table_name WHERE DATE(search_timestamp) = CURDATE()";
    $summary['today'] = $wpdb->get_var($sql);
    
    // Total searches this week
    $sql = "SELECT COUNT(*) as count FROM $table_name WHERE YEARWEEK(search_timestamp) = YEARWEEK(NOW())";
    $summary['this_week'] = $wpdb->get_var($sql);
    
    // Total searches this month
    $sql = "SELECT COUNT(*) as count FROM $table_name WHERE YEAR(search_timestamp) = YEAR(NOW()) AND MONTH(search_timestamp) = MONTH(NOW())";
    $summary['this_month'] = $wpdb->get_var($sql);
    
    // Failed searches today
    $sql = "SELECT COUNT(*) as count FROM $table_name WHERE DATE(search_timestamp) = CURDATE() AND results_count = 0";
    $summary['failed_today'] = $wpdb->get_var($sql);
    
    // Success rate today
    if ($summary['today'] > 0) {
        $summary['success_rate'] = round((($summary['today'] - $summary['failed_today']) / $summary['today']) * 100, 2);
    } else {
        $summary['success_rate'] = 0;
    }
    
    return $summary;
}

/**
 * Schedule analytics cleanup
 */
function edible_live_search_schedule_cleanup() {
    if (!wp_next_scheduled('edible_live_search_cleanup')) {
        wp_schedule_event(time(), 'daily', 'edible_live_search_cleanup');
    }
}
add_action('wp', 'edible_live_search_schedule_cleanup');

/**
 * Handle scheduled cleanup
 */
function edible_live_search_handle_cleanup() {
    edible_live_search_cleanup_analytics();
}
add_action('edible_live_search_cleanup', 'edible_live_search_handle_cleanup'); 