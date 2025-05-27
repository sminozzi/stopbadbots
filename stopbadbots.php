<?php /*
Plugin Name: StopBadBots
Plugin URI: http://stopbadbots.com
Description: Stop Bad Bots, SPAM bots and spiders. No DNS or Cloud Traffic Redirection. No Slow Down Your Site!
Version: 11.30
Text Domain: stopbadbots
Domain Path: /language
Author: Bill Minozzi
Author URI: http://stopbadbots.com
License:     GPL2
Copyright (c) 2016 / 2023 Bill Minozzi
Stopbadbots is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
StopBadBots_optin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with StopBadBots_optin. If not, see {License URI}.
Permission is hereby granted, free of charge subject to the following conditions:
The above copyright notice and this FULL permission notice shall be included in
all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.
 */
if (!defined('ABSPATH')) {
	exit;
}

$bill_debug = false;
// $bill_debug = true;

/*
function stopbadbots_clear_scheduled_hook_antihacker() {
    // Remove o evento agendado 'antihacker_cron_event_plugins_scan'
    wp_clear_scheduled_hook('antihacker_cron_event_plugins_scan');
}
*/
// Debug//
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
// ob_start();
// Fix memory
$stopbadbots_maxMemory = @ini_get('memory_limit');
$stopbadbots_last      = strtolower(substr($stopbadbots_maxMemory, -1));
$stopbadbots_maxMemory = (int) $stopbadbots_maxMemory;
if ($stopbadbots_last == 'g') {
	$stopbadbots_maxMemory = $stopbadbots_maxMemory * 1024 * 1024 * 1024;
} elseif ($stopbadbots_last == 'm') {
	$stopbadbots_maxMemory = $stopbadbots_maxMemory * 1024 * 1024;
} elseif ($stopbadbots_last == 'k') {
	$stopbadbots_maxMemory = $stopbadbots_maxMemory * 1024;
}





//if ( $stopbadbots_maxMemory < 134217728 /* 128 MB */ 
//&& $stopbadbots_maxMemory > 0 ) {
//	if ( strpos( ini_get( 'disable_functions' ), 'ini_set' ) === false ) {
//		@ini_set( 'memory_limit', '128M' );
//	}
//}
/*
if ( null !== ini_get( 'max_execution_time' ) ) {
	if ( ini_get( 'max_execution_time' ) < 60 ) {
		ini_set( 'max_execution_time', 60 );
	}
}
*/

global $wpdb;

$stopbadbot_plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
define('STOPBADBOTSVERSION', $stopbadbot_plugin_data['Version']);

define('STOPBADBOTSPATH', plugin_dir_path(__file__));
define('STOPBADBOTSURL', plugin_dir_url(__file__));
define('STOPBADBOTSDOMAIN', get_site_url());
define('STOPBADBOTSIMAGES', plugin_dir_url(__file__) . 'assets/images');

define('STOPBADBOTSPAGE', trim(sanitize_text_field($GLOBALS['pagenow'])));

define('STOPBADBOTS_CHROME', '108'); // 131.0.6723.58
define('STOPBADBOTS_FIREFOX', '108'); // 122
define('STOPBADBOTS_EDGE', '110'); // 131

define('STOPBADBOTSPATHLANGUAGE', dirname(plugin_basename(__FILE__)) . '/language/');

if (!defined('STOPBADBOTSHOMEURL')) {
	define('STOPBADBOTSHOMEURL', admin_url());
}

$stopbadbots_is_admin = stopbadbots_check_wordpress_logged_in_cookie();

require_once ABSPATH . 'wp-includes/pluggable.php';

/*
if($stopbadbots_is_admin)
  add_action( 'plugins_loaded', 'stopbadbots_localization_init' );
// */

if ($stopbadbots_is_admin) {


	//	require_once STOPBADBOTSPATH . "functions/fail2ban.php";




	// Reset activation...

	/*
    $stopbadbots_activation_date = get_option('stopbadbots_activation_date');

	if ($stopbadbots_activation_date) {
		$stopbadbots_activation_date = date('Y-m-d', $stopbadbots_activation_date);
		$today = date('Y-m-d');

		if ($stopbadbots_activation_date !== $today) {
			 if(!update_option( 'stopbadbots_was_activated', '0' ))
               add_option( 'stopbadbots_was_activated', '0' );
		}

	}
	*/

	//Function _load_textdomain_just_in_time was called incorrectly. 
	//Translation loading for the wptools domain was triggered too early. 
	//This is usually an indicator for some code in the plugin or theme running too early.
	// Translations should be loaded at the init action or later. 
	// Please see Debugging in WordPress for more information. 
	// (This message was added in version 6.7.0.)


	//add_action('plugins_loaded', 'stopbadbots_localization_init');
	//add_action('init', 'stopbadbots_localization_init');


	//if (isset($_GET['page']) && $_GET['page'] === 'settings-stop-bad-bots') {
	// Ação a ser executada apenas na página específica
	//	add_action('plugins_loaded', 'stopbadbots_localization_init');
	//} else {
	add_action('init', 'stopbadbots_localization_init');
	//}
}
//
//
//
//
//
//


//$stopbadbotsserver = sanitize_text_field( $_SERVER['SERVER_NAME'] );

$stopbadbots_request_url = sanitize_text_field($_SERVER['REQUEST_URI']);

//	`$stopbadbots_method  = sanitize_text_field( $_SERVER['REQUEST_METHOD'] );


if (isset($_SERVER['SERVER_NAME']))
	$stopbadbotsserver = sanitize_text_field($_SERVER['SERVER_NAME']);
else
	$stopbadbotsserver = sanitize_text_field(get_bloginfo('url'));




if (isset($_SERVER['REQUEST_METHOD']))
	$stopbadbots_method  = sanitize_text_field($_SERVER['REQUEST_METHOD']);
else
	$stopbadbots_method  = 'GET';


$stopbadbots_referer = stopbadbots_get_referer();

$stopbadbots_version           = trim(sanitize_text_field(get_site_option('stopbadbots_version', '')));
$stopbadbots_string_whitelist  = trim(sanitize_text_field(get_site_option('stopbadbots_string_whitelist', '')));
$astopbadbots_string_whitelist = explode(' ', $stopbadbots_string_whitelist);
$stopbadbots_ip_whitelist      = trim(sanitize_text_field(get_site_option('stopbadbots_ip_whitelist', '')));
$astopbadbots_ip_whitelist     = explode(' ', $stopbadbots_ip_whitelist);

// update_option('stopbadbots_notif_level', time());
$stopbadbots_notif_level = trim(sanitize_text_field(get_site_option('stopbadbots_notif_level', '0')));


$stopbadbots_tables_empty  = sanitize_text_field(get_option('stopbadbots_tables_empty', 'yes'));

$stopbadbots_firewall = sanitize_text_field(get_option('stopbadbots_firewall', 'yes'));

$stopbadbots_is_admin = stopbadbots_check_wordpress_logged_in_cookie();



//$stop_bad_bots_automatic_updates = sanitize_text_field( get_option( 'stop_bad_bots_automatic_updates', 'yes' ) );



if (!function_exists('wp_get_current_user')) {
	include_once ABSPATH . 'wp-includes/pluggable.php';
}

if ($stopbadbots_is_admin) {
	if (strpos($stopbadbots_request_url, 'page=jetpack')) {
		return;
	}
}


add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'stopbadbots_add_action_links');
function stopbadbots_add_action_links($links)
{
	$mylinks = array(
		'<a href="' . admin_url('admin.php?page=settings-stop-bad-bots') . '">Settings</a>',
	);
	return array_merge($links, $mylinks);
}



