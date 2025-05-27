<?php
if (!defined("ABSPATH")) {
    exit();
} // Exit if accessed directly


if (!function_exists('wptools_enqueue_scripts_with_nonce')) {
    function wptools_enqueue_scripts_with_nonce()
    {
        // Enfileirar seu script no frontend
        wp_enqueue_script('wptools-loading-time-admin-js', plugin_dir_url(__FILE__) . 'loading-time.js', array('jquery'), null, true);

        // $nonce = wp_create_nonce('wptools-add-loading-info');
        $nonce = substr(NONCE_SALT, 0, 10);
        wp_localize_script('wptools-loading-time-admin-js', 'wptools_ajax_object', array('ajax_nonce' => $nonce));
        do_action('wptools_enqueue_additional_scripts');
    }
}
add_action('wp_enqueue_scripts', 'wptools_enqueue_scripts_with_nonce');

/*
if (!function_exists('wptools_enqueue_admin_scripts_with_nonce')) {
    function wptools_enqueue_admin_scripts_with_nonce()
    {
        wp_enqueue_script('wptools-loading-time-admin-js', plugin_dir_url(__FILE__) . 'loading-time.js', array('jquery'), null, true);

        // $nonce = wp_create_nonce('wptools-add-loading-info-admin');
        $nonce = substr(NONCE_SALT, 0, 10);
        wp_localize_script('wptools-loading-time-admin-js', 'wptools_ajax_object', array('ajax_nonce' => $nonce));
        do_action('wptools_enqueue_additional_admin_scripts');
    }
}
add_action('admin_enqueue_scripts', 'wptools_enqueue_admin_scripts_with_nonce');
*/
//
//
//
//
//


// Function to register loading time in the database
if (!function_exists('wptools_register_loading_time')) {
    function wptools_register_loading_time()
    {
        global $wpdb;
        // Verify nonce
        $nonce = sanitize_text_field($_POST['nonce']);
        if (!$nonce === substr(NONCE_SALT, 0, 10)) {
            wp_send_json_error('Invalid nonce.');
            wp_die();
        }
        if (
            isset($_POST['page_url'])
            && isset($_POST['loading_time'])
        ) {
            $page_url = sanitize_text_field($_POST['page_url']);
            $loading_time = sanitize_text_field($_POST['loading_time']);

            $table_name = $wpdb->prefix . 'wptools_page_load_times';
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $charset_collate = $wpdb->get_charset_collate();
                $sql = "CREATE TABLE $table_name (
id INT PRIMARY KEY AUTO_INCREMENT,
page_url VARCHAR(255) NOT NULL,
load_time FLOAT NOT NULL,
timestamp DATETIME NOT NULL
) $charset_collate;";
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
            $data = array(
                'page_url' => $page_url,
                'load_time' => $loading_time,
                'timestamp' => current_time('mysql', 1) // Usa o horário atual do WordPress
            );
            $wpdb->insert($table_name, $data);
            wp_send_json_success('Success'); // You can send any desired success response
        } else {
            wp_send_json_error('Invalid or missing data.');
        }
        wp_die('ok'); // End the execution of the WordPress AJAX script
    }
}
// Register the AJAX action in WordPress
add_action('wp_ajax_wptools_register_loading_time', 'wptools_register_loading_time');
add_action('wp_ajax_nopriv_wptools_register_loading_time', 'wptools_register_loading_time');
