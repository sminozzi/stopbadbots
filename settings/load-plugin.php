<?php
/**
 * Configuration and loading.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $stopbadbots_settings_config;

// add_action( 'plugins_loaded', 'stopbadbots_localization_init' );
//  add_action( 'plugins_loaded', 'stopbadbots_localization_init' );


$stopbadbots_settings_config = array();

/**
* Base Directory Setting
*/
$stopbadbots_settings_config['base_dir'] = __DIR__ . '/';

/**
* Base URI Settings
* Use WordPress' plugins_url to set this if not a theme.
*/
$stopbadbots_settings_config['base_uri'] = plugins_url( 'settings', dirname( __FILE__ ) ) . '/';

/**
* Requred Classes and Libraries
*/
// get_template_part($stopbadbots_settings_config['base_dir'] . 'containers');
// get_template_part($stopbadbots_settings_config['base_dir'] . 'fields');
// get_template_part($stopbadbots_settings_config['base_dir'] . 'factories');
// get_template_part($stopbadbots_settings_config['base_dir'] . 'page-builders');

require_once plugin_dir_path( __FILE__ ) . 'containers.php';
require_once plugin_dir_path( __FILE__ ) . 'fields.php';
require_once plugin_dir_path( __FILE__ ) . 'factories.php';
require_once plugin_dir_path( __FILE__ ) . 'page-builders.php';

// get_template_part('settings/containers');
// get_template_part('settings/fields');
// get_template_part('settings/factories');
// get_template_part('settings/page-builders');