/* Begin Language */
if ($stopbadbots_is_admin) {
	function stopbadbots_stopbadbots_localization_init_fail()
	{

		if (get_option('stopbadbots_dismiss_language') == '1')
			return;


		echo '<div id="stopbadbots_an2"  class="notice notice-warning is-dismissible">';
		echo '<br />';
		echo esc_attr__('Stop Bad Bots: Could not load the localization file (Language file)', 'stopbadbots');
		echo '.<br />';
		echo 'Please, contact me at our Support Page to translate it on your language.';
		echo '.<br /><br /></div>';
	}
}
// stopbadbots_dismissible_notice2
function stopbadbots_dismissible_notice2()
{
	$r = update_option('stopbadbots_dismiss_language', '1');
	if (!$r) {
		$r = add_option('stopbadbots_dismiss_language', '1');
	}
	if ($r)
		die('OK!!!!!');
	else
		die('NNNN');
}
add_action('wp_ajax_stopbadbots_dismissible_notice2', 'stopbadbots_dismissible_notice2');


//	add_action( 'plugins_loaded', 'stopbadbots_localization_init' );
/*
function stopbadbots_localization_init()
{

	$loaded = load_plugin_textdomain('stopbadbots', false, STOPBADBOTSPATHLANGUAGE);

	if (!$loaded and get_locale() <> 'en_US') {
		if (function_exists('stopbadbots_stopbadbots_localization_init_fail'))
			add_action('admin_notices', 'stopbadbots_stopbadbots_localization_init_fail');
	}
}
*/
//
//
function stopbadbots_localization_init()
{
	$path = STOPBADBOTSPATH . 'language/';
	$locale = apply_filters('plugin_locale', determine_locale(), 'stopbadbots');

	// Full path of the specific translation file (e.g., es_AR.mo)
	$specific_translation_path = $path . "stopbadbots-$locale.mo";
	$specific_translation_loaded = false;

	// Check if the specific translation file exists and try to load it
	if (file_exists($specific_translation_path)) {
		$specific_translation_loaded = load_textdomain('stopbadbots', $specific_translation_path);
	}

	// List of languages that should have a fallback to a specific locale
	$fallback_locales = [
		'de' => 'de_DE',  // German
		'fr' => 'fr_FR',  // French
		'it' => 'it_IT',  // Italian
		'es' => 'es_ES',  // Spanish
		'pt' => 'pt_BR',  // Portuguese (fallback to Brazil)
		'nl' => 'nl_NL'   // Dutch (fallback to Netherlands)
	];

	// If the specific translation was not loaded, try to fallback to the generic version
	if (!$specific_translation_loaded) {
		$language = explode('_', $locale)[0];  // Get only the language code, ignoring the country (e.g., es from es_AR)

		if (array_key_exists($language, $fallback_locales)) {
			// Full path of the generic fallback translation file (e.g., es_ES.mo)
			$fallback_translation_path = $path . "stopbadbots-{$fallback_locales[$language]}.mo";

			// Check if the fallback generic file exists and try to load it
			if (file_exists($fallback_translation_path)) {
				load_textdomain('stopbadbots', $fallback_translation_path);
			}
		}
	}

	// Load the plugin
	load_plugin_textdomain('stopbadbots', false, plugin_basename(STOPBADBOTSPATH) . '/language/');

	//Function _load_textdomain_just_in_time was called incorrectly. 
	//Translation loading for the wptools domain was triggered too early. 
	//This is usually an indicator for some code in the plugin or theme running too early.
	// Translations should be loaded at the init action or later. 
	// Please see Debugging in WordPress for more information. 
	// (This message was added in version 6.7.0.)

}





/* End language */


/*
//require_once STOPBADBOTSPATH . 'settings/load-plugin.php';

function stopbadbots_initialize_plugin_settings()
{
	// Inicialização do plugin.
	require_once STOPBADBOTSPATH . 'settings/load-plugin.php';
}
add_action('admin_menu', 'stopbadbots_initialize_plugin_settings', 150);
*/





$stopbadbots_block_spam_contacts = sanitize_text_field(get_option('stopbadbots_block_spam_contacts', 'no'));
$stopbadbots_block_spam_comments = sanitize_text_field(get_option('stopbadbots_block_spam_comments', 'no'));
$stopbadbots_block_spam_login    = sanitize_text_field(get_option('stopbadbots_block_spam_login', 'no'));
$stopbadbots_checkversion        = sanitize_text_field(get_option('stopbadbots_checkversion', ''));
$stopbadbots_checkversion        = trim($stopbadbots_checkversion);
$stopbadbots_rate_penalty        = sanitize_text_field(get_option('stopbadbots_rate_penalty', 'unlimited'));
$stopbadbots_block_http_tools    = sanitize_text_field(get_option('stopbadbots_block_http_tools', 'no'));
$stopbadbots_enable_whitelist    = sanitize_text_field(get_option('stopbadbots_enable_whitelist', 'no'));
$stopbadbots_limit_visits        = sanitize_text_field(get_option('stopbadbots_limit_visits', 'no'));
$stopbadbots_go_pro_hide    = sanitize_text_field(get_option('stopbadbots_go_pro_hide', ''));

$stopbadbots_rate404_limiting = sanitize_text_field(get_option('stopbadbots_rate404_limiting', 'unlimited'));

// $stopbadbots_install_anti_hacker = sanitize_text_field( get_option( 'stopbadbots_install_anti_hacker', '' ) );
$stopbadbots_keep_log = sanitize_text_field(get_option('stopbadbots_keep_log', '30'));

// die(var_dump($stopbadbots_keep_log));


$stopbadbots_update_http_tools = sanitize_text_field(get_option('stopbadbots_update_http_tools', 'no'));

$stopbadbots_install_anti_hacker = sanitize_text_field(get_option('stopbadbots_install_anti_hacker', 'no'));

$stopbadbots_install_recaptcha = sanitize_text_field(get_option('stopbadbots_install_recaptcha', 'no'));

$stopbadbots_block_china = sanitize_text_field(get_option('stopbadbots_block_china', 'no'));


// Report All
$stopbadbots_my_radio_report_all_visits = sanitize_text_field(get_option('stopbadbots_my_radio_report_all_visits', 'no'));
$stopbadbots_my_radio_report_all_visits = strtolower($stopbadbots_my_radio_report_all_visits);

$stopbadbots_engine_option = sanitize_text_field(get_option('stopbadbots_engine_option', 'conservative'));


$stopbadbots_bad_host = array(
	'1and1.com',
	'ALICOULD',
	'ALISOFT',
	'ALIBABA',
	'a2hosting.com',
	'ahrefs.com',
	'akamai.com',
	'akamai.net',
	'alittle client',
	'Amazon',
	'apple',
	'ARUBA-NET',
	'azure.com',
	'bluehost',
	'bluehost.com',
	'CHINANET',
	'clients.your-server.de',
	'cloudflare',
	'colocrossing',
	'contabo.com',
	'CONTABO',
	'digitalocean.com',
	'DIGITALOCEAN',
	'dreamhost',
	'dreamhost.com',
	'ExonHost',
	'fastly.com',
	'fastly.net',
	'Gandi',
	'GoDaddy',
	'Go-Daddy',
	'googleusercontent.com',
	'greengeeks.com',
	'heroku.com',
	'Hetzner',
	'hipl',
	'hosting',
	'hostgator.com',
	'HostHatch',
	'hosteurope.com',
	'hostinger.com',
	'hostpapa.com',
	'hostwinds.com',
	'hwclouds',
	'huaway',
	'HWCLOUDS',
	'ibm.com',
	'inmotionhosting.com',
	'Internap',
	'IONOS',
	'ipage.com',
	'ipfire.org',
	'justhost.com',
	'kimsufi.com',
	'LeaseWeb',
	'lightningbase.com',
	'Limestone',
	'LINODE',
	'linode.com',
	'Linode',
	'liquidweb.com',
	'MICROSOFT',
	'MSFT',
	'moonfruit.com',
	'namecheap.com',
	'Netsons',
	'oraclecloud.com',
	'OVH',
	'reliablesite.net',
	'researchscan',
	'rackspace.com',
	'rev.synaix.de',
	'scaleway.com',
	'secureserver.net',
	'semrush',
	'server',
	'siteground.com',
	'startdedicated.com',
	'softlayer',
	'tencent.com',
	'TMDHosting',
	'upcloud.com',
	'verizon.net',
	'vps',
	'vps.ovh',
	'vultr.com',
	'webhostingpad.com',
	'wix.com',
);

