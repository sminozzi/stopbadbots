<?php

/**
 * @author    William Sergio Minozzi
 * @copyright 2016 -2024
 */
/*  If uninstall is not called from WordPress, exit */
if (! defined('WP_UNINSTALL_PLUGIN')) {
  exit();
}
$stopbadbots_option_name[] = 'stop_bad_bots_ip_active';
$stopbadbots_option_name[] = 'stop_bad_bots_referer_active';
$stopbadbots_option_name[] = 'stopbadbots_firewall';
$stopbadbots_option_name[] = 'stopbadbots_my_blacklist';
$stopbadbots_option_name[] = 'stopbadbots_my_email_to';
$stopbadbots_option_name[] = 'stopbadbots_my_radio_report_all_visits';
$stopbadbots_option_name[] = 'stopbadbots_version';
$stopbadbots_option_name[] = 'stopbadbots_per_page';
$stopbadbots_option_name[] = 'stop_bad_bots_network';
$stopbadbots_option_name[] = 'stopbadbots_last_checked';
$stopbadbots_option_name[] = 'stop_bad_bots_blank_ua';
$stopbadbots_option_name[] = 'stopbadbots_block_pingbackrequest';
$stopbadbots_option_name[] = 'stopbadbots_block_enumeration';
$stopbadbots_option_name[] = 'stopbadbots_block_false_google';
$stopbadbots_option_name[] = 'stopbadbots_block_spam_comments';
$stopbadbots_option_name[] = 'stopbadbots_block_spam_contacts';
$stopbadbots_option_name[] = 'stopbadbots_block_spam_login';
$stopbadbots_option_name[] = 'stop_bad_bots_autoupdate';
$stopbadbots_option_name[] = 'stopbadbots_enable_whitelist';
$stopbadbots_option_name[] = 'stopbadbots_block_http_tools';
$stopbadbots_option_name[] = 'stopbadbots_limit visits';
$stopbadbots_option_name[] = 'stopbadbots_string_whitelist';
$stopbadbots_option_name[] = 'astopbadbots_ip_whitelist';
$stopbadbots_option_name[] = 'stopbadbots_rate_limiting';
$stopbadbots_option_name[] = 'stopbadbots_rate_limiting_day';
$stopbadbots_option_name[] = 'stopbadbots_rate_penalty';
$stopbadbots_option_name[] = 'stop_bad_bots_autoupdate';

$stopbadbots_option_name[] = 'stopbadbots_http_tools';
$stopbadbots_option_name[] = 'stopbadbots_rate404_limiting';
$stopbadbots_option_name[] = 'stopbadbots_install_anti_hacker';

$stopbadbots_option_name[] = 'stopbadbots_keep_log';

$stopbadbots_option_name[] = 'stop_bad_bots_last_feedback';

$stopbadbots_option_name[] = 'stopbadbots_optin';

$stopbadbots_option_name[] = 'stopbadbots_update_http_tools';
$stopbadbots_option_name[] = 'stopbadbots_notif_level';
$stopbadbots_option_name[] = 'stopbadbots_install_anti_hacker';
$stopbadbots_option_name[] = 'stopbadbots_install_recaptcha';
$stopbadbots_option_name[] = 'stopbadbots_block_china';
$stopbadbots_option_name[] = 'stopbadbots_engine_option';
$stopbadbots_option_name[] = 'stopbadbots_installed';
$stopbadbots_option_name[] = 'stopbadbots_tables_empty';
$stopbadbots_option_name[] = 'stopbadbots_activation_date';
$stopbadbots_option_name[] = 'stopbadbots_was_activated';
$stopbadbots_option_name[] = 'sbb_javascript_sent_error';
$stopbadbots_option_name[] = 'sbb_javascript_error';
$stopbadbots_option_name[] = 'stopbadbots_last_notification_date';
$stopbadbots_option_name[] = 'stopbadbots_last_notification_date2';
$stopbadbots_option_name[] = 'bill_pre_checkup_finished';
$stopbadbots_option_name[] = 'bill_pre_checkup_dismissed';


for ($i = 0; $i < count($stopbadbots_option_name); $i++) {
  delete_option($stopbadbots_option_name[$i]);
  // For site options in Multisite
  delete_site_option($stopbadbots_option_name[$i]);
}
// Drop a custom db table
global $wpdb;
/*
$current_table = $wpdb->prefix . 'sbb_blacklist';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
$current_table = $wpdb->prefix . 'sbb_badips';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
$current_table = $wpdb->prefix . 'sbb_stats';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
$current_table = $wpdb->prefix . 'sbb_badref';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );

$current_table = $wpdb->prefix . 'sbb_visitorslog';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
$current_table = $wpdb->prefix . 'sbb_http_tools';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
$current_table = $wpdb->prefix . 'sbb_fingerprint';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
*/

$prefix = $wpdb->prefix;

$tables = array(
  'sbb_blacklist',
  'sbb_badips',
  'sbb_stats',
  'sbb_badref',
  'sbb_visitorslog',
  'sbb_http_tools',
  'sbb_fingerprint',
  'wptools_page_load_times',
  'bill_catch_some_bots',
  'stopbadbots_fail2ban_logs'
);



foreach ($tables as $table) {
  $current_table = $wpdb->prepare("DROP TABLE IF EXISTS %s", $prefix . $table);
  $wpdb->query($current_table);
}


wp_clear_scheduled_hook('stopbadbots_cron_hook');

$plugin_name = 'bill-catch-errors.php'; // Name of the plugin file to be removed

// Retrieve all must-use plugins
$wp_mu_plugins = get_mu_plugins();

// MU-Plugins directory
$mu_plugins_dir = WPMU_PLUGIN_DIR;

if (isset($wp_mu_plugins[$plugin_name])) {
  // Get the plugin's destination path
  $destination = $mu_plugins_dir . '/' . $plugin_name;

  // Attempt to remove the plugin
  if (!unlink($destination)) {
    // Log the error if the file could not be deleted
    error_log("Error removing the plugin file from the MU-Plugins directory: $destination");
  } else {
    // Optionally, log success if the plugin is removed successfully
    // error_log("Successfully removed the plugin file: $destination");
  }
}
