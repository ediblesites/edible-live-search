<?php
/**
 * Admin functions for Edible Live Search
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize admin functionality
 */
function edible_live_search_admin_init() {
    // Add admin menu
    add_action('admin_menu', 'edible_live_search_admin_menu');
    
    // Enqueue admin scripts and styles
    add_action('admin_enqueue_scripts', 'edible_live_search_admin_enqueue_scripts');
}

/**
 * Add admin menu
 */
function edible_live_search_admin_menu() {
    add_submenu_page(
        'options-general.php',
        __('Live Search', 'edible-live-search'),
        __('Live Search', 'edible-live-search'),
        'manage_options',
        'edible-live-search',
        'edible_live_search_settings_page'
    );
    
    add_submenu_page(
        'options-general.php',
        __('Live Search Analytics', 'edible-live-search'),
        __('Live Search Analytics', 'edible-live-search'),
        'manage_options',
        'edible-live-search-analytics',
        'edible_live_search_analytics_page'
    );
}

/**
 * Settings page callback
 */
function edible_live_search_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Handle form submission
    if (isset($_POST['submit'])) {
        check_admin_referer('edible_live_search_options');
        edible_live_search_save_settings();
    }
    
    $options = edible_live_search_get_options();
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <h2 class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active"><?php _e('General', 'edible-live-search'); ?></a>
            <a href="#appearance" class="nav-tab"><?php _e('Appearance', 'edible-live-search'); ?></a>
            <a href="#analytics" class="nav-tab"><?php _e('Analytics', 'edible-live-search'); ?></a>
            <a href="#advanced" class="nav-tab"><?php _e('Advanced', 'edible-live-search'); ?></a>
        </h2>
        
        <form method="post" action="">
            <?php wp_nonce_field('edible_live_search_options'); ?>
            
            <div id="general" class="tab-content">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Placeholder Text', 'edible-live-search'); ?></th>
                        <td>
                            <input type="text" name="edible_live_search_options[placeholder]" value="<?php echo esc_attr($options['placeholder']); ?>" class="regular-text">
                            <p class="description"><?php _e('Default placeholder text for the search input field', 'edible-live-search'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Post Types', 'edible-live-search'); ?></th>
                        <td>
                            <?php
                            $available_post_types = edible_live_search_get_available_post_types();
                            $selected_post_types = $options['post_types'];
                            
                            foreach ($available_post_types as $post_type => $label) {
                                $checked = in_array($post_type, $selected_post_types) ? 'checked' : '';
                                echo '<label><input type="checkbox" name="edible_live_search_options[post_types][]" value="' . esc_attr($post_type) . '" ' . $checked . '> ' . esc_html($label) . '</label><br>';
                            }
                            ?>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Results Limit', 'edible-live-search'); ?></th>
                        <td>
                            <input type="number" name="edible_live_search_options[results_limit]" value="<?php echo esc_attr($options['results_limit']); ?>" min="1" max="20">
                            <p class="description"><?php _e('Maximum number of results to display (1-20)', 'edible-live-search'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Minimum Characters', 'edible-live-search'); ?></th>
                        <td>
                            <input type="number" name="edible_live_search_options[min_characters]" value="<?php echo esc_attr($options['min_characters']); ?>" min="1" max="10">
                            <p class="description"><?php _e('Minimum characters before search triggers', 'edible-live-search'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Search Delay (ms)', 'edible-live-search'); ?></th>
                        <td>
                            <input type="number" name="edible_live_search_options[search_delay]" value="<?php echo esc_attr($options['search_delay']); ?>" min="100" max="2000" step="100">
                            <p class="description"><?php _e('Delay after typing stops before search executes', 'edible-live-search'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Search Fields', 'edible-live-search'); ?></th>
                        <td>
                            <?php
                            $search_fields = edible_live_search_get_search_fields();
                            $selected_fields = $options['search_fields'];
                            
                            foreach ($search_fields as $field => $label) {
                                $checked = in_array($field, $selected_fields) ? 'checked' : '';
                                echo '<label><input type="checkbox" name="edible_live_search_options[search_fields][]" value="' . esc_attr($field) . '" ' . $checked . '> ' . esc_html($label) . '</label><br>';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="appearance" class="tab-content" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Fallback Image', 'edible-live-search'); ?></th>
                        <td>
                            <input type="number" name="edible_live_search_options[fallback_image_id]" value="<?php echo esc_attr($options['fallback_image_id']); ?>" min="0">
                            <p class="description"><?php _e('Media ID for fallback image when no thumbnail is available', 'edible-live-search'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Custom CSS', 'edible-live-search'); ?></th>
                        <td>
                            <textarea name="edible_live_search_options[custom_css]" rows="10" cols="50"><?php echo esc_textarea($options['custom_css'] ?? ''); ?></textarea>
                            <p class="description"><?php _e('Custom CSS to override default styles', 'edible-live-search'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="analytics" class="tab-content" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Enable Analytics', 'edible-live-search'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="edible_live_search_options[enable_analytics]" value="1" <?php checked($options['enable_analytics'], true); ?>>
                                <?php _e('Track search queries and results', 'edible-live-search'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Data Retention (days)', 'edible-live-search'); ?></th>
                        <td>
                            <input type="number" name="edible_live_search_options[data_retention_days]" value="<?php echo esc_attr($options['data_retention_days']); ?>" min="30" max="365">
                            <p class="description"><?php _e('How long to keep analytics data (30-365 days)', 'edible-live-search'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="advanced" class="tab-content" style="display: none;">
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php _e('Enable Caching', 'edible-live-search'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="edible_live_search_options[enable_caching]" value="1" <?php checked($options['enable_caching'], true); ?>>
                                <?php _e('Cache search results for better performance', 'edible-live-search'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><?php _e('Cache Duration (seconds)', 'edible-live-search'); ?></th>
                        <td>
                            <input type="number" name="edible_live_search_options[cache_duration]" value="<?php echo esc_attr($options['cache_duration']); ?>" min="300" max="86400">
                            <p class="description"><?php _e('How long to cache results (300-86400 seconds)', 'edible-live-search'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <?php submit_button(); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('.nav-tab').click(function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tab-content').hide();
            $(target).show();
        });
    });
    </script>
    <?php
}

/**
 * Analytics page callback
 */
function edible_live_search_analytics_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $summary = edible_live_search_get_analytics_summary();
    $popular_searches = edible_live_search_get_analytics(array('type' => 'popular', 'days' => 30, 'limit' => 10));
    $failed_searches = edible_live_search_get_analytics(array('type' => 'failed', 'days' => 30, 'limit' => 10));
    ?>
    
    <div class="wrap">
        <h1><?php _e('Search Analytics', 'edible-live-search'); ?></h1>
        
        <div class="edible-analytics-summary">
            <div class="analytics-card">
                <h3><?php _e('Today', 'edible-live-search'); ?></h3>
                <div class="analytics-number"><?php echo esc_html($summary['today']); ?></div>
                <p><?php _e('Total Searches', 'edible-live-search'); ?></p>
            </div>
            
            <div class="analytics-card">
                <h3><?php _e('This Week', 'edible-live-search'); ?></h3>
                <div class="analytics-number"><?php echo esc_html($summary['this_week']); ?></div>
                <p><?php _e('Total Searches', 'edible-live-search'); ?></p>
            </div>
            
            <div class="analytics-card">
                <h3><?php _e('Success Rate', 'edible-live-search'); ?></h3>
                <div class="analytics-number"><?php echo esc_html($summary['success_rate']); ?>%</div>
                <p><?php _e('Today', 'edible-live-search'); ?></p>
            </div>
        </div>
        
        <div class="edible-analytics-details">
            <div class="analytics-column">
                <h2><?php _e('Popular Searches (Last 30 Days)', 'edible-live-search'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Search Term', 'edible-live-search'); ?></th>
                            <th><?php _e('Count', 'edible-live-search'); ?></th>
                            <th><?php _e('Avg Results', 'edible-live-search'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($popular_searches as $search) : ?>
                        <tr>
                            <td><?php echo esc_html($search->search_term); ?></td>
                            <td><?php echo esc_html($search->search_count); ?></td>
                            <td><?php echo esc_html(round($search->avg_results, 1)); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="analytics-column">
                <h2><?php _e('Failed Searches (Last 30 Days)', 'edible-live-search'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Search Term', 'edible-live-search'); ?></th>
                            <th><?php _e('Count', 'edible-live-search'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($failed_searches as $search) : ?>
                        <tr>
                            <td><?php echo esc_html($search->search_term); ?></td>
                            <td><?php echo esc_html($search->search_count); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="edible-analytics-actions">
            <a href="<?php echo admin_url('admin-ajax.php?action=edible_live_search_export&nonce=' . wp_create_nonce('edible_export')); ?>" class="button button-secondary">
                <?php _e('Export CSV', 'edible-live-search'); ?>
            </a>
        </div>
    </div>
    
    <style>
    .edible-analytics-summary {
        display: flex;
        gap: 20px;
        margin: 20px 0;
    }
    
    .analytics-card {
        background: #fff;
        border: 1px solid #ddd;
        padding: 20px;
        text-align: center;
        flex: 1;
    }
    
    .analytics-number {
        font-size: 2em;
        font-weight: bold;
        color: #0073aa;
    }
    
    .edible-analytics-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin: 20px 0;
    }
    
    .analytics-column {
        background: #fff;
        border: 1px solid #ddd;
        padding: 20px;
    }
    </style>
    <?php
}

/**
 * Save settings
 */
function edible_live_search_save_settings() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $options = edible_live_search_get_options();
    $new_options = $_POST['edible_live_search_options'] ?? array();
    
    // Sanitize and validate options
    $sanitized_options = array();
    
    // Placeholder
    $sanitized_options['placeholder'] = isset($new_options['placeholder']) ? sanitize_text_field($new_options['placeholder']) : __('Search...', 'edible-live-search');
    
    // Post types
    $sanitized_options['post_types'] = isset($new_options['post_types']) ? array_map('sanitize_text_field', $new_options['post_types']) : array('post', 'page');
    
    // Results limit
    $sanitized_options['results_limit'] = isset($new_options['results_limit']) ? max(1, min(20, intval($new_options['results_limit']))) : 5;
    
    // Minimum characters
    $sanitized_options['min_characters'] = isset($new_options['min_characters']) ? max(1, min(10, intval($new_options['min_characters']))) : 3;
    
    // Search delay
    $sanitized_options['search_delay'] = isset($new_options['search_delay']) ? max(100, min(2000, intval($new_options['search_delay']))) : 300;
    
    // Search fields
    $sanitized_options['search_fields'] = isset($new_options['search_fields']) ? array_map('sanitize_text_field', $new_options['search_fields']) : array('title', 'content');
    
    // Fallback image
    $sanitized_options['fallback_image_id'] = isset($new_options['fallback_image_id']) ? max(0, intval($new_options['fallback_image_id'])) : 0;
    
    // Custom CSS
    $sanitized_options['custom_css'] = isset($new_options['custom_css']) ? sanitize_textarea_field($new_options['custom_css']) : '';
    
    // Analytics
    $sanitized_options['enable_analytics'] = isset($new_options['enable_analytics']);
    $sanitized_options['data_retention_days'] = isset($new_options['data_retention_days']) ? max(30, min(365, intval($new_options['data_retention_days']))) : 90;
    
    // Advanced
    $sanitized_options['enable_caching'] = isset($new_options['enable_caching']);
    $sanitized_options['cache_duration'] = isset($new_options['cache_duration']) ? max(300, min(86400, intval($new_options['cache_duration']))) : 3600;
    
    // Merge with existing options
    $final_options = wp_parse_args($sanitized_options, $options);
    
    // Save options
    update_option('edible_live_search_options', $final_options);
    
    add_settings_error(
        'edible_live_search_messages',
        'edible_live_search_message',
        __('Settings saved successfully!', 'edible-live-search'),
        'updated'
    );
}

/**
 * Enqueue admin scripts and styles
 */
function edible_live_search_admin_enqueue_scripts($hook) {
    if (strpos($hook, 'edible-live-search') === false) {
        return;
    }
    
    wp_enqueue_style(
        'edible-live-search-admin',
        EDIBLE_LIVE_SEARCH_PLUGIN_URL . 'admin/css/admin-style.css',
        array(),
        EDIBLE_LIVE_SEARCH_VERSION
    );
    
    wp_enqueue_script(
        'edible-live-search-admin',
        EDIBLE_LIVE_SEARCH_PLUGIN_URL . 'admin/js/admin-script.js',
        array('jquery'),
        EDIBLE_LIVE_SEARCH_VERSION,
        true
    );
} 