// require_once STOPBADBOTSPATH . "functions/fail2ban.php";
require_once STOPBADBOTSPATH . 'functions/functions.php';


if (stopbadbots_is_really_our_server()) {
	return;
}

if ($stopbadbots_is_admin) {

	// reset if is empty
	global $wpdb;

	$table_name = $wpdb->prefix . 'sbb_blacklist';
	/*
	$query = "SELECT COUNT(*) FROM $table_name";
	$result99 = $wpdb->get_var($query);
	*/
	$result99 = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i", $table_name));


	if ($result99 == 0) {

		$r = update_option('stopbadbots_tables_empty', 'yes');
		if (!$r)
			add_option('stopbadbots_tables_empty', 'yes');
	}



	if (!class_exists('WP_List_Table')) {
		include_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	}
	require dirname(__FILE__) . '/includes/list-tables/class-sbb-list-table.php';
	require dirname(__FILE__) . '/includes/list-tables/class-sbb-list-table2.php';
	require dirname(__FILE__) . '/includes/list-tables/class-sbb-list-table3.php';
	$stopbadbots_firewall = sanitize_text_field(get_option('stopbadbots_firewall', 'yes'));
	if ($stopbadbots_checkversion != '') {
		$stopbadbots_firewall = strtolower($stopbadbots_firewall);
		$stopbadbots_engine_option = 'conservative';
	} else {
		$stopbadbots_firewall = 'no';
	}
}

if (stopbadbots_isourserver())
	$stopbadbots_firewall = 'no';


if ($stopbadbots_is_admin) {
	require_once STOPBADBOTSPATH . 'dashboard/main.php';
	require_once STOPBADBOTSPATH . 'functions/function_sysinfo.php';
}


if ($stopbadbots_is_admin) {
	//require_once(WPTOOLSPATH . 'includes/help/help.php');
	//add_action('setup_theme', 'stopbadbots_load_settings');

	function stopbadbots_load_settings()
	{
		require_once(STOPBADBOTSPATH . "settings/load-plugin.php");
		require_once(STOPBADBOTSPATH . "settings/options/plugin_options_tabbed.php");
	}
}

if ($stopbadbots_is_admin) {
	// include_once STOPBADBOTSPATH . 'functions/health.php';
	function stopbadbots_add_admscripts()
	{

		global $stopbadbots_request_url;
		//global $stopbadbots_tables_empty;

		wp_enqueue_style('sbb-bill-datatables-jquery', STOPBADBOTSURL . 'assets/css/jquery.dataTables.min.css');

		wp_enqueue_style('sbb-bill-datatables-css', STOPBADBOTSURL . 'assets/css/stopbadbots-datatable.css');

		wp_enqueue_style('admin_enqueue_scripts', STOPBADBOTSURL . 'settings/styles/admin-settings.css');

		$pos = strpos($stopbadbots_request_url, 'page=stopbadbots');
		if ($pos !== false) {

			wp_enqueue_script(
				'sbb-botstrap',
				STOPBADBOTSURL .
					'assets/js/bootstrap.bundle.min.js',
				array('jquery')
			);
		}

		/*
		wp_register_script(
			"sbb-js-toast",
			STOPBADBOTSURL . "assets/js/jquery.toast.js",
			false
		);
		wp_enqueue_script("sbb-js-toast");
		*/



		wp_enqueue_style('sbb-bill-datatables-jquery', STOPBADBOTSURL . 'assets/css/jquery.dataTables.min.css');

		$pos  = strpos($stopbadbots_request_url, 'page=stop_bad_bots_plugin');
		$pos2 = strpos($stopbadbots_request_url, 'wp-admin/index.php');

		$pos3 = substr($stopbadbots_request_url, -10) == '/wp-admin/';


		$pos4 = strpos($stopbadbots_request_url, 'stopbadbots_my-custom-submenu-page-stats');
		$pos_fail2ban = strpos($stopbadbots_request_url, 'page=stopbadbots_my-custom-submenu-page-fail2ban');
		if ($pos !== false || $pos2 !== false || $pos3 || $pos4 !== false || $pos_fail2ban !== false) {
			wp_enqueue_script(
				'sbb-flot',
				STOPBADBOTSURL .
					'assets/js/jquery.flot.min.js',
				array('jquery')
			);
			wp_enqueue_script(
				'sbb-flotpie',
				STOPBADBOTSURL .
					'assets/js/jquery.flot.pie.js',
				array('jquery')
			);
		}

		wp_enqueue_script(
			'sbb-circle',
			STOPBADBOTSURL .
				'assets/js/radialIndicator.js',
			array('jquery')
		);
		wp_enqueue_script(
			'sbb-easing',
			STOPBADBOTSURL .
				'assets/js/jquery.easing.min.js',
			array('jquery')
		);
		wp_enqueue_script(
			'sbb-datatables10',
			STOPBADBOTSURL .
				'assets/js/jquery.dataTables.min.js',
			array('jquery')
		);
		wp_localize_script('sbb-datatables10', 'datatablesajax', array('url' => admin_url('admin-ajax.php')));
		wp_enqueue_script(
			'botstrap40',
			STOPBADBOTSURL .
				'assets/js/dataTables.bootstrap4.min.js',
			array('jquery')
		);
		wp_enqueue_script(
			'sbb-datatables20',
			STOPBADBOTSURL .
				'assets/js/dataTables.buttons.min.js',
			array('jquery')
		);
		$pos = strpos($stopbadbots_request_url, 'page=stopbadbots_my-custom-submenu-page');
		if ($pos !== false) {
			wp_register_script(
				'sbb-datatables_visitors_sbb',
				STOPBADBOTSURL .
					'assets/js/stopbadbots_table.js',
				array(),
				'1.0',
				true
			);
			wp_enqueue_script('sbb-datatables_visitors_sbb');
		}

		wp_enqueue_script(
			'stopbadbots-dashboard-script',
			STOPBADBOTSURL .
				'assets/js/dashboard.js',
			array('jquery'),
			'1.0',
			true
		);

		wp_enqueue_script(
			'stopbadbots-chart-script',
			STOPBADBOTSURL .
				'assets/js/chart.min.js',
			array('jquery'),
			'1.0',
			true
		);
	}
	add_action('admin_enqueue_scripts', 'stopbadbots_add_admscripts', 1000);
}
function stopbadbots_add_scripts()
{
	wp_register_script(
		'stopbadbots-main-js',
		STOPBADBOTSURL .
			'assets/js/stopbadbots.js',
		array('jquery')
	);
	wp_enqueue_script('stopbadbots-main-js');
}
function stopbadbots_add_scripts_main()
{
	wp_register_script(
		'stopbadbots-main-js',
		STOPBADBOTSURL .
			'assets/js/stopbadbots-main.js',
		array('jquery')
	);
	wp_enqueue_script('stopbadbots-main-js');
}

if ($stopbadbots_is_admin) {
	add_action('admin_enqueue_scripts', 'stopbadbots_add_scripts_main');
}

add_action('wp_enqueue_scripts', 'stopbadbots_add_scripts');

if ($stopbadbots_is_admin) {
	add_action('admin_menu', 'stopbadbots_add_menu_items');
	add_action('admin_menu', 'stopbadbots_add_menu_fail2ban');
	add_filter('set-screen-option', 'stopbadbots_set_screen_options', 10, 3);
}

$stopbadbots_active         = sanitize_text_field(get_option('stop_bad_bots_active', 'yes'));
$stopbadbots_active         = strtolower($stopbadbots_active);
$stopbadbots_ip_active      = sanitize_text_field(get_option('stop_bad_bots_ip_active', 'yes'));
$stopbadbots_ip_active      = strtolower($stopbadbots_ip_active);
$stopbadbots_referer_active = sanitize_text_field(get_option('stop_bad_bots_referer_active', 'yes'));
$stopbadbots_referer_active = strtolower($stopbadbots_referer_active);
// Report Firewall
$stopbadbots_Report_Blocked_Firewall = sanitize_text_field(get_option('stopbadbots_Blocked_Firewall', 'no'));
$stopbadbots_Report_Blocked_Firewall = strtolower($stopbadbots_Report_Blocked_Firewall);

$stop_bad_bots_network             = sanitize_text_field(get_option('stop_bad_bots_network', 'yes'));
$stop_bad_bots_network             = strtolower($stop_bad_bots_network);
$stop_bad_bots_blank_ua            = sanitize_text_field(get_option('stop_bad_bots_blank_ua', 'no'));
$stop_bad_bots_blank_ua            = strtolower($stop_bad_bots_blank_ua);
$stopbadbots_block_pingbackrequest = sanitize_text_field(get_option('stopbadbots_block_pingbackrequest', 'no'));
$stopbadbots_block_enumeration     = sanitize_text_field(get_option('stopbadbots_block_enumeration', 'no'));
$stopbadbots_block_false_google    = sanitize_text_field(get_option('stopbadbots_block_false_google', 'no'));
$stopbadbots_rate_limiting         = sanitize_text_field(get_option('stopbadbots_rate_limiting', 'unlimited'));
$stopbadbots_rate_limiting_day     = sanitize_text_field(get_option('stopbadbots_rate_limiting_day', 'unlimited'));
// $stopbadbots_version = trim(sanitize_text_field(get_site_option('stopbadbots_version', '')));


$stopbadbots_admin_email = trim(get_option('stopbadbots_my_email_to'));
if (!empty($stopbadbots_admin_email)) {
	if (!is_email($stopbadbots_admin_email)) {
		$stopbadbots_admin_email = '';
		update_option('stopbadbots_my_email_to', '');
	}
}
if (empty($stopbadbots_admin_email)) {
	$stopbadbots_admin_email = sanitize_text_field(get_option('admin_email'));
}
// Firewall
if (!$stopbadbots_is_admin) {
	if ($stopbadbots_firewall != 'no' and $stopbadbots_checkversion != '') {
		$stopbadbots_request_uri_array   = array('@eval', 'eval\(', 'UNION(.*)SELECT', '\(null\)', 'base64_', '\/localhost', '\%2Flocalhost', '\/pingserver', 'wp-config\.php', '\/config\.', '\/wwwroot', '\/makefile', 'crossdomain\.', 'proc\/self\/environ', 'usr\/bin\/perl', 'var\/lib\/php', 'etc\/passwd', '\/https\:', '\/http\:', '\/ftp\:', '\/file\:', '\/php\:', '\/cgi\/', '\.cgi', '\.cmd', '\.bat', '\.exe', '\.sql', '\.ini', '\.dll', '\.htacc', '\.htpas', '\.pass', '\.asp', '\.jsp', '\.bash', '\/\.git', '\/\.svn', ' ', '\<', '\>', '\/\=', '\.\.\.', '\+\+\+', '@@', '\/&&', '\/Nt\.', '\;Nt\.', '\=Nt\.', '\,Nt\.', '\.exec\(', '\)\.html\(', '\{x\.html\(', '\(function\(', '\.php\([0-9]+\)', '(benchmark|sleep)(\s|%20)*\(', 'indoxploi', 'xrumer');
		$stopbadbots_query_string_array  = array('@@', '\(0x', '0x3c62723e', '\;\!--\=', '\(\)\}', '\:\;\}\;', '\.\.\/', '127\.0\.0\.1', 'UNION(.*)SELECT', '@eval', 'eval\(', 'base64_', 'localhost', 'loopback', '\%0A', '\%0D', '\%00', '\%2e\%2e', 'allow_url_include', 'auto_prepend_file', 'disable_functions', 'input_file', 'execute', 'file_get_contents', 'mosconfig', 'open_basedir', '(benchmark|sleep)(\s|%20)*\(', 'phpinfo\(', 'shell_exec\(', '\/wwwroot', '\/makefile', 'path\=\.', 'mod\=\.', 'wp-config\.php', '\/config\.', '\$_session', '\$_request', '\$_env', '\$_server', '\$_post', '\$_get', 'indoxploi', 'xrumer');
		$stopbadbots_request_uri_string  = false;
		$stopbadbots_query_string_string = false;
		if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
			$stopbadbots_request_uri_string = sanitize_text_field($_SERVER['REQUEST_URI']);
		}
		if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
			$stopbadbots_query_string_string = sanitize_text_field($_SERVER['QUERY_STRING']);
		}
		if ($stopbadbots_request_uri_string || $stopbadbots_query_string_string) {
			if (
				preg_match('/' . implode('|', $stopbadbots_request_uri_array) . '/i', $stopbadbots_request_uri_string, $matches)
				|| preg_match('/' . implode('|', $stopbadbots_query_string_array) . '/i', $stopbadbots_query_string_string, $matches2)
			) {
				stopbadbots_stats_moreone('qfire');
				if ($stopbadbots_Report_Blocked_Firewall == 'yes') {
					if (isset($matches)) {
						if (is_array($matches)) {
							if (count($matches) > 0) {
								stopbadbots_alertme3($matches[0]);
							}
						}
					}
					if (isset($matches2)) {
						if (is_array($matches2)) {
							if (count($matches2) > 0) {
								stopbadbots_alertme3($matches2[0]);
							}
						}
					}
				}
				stopbadbots_response('Firewall');
				// wp_die("");
			} // Endif match...
		} // endif if ($stopbadbots_query_string_string || $user_agent_string)
	} // firewall <> no
}

/*
function stopbadbots_record_log( $stopbadbots_why_block = '' ) {

global $wpdb;
	global $stopbadbots_ip;
	global $stopbadbots_is_human;
	//global $stopbadbots_method;
	//global $stopbadbots_request_url;
	//global $stopbadbots_referer;
	global $stopbadbots_userAgentOri;
	// global $stopbadbots_access;
	===== global $stopbadbots_amy_whitelist;
*/


// Google and ...
if ($stopbadbots_maybe_search_engine and stopbadbots_really_search_engine($stopbadbots_userAgentOri)) {
	global $stopbadbots_is_human;
	$stopbadbots_is_human = '0';
	stopbadbots_record_log();
	return;
}





if (!empty($stopbadbots_userAgent) and !$stopbadbots_is_admin  and !stopbadbots_block_whitelist_string() and !stopbadbots_block_whitelist_IP()) {
	if (stopbadbots_crawlerDetect($stopbadbots_userAgent) and $stopbadbots_active != 'no') {
		stopbadbots_moreone($stopbadbots_userAgentOri); // +1
		stopbadbots_stats_moreone('qnick');
		if ($stopbadbots_my_radio_report_all_visits == 'yes') {
			stopbadbots_alertme($stopbadbots_userAgentOri);
		}
		stopbadbots_complete_bot_data($stopbadbots_found);
		if ($stop_bad_bots_network != 'no') {
			stopbadbots_upload_new_bots();
		}
		stopbadbots_response('Blocked by Name');
	}
}
if (!empty($stopbadbots_ip) and !$stopbadbots_is_admin) {
	if (stopbadbots_visitoripDetect($stopbadbots_ip) and $stopbadbots_ip_active != 'no' and !stopbadbots_block_whitelist_string() and !stopbadbots_block_whitelist_IP()) {
		stopbadbots_moreone2($stopbadbots_ip); // +1
		stopbadbots_stats_moreone('qip');
		if ($stopbadbots_my_radio_report_all_visits == 'yes') {
			stopbadbots_alertme2($stopbadbots_ip);
		}
		stopbadbots_response('Blocked By IP');
		// wp_die();
	}
}
// Block HTTP_tools
if (!empty($stopbadbots_userAgent) and !$stopbadbots_is_admin  and !stopbadbots_block_whitelist_string() and !stopbadbots_block_whitelist_IP()) {
	if (!empty(stopbadbots_block_httptools()) and $stopbadbots_block_http_tools != 'no') {
		stopbadbots_moreone_http(stopbadbots_block_httptools()); // +1
		stopbadbots_stats_moreone('qtools');
		if ($stopbadbots_my_radio_report_all_visits == 'yes') {
			stopbadbots_alertme12(stopbadbots_block_httptools());
		}
		stopbadbots_response('HTTP Tools');
		// wp_die();
	}
}



/* ------------ July 2021 ------------------- */

// -------------------------  Step 2
$pos = stripos($stopbadbots_request_url, '_grava_fingerprint');

if ($stopbadbots_engine_option != 'minimal') {


	if (
		!$stopbadbots_maybe_search_engine
		and !stopbadbots_block_whitelist_string()
		and $pos === false
		and !stopbadbots_isourserver()
		and !$stopbadbots_is_admin
		and !is_super_admin()
	) {


		if ($stopbadbots_is_human != '1') {


			// Chrome and firefox old and browser == linux
			$stopbadbots_ua_browser = stopbadbots_find_ua_browser($stopbadbots_userAgentOri);
			$stopbadbots_ua_version = stopbadbots_find_ua_version($stopbadbots_userAgentOri, $stopbadbots_ua_browser);

			$stopbadbots_ua_os = stopbadbots_find_ua_os($stopbadbots_userAgentOri);

			$stopbadbots_template = false;

			if ($stopbadbots_ua_os == 'Linux') {
				$stopbadbots_template = true;
			}


			if ($stopbadbots_ua_browser == 'Chrome' and !empty($stopbadbots_ua_version)) {
				if (version_compare($stopbadbots_ua_version, STOPBADBOTS_CHROME) <= 0) {
					$stopbadbots_template = true;
				}
			}

			if ($stopbadbots_ua_browser == 'Firefox' and !empty($stopbadbots_ua_version)) {
				if (version_compare($stopbadbots_ua_version, STOPBADBOTS_FIREFOX) <= 0) {
					$stopbadbots_template = true;
				}
			}

			if ($stopbadbots_ua_browser == 'Edge' and !empty($stopbadbots_ua_version)) {
				if (version_compare($stopbadbots_ua_version, STOPBADBOTS_EDGE) <= 0) {
					$stopbadbots_template = true;
				}
			}

			if ($stopbadbots_ua_browser == 'MSIE' and !empty($stopbadbots_ua_version)) {
				//if (version_compare($stopbadbots_ua_version, '11') <= 0) {
				$stopbadbots_template = true;
				//}
			}


			// second time...
			if ($stopbadbots_is_human == '0') {
				$stopbadbots_template = true;
			}


			add_action('template_redirect', 'stopbadbots_final_step');




			if ($stopbadbots_engine_option == 'maximum') {
				$stopbadbots_template = true;
			}

			// Check host...
			if ($stopbadbots_template) {
				if (!isset($_COOKIE['_ga']) and !isset($_COOKIE['__utma'])) {


					if ($stopbadbots_engine_option != 'conservative') {

						if (stopbadbots_is_bad_hosting($stopbadbots_ip)) {
							stopbadbots_add_temp_ip();
							stopbadbots_stats_moreone('qbrowser');
							if ($stopbadbots_my_radio_report_all_visits == 'yes') {
								stopbadbots_alertme14($stopbadbots_ip);
							}
							stopbadbots_record_log('Blocked Fake Browser (1)');
							header('HTTP/1.1 403 Forbidden');
							header('Status: 403 Forbidden');
							header('Connection: Close');
							die();
						}

						if (stopbadbots_is_bad_hosting2($stopbadbots_ip)) {
							stopbadbots_add_temp_ip();
							stopbadbots_stats_moreone('qbrowser');
							if ($stopbadbots_my_radio_report_all_visits == 'yes') {
								stopbadbots_alertme14($stopbadbots_ip);
							}
							stopbadbots_record_log('Blocked Fake Browser (2)');
							header('HTTP/1.1 403 Forbidden');
							header('Status: 403 Forbidden');
							header('Connection: Close');
							die();
						}
					}

					if ($stopbadbots_engine_option == 'maximum') {
						function stoppadbots_page_template()
						{
							return STOPBADBOTSPATH . 'template/content_stopbadbots.php';
						}
						add_filter('template_include', 'stoppadbots_page_template');
						header('Refresh: 3;');
					}
				}
			}
		}   // if ($stopbadbots_is_human == '1')
	}
} // if($stopbadbots_engine_option != 'minimal')


/*   ------------------------------     END STEP 2 */

/* ------------ End July 2021 ------------------- */


function stopbadbots_render_list_page()
{
	$test_list_table = new sbb_List_Table();
	$test_list_table->sbb_prepare_items();
	include dirname(__FILE__) . '/includes/list-tables/page.php';
}
function stopbadbots_render_list_page2()
{
	$stopbadbots_list_table2 = new sbb_List_Table2();
	$stopbadbots_list_table2->sbb_prepare_items2();
	include dirname(__FILE__) . '/includes/list-tables/page2.php';
}
function stopbadbots_render_list_page3()
{
	$stopbadbots_list_table3 = new sbb_List_Table3();
	$stopbadbots_list_table3->sbb_prepare_items3();
	include dirname(__FILE__) . '/includes/list-tables/page3.php';
}
register_activation_hook(__FILE__, 'stopbadbots_plugin_was_activated');
add_action('admin_menu', 'stopbadbots_add_admin_menu1');
add_action('admin_menu', 'stopbadbots_add_admin_menu2');
add_action('admin_menu', 'stopbadbots_add_admin_menu3');
add_action('admin_init', 'stopbadbots_settings_init');
add_action('admin_init', 'stopbadbots_settings2_init');
add_action('admin_init', 'stopbadbots_settings3_init');

/*
function stopbadbots_load_activate() {
	if ( $stopbadbots_is_admin or is_super_admin() ) {
		// require_once STOPBADBOTSPATH . 'includes/feedback/activated-manager.php';
	}
}
add_action( 'in_admin_footer', 'stopbadbots_load_activate' );
*/

// $buffer = ob_get_flush();
// add_action('admin_menu', 'stopbadbots_add_menu_items9');

function stopbadbots_sbb_custom_dashboard_help()
{
	global $stopbadbots_checkversion;
	$perc = stopbadbots_find_perc();
	if ($perc < 70) {
		$color = '#ff0000';
	} else {
		$color = '#000000';
	}
	echo '<img src="' . esc_url(STOPBADBOTSURL) . '/images/logo.png" style="text-align:center; max-width: 200px;margin: 0px 0 auto;"  />';
	echo '<br />';
	echo '<br />';
	if ($stopbadbots_checkversion == '') {
		echo '<img src="' . esc_url(STOPBADBOTSURL) . '/assets/images/unlock-icon-red-small.png" style="text-align:center; max-width: 20px;margin: 0px 0 auto;"  />';
		echo '<h2 style="margin-top: -39px; margin-left: 30px; color:' . esc_attr($color) . '; font-weight: bold;" >';
	} else {
		echo '<h2 style="margin-top: -22px; margin-left: update0px; color:' . esc_attr($color) . '; font-weight: bold;">';
	}
	echo '<span style = "color:' . esc_attr($color) . '">';
	echo esc_attr__('Protection rate:', 'stopbadbots') . ' ' . esc_attr($perc) . '%';
	echo '</h2>';
	$site = STOPBADBOTSHOMEURL . 'admin.php?page=stop_bad_bots_plugin';
	// echo 'For details, visit the plugin dashboard.';
	echo '<h3><a href="' . esc_url($site) . '">' . esc_attr__("For details, visit the plugin dashboard", "stopbadbots") . '</a></h3>';
	echo '<br />';
	echo '<center><strong><big>' . esc_attr__("Attacks Blocked Last 15 days", "stopbadbots") . '</big></strong></center>';
	echo '<br />';
	include_once 'dashboard/botsgraph.php';
	echo '<br />';
	echo '<hr>';
	echo '<br />';
	echo '<br />';
	echo '<center><strong><big>' . esc_attr__("Total Attacks Blocked By Type", "stopbadbots") . '</big></strong></center>';
	echo '<br />';
	include_once 'dashboard/botsgraph_pie.php';
	echo '<br />';
	echo '<br />';
	echo '<br />';
	echo '<br />';
	echo '<hr>';
	echo '<br />';
	echo '<center><strong><big>' . esc_attr__("Total Attacks Blocked By IP", "stopbadbots") . '</big></strong></center>';
	echo '<br />';
	include_once 'dashboard/topips.php';
	echo '<br />';
	echo '<br />';
	$site = esc_url(STOPBADBOTSHOMEURL) . 'admin.php?page=stop_bad_bots_plugin';
	echo '<a href="' . esc_attr($site) . '" class="button button-primary">Details</a>';
	echo '<br /><br />';
	// echo esc_html($bd_msg);
	echo '</p>';
}
function stopbadbots_add_dashboard_widgets()
{
	// wp_add_dashboard_widget('stopbadbots-dashboard', 'Stop Bad Bots Activities', 'stopbadbots_sbb_custom_dashboard_help', 'dashboardsbb', 'normal', 'high');
	wp_add_dashboard_widget('stopbadbots-dashboard', 'Stop Bad Bots Activities', 'stopbadbots_sbb_custom_dashboard_help');
}
if ($stopbadbots_is_admin) {
	$pos2 = strpos($stopbadbots_request_url, 'wp-admin/index.php');
	$pos3 = strpos($stopbadbots_request_url, 'page=');
	$pos4 = strpos($stopbadbots_request_url, 'stop_bad_bots_plugin');
	if ($pos2 !== false or ($pos3 !== false and $pos4 !== false)) {
		add_action('wp_dashboard_setup', 'stopbadbots_add_dashboard_widgets');
	}
}
// Bad Referer
function stopbadbots_get_referer()
{
	if (isset($_SERVER['HTTP_REFERER'])) {
		$stopbadbots_referer = sanitize_text_field($_SERVER['HTTP_REFERER']);

		$stopbadbots_referer = trim(parse_url($stopbadbots_referer, PHP_URL_HOST));
		if (gettype($stopbadbots_referer) == 'string') {
			return $stopbadbots_referer;
		} else {
			return '';
		}
	} else {
		return '';
	}
}

if ($stopbadbots_referer_active != 'no') {

	$badreferer = '';
	if (stopbadbots_ReferDetect($stopbadbots_referer) and !$stopbadbots_is_admin  and !stopbadbots_block_whitelist_string() and !stopbadbots_block_whitelist_IP()) {
		global $badreferer;
		stopbadbots_moreone4($badreferer); // +1
		stopbadbots_stats_moreone('qref');
		if ($stopbadbots_my_radio_report_all_visits == 'yes') {
			stopbadbots_alertme4($badreferer);
		}
		/*
		if($stop_bad_bots_network != 'no')
		upload_new_badreferer();
		exit;
		 */
		stopbadbots_response('Bad Referrer');
	}
}
if ($stop_bad_bots_blank_ua == 'yes' and !$stopbadbots_is_admin) {


	if (!stopbadbots_isourserver()) {
		if (empty(trim($stopbadbots_userAgentOri))) {
			stopbadbots_stats_moreone('qua');
			if ($stopbadbots_my_radio_report_all_visits == 'yes') {
				stopbadbots_alertme5();
			}
			stopbadbots_response('Blank User Agent');
		}
	}
}
if (!$stopbadbots_is_admin) {
	if ($stopbadbots_block_pingbackrequest == 'yes') {
		add_action('xmlrpc_call', 'stopbadbots_block_pingback_hook');
	}
	if ($stopbadbots_block_enumeration == 'yes') {
		stopbadbots_block_enumeration();
	}
	if ($stopbadbots_block_false_google == 'yes') {
		if (stopbadbots_check_false_googlebot()) {
			stopbadbots_stats_moreone('qother');
			if ($stopbadbots_my_radio_report_all_visits == 'yes') {
				stopbadbots_alertme8();
			}
			stopbadbots_response('False Google MSN/Bing or Yahoo Bot');
		}
	}
}


function stopbadbots_stop_bad_bots_init()
{
	global $stopbadbots_go_pro_hide;
	$stop_bad_bots_today = date('Ymd', strtotime('+0 days'));
	if ($stopbadbots_go_pro_hide < $stop_bad_bots_today or $stopbadbots_go_pro_hide == '') {
		echo '<script type="text/javascript">
            jQuery(document).ready(function() {
            jQuery(".sbb_bill_go_pro_container").css("display", "block");
            }); // end (jQuery);
            </script>';
	} else {
		echo '<script type="text/javascript">
            jQuery(document).ready(function() {
            jQuery(".sbb_bill_go_pro_container").css("display", "none");
            }); // end (jQuery);
            </script>';
	}
}
add_action('admin_notices', 'stopbadbots_stop_bad_bots_init');
add_action('wp_ajax_stopbadbots_go_pro_hide', 'stopbadbots_go_pro_hide');
// update_option('stopbadbots_go_pro_hide','');
remove_action('shutdown', 'wp_ob_end_flush_all', 1);
function stopbadbots_end_flush()
{
	$levels = ob_get_level();
	for ($i = 0; $i < $levels; $i++) {
		if (ob_get_contents()) {
			// ob_flush();
			if ($i == 0) {
				@ob_end_flush();
			} else {
				ob_end_flush();
			}
		}
	}
}

add_action('shutdown', 'stopbadbots_end_flush', 10, 0);

if ($stopbadbots_is_admin) {
	require_once STOPBADBOTSPATH . 'table/visitors.php';
	require_once STOPBADBOTSPATH . 'includes/visits_stats/visits_dashboard.php';
}


add_action('admin_menu', 'stopbadbots_add_menu_items9');


function stopbadbots_custom_toolbar_link($wp_admin_bar)
{
	global $wp_admin_bar;
	global $stopbadbots_is_admin;
	$site = STOPBADBOTSHOMEURL . 'admin.php?page=stop_bad_bots_plugin&tab=notifications';
	$args = array(
		'id'    => 'stopbadbots',
		'title' => '<div class="stopbadbots-logo"></div><span class="text"> Stop Bad Bots</span>',
		'href'  => $site,
		'meta'  => array(
			'class' => 'stopbadbots',
			'title' => '',
		),
	);
	$wp_admin_bar->add_node($args);
	echo '<style>';
	echo '#wpadminbar .stopbadbots  {
      background: red !important;
      color: black !important;
    }';
	$logourl = STOPBADBOTSIMAGES . '/sologo-gray.png';
	echo '#wpadminbar .stopbadbots-logo  {
      background-image: url("' . esc_url($logourl) . '");
      float: left;
      width: 26px;
      height: 30px;
      background-repeat: no-repeat;
      background-position: 0 6px;
      background-size: 20px;
    }';
	echo '</style>';
}

$stopbadbots_timeout_level = time() > ($stopbadbots_notif_level + 60 * 60 * 24 * 7);
// $stopbadbots_timeout_level = time() > ($stopbadbots_notif_level + 10 );

if ($stopbadbots_timeout_level) {

	if (stopbadbots_find_perc() < 80) {
		$stopbadbots_timeout_level = true;
	} else {
		$stopbadbots_timeout_level = false;
	}
}


if ($stopbadbots_timeout_level or $stopbadbots_active != 'yes' or $stopbadbots_ip_active != 'yes' or $stopbadbots_referer_active != 'yes') {
	if (!is_multisite() and $stopbadbots_is_admin) {
		add_action('admin_bar_menu', 'stopbadbots_custom_toolbar_link', 999);
	}
}
//
// require_once STOPBADBOTSPATH . "functions/functions_api.php";
function stopbadbots_add_cors_http_header()
{
	header('Access-Control-Allow-Origin: https://stopbadbots.com');
}


function stopbadbots_plugin_installed($slug)
{
	$all_plugins = get_plugins();
	foreach ($all_plugins as $key => $value) {
		$plugin_file    = $key;
		$slash_position = strpos($plugin_file, '/');
		$folder         = substr($plugin_file, 0, $slash_position);
		// match FOLDER against SLUG
		if ($slug == $folder) {
			return true;
		}
	}
	return false;
}

function stopbadbots_load_upsell()
{
	global $stopbadbots_checkversion;
	global $stopbadbots_is_admin;

	//wp_enqueue_style( 'stopbadbots-more2', STOPBADBOTSURL . 'includes/more/more2.css' );
	//wp_register_script( 'stopbadbots-more2-js', STOPBADBOTSURL . 'includes/more/more2.js', array( 'jquery' ) );
	//wp_enqueue_script( 'stopbadbots-more2-js' );



	if (!empty($stopbadbots_checkversion)) {
		return;
	}

	if (isset($_COOKIE["sbb_dismiss"])) {

		$today = time();
		if (!update_option('stopbadbots_go_pro_hide', $today))
			add_option('stopbadbots_go_pro_hide', $today);
	}

	$stopbadbots_go_pro_hide = trim(get_option('stopbadbots_go_pro_hide', ''));
	// $stopbadbots_go_pro_hide = '';
	// Debug ...


	if (strlen($stopbadbots_go_pro_hide) < 10)
		$stopbadbots_go_pro_hide = strtotime($stopbadbots_go_pro_hide);


	if (empty(trim($stopbadbots_go_pro_hide))) {

		// $wtime = strtotime('-5 days');
		$wtime = time() - (3600 * 24 * 5);
		update_option('stopbadbots_go_pro_hide', $wtime);
		$stopbadbots_go_pro_hide = $wtime;
		$delta                        = 0;
	} else {

		$now   = time();
		$delta = $now - $stopbadbots_go_pro_hide;
	}


	//$delta = time();
	//die();

	// debug
	// 
	// $delta = time();
	// $delta = 0;


	$stopbadbots_activation_date = get_option('stopbadbots_activation_date');

	if ($stopbadbots_activation_date) {
		$stopbadbots_activation_date = date('Y-m-d', $stopbadbots_activation_date);
		$today = date('Y-m-d');

		if ($stopbadbots_activation_date === $today) {
			$delta = 0;
		}
	}



	if ($delta > (3600 * 24 * 14)) {

		$list = 'enqueued';
		if (!wp_script_is('bill-css-vendor-fix', $list)) {
			include_once STOPBADBOTSPATH . 'includes/vendor/vendor.php';
			wp_enqueue_style('bill-css-vendor-fix', STOPBADBOTSURL . 'includes/vendor/vendor_fix.css');

			wp_register_script('bill-js-vendor', STOPBADBOTSURL . 'includes/vendor/vendor.js', array('jquery'), STOPBADBOTSVERSION, true);
			wp_enqueue_script('bill-js-vendor');

			wp_enqueue_style('bill-css-vendor-sbb', STOPBADBOTSURL . 'includes/vendor/vendor.css');
		}
	}

	wp_register_script('bill-js-vendor-sidebar', STOPBADBOTSURL . 'includes/vendor/vendor-sidebar.js', array('jquery'), STOPBADBOTSVERSION, true);
	wp_enqueue_script('bill-js-vendor-sidebar');

	//	wp_enqueue_style( 'bill-css-vendor-sbb', STOPBADBOTSURL . 'includes/vendor/vendor.css' );
}




if (!function_exists('wp_get_current_user')) {
	include_once ABSPATH . 'wp-includes/pluggable.php';
}


if ($stopbadbots_is_admin) {
	add_action('admin_enqueue_scripts', 'stopbadbots_load_upsell');
	add_action('wp_ajax_stopbadbots_install_plugin', 'stopbadbots_install_plugin');
	add_action('wp_ajax_stopbadbots_install_ah_plugin', 'stopbadbots_install_ah_plugin');
}


function stopbadbots_go_pro_hide2()
{
	// $today = date('Ymd', strtotime('+06 days'));
	$today = time();
	if (!update_option('stopbadbots_go_pro_hide', $today)) {
		add_option('stopbadbots_go_pro_hide', $today);
	}
	wp_die();
}
add_action('wp_ajax_stopbadbots_go_pro_hide2', 'stopbadbots_go_pro_hide2');



// Cron customized

add_action('template_redirect', 'stopbadbots_check_cron_request');

function stopbadbots_check_cron_request()
{
	if (get_transient('stopbadbots_cron_clear'))
		return;
	else
		set_transient('stopbadbots_cron_clear', true, MINUTE_IN_SECONDS * 5);
	// set_transient('stopbadbots_cron_clear', true, DAY_IN_SECONDS);
	if (did_action('template_redirect')) {
		try {
			$execute_cron = get_query_var('execute-cron');
			if ($execute_cron !== null) {
				stopbadbots_cron_function_clear();
			} else {
				$cron_url = home_url('/?execute-cron');
				$args = array(
					'timeout' => 5,
					'blocking' => false,
					'sslverify' => apply_filters('https_local_ssl_verify', false)
				);
				$result = wp_remote_post($cron_url, $args);
				if (is_wp_error($result)) {
					// Debug...
				}
			}
		} catch (Exception $e) {
			// debug...
		}
	}
}



/*
function stopbadbots_check_cron_request_debug()
{
	//error_log('stopbadbots_check_cron_request iniciado'); // Log de início

	if (get_transient('stopbadbots_cron_clear')) {
		error_log('Transiente stopbadbots_cron_clear existe. Saindo da função.'); // Log se o transiente existir
		return;
	} else {
		set_transient('stopbadbots_cron_clear', true, MINUTE_IN_SECONDS * 5);
		error_log('Transiente stopbadbots_cron_clear definido'); // Log ao definir o transiente
	}

	if (did_action('template_redirect')) {
		try {
			$execute_cron = get_query_var('execute-cron');
			error_log('Valor de execute-cron: ' . print_r($execute_cron, true)); // Log do valor da query

			if ($execute_cron !== null) {
				stopbadbots_cron_function_clear();
			} else {
				$cron_url = home_url('/?execute-cron');
				$args = array(
					'timeout' => 5,
					'blocking' => false,
					'sslverify' => apply_filters('https_local_ssl_verify', false)
				);
				$result = wp_remote_post($cron_url, $args);
				if (is_wp_error($result)) {
					error_log('Erro na requisição wp_remote_post: ' . $result->get_error_message()); // Log do erro
				}
			}
		} catch (Exception $e) {
			error_log('Exceção capturada: ' . $e->getMessage()); // Log da exceção
		}
	}
}
*/


function stopbadbots_add_more_plugins()
{


	if (is_multisite()) {
		add_submenu_page(
			'stop_bad_bots_plugin', // $parent_slug
			'More Tools Same Author', // string $page_title
			'More Tools Same Author', // string $menu_title
			'manage_options', // string $capability
			'stopbadbots_more_plugins', // menu slug
			'stopbadbots_more_plugins', // callable function
			11 // position
		);
	} else {

		add_submenu_page(
			'stop_bad_bots_plugin', // $parent_slug
			'More Tools Same Author', // string $page_title
			'More Tools Same Author', // string $menu_title
			'manage_options', // string $capability
			// 'wptools_options39', // menu slug
			// 'wptools_new_more_plugins', // callable function
			'stopbadbots_new_more_plugins', // menu slug
			'stopbadbots_new_more_plugins', // callable function
			33 // position
		);
	}
}
add_action('admin_menu', 'stopbadbots_add_more_plugins');



function stopbadbots_check_wordpress_logged_in_cookie()
{
	// Percorre todos os cookies definidos
	foreach ($_COOKIE as $key => $value) {
		// Verifica se algum cookie começa com 'wordpress_logged_in_'
		if (strpos($key, 'wordpress_logged_in_') === 0) {
			// Cookie encontrado
			return true;
		}
	}
	// Cookie não encontrado
	return false;
}


// -------------------------------------


function stopbadbots_bill_more()
{
	global $stopbadbots_is_admin;
	if ($stopbadbots_is_admin and current_user_can("manage_options")) {
		$declared_classes = get_declared_classes();
		foreach ($declared_classes as $class_name) {
			if (strpos($class_name, "Bill_show_more_plugins") !== false) {
				//    return;
			}
		}
		require_once dirname(__FILE__) . "/includes/more-tools/class_bill_more.php";
	}
}
add_action("init", "stopbadbots_bill_more", 5);

//

function stopbadbots_load_chat()
{
	if (function_exists('is_admin') && function_exists('current_user_can')) {
		if (is_admin() and current_user_can("manage_options")) {
			// ob_start();
			//debug2();

			if (!class_exists('stopbadbots_BillChat\ChatPlugin')) {
				require_once dirname(__FILE__) . "/includes/chat/class_bill_chat.php";
			}



			// ob_end_clean();
		}
	}
}
add_action('wp_loaded', 'stopbadbots_load_chat');
//
////
//
//
//


function stopbadbots_bill_hooking_diagnose()
{
	global $stopbadbots_is_admin;
	if ($stopbadbots_is_admin and current_user_can("manage_options")) {
		$declared_classes = get_declared_classes();
		foreach ($declared_classes as $class_name) {
			if (strpos($class_name, "Bill_Diagnose") !== false) {
				return;
			}
		}
		$plugin_slug = 'stopbadbots';
		$plugin_text_domain = $plugin_slug;
		$notification_url = "https://wpmemory.com/fix-low-memory-limit/";
		$notification_url2 =
			"https://wptoolsplugin.com/site-language-error-can-crash-your-site/";
		require_once dirname(__FILE__) . "/includes/diagnose/class_bill_diagnose.php";
	}
}
// add_action("plugins_loaded", "stopbadbots_bill_hooking_diagnose",10);
add_action('init', 'stopbadbots_bill_hooking_diagnose', 10);



function stopbadbots_bill_hooking_catch_errors()
{
	global $stopbadbots_plugin_slug;
	global $stopbadbots_is_admin;

	if (!function_exists("bill_check_install_mu_plugin")) {
		require_once dirname(__FILE__) . "/includes/catch-errors/bill_install_catch_errors.php";
	}
	$declared_classes = get_declared_classes();
	foreach ($declared_classes as $class_name) {
		if (strpos($class_name, "bill_catch_errors") !== false) {
			return;
		}
	}
	$stopbadbots_plugin_slug = 'stopbadbots';
	require_once dirname(__FILE__) . "/includes/catch-errors/class_bill_catch_errors.php";
}
add_action("init", "stopbadbots_bill_hooking_catch_errors", 15);

function stopbadbots_bill_hooking_catch_bots()
{
	$declared_classes = get_declared_classes();
	foreach ($declared_classes as $class_name) {
		if (strpos($class_name, "Bill_Catch_Bots") !== false) {
			return;
		}
	}
	require_once dirname(__FILE__) . "/includes/catch-bots/class_bill_catch_bots.php";
}
add_action("init", "stopbadbots_bill_hooking_catch_bots", 15);


// ------------------------

function stopbadbots_load_feedback()
{
	global $stopbadbots_is_admin;
	if ($stopbadbots_is_admin and current_user_can("manage_options")) {
		// ob_start();
		//
		require_once dirname(__FILE__) . "/includes/feedback-last/feedback-last.php";
		// ob_end_clean();
		//
	}
	//
}
add_action('wp_loaded', 'stopbadbots_load_feedback', 10);


// ------------------------


function stopbadbots_bill_install()
{
	global $stopbadbots_is_admin;
	if ($stopbadbots_is_admin and current_user_can("manage_options")) {
		$declared_classes = get_declared_classes();
		foreach ($declared_classes as $class_name) {
			if (strpos($class_name, "Bill_Class_Plugins_Install") !== false) {
				return;
			}
		}
		if (!function_exists('bill_install_ajaxurl')) {
			function bill_install_ajaxurl()
			{
				echo '<script type="text/javascript">
					var ajaxurl = "' .
					esc_attr(admin_url("admin-ajax.php")) .
					'";
					</script>';
			}
		}
		// ob_start();
		$plugin_slug = 'stopbadbots';
		$plugin_text_domain = $plugin_slug;
		$notification_url = "https://wpmemory.com/fix-low-memory-limit/";
		$notification_url2 =
			"https://wptoolsplugin.com/site-language-error-can-crash-your-site/";
		$logo = STOPBADBOTSIMAGES . '/logo.png';
		//$plugin_adm_url = admin_url('tools.php?page=stopbadbots_new_more_plugins');
		$plugin_adm_url = admin_url();
		require_once dirname(__FILE__) . "/includes/install-checkup/class_bill_install.php";
		// ob_end_clean();
	}
}
add_action('wp_loaded', 'stopbadbots_bill_install', 15);
// add_action('wp_head', 'stopbadbots_bill_install',15);
// add_action('admin_init', 'stopbadbots_bill_install',30);


function stopbadbots_more_plugins()
{
	echo '<script>';
	echo 'window.location.replace("' . esc_attr(STOPBADBOTSHOMEURL) . 'plugin-install.php?s=sminozzi&tab=search&type=author");';
	echo '</script>';
}



// add_action('wp_loaded', 'stopbadbots_new_more_plugins');

function stopbadbots_new_more_plugins()
{
	stopbadbots_show_logo();
	$plugin = new stopbadbots_Bill_show_more_plugins();
	$plugin->bill_show_plugins();
}



function stopbadbots_initialize_plugin_settings()
{
	// Inicialização do plugin.
	if (is_admin() and current_user_can("manage_options")) {
		//require_once STOPBADBOTSPATH . "functions/fail2ban.php";
		require_once STOPBADBOTSPATH . 'settings/load-plugin.php';
		require_once(STOPBADBOTSPATH . "settings/options/plugin_options_tabbed.php");
	}
}
add_action('init', 'stopbadbots_initialize_plugin_settings', 150);

// fail2ban
// Function to create the Fail2Ban logs table in the WordPress database
function stopbadbots_create_fail2ban_table()
{
	global $wpdb;

	// Table name with WordPress prefix
	$table_name = $wpdb->prefix . 'stopbadbots_fail2ban_logs';
	$charset_collate = $wpdb->get_charset_collate();

	// SQL statement to create the table
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ip VARCHAR(45) NOT NULL,
        timestamp DATETIME NOT NULL,
        jail VARCHAR(100) NOT NULL,
        reason TEXT,
        attempts INT NOT NULL,
        log_line TEXT,
        host VARCHAR(100),
        port INT,
        protocol VARCHAR(10),
        ban_duration INT NOT NULL,
        INDEX idx_timestamp (timestamp),
        INDEX idx_ip (ip),
        INDEX idx_jail (jail),
        INDEX idx_attempts (attempts)
    ) $charset_collate;";

	// Execute the table creation
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

// Hook to run the table creation when the plugin is activated
register_activation_hook(__FILE__, 'stopbadbots_create_fail2ban_table');

require_once STOPBADBOTSPATH . "functions/fail2ban.php";
