<?php

/**
 * @author Bill Minozzi
 * @copyright 2016-2023
 */
if (!defined('ABSPATH')) {
	exit;
}

$stopbadbots_ip            = sbb_findip();


$stopbadbots_userAgentOri = stopbadbots_get_ua();
$stopbadbots_userAgent    = strtolower(trim(strtolower($stopbadbots_userAgentOri)));
// $stopbadbots_userAgentOri = '';
/*
$stopbadbots_ip = sbb_findip();
// $stopbadbots_ip = '101.4.136.34';
$stopbadbots_userAgentOri = stopbadbots_get_ua();
// $stopbadbots_userAgentOri = '';
*/
// $userAgent = 'Acoon';
/*
$userAgent = 'Acoon';
$stopbadbots_userAgentOri = 'Acoon';
*/
// stopbadbots_record_log($stopbadbots_response = '403');


$stopbadbots_http_tools  = trim(get_site_option('stopbadbots_http_tools', ''));
$astopbadbots_http_tools = explode(PHP_EOL, $stopbadbots_http_tools);

$stopbadbots_maybe_search_engine = stopbadbots_maybe_search_engine($stopbadbots_userAgentOri);



if (stopbadbots_is_really_our_server()) {
	// return;
}

// Google and ...
if ($stopbadbots_maybe_search_engine and stopbadbots_really_search_engine($stopbadbots_userAgentOri)) {
	$stopbadbots_is_human = '0';
	return;
}

/*
	By default, version_compare() returns -1
	if the first version is lower than the second,
	0 if they are equal, and
	1 if the second is lower.
*/

// Install
if (version_compare(trim(STOPBADBOTSVERSION), trim($stopbadbots_version)) > 0) {
	try {
		if (isset($_SERVER['SERVER_ADDR'])) {
			if (strpos(sanitize_text_field($_SERVER['SERVER_ADDR']), '.212.229.') !== false)
				update_option('stopbadbots_keep_log', 360);
		}
	} catch (Exception $e) {
		//
	}
	/*
	if ( version_compare( trim( $stopbadbots_version ), '6.57' ) < 1 ) {

		$stopbadbots_table = $wpdb->prefix . 'sbb_fingerprint';
		if ( stopbadbots_tablexist( $stopbadbots_table ) ) {

			$sql = "TRUNCATE TABLE `$stopbadbots_table`";
			$wpdb->query( sanitize_text_field( $sql ) );

		}
	}
	*/

	/*
	$time_limit = (int) ini_get( 'max_execution_time' );
	if ( $time_limit < 120 ) {
		// @ini_set( 'max_execution_time', 120 );
	}
	*/


	if ($stopbadbots_go_pro_hide == '') {
		$today = date('Ymd', strtotime('+01 days'));
		if (!update_option('stopbadbots_go_pro_hide', $today)) {
			add_option('stopbadbots_go_pro_hide', $today);
		}
	}
	if (empty($stopbadbots_string_whitelist)) {
		stopbadbots_create_whitelist();
	}

	if (empty($stopbadbots_http_tools) or $stopbadbots_update_http_tools == 'yes') {
		stopbadbots_create_httptools();
	}

	$stopbadbots_http_tools  = trim(get_site_option('stopbadbots_http_tools', ''));
	$astopbadbots_http_tools = explode(PHP_EOL, $stopbadbots_http_tools);

	stopbadbots_create_db();
	stopbadbots_upgrade_db();
	stopbadbots_create_db2();
	stopbadbots_upgrade_db2();
	stopbadbots_create_db3();
	stopbadbots_create_db4();
	//stopbadbots_upgrade_db4();
	stopbadbots_create_db5();
	stopbadbots_create_db6();
	stopbadbots_upgrade_fingerprint();
	if (empty($stopbadbots_http_tools) or $stopbadbots_update_http_tools == 'yes') {
		stopbadbots_update_httptools($astopbadbots_http_tools);
	}
	stopbadbots_create_db_stats();
	//stopbadbots_upgrade_stats();
	//stopbadbots_check_db_sbb_blacklist();

	// old place...
	//stopbadbots_fill_db_froma();
	//stopbadbots_fill_db_froma2();
	//stopbadbots_fill_db_froma3();


	stopbadbots_sbb_populate_stats();
	// Default yes
	if (sanitize_text_field(get_option('stop_bad_bots_network', '') == '')) {
		add_option('stop_bad_bots_network', 'yes');
	}

	if (!add_option('stopbadbots_version', STOPBADBOTSVERSION)) {
		update_option('stopbadbots_version', STOPBADBOTSVERSION);
	}
} // end Install


// Complete Install

$BILLPRODUCT = 'STOPBADBOTS';
$BILLCLASS = 'ACTIVATED_' . $BILLPRODUCT;

if ($stopbadbots_tables_empty == 'yes') { // and isset($_COOKIE[$BILLCLASS])){
	$stopbadbots_installed    = sanitize_text_field(get_option('stopbadbots_installed', ''));
	if ($stopbadbots_installed != '') {
		// Obter o timestamp atual (segundos desde 1º de janeiro de 1970)
		$stopbadbots_currentTimestamp = time();
		// Obter o timestamp de 1 minuto atrás
		$stopbadbots_oneMinuteAgoTimestamp = $stopbadbots_currentTimestamp - 60; // 60 segundos em 1 minuto
		// Verificar se já passou um minuto desde $stopbadbots_installed
		if ($stopbadbots_installed <= $stopbadbots_oneMinuteAgoTimestamp) {
			function stopbadbots_my_ajax_nonce()
			{
				return wp_create_nonce('stopbadbots_ajax_fill_nonce');
			}
			function stopbadbots_bill_enqueue()
			{
				global $stopbadbots_tables_empty;
				if ($stopbadbots_tables_empty == 'yes') {
					wp_register_script(
						'stopbadbots-ajax-fill',
						STOPBADBOTSURL . 'assets/js/stopbadbots-fill-tables.js',
						array('jquery')
					);
					wp_enqueue_script('stopbadbots-ajax-fill');
					wp_localize_script('stopbadbots-ajax-fill', 'StopBadBotsmyAjaxFill', array('nonce' => stopbadbots_my_ajax_nonce()));
					$r = wp_enqueue_style('sbb-bill-fill-tables-css', STOPBADBOTSURL . 'assets/css/stopbadbots-fill-tables.css');
				}
			}
			add_action('admin_enqueue_scripts', 'stopbadbots_bill_enqueue', 1000);

			if (!wp_doing_ajax()) {
				function stopbadbots_show_fill_tables_dialog()
				{
					echo '<div id="stopbadbots_import-dialog" style="display: block;">
							<h2>Stop Bad Bots Plugin Updating Tables</h2>
							<br>
							Please wait. Do not reload the page or click back. <br>
							<br>
							This operation may take one or two minutes depending of your server.
							<br><br>
							<img alt="aux" src="/wp-admin/images/wpspin_light-2x.gif" id="imagewait-1" />
						  </div>';
				}
				add_action('admin_notices', 'stopbadbots_show_fill_tables_dialog');
			}
			add_action('wp_ajax_stopbadbots_import_tables_callback', 'stopbadbots_import_tables_callback');
		}
	}
	function stopbadbots_import_tables_callback()
	{
		check_ajax_referer('stopbadbots_ajax_fill_nonce', 'security');
		if (!stopbadbots_fill_db_froma()) {
			die(' error -3');
		}
		if (!stopbadbots_fill_db_froma2()) {
			die(' error -4');
		}
		if (!stopbadbots_fill_db_froma3()) {
			die(' error -5');
		}
		$r = update_option('stopbadbots_tables_empty', 'no');
		if (!$r)
			add_option('stopbadbots_tables_empty', 'no');
		wp_die('success');
	} // end function 
}
// end complete Install


/* ---------------STEP 1 Tem Fingerprint? ------------------ */

if ($stopbadbots_engine_option != 'minimal') {

	// auto declared s. e. == 0
	$stopbadbots_is_human = '?';
	// auto declare is s.e ?

	$stopbadbots_mysearch = array(
		'bingbot',
		'googlebot',
		'msn.com',
		'slurp',
		'facebookexternalhit',
		'AOL',
		'Baidu',
		'Bingbot',
		'DuckDuck',
		'Teoma',
		'Yahoo',
		'seznam',
		'Yandex',
		'Twitterbot',
		'facebookexternalhit',
	);
	for ($i = 0; $i < count($stopbadbots_mysearch); $i++) {
		if (stripos($stopbadbots_userAgent, $stopbadbots_mysearch[$i]) !== false) {
			$stopbadbots_is_human = '0';
		}
	}


	add_action('wp_head', 'stopbadbots_ajaxurl');


	$qrow = 0;
	if ($stopbadbots_is_human !== '0') {

		$stopbadbots_table = $wpdb->prefix . 'sbb_fingerprint';

		/*
		$result = $wpdb->get_results(
			$wpdb->prepare("SELECT  fingerprint,deny FROM `$stopbadbots_table` 
		WHERE ip = %s
		AND fingerprint != '' limit 1",
				$stopbadbots_ip
			)
		);
		*/
		$result = $wpdb->get_results($wpdb->prepare("SELECT fingerprint, deny FROM %i WHERE ip = %s AND fingerprint != '' LIMIT 1", $stopbadbots_table, $stopbadbots_ip));



		if (!empty($wpdb->last_error)) {
			stopbadbots_create_db6();
			$qrow = 0;
		} else {
			$qrow = $wpdb->num_rows;
		}

		if ($qrow < 1) {
			add_action('wp_enqueue_scripts', 'stopbadbots_include_scripts');
			add_action('admin_enqueue_scripts', 'stopbadbots_include_scripts');
		}
	}


	// $stopbadbots_is_human = '?';


	$pos                  = stripos($stopbadbots_request_url, '_grava_fingerprint');

	if ($qrow < 1 and !isset($_COOKIE['stopbadbots_cookie'])) {

		if ($stopbadbots_is_human != '0') {
			if (stopbadbots_first_time() > 0) {
				$stopbadbots_is_human = '0';
			} else {
				$stopbadbots_is_human = '?';
			}
		}
	} elseif (
		!$stopbadbots_maybe_search_engine
		and !stopbadbots_block_whitelist_string()
		and $pos === false
		and !stopbadbots_isourserver()
		and !stopbadbots_check_wordpress_logged_in_cookie()
	) {

		$stopbadbots_fingerprint_filed      = '';
		$stopbadbots_fingerprint_deny_filed = 0;


		// Tem Fingerprint
		if (isset($result[0]->fingerprint)) {
			$stopbadbots_fingerprint_filed      = trim($result[0]->fingerprint);
			$stopbadbots_fingerprint_deny_filed = trim($result[0]->deny);
			// $fingerprint_deny_filed  = trim($row2020[1]);
		}

		if ($stopbadbots_fingerprint_deny_filed <> 0) {
			stopbadbots_stats_moreone('qbrowser');
			if ($stopbadbots_my_radio_report_all_visits == 'yes') {
				stopbadbots_alertme14($stopbadbots_ip);
			}
			stopbadbots_record_log('Blocked Fake Browser (3)');
			header('HTTP/1.1 403 Forbidden');
			header('Status: 403 Forbidden');
			header('Connection: Close');
			die();
		}





		if (isset($_COOKIE['stopbadbots_cookie']) and empty($stopbadbots_fingerprint_filed)) {
			$stopbadbots_fingerprint_filed = sanitize_text_field($_COOKIE['stopbadbots_cookie']);
		}

		// #America/Chicago#300#win32#Windows#0,false,false#Google Inc.~ANGLE (Intel(R) HD Graphics Direct3D11 vs_5_0 ps_5_0)
		// Asia/Shanghai
		// Asia/Chongqing
		// Asia/Harbin
		// Asia/Kashgar
		// Asia/Urumqi
		// Asia/Beijing
		// Asia/Shenzhen
		// Asia/Lhasa
		// Hong_Kong
		// Macau


		if (!empty($stopbadbots_checkversion) and $stopbadbots_block_china == 'yes') {

			if (!empty($stopbadbots_fingerprint_filed)) {
				if (
					strpos($stopbadbots_fingerprint_filed, 'Asia/Shanghai') !== false
					or strpos($stopbadbots_fingerprint_filed, 'Asia/Hong_Kong') !== false
					or strpos($stopbadbots_fingerprint_filed, 'Asia/Macau') !== false
				) {
					stopbadbots_stats_moreone('qbrowser');
					if ($stopbadbots_my_radio_report_all_visits == 'yes') {
						stopbadbots_alertme15($stopbadbots_ip);
					}
					stopbadbots_record_log('Blocked China');
					header('HTTP/1.1 403 Forbidden');
					header('Status: 403 Forbidden');
					header('Connection: Close');
					die();
				}
				if (strpos($stopbadbots_fingerprint_filed, 'America/Havana') !== false) {
					if ($stopbadbots_my_radio_report_all_visits == 'yes') {
						stopbadbots_alertme15($stopbadbots_ip);
					}
					stopbadbots_record_log('Blocked Cuba');
					header('HTTP/1.1 403 Forbidden');
					header('Status: 403 Forbidden');
					header('Connection: Close');
					die();
				}
				if (strpos($stopbadbots_fingerprint_filed, 'Asia/Pyongyang') !== false) {
					if ($stopbadbots_my_radio_report_all_visits == 'yes') {
						stopbadbots_alertme15($stopbadbots_ip);
					}
					stopbadbots_record_log('Blocked North Korea');
					header('HTTP/1.1 403 Forbidden');
					header('Status: 403 Forbidden');
					header('Connection: Close');
					die();
				}
			}
		}


		// if ( ! empty( $stopbadbots_fingerprint_filed ) and $stopbadbots_engine_option != 'conservative' ) {
		if (!empty($stopbadbots_fingerprint_filed) and $stopbadbots_engine_option != 'conservative' and isset($_COOKIE['stopbadbots_cookie'])) {


			$afingerprint = explode('#', $stopbadbots_fingerprint_filed);
			$is_linux     = false;

			if (gettype($afingerprint) == 'array') {

				if (count($afingerprint) > 1) {
					// $timezone = $afingerprint[1];
					if (isset($afingerprint[3])) {

						if (stripos($afingerprint[3], 'linux x86_64') !== false) {
							$is_linux = true;
						}
					}


					if (trim(stopbadbots_find_ua_os($stopbadbots_userAgentOri)) == 'Linux') {
						$is_linux_ua = true;
					} else {
						$is_linux_ua = false;
					}

					// mozilla/5.0 (linux; android 6.0.1; sm-j500m) applewebkit/537.36 (khtml, like gecko) chrome/91.0.4472.101 mobile safari/537.36
					// #America/Belem#180#linux armv7l#Android#5,true,true#Qualcomm~Adreno (TM) 306


					if (($is_linux or $is_linux_ua) and ($is_linux != $is_linux_ua)) {

						//die(var_export($is_linux_ua));


						stopbadbots_stats_moreone('qbrowser');
						if ($stopbadbots_my_radio_report_all_visits == 'yes') {
							stopbadbots_alertme14($stopbadbots_ip);
						}
						stopbadbots_record_log('Blocked Fake Browser');
						header('HTTP/1.1 403 Forbidden');
						header('Status: 403 Forbidden');
						header('Connection: Close');
						die();
					}
				}
			}

			// $stopbadbots_is_human = '1';
		}
		if (!empty($stopbadbots_fingerprint_filed))
			$stopbadbots_is_human = '1';
	} else {
		$stopbadbots_is_human = '1';
	}
} // if($stopbadbots_engine_option != 'minimal'){
else
	$stopbadbots_is_human = '1';


// -----------------End step 1----------------------



add_action('wp_ajax_stopbadbots_get_ajax_data', 'stopbadbots_get_ajax_data');
// add_action('wp_ajax_nopriv_stopbadbots_get_ajax_data', 'stopbadbots_get_ajax_data');
add_action('wp_ajax_stopbadbots_add_blacklist', 'stopbadbots_add_blacklist');
add_action('wp_ajax_nopriv_stopbadbots_add_blacklist', 'stopbadbots_add_blacklist');
add_action('wp_ajax_stopbadbots_add_whitelist', 'stopbadbots_add_whitelist');
add_action('wp_ajax_nopriv_stopbadbots_add_whitelist', 'stopbadbots_add_whitelist');

add_action('wp_ajax_stopbadbots_add_blacklist', 'stopbadbots_add_blacklist');
add_action('wp_ajax_nopriv_stopbadbots_add_blacklist', 'stopbadbots_add_blacklist');

$stopbadbots_http_tools  = trim(get_site_option('stopbadbots_http_tools', ''));
$astopbadbots_http_tools = explode(PHP_EOL, $stopbadbots_http_tools);


if ($stopbadbots_is_human != '0') {
	add_action('wp_ajax_stopbadbots_grava_fingerprint', 'stopbadbots_grava_fingerprint');
	// If you wanted to also use the function for non-logged in users (in a theme for example)
	add_action('wp_ajax_nopriv_stopbadbots_grava_fingerprint', 'stopbadbots_grava_fingerprint');
}


if (!$stopbadbots_is_admin and $stopbadbots_block_spam_contacts == 'yes') {
	if (isset($_POST['stopbadbots_wpforms'])) {
		global $stopbadbots_my_radio_report_all_visits;
		if (stopbadbots_check_for_spam()) {
			stopbadbots_stats_moreone('qcon');
			if ($stopbadbots_my_radio_report_all_visits == 'yes') {
				stopbadbots_alertme9($stopbadbots_ip);
			}
			stopbadbots_record_log('Blocked Spam Contact');
			header('HTTP/1.1 403 Forbidden');
			header('Status: 403 Forbidden');
			header('Connection: Close');
			die();
		}
		if (stopbadbots_is_spammer($stopbadbots_ip)) {
			stopbadbots_stats_moreone('qcon');
			if ($stopbadbots_my_radio_report_all_visits == 'yes') {
				stopbadbots_alertme9($stopbadbots_ip);
			}
			stopbadbots_record_log('Blocked Spam Contact');
			header('HTTP/1.1 403 Forbidden');
			header('Status: 403 Forbidden');
			header('Connection: Close');
			die();
		}
	}
}
if ($stopbadbots_block_spam_login == 'yes') {
	add_action('wp_authenticate_user', 'stopbadbos_validate_login', 10, 2);
}
if (!$stopbadbots_is_admin  and $stopbadbots_block_spam_contacts == 'yes') {
	add_filter('wpcf7_validate', 'stopbadbots_check_4spammer', 10, 2);
}
if (!$stopbadbots_is_admin  and $stopbadbots_block_spam_comments == 'yes') {
	add_filter('preprocess_comment', 'stopbadbots_check_comment', 1);
}
$stopbadbots_now   = strtotime('now');
$stopbadbots_after = strtotime('now') + (3600);


add_filter('stopbadbots_custom_menu_order', 'stopbadbots_change_note_submenu_order');
if ($stopbadbots_is_admin) {
	if (isset($_GET['page'])) {
		$page = sanitize_text_field($_GET['page']);
		if ($page == 'stop_bad_bots_plugin' or $page == 'sbb_my-custom-submenu-page' or $page == 'sbb_my-custom-submenu-page2' or $page == 'sbb_my-custom-submenu-page3' or $page == 'stopbadbots_my-custom-submenu-page') {
			add_action('admin_head', 'stopbadbots_contextual_help');
		}
	}
}



function stopbadbots_custom_menu_order($menu_ord)
{

	//	var_dump($menu_ord);
	/*
    if (!$menu_ord) return true;
    return array(
     'index.php', // this represents the dashboard link
     'edit.php?post_type=events', // this is a custom post type menu
     'edit.php?post_type=news', 
     'edit.php?post_type=articles', 
     'edit.php?post_type=faqs', 
     'edit.php?post_type=mentors',
     'edit.php?post_type=testimonials',
     'edit.php?post_type=services',
     'edit.php?post_type=page', // this is the default page menu
     'edit.php', // this is the default POST admin menu 
 );
 */
}
/*
add_filter('stopbadbots_custom_menu_order', 'stopbadbots_custom_menu_order');
add_filter('menu_order', 'stopbadbots_custom_menu_order');
*/

/*
add_filter( 'stopbadbots_custom_menu_order', '__return_true' );
 
add_filter( 'menu_order', 'stopbadbots_my_menu_order' );
*/

function stopbadbots_my_menu_order($menu_order)
{
	global $submenu;


	$settings = $submenu['options-general.php'];
	foreach ($settings as $key => $details) {
		if ($details[2] == 'blogging') {
			$index = $key;
		}
	}
	# Set the 'Blogging' menu below 'General'
	$submenu['options-general.php'][11] = $submenu['options-general.php'][$index];
	unset($submenu['options-general.php'][$index]);
	# Reorder the menu based on the keys in ascending order
	ksort($submenu['options-general.php']);
	# Return the new submenu order
	return $menu_order;
}


/* ------------------------------------- */
add_action('template_redirect', 'stopbadbots_record_log');
/* ------------------------------------- */

/* Functions */

function stopbadbots_contextual_help()
{
	// $myhelp = '<br>' .esc_attr__("Stop Bad Bots from stealing you.", "stopbadbots");
	$myhelp       = esc_attr__(
		'Read the StartUp guide at Stop Bad Bots Settings page. (WP Dashboard => Stop Bad Bots = Settings)',
		'stopbadbots'
	);
	$myhelp      .= '<br />';
	$myhelp      .= '<br />' . esc_attr__(
		'Go to Dashboard Page for more information, Online Guide and Support. (WP Dashboard => Stop Bad Bots = Dashboard)',
		'stopbadbots'
	);
	$myhelp      .= '<br />';
	$myhelp      .= '<br />' . esc_attr__(
		'Go to Visits Log Page for details about the visits. (WP Dashboard => Stop Bad Bots = Visits Log)',
		'stopbadbots'
	);
	$myhelp      .= '<br />';
	$myhelp      .= '<br />';
	$myhelp      .= esc_attr__('Visit the', 'stopbadbots');
	$myhelp      .= '&nbsp<a href="https://stopbadbots.com" target="_blank">';
	$myhelp      .= esc_attr__('plugin site', 'stopbadbots');
	$myhelp      .= '</a>&nbsp;';
	$myhelp      .= esc_attr__('for more details, Support and online guide.', 'stopbadbots');
	$myhelptable  = '<br />';



	$myhelptable .= 'This table is Ajax powered, eliminating the need for manual reloading. Simply click the "Reload Only Content" button or utilize the filters and column headers to update the table dynamically.';
	$myhelptable .= '<br />';

	$myhelptable .= '<br />';
	$myhelptable .= 'Filter:';
	$myhelptable .= '<br />';
	$myhelptable .= 'Write, for example, Denied or Googlebot';
	$myhelptable .= '<br />';
	$myhelptable .= '<br />';


	$myhelptable .= 'Main Response Codes:';
	$myhelptable .= '<br />';


	$myhelptable .= '200 = Normal (content is empty if is a bot)';
	$myhelptable .= '<br />';
	$myhelptable .= '403 = Forbidden (page content doesn\'t show)';
	$myhelptable .= '<br />';
	$myhelptable .= '404 = Page Not Found';
	$myhelptable .= '<br />';
	$myhelptable .= '<br />';

	$myhelptable .= 'Access:';
	$myhelptable .= '<br />';
	$myhelptable .= 'Masked: The actual page content is not being displayed. (not available in Minimal Engine Management option)';
	$myhelptable .= '<br />';
	$myhelptable .= 'Denied: With response code 403.';
	$myhelptable .= '<br />';
	$myhelptable .= 'OK = The page content is being displayed';
	$myhelptable .= '<br />';
	$myhelptable .= '<br />';

	$myhelptable .= 'Main Methods:';
	$myhelptable .= '<br />';
	$myhelptable .= 'GET is used to request data from a specified resource.';
	$myhelptable .= '<br />';
	$myhelptable .= 'POST is used to send data to a server to create/update a resource.';
	$myhelptable .= '<br />';
	$myhelptable .= 'HEAD is almost identical to GET, but without the response body.';
	$myhelptable .= '<br />';
	$myhelptable .= '<br />';
	$myhelptable .= 'URL BLANK:';
	$myhelptable .= '<br />';
	$myhelptable .= 'It is your Homepage.';
	$myhelptable .= '<br />';
	$myhelptable .= '<br />';
	$screen       = get_current_screen();



	//$myhelptable .= '<br />';
	//$myhelptable .= 'id: '.$screen->id;
	//$myhelptable .= '<br />';

	if (isset($screen->id) and $screen->id == 'stop-bad-bots_page_stopbadbots_my-custom-submenu-page') {

		$screen->add_help_tab(
			array(
				'id'      => 'stopbadbots-visitors-log',
				'title'   => esc_attr__('Visits Log', 'stopbadbots'),
				'content' => '<p>' . $myhelptable . '</p>',
			)
		);

		$screen->add_help_tab(
			array(
				'id'      => 'stopbadbots-overview-tab',
				'title'   => esc_attr__('Overview', 'stopbadbots'),
				'content' => '<p>' . $myhelp . '</p>',
			)
		);
	} else {
		$screen->add_help_tab(
			array(
				'id'      => 'stopbadbots-overview-tab',
				'title'   => esc_attr__('Overview', 'stopbadbots'),
				'content' => '<p>' . $myhelp . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 'stopbadbots-visitors-log',
				'title'   => esc_attr__('Visits Log', 'stopbadbots'),
				'content' => '<p>' . $myhelptable . '</p>',
			)
		);
	}



	return;
}

function stopbadbots_adm_enqueue_scripts2()
{
	global $stopbadbots_current_screen;
	wp_enqueue_script('wp-pointer');
	require_once ABSPATH . 'wp-admin/includes/screen.php';
	$myscreen            = get_current_screen();
	$stopbadbots_current_screen = $myscreen->id;
	$dismissed_string    = get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true);
	// $dismissed = explode(',', (string) get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true));
	// if (in_array('plugins', $dismissed)) {

	if (!empty($dismissed_string)) {
		$r = update_option('stopbadbots_was_activated', '0');
		if (!$r) {
			add_option('stopbadbots_was_activated', '0');
		}
		return;
	}
	if (get_option('stopbadbots_was_activated', '0') == '1') {
		add_action('admin_print_footer_scripts', 'stopbadbots_admin_print_footer_scripts');
	}
}

function stopbadbots_admin_print_footer_scripts()
{
	global $stopbadbots_current_screen;

	$pointer_content  = '<h3>' . esc_attr__('Open Stop Bad Bots Plugin Here!', 'stopbadbots') . '</h3>';
	$pointer_content .= '<p>' . esc_attr__('Just Click Over Stop Bad Bots, then Go To Settings=>StartUp Guide.', 'stopbadbots') . '</p>';



	$allowed_atts = array();


	$my_allowed = array('h3' => array(), 'p' => array());


?>
	<script type="text/javascript">
		//<![CDATA[
		// setTimeout( function() { this_pointer.pointer( 'close' ); }, 400 );
		jQuery(document).ready(function($) {
			$('#toplevel_page_stop_bad_bots_plugin').pointer({
				content: '<?php echo wp_kses($pointer_content, $my_allowed); ?>',
				position: {
					edge: 'left',
					align: 'right'
				},
				close: function() {
					// Once the close button is hit
					$.post(ajaxurl, {
						pointer: '<?php echo esc_attr($stopbadbots_current_screen); ?>',
						action: 'dismiss-wp-pointer'
					});
				}
			}).pointer('open');
			/* $('.wp-pointer-undefined .wp-pointer-arrow').css("right", "50px"); */
		});
		//]]>
	</script>
<?php
}


function stopbadbots_go_pro_hide()
{
	$today = date('Ymd', strtotime('+07 days'));
	if (!update_option('stopbadbots_go_pro_hide', $today)) {
		add_option('stopbadbots_go_pro_hide', $today);
	}
}


function stopbadbots_create_httptools()
{
	$tools_list = array(
		'4D_HTTP_Client',
		'android-async-http',
		'axios',
		'andyhttp',
		'Aplix',
		'akka-http',
		'attohttpc',
		'curl',
		'CakePHP',
		'Cowblog',
		'DAP/NetHTTP',
		'Dispatch',
		'fasthttp',
		'FireEyeHttpScan',
		'Go-http-client',
		'Go1.1packagehttp',
		'Go 1.1 package http',
		'Go http package',
		'Go-http-client',
		'Gree_HTTP_Loader',
		'grequests',
		'GuzzleHttp',
		'hyp_http_request',
		'HTTPConnect',
		'http generic',
		'Httparty',
		'HTTPing',
		'http-ping',
		'http.rb/',
		'HTTPREAD',
		'Java-http-client',
		'Jodd HTTP',
		'raynette_httprequest',
		'java/',
		'kurl',
		'Laminas_Http_Client',
		'libsoup',
		'lua-resty-http',
		'mozillacompatible',
		'nghttp2',
		'mio_httpc',
		'Miro-HttpClient',
		'php/',
		'phpscraper',
		'PHX HTTP',
		'PHX HTTP Client',
		'python-requests',
		'Python-urllib',
		'python-httpx',
		'restful',
		'rpm-bot',
		'RxnetHttp',
		'scalaj-http',
		'SP-Http-Client',
		'Stilo OMHTTP',
		'tiehttp',
		'Valve/Steam',
		'Wget',
		'WP-URLDetails',
		'Zend_Http_Client',
		'ZendHttpClient',
	);

	$text = '';
	for ($i = 0; $i < count($tools_list); $i++) {
		$text .= $tools_list[$i] . PHP_EOL;
	}
	if (!add_option('stopbadbots_http_tools', $text)) {
		update_option('stopbadbots_http_tools', $text);
	}
}
function stopbadbots_create_whitelist()
{

	if (!current_user_can('administrator')) {
		return;
	}


	$mywhitelist = array(
		'AOL',
		'Baidu',
		'Bingbot',
		'msn',
		'DuckDuck',
		'facebook',
		'GTmetrix',
		'google',
		'Lighthouse',
		'msn',
		'paypal',
		'Stripe',
		'SiteUptime',
		'Teoma',
		'Yahoo',
		'slurp',
		'seznam',
		'Twitterbot',
		'webgazer',
		'Yandex',
	);
	$text        = '';
	for ($i = 0; $i < count($mywhitelist); $i++) {
		$text .= $mywhitelist[$i] . PHP_EOL;
	}
	if (!add_option('stopbadbots_string_whitelist', $text)) {
		update_option('stopbadbots_string_whitelist', $text);
	}
}
function stopbadbots_add_temp_ip()
{
	global $wpdb;
	global $stopbadbots_ip;
	$botflag    = '6';
	$table_name = $wpdb->prefix . 'sbb_badips';

	/*
	$results9  = $wpdb->get_results(
		$wpdb->prepare("SELECT  * FROM `$table_name` 
     WHERE botip = %s limit 1",
			$stopbadbots_ip
		)
	);
	*/
	$results9 = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE botip = %s LIMIT 1", $table_name, $stopbadbots_ip));





	if (count($results9) > 0) {
		return;
	}


	/*
	CREATE TABLE `wp_sbb_badips` (
		`id` mediumint(9) NOT NULL,
		`botip` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`botobs` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`botstate` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`botblocked` mediumint(9) NOT NULL,
		`botdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
		`added` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`botflag` varchar(1) COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`botcountry` varchar(2) COLLATE utf8mb4_unicode_520_ci NOT NULL
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
	*/
	/*
	$r = $wpdb->get_results(
		$wpdb->prepare(
			"INSERT INTO `$table_name` 
			(botip, botstate, botflag, added)		
		VALUES (%s, 'Enabled' , %s , 'Temp')",
			$stopbadbots_ip,
			$botflag
		)
	);
	*/

	// botobs, botblocked e botcountry
	/*
	$r = $wpdb->get_results($wpdb->prepare(
		"INSERT INTO `$table_name` 
		(botip, botobs, botstate, botblocked, added, botflag, botcountry)		
		VALUES (%s, '', 'Enabled', 0, 'Temp', %s, '')",
		$stopbadbots_ip,
		$botflag
	)
	);
	*/
	$r = $wpdb->query($wpdb->prepare("INSERT INTO %i (botip, botobs, botstate, botblocked, added, botflag, botcountry) VALUES (%s, '', 'Enabled', 0, 'Temp', %s, '')", $table_name, $stopbadbots_ip, $botflag));
}



function stopbadbots_ajaxurl()
{
	echo '<script type="text/javascript">
           var ajaxurl = "' . esc_attr(admin_url('admin-ajax.php')) . '";
         </script>';
}

function stopbadbots_get_ajax_data()
{
	if (current_user_can('administrator')) {
		require_once 'server_processing.php';
	} else {
		// Se o usuário não for um administrador, retorna uma mensagem de erro
		wp_die('No permissions!');
	}

	wp_die();
}




function stopbadbots_final_step()
{

	global $stopbadbots_ip;
	global $stopbadbots_is_human;
	global $stopbadbots_rate404_limiting;
	// global $stopbadbots_radio_limit_visits;
	global $stopbadbots_limit_visits;
	global $stopbadbots_rate_limiting;
	global $stopbadbots_my_radio_report_all_visits;
	global $stopbadbots_rate_limiting_day;
	global $stopbadbots_userAgentOri;
	global $stopbadbots_maybe_search_engine;
	global $stopbadbots_is_admin;

	if ($stopbadbots_is_admin  or stopbadbots_block_whitelist_IP()) {
		return;
	}
	if ($stopbadbots_maybe_search_engine) {
		return;
	}
	if (is_404()) {
		$stopbadbots_response = '404';
	} else {
		$stopbadbots_response = http_response_code();
	}
	if ($stopbadbots_response == '404') {
		// Excess 404
		if ($stopbadbots_rate404_limiting != 'unlimited' and $stopbadbots_limit_visits == 'yes') {
			if (stopbadbots_howmany_visit_404($stopbadbots_rate404_limiting) >= $stopbadbots_rate404_limiting and stopbadbots_howmany_visit_200() < 1) {
				stopbadbots_stats_moreone('qrate');
				// stopbadbots_add_blacklist($stopbadbots_ip);
				stopbadbots_add_temp_ip();
				if ($stopbadbots_my_radio_report_all_visits == 'yes') {
					stopbadbots_alertme13($stopbadbots_userAgentOri);
				}
				stopbadbots_response('Exceed 404 Rating Limit');
			}
		}
	}
	if ($stopbadbots_limit_visits == 'yes' and !$stopbadbots_is_admin  and !stopbadbots_block_whitelist_string() and !stopbadbots_block_whitelist_IP()) {
		if ($stopbadbots_rate_limiting == 'unlimited' or $stopbadbots_is_human == '1') {
			$stopbadbots_rate_limiting = 999999;
		}
		if (stopbadbots_howmany_bots_visit() > $stopbadbots_rate_limiting) {
			stopbadbots_moreone2($stopbadbots_ip); // +1
			stopbadbots_stats_moreone('qrate');
			if ($stopbadbots_my_radio_report_all_visits == 'yes') {
				stopbadbots_alertme13($stopbadbots_userAgentOri);
			}
			stopbadbots_add_temp_ip();
			stopbadbots_response('Rate Limit');
		}
	}
	if ($stopbadbots_limit_visits == 'yes' and !$stopbadbots_is_admin  and !stopbadbots_block_whitelist_string() and !stopbadbots_block_whitelist_IP()) {
		$quant = 999999;
		switch ($stopbadbots_rate_limiting_day) {
			case 1:
				$quant = 5;
				break;
			case 2:
				$quant = 10;
				break;
			case 3:
				$quant = 20;
				break;
			case 4:
				$quant = 50;
				break;
			case 5:
				$quant = 100;
				break;
		}
		if ($stopbadbots_is_human == '1') {
			$quant = 10000;
		}
		if (stopbadbots_howmany_bots_visit2() > $quant) {
			stopbadbots_moreone2($stopbadbots_ip); // +1
			stopbadbots_stats_moreone('qrate');
			if ($stopbadbots_my_radio_report_all_visits == 'yes') {
				stopbadbots_alertme13($stopbadbots_userAgentOri);
			}
			stopbadbots_add_temp_ip();
			stopbadbots_response('Rate Limit');
		}
	}
}

function stopbadbots_include_scripts()
{
	wp_enqueue_script('jquery');
	if (!class_exists('TM_Builder_Core')) {
		wp_enqueue_script('jquery-ui-core');
	}
	wp_register_script(
		'sbb-scripts',
		STOPBADBOTSURL .
			'assets/js/stopbadbots_fingerprint.js',
		array('jquery'),
		null,
		true
	); // true = footer
	wp_enqueue_script('sbb-scripts');
}

function stopbadbots_template_include($template)
{
	// return STOPBADBOTSPATH . 'template/stopbadbots_content_click.php';
}


function stopbadbots_add_menu_items()
{
	$stopbadbots_table_page = add_submenu_page(
		'stop_bad_bots_plugin', // $parent_slug
		'Bad Bots Table', // string $page_title
		'Bad Bots Table', // string $menu_title
		'manage_options', // string $capability
		'sbb_my-custom-submenu-page',
		'stopbadbots_render_list_page'
	);
	add_action("load-$stopbadbots_table_page", 'stopbadbots_screen_options');
	$stopbadbots_table_page = add_submenu_page(
		'stop_bad_bots_plugin', // $parent_slug
		'Bad IPs Table', // string $page_title
		'Bad IPs Table', // string $menu_title
		'manage_options', // string $capability
		'sbb_my-custom-submenu-page2',
		'stopbadbots_render_list_page2'
	);
	add_action("load-$stopbadbots_table_page", 'stopbadbots_screen_options2');
	$stopbadbots_table_page = add_submenu_page(
		'stop_bad_bots_plugin', // $parent_slug
		'Bad Referer Table', // string $page_title
		'Bad Referer Table', // string $menu_title
		'manage_options', // string $capability
		'sbb_my-custom-submenu-page3',
		'stopbadbots_render_list_page3'
	);





	add_action("load-$stopbadbots_table_page", 'stopbadbots_screen_options3');
	//
	// add_submenu_page('car_dealer_plugin', 'Team', 'Team', 'manage_options', 'md-team', 'cardealer_team_callback');
}
function stopbadbots_gopro_callback9()
{
	echo '<script type="text/javascript">';
	echo 'window.location = "https://stopbadbots.com/premium/";';
	echo '</script>';
}
function stopbadbots_add_menu_items9()
{
	global $stopbadbots_checkversion;
	if (empty($stopbadbots_checkversion)) {
		$sbb_gopro_page = add_submenu_page(
			'stop_bad_bots_plugin', // $parent_slug
			'Go Pro', // string $page_title
			'<font color="#FF6600">Go Pro</font>', // string $menu_title
			'manage_options', // string $capability
			'sbb_my-custom-submenu-page9',
			'stopbadbots_gopro_callback9'
		);
		add_action("load-$sbb_gopro_page", 'stopbadbots_screen_options9');
	}
}
function stopbadbots_change_note_submenu_order($menu_ord)
{
	global $submenu;
	function stopbadbots_str_replace_json($search, $replace, $subject)
	{
		return json_decode(str_replace($search, $replace, wp_json_encode($subject)), true);
	}
	$key     = 'Stop Bad Bots';
	$val     = 'Dashboard';
	$submenu = stopbadbots_str_replace_json($key, $val, $submenu);
}
function stopbadbots_screen_options()
{
	global $stopbadbots_table_page;
	$screen = get_current_screen();
	if (trim($screen->id) != 'stop-bad-bots_page_sbb_my-custom-submenu-page') {
		return;
	}
	$args = array(
		'label'   => esc_attr__('Bots per page', 'stopbadbots'),
		'default' => 10,
		'option'  => 'stopbadbots_per_page',
	);
	add_screen_option('per_page', $args);
}
function stopbadbots_screen_options2()
{
	global $stopbadbots_table_page;
	$screen = get_current_screen();
	if (trim($screen->id) != 'stop-bad-bots_page_sbb_my-custom-submenu-page2') {
		return;
	}
	$args = array(
		'label'   => esc_attr__('IPs per page', 'stopbadbots'),
		'default' => 10,
		'option'  => 'stopbadbots_per_page',
	);
	add_screen_option('per_page', $args);
}
function stopbadbots_screen_options3()
{
	global $stopbadbots_table_page;
	$screen = get_current_screen();
	if (trim($screen->id) != 'stop-bad-bots_page_sbb_my-custom-submenu-page3') {
		return;
	}
	$args = array(
		'label'   => esc_attr__('Bad Referers per page', 'stopbadbots'),
		'default' => 10,
		'option'  => 'stopbadbots_per_page',
	);
	add_screen_option('per_page', $args);
}
function stopbadbots_screen_options9()
{
	global $stopbadbots_table_page;
	$screen = get_current_screen();
	if (trim($screen->id) != 'stop-bad-bots_page_sbb_my-custom-submenu-page9') {
		return;
	}
	$args = array(
		'label'   => esc_attr__('Bad Referers per page', 'stopbadbots'),
		'default' => 10,
		'option'  => 'stopbadbots_per_page',
	);
	add_screen_option('per_page', $args);
}
function stopbadbots_set_screen_options($status, $option, $value)
{
	if ('stopbadbots_per_page' == $option) {
		return $value;
	}
}
function stopbadbots_alertme($stopbadbots_userAgentOri)
{
	global $stopbadbotsserver, $stopbadbots_found, $stopbadbots_admin_email, $stopbadbots_ip;
	$subject   = esc_attr__('Detected Bot on', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Bot was detected and blocked.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('User Agent', 'stopbadbots') . '........: ' . $stopbadbots_userAgentOri;
	$message[] = esc_attr__('Robot IP Address', 'stopbadbots') . '..: ' . $stopbadbots_ip;
	$message[] = esc_attr__('String Found:', 'stopbadbots') . '...... ' . $stopbadbots_found;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme2($stopbadbots_ip)
{
	global $stopbadbotsserver, $stopbadbots_found, $stopbadbots_admin_email;
	$subject   = esc_attr__('Detected Bot on', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Bot was detected and blocked.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('Robot IP Address', 'stopbadbots') . '..: ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme3($stopbadbots_string)
{
	global $stopbadbotsserver, $stopbadbots_found, $stopbadbots_admin_email, $stopbadbots_ip;
	$subject   = esc_attr__('Detected Bot on', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Malicious bot was detected and blocked by firewall.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('Robot IP Address', 'stopbadbots') . '..: ' . $stopbadbots_ip;
	$message[] = esc_attr__('Malicious String Found:', 'stopbadbots') . ' ' . $stopbadbots_string;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme4($stopbadbots_string)
{
	global $stopbadbotsserver, $stopbadbots_found, $stopbadbots_admin_email, $stopbadbots_ip;
	$subject   = esc_attr__('Detected Bot on', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Bad Referer Bot was detected and blocked by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('Referer String Found:', 'stopbadbots') . ' ' . $stopbadbots_string;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme5()
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip;
	$subject   = esc_attr__('Detected Possible Bot on', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Empty User Agent was detected and blocked by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme6()
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip;
	$subject   = esc_attr__('Detected Possible Bot on', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('PingBack Requested was detected and blocked by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme7()
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip;
	$subject   = esc_attr__('Detected Possible Bot on', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('User Enumeration was detected and blocked by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme8()
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip, $stopbadbots_userAgentOri;
	$subject   = esc_attr__('Detected Possible Bot on', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('False Google/Bing/Msn was detected and blocked by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__('Fake User Agent:', 'stopbadbots') . ' ' . $stopbadbots_userAgentOri;
	$message[] = '';
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme9()
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip, $stopbadbots_userAgentOri;
	$subject   = esc_attr__('Detected Spammer in Contact Form', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__('You can stop emails at the Notifications Settings Tab.', 'stopbadbots');
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme10()
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip, $stopbadbots_userAgentOri;
	$subject   = esc_attr__('Detected Spammer in Comments Form', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme11()
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip, $stopbadbots_userAgentOri;
	$subject   = esc_attr__('Detected bot in Login Form', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme12($httptool)
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip, $stopbadbots_userAgentOri;
	$subject   = esc_attr__('Detected bot using HTTP tools', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('HTTP tool:', 'stopbadbots') . ' ' . $httptool;
	$message[] = '';
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme13()
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip;
	$subject   = esc_attr__('Blocked Bot by Rate Limiting', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme14()
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip;
	$subject   = esc_attr__('Blocked Bot by Fake Browser (User Agent)', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function stopbadbots_alertme15()
{
	global $stopbadbotsserver, $stopbadbots_admin_email, $stopbadbots_ip;
	$subject   = esc_attr__('Blocked Visitor by Country (China, Cuba, North Korea)', 'stopbadbots') . ' ' . $stopbadbotsserver;
	$message[] = esc_attr__('Date', 'stopbadbots') . '..............: ' . date('F j, Y, g:i a');
	$message[] = esc_attr__('IP Found:', 'stopbadbots') . ' ' . $stopbadbots_ip;
	$message[] = '';
	$message[] = esc_attr__('eMail sent by Stop Bad Bots Plugin.', 'stopbadbots');
	$message[] = esc_attr__(
		'You can stop emails at the Notifications Settings Tab.',
		'stopbadbots'
	);
	$message[] = esc_attr__('Dashboard => Stop Bad Bots => Settings.', 'stopbadbots');
	$message[] = '';
	$message[] = esc_attr__('Visit us to learn how to get Weekly Updates and more features:', 'stopbadbots');
	$message[] = 'https://stopbadbots.com/premium';
	$msg       = join("\n", $message);
	wp_mail($stopbadbots_admin_email, $subject, $msg);
	return;
}
function sbb_findip()
{
	$ip     = '';
	$headers = array(
		'HTTP_CF_CONNECTING_IP', // CloudFlare
		'HTTP_CLIENT_IP', // Bill
		'HTTP_X_REAL_IP', // Bill
		'HTTP_X_FORWARDED', // Bill
		'HTTP_FORWARDED_FOR', // Bill
		'HTTP_FORWARDED', // Bill
		'HTTP_X_CLUSTER_CLIENT_IP', // Bill
		'HTTP_X_FORWARDED_FOR', // Squid and most other forward and reverse proxies
		'REMOTE_ADDR', // Default source of remote IP
	);
	for ($x = 0; $x < 8; $x++) {
		foreach ($headers as $header) {
			/*
			if(!array_key_exists($header, $_SERVER))
			continue;
			 */
			if (!isset($_SERVER[$header])) {
				continue;
			}
			$myheader = trim(sanitize_text_field($_SERVER[$header]));
			if (empty($myheader)) {
				continue;
			}
			$ip = trim(sanitize_text_field($_SERVER[$header]));
			if (empty($ip)) {
				continue;
			}
			if (false !== ($comma_index = strpos(sanitize_text_field($_SERVER[$header]), ','))) {
				$ip = substr($ip, 0, $comma_index);
			}
			// First run through. Only accept an IP not in the reserved or private range.
			if ($ip == '127.0.0.1') {
				$ip = '';
				continue;
			}
			if (0 === $x) {
				$ip = filter_var(
					$ip,
					FILTER_VALIDATE_IP,
					FILTER_FLAG_NO_RES_RANGE |
						FILTER_FLAG_NO_PRIV_RANGE
				);
			} else {
				$ip = filter_var($ip, FILTER_VALIDATE_IP);
			}
			if (!empty($ip)) {
				break;
			}
		}
		if (!empty($ip)) {
			break;
		}
	}
	if (!empty($ip)) {


		$ip = filter_var($ip, FILTER_VALIDATE_IP);

		if ($ip)
			return $ip;
		else
			return 'unknow';
	} else {
		return 'unknow';
	}
}
// $stopbadbots_ip = sbb_findip();
function stopbadbots_plugin_was_activated()
{
	global $stopbadbots_wp_blacklist;
	global $stopbadbots_update_http_tools;
	global $astopbadbots_http_tools;

	// if ( false ===  get_transient( 'bill_set_vendor' ) ) {
	// set_transient( 'bill_set_vendor', '1', 3600*24 );
	// }

	// $wtime = strtotime('-05 days');
	// update_option('stopbadbots_go_pro_hide', $wtime);
	// $stopbadbots_go_pro_hide =  $wtime;

	add_option('stopbadbots_was_activated', '1');
	update_option('stopbadbots_was_activated', '1');


	$stopbadbots_installed = trim(get_option('stopbadbots_installed', ''));
	if (empty($stopbadbots_installed)) {
		add_option('stopbadbots_installed', time());
		update_option('stopbadbots_installed', time());
	}

	if (empty($stopbadbots_http_tools) or $stopbadbots_update_http_tools == 'yes') {

		stopbadbots_create_httptools();
		stopbadbots_update_httptools($astopbadbots_http_tools);
	}

	stopbadbots_create_db();
	stopbadbots_upgrade_db();
	stopbadbots_create_db2();
	stopbadbots_upgrade_db2();
	stopbadbots_create_db3();
	stopbadbots_create_db4(); // visitors
	//stopbadbots_upgrade_db4();
	stopbadbots_create_db5();
	stopbadbots_create_db6(); // finger
	stopbadbots_create_db_stats();
	stopbadbots_sbb_populate_stats();

	// Pointer

	$r = update_option('stopbadbots_was_activated', '1');
	if (!$r) {
		add_option('stopbadbots_was_activated', '1');
	}
	$pointers = get_user_meta(get_current_user_id(), 'dismissed_wp_pointers', true);
	$pointers = ''; // str_replace( 'plugins', '', $pointers );
	update_user_meta(get_current_user_id(), 'dismissed_wp_pointers', $pointers);
}


function stopbadbots_fill_db_froma()
{



	global $wpdb, $wp_filesystem;
	$table_name      = $wpdb->prefix . 'sbb_blacklist';
	$charset_collate = $wpdb->get_charset_collate();
	$botsfile        = STOPBADBOTSPATH . 'assets/bots.txt';
	$botshandle      = @fopen($botsfile, 'r');

	if (!is_readable($botsfile)) {
		return false; // O arquivo existe, mas não pode ser lido
	}
	if ($botshandle) {
		// $delete = "delete from " . $table_name . " WHERE botblocked < 1 and botstate <> 'Disabled'";
		// $wpdb->query($delete);
		while (($botsbuffer = fgets($botshandle, 4096)) !== false) {
			$asplit = explode(',', $botsbuffer);
			if (count($asplit) < 3) {
				continue;
			}
			$botnickname = trim($asplit['0']);
			$botname     = trim($asplit['1']);
			$newbotflag  = trim($asplit['2']);
			if ($newbotflag == 'C') {
				$botflag = '6';
			} else {
				$botflag = '3';
			}
			/*
			$results9  = $wpdb->get_results(
				$wpdb->prepare("SELECT  * FROM `$table_name` 
			 WHERE botnickname = %s limit 1",
					$botnickname
				)
			);
			*/
			$results9 = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE botnickname = %s LIMIT 1", $table_name, $botnickname));


			if (count($results9) > 0 or empty($botnickname)) {
				continue;
			}


			/*
			CREATE TABLE `wp_sbb_blacklist` (
				`id` mediumint(9) NOT NULL,
				`botnickname` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL,
				`botname` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
				`boturl` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
				`botip` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
				`botobs` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
				`botstate` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
				`botblocked` mediumint(9) NOT NULL,
				`botdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
				`botflag` varchar(1) COLLATE utf8mb4_unicode_520_ci NOT NULL,
				`botua` text COLLATE utf8mb4_unicode_520_ci NOT NULL
			  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
*/

			/*
			$r = $wpdb->get_results(
				$wpdb->prepare(
					"INSERT INTO `$table_name` 
			    	(botua,botblocked,botobs,botip,boturl,botnickname, botname, botstate, botflag)
 
                VALUES ('', 0, '', '', '', %s, %s , 'Enabled', %s)",
					$botnickname,
					$botname,
					$botflag
				)
			);
			*/

			$wpdb->query($wpdb->prepare("INSERT INTO %i (botua, botblocked, botobs, botip, boturl, botnickname, botname, botstate, botflag, botdate) VALUES ('', 0, '', '', '', %s, %s, 'Enabled', %s, CURRENT_TIMESTAMP())", $table_name, $botnickname, $botname, $botflag));
		} // End Loop
		if (!feof($botshandle)) {
			// echo "Error: unexpected fgets() fail\n";
			return false;
		}
	} // end open
	else
		return false;
	fclose($botshandle);
	return true;
} // end Function stopbadbots_fill_db_froma


function stopbadbots_fill_db_froma2()
{
	global $wpdb;
	global $wp_filesystem;



	$table_name = $wpdb->prefix . 'sbb_badips';
	if (!stopbadbots_tablexist($table_name)) {
		stopbadbots_create_db2();
	}
	$charset_collate = $wpdb->get_charset_collate();
	$botsfile        = STOPBADBOTSPATH . 'assets/botsip.txt';
	if (!is_readable($botsfile)) {
		return false; // O arquivo existe, mas não pode ser lido
	}


	$botshandle = @fopen($botsfile, 'r');
	if ($botshandle) {
		while (($botsbuffer = fgets($botshandle, 4096)) !== false) {
			$asplit = explode(',', $botsbuffer);
			// echo count($asplit);
			if (count($asplit) < 3) {
				continue;
			}
			$botip      = trim($asplit['0']);
			$newbotflag = trim($asplit['1']);
			if ($newbotflag == 'C') {
				$botflag = '6';
			} else {
				$botflag = '3';
			}
			$botcountry = trim($asplit['2']);
			/*
			$results9  = $wpdb->get_results(
				$wpdb->prepare("SELECT  * FROM `$table_name` 
			 WHERE botip = %s limit 1",
					$botip
				)
			);
			*/
			$results9 = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE botip = %s LIMIT 1", $table_name, $botip));


			if (count($results9) > 0 or empty($botip)) {
				continue;
			}

			/*
				 $r = $wpdb->get_results(
					$wpdb->prepare(
						"INSERT INTO `$table_name` 
					(botblocked,botobs,botip, botstate, botflag, botcountry, added)
					VALUES (0, '',  %s, 'Enabled', %s, %s, 'Plugin')",
 					     $botip, 
						 $botflag,
						 $botcountry
					)
				);
				*/

			/*
				$wpdb->query(
					$wpdb->prepare(
						"INSERT INTO `$table_name` 
						(botip, botobs, botstate, botblocked, botdate, added, botflag, botcountry)
						VALUES (%s, '', 'Enabled', 0, CURRENT_TIMESTAMP(), 'Plugin', %s, %s)",
						$botip,
						$botflag,
						$botcountry
					)
				);
				*/
			$wpdb->query($wpdb->prepare("INSERT INTO %i (botip, botobs, botstate, botblocked, botdate, added, botflag, botcountry) VALUES (%s, '', 'Enabled', 0, CURRENT_TIMESTAMP(), 'Plugin', %s, %s)", $table_name, $botip, $botflag, $botcountry));
		} // End Loop
		if (!feof($botshandle)) {
			// echo "Error: unexpected fgets() fail\n";
			return false;
		}
	} // end open
	fclose($botshandle);
	return true;
} // end Function
function stopbadbots_fill_db_froma3()
{



	global $wpdb, $wp_filesystem;
	$table_name = $wpdb->prefix . 'sbb_badref';
	if (!stopbadbots_tablexist($table_name)) {
		stopbadbots_create_db3();
	}
	$charset_collate = $wpdb->get_charset_collate();
	$botsfile        = STOPBADBOTSPATH . 'assets/botsref.txt';
	$botshandle      = @fopen($botsfile, 'r');
	if (!is_readable($botsfile)) {
		return false; // O arquivo existe, mas não pode ser lido
	}
	if ($botshandle) {
		while (($botsbuffer = fgets($botshandle, 4096)) !== false) {
			$asplit = explode(',', $botsbuffer);
			if (count($asplit) < 1) {
				continue;
			}
			$botname  = trim($asplit['0']);

			/*
			$results9  = $wpdb->get_results(
				$wpdb->prepare("SELECT  * FROM `$table_name` 
			 WHERE botname = %s limit 1",
					$botname
				)
			);
			*/
			$results9 = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE botname = %s LIMIT 1", $table_name, $botname));

			if (count($results9) > 0 or empty($botname)) {
				continue;
			}

			/*

			CREATE TABLE `wp_sbb_badref` (
			`id` mediumint(9) NOT NULL,
			`botname` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`botstate` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`botblocked` mediumint(9) NOT NULL,
			`botdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
			`added` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`botobs` text COLLATE utf8mb4_unicode_520_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
						$r = $wpdb->get_results(
				$wpdb->prepare(
					"INSERT INTO `$table_name` 
				(botobs,botblocked,botname, botstate, added) 
                VALUES ('', 0, %s, 'Enabled', 'Plugin')",
					$botname
				));
				*/


			$wpdb->query($wpdb->prepare("INSERT INTO %i (botname, botstate, botblocked, botdate, added, botobs) VALUES (%s, 'Enabled', 0, CURRENT_TIMESTAMP(), 'Plugin', '')", $table_name, $botname));
		} // End Loop
		if (!feof($botshandle)) {
			// echo "Error: unexpected fgets() fail\n";
			return false;
		}
	} // end open
	fclose($botshandle);
	return true;
} // end Function

function stopbadbots_create_db()
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	// creates my_table in database if not exists
	$table           = $wpdb->prefix . 'sbb_blacklist';

	if (stopbadbots_tablexist($table)) {
		return;
	}


	$charset_collate = $wpdb->get_charset_collate();
	$sql             = "CREATE TABLE $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `botnickname` varchar(30) NOT NULL,
        `botname` text NOT NULL,
        `boturl` text NOT NULL,
        `botip` varchar(100) NOT NULL,
        `botobs` text NOT NULL,
        `botstate` varchar(10) NOT NULL,
        `botblocked` mediumint(9) NOT NULL,
        `botdate` timestamp NOT NULL,
        `botflag` varchar(1) NOT NULL,
        `botua` text NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`botnickname`)
    ) $charset_collate;";
	// KEY `botnickname` (`botnickname`)
	dbDelta($sql);
}
function stopbadbots_create_db2()
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	// creates my_table in database if not exists
	$table = $wpdb->prefix . 'sbb_badips';
	if (stopbadbots_tablexist($table)) {
		return;
	}
	$charset_collate = $wpdb->get_charset_collate();
	$sql             = "CREATE TABLE $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `botip` varchar(100) NOT NULL,
        `botobs` text NOT NULL,
        `botstate` varchar(10) NOT NULL,
        `botblocked` mediumint(9) NOT NULL,
        `botdate` timestamp NOT NULL,
        `added` varchar(30)NOT NULL,
        `botflag` varchar(1) NOT NULL,
        `botcountry` varchar(2) NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`botip`)
    ) $charset_collate;";
	// KEY `botnickname` (`botnickname`)
	dbDelta($sql);
}
function stopbadbots_create_db3()
{
	// sbb_blockedref
	/*
	CREATE TABLE `sbb_blockedref` (
	`id` int(11) NOT NULL,
	`name` varchar(50) NOT NULL,
	`status` varchar(1) NOT NULL,
	`flag` varchar(1) NOT NULL,
	`date` datetime NOT NULL,
	`added` varchar(30)NOT NULL,
	`obs` text NOT NULL
	 */
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	// creates my_table in database if not exists
	$table = $wpdb->prefix . 'sbb_badref';
	if (stopbadbots_tablexist($table)) {
		return;
	}
	$charset_collate = $wpdb->get_charset_collate();
	$sql             = "CREATE TABLE $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `botname` varchar(100) NOT NULL,
        `botstate` varchar(10) NOT NULL,
        `botblocked` mediumint(9) NOT NULL,
        `botdate` timestamp NOT NULL,
        `added` varchar(30)NOT NULL,
        `botobs` text NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`botname`)
    ) $charset_collate;";
	dbDelta($sql);
}
function stopbadbots_create_db4()
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	// creates my_table in database if not exists
	$table = $wpdb->prefix . 'sbb_visitorslog';
	if (stopbadbots_tablexist($table)) {
		return;
	}
	$charset_collate = $wpdb->get_charset_collate();
	$sql             = "CREATE TABLE $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `ip` text NOT NULL,
        `date` timestamp NOT NULL,
        `human` varchar(10) NOT NULL,
        `response` varchar(5) NOT NULL,
        `bot` varchar(1) NOT NULL,
        `method` varchar(10) NOT NULL,
        `url` text NOT NULL,
        `referer` text NOT NULL,  
        `ua` TEXT NOT NULL,
        `access` varchar(10) NOT NULL,
        `reason` text NOT NULL,
    UNIQUE (`id`)
    ) $charset_collate;";
	dbDelta($sql);

	$sql = 'CREATE INDEX ip ON ' . $table . ' (`ip`(40))';
	dbDelta($sql);

	// $sql = "CREATE INDEX bot ON " . $table . " (bot)";
	$sql = 'CREATE INDEX bot ON ' . $table . ' (`bot`(1))';
	dbDelta($sql);

	// $sql = "CREATE INDEX human ON " . $table . " (human)";
	$sql = 'CREATE INDEX human ON ' . $table . ' (`human`(10))';
	dbDelta($sql);
}
function stopbadbots_create_db5()
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	// creates my_table in database if not exists
	$table = $wpdb->prefix . 'sbb_http_tools';
	if (stopbadbots_tablexist($table)) {
		return;
	}
	$charset_collate = $wpdb->get_charset_collate();
	$sql             = "CREATE TABLE $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `quant` int NOT NULL,
        `flag` varchar(1) NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`name`)
    ) $charset_collate;";
	dbDelta($sql);
}
function stopbadbots_create_db6()
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	// creates my_table in database if not exists
	$table = $wpdb->prefix . 'sbb_fingerprint';
	if (stopbadbots_tablexist($table)) {
		return;
	}
	$charset_collate = $wpdb->get_charset_collate();
	$sql             = "CREATE TABLE $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `ip` varchar(50) NOT NULL,
        `fingerprint` text NOT NULL,
        `deny` int(4) NOT NULL,
        `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (`id`),
    UNIQUE (`ip`)
    ) $charset_collate;";
	dbDelta($sql);
}
function stopbadbots_upgrade_db()
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_blacklist';
	if (!stopbadbots_tablexist($table_name)) {
		return;
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'botblocked'";
	$wpdb->query($wpdb->prepare("SHOW COLUMNS FROM %i LIKE 'botblocked'", $table_name));


	if (empty($wpdb->num_rows)) {
		//$alter = 'ALTER TABLE ' . $table_name . ' ADD botblocked mediumint(9) NOT NULL';
		ob_start();
		$wpdb->query($wpdb->prepare("ALTER TABLE %i ADD botblocked mediumint(9) NOT NULL", $table_name));
		ob_end_clean();
	}
	// Upgrade to new names
	// $stopbadbots_option_name[0] = 'stop_bad_bots_active';
	$stopbadbots_option_name[1] = 'my_blacklist';
	$stopbadbots_option_name[2] = 'my_email_to';
	$stopbadbots_option_name[3] = 'my_radio_report_all_visits';
	for ($i = 1; $i < 4; $i++) {
		$stopbadbots_option   = get_site_option($stopbadbots_option_name[$i]);
		$stopbadbots_new_name = 'stopbadbots_' . $stopbadbots_option_name[$i];
		add_site_option($stopbadbots_new_name, $stopbadbots_option);
		// update_site_option();
		delete_option($stopbadbots_option_name[$i]);
		// For site options in Multisite
		delete_site_option($stopbadbots_option_name[$i]);
	}
}
function stopbadbots_upgrade_db2()
{
	global $wpdb, $wp_filesystem;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_badips';
	if (!stopbadbots_tablexist($table_name)) {
		return;
	}
	//$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'botcountry'";
	$wpdb->query($wpdb->prepare("SHOW COLUMNS FROM %i LIKE 'botcountry'", $table_name));

	if (empty($wpdb->num_rows)) {
		//$alter = 'ALTER TABLE ' . $table_name . ' ADD botcountry varchar(2) NOT NULL';
		ob_start();
		$wpdb->query($wpdb->prepare("ALTER TABLE %i ADD botcountry varchar(2) NOT NULL", $table_name));
		ob_end_clean();
	}
	$charset_collate = $wpdb->get_charset_collate();
	$botsfile        = STOPBADBOTSPATH . 'assets/botsip.txt';
	$botshandle      = @fopen($botsfile, 'r');
	if ($botshandle) {
		while (($botsbuffer = fgets($botshandle, 4096)) !== false) {
			$asplit = explode(',', $botsbuffer);
			if (count($asplit) < 3) {
				continue;
			}
			$botip      = trim($asplit['0']);
			$botcountry = trim($asplit['2']);

			/*
			$query      = 'SELECT * FROM ' . $table_name . " where botip = '" . $botip .
				"' limit 1";
			$results9   = $wpdb->get_results( sanitize_text_field( $query ) );
			*/

			$results9 = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM %i WHERE botip = %s LIMIT 1",
					$table_name,
					$botip
				)
			);


			if (count($results9) < 1 or empty($botip)) {
				continue;
			}

			/*
			$query = 'UPDATE ' . $table_name .
				" SET botcountry = '" . $botcountry . "'
                WHERE botip = '" . $botip . "' LIMIT 1";
			$r     = $wpdb->get_results( sanitize_text_field( $query ) );
			*/
			$query = $wpdb->prepare(
				"UPDATE %i SET botcountry = %s WHERE botip = %s LIMIT 1",
				$table_name,
				$botcountry,
				$botip
			);
			$r = $wpdb->get_results($query);
		} // End Loop
		if (!feof($botshandle)) {
			// echo "Error: unexpected fgets() fail\n";
			return false;
		}
	} // end open
	fclose($botshandle);
}
/*
function stopbadbots_upgrade_db4() {
	global $wpdb, $wp_filesystem;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	// $table_name = $wpdb->prefix . "sbb_badips";
	$table_name = $wpdb->prefix . 'sbb_visitorslog';
	if ( ! stopbadbots_tablexist( $table_name ) ) {
		return;
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'human'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( empty( $wpdb->num_rows ) ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD human varchar(10) NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'method'";
	$wpdb->query( sanitize_text_field( sanitize_text_field( $query ) ) );
	// VAR_DUMP($wpdb->num_rows);
	if ( empty( $wpdb->num_rows ) ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD method text NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'url'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( empty( $wpdb->num_rows ) ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD url text NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'referer'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( empty( $wpdb->num_rows ) ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD referer text NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'ua'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( empty( $wpdb->num_rows ) ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD ua text NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'access'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( empty( $wpdb->num_rows ) ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD access varchar(10) NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$alter = 'ALTER TABLE ' . $table_name . ' modify human varchar(10)';
	ob_start();
	$wpdb->query( sanitize_text_field( $alter ) );
	ob_end_clean();
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'reason'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( empty( $wpdb->num_rows ) ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD reason text NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$alter = 'ALTER TABLE ' . $table_name . ' MODIFY `ip` TEXT NOT NULL';
	ob_start();
	$wpdb->query( sanitize_text_field( $alter ) );
	ob_end_clean();
	// $wpdb->get_charset_collate();

	$query = "SELECT COUNT(1) indexExists FROM INFORMATION_SCHEMA.STATISTICS
    WHERE table_schema=DATABASE() AND table_name='" . $table_name . "' AND index_name='bot'";

	$result = $wpdb->get_var( sanitize_text_field( $query ) );
	if ( $result < 1 ) {
		$alter = 'CREATE INDEX bot ON ' . $table_name . ' (`bot`(1))';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}

	$query = "SELECT COUNT(1) indexExists FROM INFORMATION_SCHEMA.STATISTICS
    WHERE table_schema=DATABASE() AND table_name='" . $table_name . "' AND index_name='human'";

	$result = $wpdb->get_var( sanitize_text_field( $query ) );
	if ( $result < 1 ) {
		$alter = 'CREATE INDEX human ON ' . $table_name . ' (`human`(10))';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}

	$query = "SELECT COUNT(1) indexExists FROM INFORMATION_SCHEMA.STATISTICS
    WHERE table_schema=DATABASE() AND table_name='" . $table_name . "' AND index_name='ip'";

	$result = $wpdb->get_var( sanitize_text_field( $query ) );
	if ( $result < 1 ) {
		$alter = 'CREATE INDEX ip ON ' . $table_name . ' (`ip`(40))';
		$alter = 'ALTER TABLE ' . $table_name . ' ADD INDEX  (`ip`(40))';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
}
*/
/*
function stopbadbots_upgrade_stats() {
	global $wpdb, $wp_filesystem;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_stats';
	if ( ! stopbadbots_tablexist( $table_name ) ) {
		return;
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qfire'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qfire text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qref'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qref text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qua'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qua text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qping'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qping text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'quenu'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD quenu text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qother'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qother text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qlogin'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qlogin text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qcon'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qcon text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qcom'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qcom text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qfalseg'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qfalseg text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}

	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qtools'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qtools text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}

	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qbrowser'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qbrowser text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}

	$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'qrate'";
	$wpdb->query( sanitize_text_field( $query ) );
	if ( $wpdb->num_rows < 1 ) {
		$alter = 'ALTER TABLE ' . $table_name . ' ADD qrate text COLLATE utf8mb4_unicode_520_ci NOT NULL';
		ob_start();
		$wpdb->query( sanitize_text_field( $alter ) );
		ob_end_clean();
	}
}
*/

function stopbadbots_upgrade_fingerprint()
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_fingerprint';
	if (!stopbadbots_tablexist($table_name)) {
		return;
	}
	//$query = 'SHOW COLUMNS FROM ' . $table_name . " LIKE 'deny'";
	//$wpdb->query( sanitize_text_field( $query ) );
	$wpdb->query($wpdb->prepare("SHOW COLUMNS FROM %i LIKE 'deny'", $table_name));


	if (empty($wpdb->num_rows)) {
		//$alter = 'ALTER TABLE ' . $table_name . ' ADD deny int(4) NOT NULL';
		ob_start();
		//$wpdb->query( sanitize_text_field( $alter ) );
		$wpdb->query($wpdb->prepare("ALTER TABLE %i ADD deny int(4) NOT NULL", $table_name));
		ob_end_clean();
	}
}


function stopbadbots_moreone($stopbadbots_userAgentOri)
{
	global $stopbadbots_found, $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_blacklist';

	/*
	$query      = 'UPDATE ' . $table_name .
		" SET botblocked = botblocked+1 WHERE botnickname = '" . $stopbadbots_found . "'";
	$wpdb->query( sanitize_text_field( $query ) );
	*/

	$r = $wpdb->query(
		$wpdb->prepare(
			"UPDATE %i 
			SET botblocked = botblocked + 1
			WHERE botnickname = %s",
			$table_name,
			$stopbadbots_found
		)
	);
}

function stopbadbots_moreone_http($nametool)
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_http_tools';

	/*
	$query      = 'UPDATE ' . $table_name .
		" SET quant = quant+1 WHERE name = '" . $nametool . "'";
	$wpdb->query( sanitize_text_field( $query ) );
	*/

	$r = $wpdb->query(
		$wpdb->prepare(
			"UPDATE %i 
			SET quant = quant + 1
			WHERE name = %s",
			$table_name,
			$nametool
		)
	);
}
function stopbadbots_moreone2($stopbadbots_ip)
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_badips';
	/*
	$query      = 'UPDATE ' . $table_name .
		" SET botblocked = botblocked+1 WHERE botip = '" . $stopbadbots_ip . "'";
	$wpdb->query( sanitize_text_field( $query ) );
	*/

	$r = $wpdb->query(
		$wpdb->prepare(
			"UPDATE %i 
			SET botblocked = botblocked + 1
			WHERE botip = %s",
			$table_name,
			$stopbadbots_ip
		)
	);
}
function stopbadbots_moreone4($stopbadbotsreferer)
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_badref';

	/*
	$query      = 'UPDATE ' . $table_name .
		" SET botblocked = botblocked+1 WHERE botname = '" . $stopbadbotsreferer . "'";
	$wpdb->query( sanitize_text_field( $query ) );
	*/

	$r = $wpdb->query(
		$wpdb->prepare(
			"UPDATE %i 
			SET botblocked = botblocked + 1
			WHERE botname = %s",
			$table_name,
			$stopbadbotsreferer
		)
	);
}

/*
function stopbadbots_load_feedback3() {
	global $stopbadbots_request_url;

	$timestamp = get_option( 'stopbadbots_loaded_timestamp', '0' );
	$interval = time() - intval($timestamp);

    // deactivated if cheating...
    if ( $timestamp != '0' && $interval > 5  ) {

		update_option( 'stopbadbots_was_activated', '0' );
		
		update_option( 'stopbadbots_loaded_timestamp', '0' );

		$plugin_path = STOPBADBOTSPATH.'stopbadbots.php';
		//  ?? deactivate_plugins( $plugin_path );

		return;
	}
	

	$timestamp = get_option( 'stopbadbots_loaded_timestamp', '0' );
	if($timestamp == '0')
    	update_option( 'stopbadbots_loaded_timestamp', time() );


	
		if (strpos($stopbadbots_request_url, 'plugins.php') !== false) 
	{
		wp_register_style( 'bill-feedback-sbb-css', STOPBADBOTSURL. 'includes/feedback/feedback-plugin.css' );
		wp_enqueue_style( 'bill-feedback-sbb-css' );

		wp_register_script( 'bill-feedback-sbb-js', STOPBADBOTSURL.'includes/feedback/activated-manager.js' , array( 'jquery' ), STOPBADBOTSVERSION, true );
		wp_enqueue_script( 'bill-feedback-sbb-js' );
	}


}
*/

/*
function stopbadbots_include_file_after_plugins_loaded() {
	global $stopbadbots_request_url;
	if (strpos($stopbadbots_request_url, 'plugins.php') !== false) 
		require_once (STOPBADBOTSPATH . 'includes/feedback/activated-manager.php');
}
*/


if ($stopbadbots_is_admin) {



	// die(var_dump(get_option( 'stopbadbots_was_activated', '0' )));
	// activated...

	//


	if (get_option('stopbadbots_was_activated', '0') == '1') {


		/*
		$ahplugin = ABSPATH . 'wp-content/plugins/antihacker/antihacker.php';
		if (file_exists($ahplugin)) {
			// Plugin is installed 
		} else {
			// Plugin is not installed
			$BILLCLASS = 'ACTIVATED_STOPBADBOTS';
			// plugins.php
			if(!isset($_COOKIE[$BILLCLASS])){
				//add_action('wp_loaded', 'stopbadbots_load_feedback3');
				//add_action('plugins_loaded', 'stopbadbots_include_file_after_plugins_loaded');
				//add_action( 'admin_notices', 'stopbadbots_include_file_after_plugins_loaded' );

			}
		}
		*/


		//add_option( 'stopbadbots_was_activated', '0' );
		//update_option( 'stopbadbots_was_activated', '0' );


	} elseif (get_option('stopbadbots_was_activated', '0') == '0') {

		// pointer
		add_action('admin_enqueue_scripts', 'stopbadbots_adm_enqueue_scripts2');
	}
}

function stopbadbots_settings_init()
{
	register_setting('pluginPage', 'stopbadbots_settings', 'stopbadbots_my_field_sanitize');
	//register_setting( 'my_settings_group', 'my_option_name', 'my_sanitize_callback' );

	add_settings_section(
		'sbb_pluginPage_section',
		__(
			'Add new bad bot to the bad bots Table.',
			'stopbadbots'
		),
		'stopbadbots_settings_section_callback',
		'pluginPage'
	);
	add_settings_field(
		'stopbadbots_text_field_0',
		__('Bad Bot Nickname:', 'stopbadbots'),
		'stopbadbots_text_field_0_render',
		'pluginPage',
		'sbb_pluginPage_section'
	);
}

//function stopbadbots_my_field_sanitize($value) {
//	return sanitize_text_field($value);
//}

function stopbadbots_my_field_sanitize($value)
{
	return is_array($value) ? array_map('sanitize_text_field', $value) : sanitize_text_field($value);
}

function stopbadbots_settings2_init()
{
	register_setting('pluginPage2', 'stopbadbots_settings2', 'stopbadbots_my_field_sanitize');
	add_settings_section(
		'sbb_pluginPage2_section',
		__(
			'Add new bad IP to the bad IPs Table.',
			'stopbadbots'
		),
		'stopbadbots_settings_section2_callback',
		'pluginPage2'
	);
	add_settings_field(
		'stopbadbots_text_field_2',
		__('Bad Bot IP:', 'stopbadbots'),
		'stopbadbots_text_field_2_render',
		'pluginPage2',
		'sbb_pluginPage2_section'
	);
}
function stopbadbots_settings3_init()
{
	register_setting('pluginPage3', 'stopbadbots_settings3', 'stopbadbots_my_field_sanitize');
	add_settings_section(
		'sbb_pluginPage3_section',
		__(
			'Add new bad Referer to the bad Referer Table.',
			'stopbadbots'
		),
		'stopbadbots_settings_section3_callback',
		'pluginPage3'
	);
	add_settings_field(
		'stopbadbots_text_field_3',
		__('Bad Referer Name:', 'stopbadbots'),
		'stopbadbots_text_field_3_render',
		'pluginPage3',
		'sbb_pluginPage3_section'
	);
}
function stopbadbots_text_field_0_render()
{
	//$options = esc_attr(get_option( 'stopbadbots_settings' ));
	echo "<input type='text' name='stopbadbots_settings[sbb_input_nickname]' value=''>";
}
function stopbadbots_text_field_2_render()
{
	//$options = esc_attr(get_option( 'stopbadbots_settings2' ));
	echo "<input type='text' name='stopbadbots_settings2[sbb_input_ip])' value=''>";
}
function stopbadbots_text_field_3_render()
{
	//$options = esc_attr(get_option( 'stopbadbots_settings3' ));
	echo "<input type='text' name='stopbadbots_settings3[sbb_input_ref])' value=''>";
}
function stopbadbots_settings_section_callback()
{
	echo esc_attr__(
		'In addiction to default system table, you can add one or more string to the table.',
		'stopbadbots'
	);
	echo '<br />';
	echo esc_attr__('Example: SpiderBot (no case sensitive)', 'stopbadbots');
	echo '&nbsp;';
	echo esc_attr__('Just a piece of the name is enough.', 'stopbadbots');
	echo '&nbsp;';
	echo esc_attr__(
		'For example, if you put "bot" will block all bots with the string bot at user agent name.',
		'stopbadbots'
	);
	echo '&nbsp;';
	echo esc_attr__(
		'Attention: In this case, you will block also google bot because their name is GoogleBot.',
		'stopbadbots'
	);
	echo '<br />';
	echo '<b>';
	echo esc_attr__('Do not use special characters neither HTML', 'stopbadbots');
	echo '</b>';
	echo '<br />';
	echo esc_attr__(
		"Add one bad bot each time. The table don't accept duplicate nicknames.",
		'stopbadbots'
	);
}
function stopbadbots_settings_section2_callback()
{
	echo esc_attr__(
		'In addiction to default ip table, you can add one or more ip to the table.',
		'stopbadbots'
	);
	echo '<br />';
	echo esc_attr__(
		"Add one bad ip each time. The table don't accept duplicate ips.",
		'stopbadbots'
	);
	echo '<br />';
	echo esc_attr__(
		'Be carefull. This IP will be blocked to access your site.',
		'stopbadbots'
	);
}
function stopbadbots_settings_section3_callback()
{
	echo esc_attr__(
		'In addiction to default referer table, you can add one or more referers to the table.',
		'stopbadbots'
	);
	echo '<br />';
	echo esc_attr__(
		"Add one bad referer each time. The table don't accept duplicate names.",
		'stopbadbots'
	);
	echo '<br />';
	echo esc_attr__(
		'Be carefull. This Referer will be blocked to access your site. Do not use HTML',
		'stopbadbots'
	);
}
function stopbadbots_admin_notice__success()
{
	echo '<div class="notice notice-success is-dismissible">';
	echo '<p>';
	esc_attr_e('Bot included at table!', 'stopbadbots');
	echo '</p>';
	echo '</div>';
}
function stopbadbots_admin_notice__fail()
{
?>
	<div class="notice notice-error is-dismissible">
		<p>
			<?php
			esc_attr_e(
				'Fail to include bot! Check bot nickname and remember Duplicates are not allowed. ',
				'stopbadbots'
			);
			?>
		</p>
	</div>
<?php
}
function stopbadbots_admin_notice2__success()
{
?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_attr_e('IP included at table!', 'stopbadbots'); ?></p>
	</div>
<?php
}
function stopbadbots_admin_notice2__fail()
{
?>
	<div class="notice notice-error is-dismissible">
		<p>
			<?php
			esc_attr_e(
				'Fail to include IP! Check bot IP and remember Duplicates are not allowed. ',
				'stopbadbots'
			);
			?>
		</p>
	</div>
<?php
}
function stopbadbots_admin_notice3__success()
{
?>
	<div class="notice notice-success is-dismissible">
		<p><?php esc_attr_e('Referer included at table!', 'stopbadbots'); ?></p>
	</div>
<?php
}
function stopbadbots_admin_notice3__fail()
{
?>
	<div class="notice notice-error is-dismissible">
		<p>
			<?php
			esc_attr_e(
				'Fail to include Referer! Check referer name and remember Duplicates are not allowed. ',
				'stopbadbots'
			);
			?>
		</p>
	</div>
<?php
}
function stopbadbots_options_page()
{
?>
	<form action='options.php' method='post'>
		<h1>Stop Bad Bots Plugin</h1>
		<?php
		settings_fields('pluginPage');
		do_settings_sections('pluginPage');
		submit_button();
		?>
		<?php stopbadbots_update_db(); ?>
	</form>
<?php
}
function stopbadbots_options_page2()
{
?>
	<form action='options.php' method='post'>
		<h1>Stop Bad Bots Plugin</h1>
		<?php
		settings_fields('pluginPage2');
		do_settings_sections('pluginPage2');
		submit_button();
		?>
		<?php stopbadbots_update_db2(); ?>
	</form>
<?php
}
function stopbadbots_options_page3()
{
?>
	<form action='options.php' method='post'>
		<h1>Stop Bad Bots Plugin</h1>
		<?php
		settings_fields('pluginPage3');
		do_settings_sections('pluginPage3');
		submit_button();
		?>
		<?php stopbadbots_update_db3(); ?>
	</form>
<?php
}
function stopbadbots_update_db()
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'sbb_blacklist';
	if (!stopbadbots_tablexist($table_name)) {
		return;
	}
	$options = get_option('stopbadbots_settings');
	// var_dump($options);
	if (isset($options['sbb_input_nickname'])) {

		$nickname = sanitize_text_field($options['sbb_input_nickname']);

		if (!empty($nickname)) {

			/*
			$r = $wpdb->get_results(
				$wpdb->prepare(
					"INSERT INTO `$table_name` 
                (botnickname,botname,botstate,botflag,botdate) 
                VALUES (%s, %s , 'Enabled', '1' , now())",
					$nickname,
					$nickname
				)
			);

			CREATE TABLE `wp_sbb_blacklist` (
			`id` mediumint(9) NOT NULL,
			`botnickname` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`botname` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`boturl` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`botip` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`botobs` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`botstate` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`botblocked` mediumint(9) NOT NULL,
			`botdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
			`botflag` varchar(1) COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`botua` text COLLATE utf8mb4_unicode_520_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
			*/

			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO `$table_name` 
					(botnickname, botname, boturl, botip, botobs, botstate, botblocked, botdate, botflag, botua)
					VALUES (%s, %s, '', '', '', 'Enabled', 0, CURRENT_TIMESTAMP(), '1', '')",
					$nickname,
					$nickname
				)
			);
		} else {
			$r = false;
		}

		if (!empty($wpdb->last_error)) {
			stopbadbots_admin_notice__fail();
		} else {
			stopbadbots_admin_notice__success();
		}
		// clear sbb_input_nickname
		unset($options['sbb_input_nickname']);
		update_option('stopbadbots_settings', $options);
	}
	return;
}
function stopbadbots_update_db2()
{
	global $wpdb, $_POST;
	$table_name = $wpdb->prefix . 'sbb_badips';
	if (!stopbadbots_tablexist($table_name)) {
		return;
	}
	$options = get_option('stopbadbots_settings2');
	// var_dump($options);
	if (isset($options['sbb_input_ip'])) {
		$ip = $options['sbb_input_ip'];
		$r  = false;
		$ip = trim($ip);
		if (!empty($ip)) {
			if (filter_var($ip, FILTER_VALIDATE_IP)) {


				/*
				CREATE TABLE `wp_sbb_badips` (
					`id` mediumint(9) NOT NULL,
					`botip` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
					`botobs` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
					`botstate` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
					`botblocked` mediumint(9) NOT NULL,
					`botdate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
					`added` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL,
					`botflag` varchar(1) COLLATE utf8mb4_unicode_520_ci NOT NULL,
					`botcountry` varchar(2) COLLATE utf8mb4_unicode_520_ci NOT NULL
				  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
				$r = $wpdb->get_results(
					$wpdb->prepare(
						"INSERT INTO `$table_name` 
                    (botip,botstate,botflag,botdate,added) 
                    VALUES (%s, 'Enabled', '1' , now(), 'User')",
						$ip
					)
				);
				*/
				/*
				$wpdb->query(
					$wpdb->prepare(
						"INSERT INTO `$table_name` 
						(botnickname, botname, boturl, botip, botobs, botstate, botblocked, botdate, botflag, botua, added)
						VALUES ('', '', '', %s, '', 'Enabled', 0, CURRENT_TIMESTAMP(), '1', '', 'User')",
						$ip
					)
				);
				*/
				$r = $wpdb->get_results(
					$wpdb->prepare(
						"INSERT INTO `$table_name` 
						(botip, botstate, botflag, botdate, added) 
						VALUES (%s, 'Enabled', '1', now(), 'User')",
						sanitize_text_field($ip) // Sanitizing $ip before inserting into the query
					)
				);
			} else {
				$r = false;
			}

			if (empty($wpdb->last_error)) {
				stopbadbots_admin_notice2__success();
			} else {
				stopbadbots_admin_notice2__fail();
			}
		}
		// clear sbb_input_ip
		unset($options['sbb_input_ip']);
		update_option('stopbadbots_settings2', $options);
	}
	return;
}
function stopbadbots_update_db3()
{
	global $wpdb, $_POST;
	$table_name = $wpdb->prefix . 'sbb_badref';
	if (!stopbadbots_tablexist($table_name)) {
		return;
	}
	$options = get_option('stopbadbots_settings3');
	if (isset($options['sbb_input_ref'])) {
		$ref = sanitize_text_field($options['sbb_input_ref']);
		$r = false;
		if (!empty($ref)) {

			// $r = $wpdb->query(sanitize_text_field($query));
			/*
			$r = $wpdb->get_results(
				$wpdb->prepare(
					"INSERT INTO `$table_name` 
                (botname,botstate,botdate,added) 
                VALUES (%s, 'Enabled', now(), 'User')",
					$ref
				)
			);
			*/

			$wpdb->query(
				$wpdb->prepare(
					"INSERT INTO `$table_name` 
					(botname, botstate, botblocked, botdate, added, botobs)
					VALUES (%s, 'Enabled', 0, CURRENT_TIMESTAMP(), 'User', '')",
					$ref
				)
			);


			if (empty($wpdb->last_error)) {
				stopbadbots_admin_notice3__success();
			} else {
				stopbadbots_admin_notice3__fail();
			}
		} else {
			stopbadbots_admin_notice3__fail();
		}
		// clear sbb_input_ip
		unset($options['sbb_input_ref']);
		update_option('stopbadbots_settings3', $options);
	}
	return;
}
/*
function stopbadbots_check_db_sbb_blacklist() {
	 global $wpdb;
	$table_name = $wpdb->prefix . 'sbb_blacklist';
	if ( ! stopbadbots_tablexist( $table_name ) ) {
		return;
	}
	$res       = $wpdb->get_col( "DESC {$table_name}", 0 );
	$num_files = count( $res );
	if ( $num_files < 11 ) {
		$query = 'ALTER TABLE  `' . $table_name . '`
       ADD  `botdate` TIMESTAMP NOT NULL,
       ADD  `botflag` VARCHAR( 1 ) NOT NULL,
       ADD  `botua` TEXT NOT NULL';
		$r     = $wpdb->query( sanitize_text_field( $query ) );
	}
	
}
*/
function stopbadbots_upload_new_bots()
{
	global $wpdb;
	if (!stopbadbots_gocom()) {
		return;
	}

	$table_name = $wpdb->prefix . 'sbb_blacklist';
	//$query      = 'select * from ' . $table_name .
	//	' where botflag = "2" or botflag = "1" ';
	//$result     = $wpdb->get_row( sanitize_text_field( $query ) );
	$result = $wpdb->get_row($wpdb->prepare('SELECT * FROM %i WHERE botflag = "2" OR botflag = "1"', $wpdb->prefix . 'sbb_blacklist'));


	if (!$result) {
		return;
	}
	$id       = $result->id;
	$ua       = $result->botua;
	$ip       = $result->botip;
	$date     = $result->botdate;
	$nickname = $result->botnickname;
	$myarray  = array(
		'ua'       => $ua,
		'ip'       => $ip,
		'date'     => $date,
		'nickname' => $nickname,
		'version'  => STOPBADBOTSVERSION,
	);
	$url      = 'https://stopbadbots.com/api/httpapi.php';
	$response = wp_remote_post(
		$url,
		array(
			'method'      => 'POST',
			'timeout'     => 15,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $myarray,
			'cookies'     => array(),
		)
	);
	if (is_wp_error($response)) {
		// $error_message = $response->get_error_message();
		// echo "Something went wrong: $error_message";
		stopbadbots_confail();
	} else {
		$botflag = '4';
		if (!empty($ua) and !empty($ip)) {
			$botglag = '6';
		}

		/*
		$query  = 'update ' . $table_name . " set botflag = '" . $botflag .
			"' WHERE id ='" . $id . "'";
		$result = $wpdb->query( sanitize_text_field( $query ) );
		*/

		$result = $wpdb->query(
			$wpdb->prepare(
				"UPDATE `$table_name` 
				SET botflag = %s
				WHERE id = %s",
				$botflag,
				$id
			)
		);
	}
}
function stopbadbots_get_ua()
{
	if (!isset($_SERVER['HTTP_USER_AGENT'])) {
		return '';
	}
	$ua = trim(sanitize_text_field($_SERVER['HTTP_USER_AGENT']));
	$ua = stopbadbots_sbb_clear_extra($ua);
	return $ua;
}
function stopbadbots_sbb_clear_extra($mystring)
{
	$mystring = str_replace('$', 'S;', $mystring);
	$mystring = str_replace('{', '!', $mystring);
	$mystring = str_replace('shell', 'chell', $mystring);
	$mystring = str_replace('curl', 'kurl', $mystring);
	$mystring = str_replace('<', '&lt;', $mystring);
	$mystring = str_replace('=', '&#61;', $mystring);
	return $mystring;
}
function stopbadbots_complete_bot_data($nickname)
{
	global $wpdb;
	if (empty($nickname)) {
		return;
	}
	$table_name = $wpdb->prefix . 'sbb_blacklist';

	/*
	$query      = 'select * from ' . $table_name . ' where botnickname =  "' . $nickname .
		'" and botflag != "6" limit 1';
	$result     = $wpdb->get_row( sanitize_text_field( $query ) );
	*/


	$result  = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM `$table_name` 
     WHERE botnickname = %s AND botflag != '6' limit 1",
			$nickname
		)
	);

	if (!$result) {
		return;
	}
	$id   = $result->id;
	$uadb = $result->botua;
	$ipdb = $result->botip;
	if (empty($uadb) and empty($ipdb)) {
	} else {
		return;
	}
	$ua    = stopbadbots_get_ua();
	$ip    = sbb_findip();
	$maybe = false;
	if (empty($uadb) and !empty($ua)) {
		$maybe = true;
	}
	if (empty($ipdb) and !empty($ip)) {
		$maybe = true;
	}
	if ($maybe) {
	} else {
		return;
	}
	$ua     = wp_json_encode($ua);

	/*
	$sql    = 'update ' . $table_name . " SET
     botua = '" . esc_attr( $ua ) . "',
     botip = '" . esc_attr( $ip ) . "',
     botflag = '2'
     WHERE
     id = '" . $id . "'
     limit 1";
	$result = $wpdb->query( $sql );
	*/

	$result = $wpdb->query(
		$wpdb->prepare(
			"UPDATE `$table_name` 
			SET botua = %s, 
			botip = %s, 
			botflag = '2'
			WHERE id = %s LIMIT 1",
			$ua,
			$ip,
			$id
		)
	);





	return;
}
if (get_option('stop_bad_bots_network', '') == 'yes') {
	add_action('plugins_loaded', 'stopbadbots_chk_update');
	add_action('plugins_loaded', 'stopbadbots_chk_update2');
}
function stopbadbots_chk_update()
{
	global $wpdb, $stopbadbots_checkversion;
	$table_name = $wpdb->prefix . 'sbb_blacklist';
	if (!stopbadbots_gocom()) {
		return;
	}
	$last_checked = get_option('stopbadbots_last_checked', '0');
	if (empty($stopbadbots_checkversion)) {
		$days = 120;
	} else {
		$days = 7;
	}
	$write = time() - (8 * 24 * 3600);
	if ($last_checked == '0') {
		if (!add_option('stopbadbots_last_checked', $write)) {
			update_option('stopbadbots_last_checked', $write);
		}
		return;
	} elseif (($last_checked + ($days * 24 * 3600)) > time()) {
		return;
	}
	ob_start();
	$domain_name = get_site_url();
	$urlParts    = parse_url($domain_name);

	// $domain_name = preg_replace( '/^www\./', '', $urlParts['host'] );


	if (isset($urlParts['host']) && !is_null($urlParts['host'])) {
		$domain_name = preg_replace('/^www\./', '', $urlParts['host']);
	} else {
		// Handle missing or null host (e.g., set an empty domain name)
		$domain_name = '';
	}




	$myarray     = array(
		'last_checked'             => $last_checked,
		'stopbadbots_checkversion' => $stopbadbots_checkversion,
		'version'                  => STOPBADBOTSVERSION,
		'domain_name'              => $domain_name,
	);
	$url         = 'https://stopbadbots.com/api/httpapi.php';
	// $bot_nickname = 'test';
	$response = wp_remote_post(
		$url,
		array(
			'method'      => 'POST',
			'timeout'     => 15,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $myarray,
			'cookies'     => array(),
		)
	);
	if (is_wp_error($response)) {
		$error_message = $response->get_error_message();
		// echo "Something went wrong: $error_message";
		stopbadbots_confail();
		ob_end_clean();
		return;
	}
	$r = trim($response['body']);
	$r = json_decode($r, true);


	if ($r === null) {
		stopbadbots_confail();
		ob_end_clean();
		return;
	}

	$q = count($r);

	// if($r !== null) {

	if (!is_numeric($q)) {
		stopbadbots_confail();
		ob_end_clean();
		return;
	}

	if ($q == 1) {
		$botip = trim($r[0]['ip']);
		if ($botip == '-9') {
			update_option('stopbadbots_checkversion', '');
		}
	} else {
		for ($i = 0; $i < $q; $i++) {

			if (!isset($r[$i]['botnickname']) or !isset($r[$i]['botname']) or !isset($r[$i]['botip']) or !isset($r[$i]['botua'])) {
				continue;
			}

			$botnickname = trim(sanitize_text_field($r[$i]['botnickname']));
			$botname     = trim(sanitize_text_field($r[$i]['botname']));
			$botip       = trim(sanitize_text_field($r[$i]['botip']));
			$botua       = trim(sanitize_text_field($r[$i]['botua']));

			if (empty($botnickname) or empty($botname) or empty($botip) or empty($botua)) {
				continue;
			}
			// delete
			if ($botip == '-1') {


				/*
				$query = 'DELETE FROM  ' . $table_name . " WHERE botnickname = '" . $botnickname .
					"' LIMIT 1";
				$ret   = $wpdb->get_results( sanitize_text_field( $query ) );
				*/

				$ret  = $wpdb->get_results(
					$wpdb->prepare(
						"DELETE * FROM `$table_name` 
				 WHERE bonickname = %s limit 1",
						$botnikname
					)
				);
				continue;
			} else {

				/*
				$query = 'select COUNT(*) from ' . $table_name . " WHERE botnickname = '" . $botnickname .
					"' LIMIT 1";
					*/


				$results90  = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM `$table_name` 
					 WHERE botnickname= %s limit 1",
						$botnickname
					)
				);

				if ($results90  > 0) {
					continue;
				}

				/*
				$ret = $wpdb->get_results(
					$wpdb->prepare(
						"INSERT INTO `$table_name` 
					(botnickname, botname, botip, botua, botstate, botflag)
					VALUES (%s, %s , %s, %s, 'Enabled', '9')",
						$botnickname,
						$botname,
						$botip,
						$botua)
				);
				*/

				$ret = $wpdb->get_results(
					$wpdb->prepare(
						"INSERT INTO `$table_name` 
						(botnickname, botname, boturl, botip, botobs, botstate, botblocked, botdate, botflag, botua)
						VALUES (%s, %s, '', %s, '', 'Enabled', 0, CURRENT_TIMESTAMP(), '9', %s)",
						$botnickname,
						$botname,
						$botip,
						$botua
					)
				);
			}
		}
	}

	if (!add_option('stopbadbots_last_checked', time())) {
		update_option('stopbadbots_last_checked', time());
	}
	ob_end_clean();
}
function stopbadbots_chk_update2()
{
	global $wpdb, $stopbadbots_checkversion;
	if (!stopbadbots_gocom()) {
		return;
	}
	$table_name   = $wpdb->prefix . 'sbb_badips';
	$last_checked = get_option('stopbadbots_last_checked2', '0');
	if (empty($stopbadbots_checkversion)) {
		$days = 120;
	} else {
		$days = 7;
	}
	$write = time() - (8 * 24 * 3600);
	if ($last_checked == '0') {
		if (!add_option('stopbadbots_last_checked2', $write)) {
			update_option('stopbadbots_last_checked2', $write);
		}
		return;
	} elseif (($last_checked + ($days * 24 * 3600)) > time()) {
		return;
	}
	ob_start();
	$domain_name = get_site_url();
	$urlParts    = parse_url($domain_name);

	//$domain_name = preg_replace( '/^www\./', '', $urlParts['host'] );

	if (isset($urlParts['host']) && !is_null($urlParts['host'])) {
		$domain_name = preg_replace('/^www\./', '', $urlParts['host']);
	} else {
		// Handle missing or null host (e.g., set an empty domain name)
		$domain_name = '';
	}




	$myarray     = array(
		'last_checked'             => $last_checked,
		'stopbadbots_checkversion' => $stopbadbots_checkversion,
		'version'                  => STOPBADBOTSVERSION,
		'domain_name'              => $domain_name,
	);
	$url         = 'https://stopbadbots.com/api/httpapiip.php';
	$response    = wp_remote_post(
		$url,
		array(
			'method'      => 'POST',
			'timeout'     => 15,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $myarray,
			'cookies'     => array(),
		)
	);
	if (is_wp_error($response)) {
		$error_message = $response->get_error_message();
		// echo "Something went wrong: $error_message";
		stopbadbots_confail();
		ob_end_clean();
		return;
	}
	$r = trim($response['body']);
	$r = json_decode($r, true);
	if (!$r or !is_array($r)) {
		ob_end_clean();
		return;
	}
	$q = count($r);
	if ($q == 1) {
		if (isset($r[0]['ip'])) {
			$botip = trim($r[0]['ip']);
			if ($botip == '-9') {
				update_option('stopbadbots_checkversion', '');
			}
		}
	} else {
		for ($i = 0; $i < $q; $i++) {

			if (!isset($r[$i]['ip']) or !isset($r[$i]['country']) or !isset($r[$i]['flag'])) {
				continue;
			}

			$botip      = trim(sanitize_text_field($r[$i]['ip']));
			$botcountry = trim(sanitize_text_field($r[$i]['country']));
			$botflag    = trim(sanitize_text_field($r[$i]['flag']));

			if (empty($botip)) {
				continue;
			}
			// delete
			if ($botflag == '-1') {

				/*
				$query = 'DELETE FROM  ' . $table_name . " WHERE botip = '" . $botip .
					"' LIMIT 1";
				$ret   = $wpdb->get_results( sanitize_text_field( $query ) );
				*/

				$ret  = $wpdb->get_results(
					$wpdb->prepare(
						"DELETE FROM `$table_name` 
				 WHERE botip = %s limit 1",
						$botip
					)
				);
				continue;
			} else {

				/*
				$query = 'select COUNT(*) from ' . $table_name . " WHERE botip = '" . $botip .
					"' LIMIT 1";
				*/

				$results90  = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM `$table_name` 
					 WHERE botip = %s limit 1",
						$botip
					)
				);

				if ($results90  > 0) {
					continue;
				}

				/*
				$ret = $wpdb->get_results(
					$wpdb->prepare(
					"INSERT INTO `$table_name` 
					(botip, botstate, botflag, botcountry, added) 
					VALUES (%s, 'Enabled', '9' , %s, 'Plugin')",
						$botip,
						$botcountry
					)
				);
				*/

				$ret = $wpdb->get_results(
					$wpdb->prepare(
						"INSERT INTO `$table_name` 
						(botip, botobs, botstate, botblocked, botdate, added, botflag, botcountry)
						VALUES (%s, '', 'Enabled', 0, CURRENT_TIMESTAMP(), 'Plugin', '9', %s)",
						$botip,
						$botcountry
					)
				);
			}
		}
	}
	if (!add_option('stopbadbots_last_checked2', time())) {
		update_option('stopbadbots_last_checked2', time());
	}
	ob_end_clean();
}
function stopbadbots_visitoripDetect($stopbadbots_ip)
{
	global $wpdb;

	if (stopbadbots_isourserver()) {
		return false;
	}

	$current_table = $wpdb->prefix . 'sbb_badips';
	// $result = $wpdb->get_results("SELECT botip FROM $current_table WHERE `botip` = '$stopbadbots_ip' ");
	$result = $wpdb->get_results("SELECT botip FROM $current_table WHERE `botip` = '$stopbadbots_ip' and `botstate` = 'Enabled' ");
	$result = $wpdb->get_results($wpdb->prepare("SELECT botip FROM %i WHERE `botip` = %s AND `botstate` = 'Enabled'", $current_table, $stopbadbots_ip));



	if ($wpdb->num_rows > 0) {
		return true;
	} else {
		return false;
	}
}

function stopbadbots_isourserver()
{
	global $stopbadbots_ip;

	try {
		if (isset($_SERVER['SERVER_ADDR'])) {
			$server_ip = sanitize_text_field($_SERVER['SERVER_ADDR']);
		} elseif (function_exists("gethostname") and function_exists("gethostbyname")) {
			$server_ip = sanitize_text_field(gethostbyname(gethostname()));
		} else {
			return false;
		}
	} catch (Exception $e) {
		// echo 'Caught exception: ',  $e->getMessage(), "\n";
		return false;
	}


	if (!filter_var($server_ip, FILTER_VALIDATE_IP))
		return false;


	if ($server_ip == $stopbadbots_ip) {
		return true;
	}

	if (stopbadbots_block_whitelist_ip()) {
		return true;
	}

	if (stopbadbots_block_whitelist_string()) {
		return true;
	}

	return false;
}

function stopbadbots_is_really_our_server()
{
	global $stopbadbots_ip;

	try {
		if (isset($_SERVER['SERVER_ADDR'])) {
			$server_ip = sanitize_text_field($_SERVER['SERVER_ADDR']);
		} elseif (function_exists("gethostname") and function_exists("gethostbyname")) {
			$server_ip = sanitize_text_field(gethostbyname(gethostname()));
		} else {
			return false;
		}
	} catch (Exception $e) {
		// echo 'Caught exception: ',  $e->getMessage(), "\n";
		return false;
	}


	if (!filter_var($server_ip, FILTER_VALIDATE_IP))
		return false;


	if ($server_ip == $stopbadbots_ip) {
		return true;
	}

	return false;
}


function stopbadbots_block_httptools()
{
	global $stopbadbots_userAgentOri;
	global $astopbadbots_http_tools;
	global $stopbadbots_maybe_search_engine;

	if (stopbadbots_isourserver()) {
		return '';
	}

	if ($stopbadbots_maybe_search_engine) {
		return '';
	}

	if (stopbadbots_block_whitelist_ip()) {
		return '';
	}

	if (stopbadbots_block_whitelist_string()) {
		return '';
	}

	if (count($astopbadbots_http_tools) < 1) {
		return '';
	}
	for ($i = 0; $i < count($astopbadbots_http_tools); $i++) {
		$toolnickname = $astopbadbots_http_tools[$i];
		if (stripos($stopbadbots_userAgentOri, $toolnickname) !== false) {
			return $toolnickname;
		}
	}
	return '';
}


function stopbadbots_block_whitelist_string()
{
	global $stopbadbots_userAgentOri;
	global $astopbadbots_string_whitelist;
	// global $astopbadbots_ip_whitelist;

	if (gettype($astopbadbots_string_whitelist) != 'array') {
		return;
	}

	if (count($astopbadbots_string_whitelist) < 1) {
		return false;
	}
	for ($i = 0; $i < count($astopbadbots_string_whitelist); $i++) {
		$string_name = $astopbadbots_string_whitelist[$i];
		if (stripos($stopbadbots_userAgentOri, $string_name) !== false) {
			return true;
		}
	}
	return false;
}


function stopbadbots_block_whitelist_IP()
{
	global $stopbadbots_ip;
	global $astopbadbots_ip_whitelist;

	if (gettype($astopbadbots_ip_whitelist) != 'array') {
		return;
	}

	if (count($astopbadbots_ip_whitelist) < 1) {
		return false;
	}
	for ($i = 0; $i < count($astopbadbots_ip_whitelist); $i++) {
		$ip_address = $astopbadbots_ip_whitelist[$i];
		if (stripos($ip_address, $stopbadbots_ip) !== false) {
			return true;
		}
	}
	return false;
}
function stopbadbots_crawlerDetect($stopbadbots_userAgentOri)
{
	global $wpdb, $stopbadbots_found, $stopbadbots_ip, $stopbadbots_userAgentOri;

	if (stopbadbots_isourserver()) {
		return false;
	}

	$foundit = strpos($stopbadbots_userAgentOri, 'WordPress');
	if ($foundit !== false) {
		return false;
	}
	$current_table = $wpdb->prefix . 'sbb_blacklist';
	$result        = $wpdb->get_results("SELECT botnickname, id FROM $current_table WHERE `botstate` LIKE 'Enabled' ");
	$stopbadbots_found     = '';
	foreach ($result as $results) {
		$botnickname = trim($results->botnickname);
		if (strlen($botnickname) < 3) {
			continue;
		}
		if (stripos($stopbadbots_userAgentOri, $botnickname) !== false) {
			$stopbadbots_found = $botnickname;
			// 2024 mar 31
			return true;
		}
	}
	if (!empty($stopbadbots_found)) {
		return true;
	}
	if (get_option('stop_bad_bots_network', '') != 'yes') {
		return false;
	}
	if (!stopbadbots_gocom()) {
		return false;
	}
	// New
	// not found
	$lookfor       = array(
		'bot',
		'apache',
		'crawler',
		'elinks',
		'http',
		// 'java',
		'spider',
		'link',
		'fetcher',
		'scanner',
		'grabber',
		'collector',
		'capture',
		'seo',
		'.com',
	);
	$maybefoundbot = false;
	for ($i = 0; $i < count($lookfor); $i++) {
		$foundit = strpos($stopbadbots_userAgentOri, strtolower($lookfor[$i]));
		if ($foundit !== false) {
			$maybefoundbot = true;
			break;
		}
	}
	if ($maybefoundbot == false) {
		return false;
	}
	// else have bot at ua
	$agentsok = array(
		' link ',
		'_seon',
		'addthis',
		'adsbot',
		'adsbot-google',
		'acquia.com',
		'apercite',
		'apple',
		'appcontrols.com',
		'aranhabot', // amazon
		'avant browser',
		'avantbrowser',
		'baidu',
		'baiduspider',
		'barion.com',
		'binarycanary.com',
		'bingbot',
		'bla',
		'blogger.com',
		'blogmuraBot',
		'bloglovin',
		'bot@eright.com',
		'botwarz',
		'boxcar',
		'browserproxy',
		'bsalsa.com',
		'build/prolink',
		'bublup.com',
		'campus bot',
		'cablink',
		'callpage',
		'chainlink',
		'checksite',
		'choosito.com',
		'collect-peers',
		'cloudsystemnetwork',
		'cron-job.org',
		'code.google.com/apis/maps/',
		'conbot',
		'crisp.chat',
		'cronless',
		'cubot',
		'cubot_note',
		'cula.io',
		'docs.google.com',
		'deluge-torrent',
		'djangoproject',
		'domeinnaambeleid',
		'dotclear',
		'downcast.fm',
		'dpdesk.com',
		'drive.google.com',
		'drupal',
		'dusterio',
		'dynamic Wrapper',
		'EchoboxBot',
		'elinks/0',
		'entireweb',
		'exalead',
		'ezine',
		'facebook',
		'flipboard',
		'freshping.io',
		'fdm',
		'feed',
		'feedfetcher-google',
		'feedparser',
		'feedzirra',
		'free-counter.co.uk',
		'fuelbot',
		'galaxy',
		'GoBadLinks',
		'google-analytics.com',
		'google-youtube-links',
		'google.com',
		'google.com/merchants',
		'googlebot',
		'googleimageproxy',
		'Google-Site-Verification',
		'gregarius',
		'hyperspin',
		'ichiro-goo',
		'iis.net',
		'istat.it',
		'ithemes.com',
		'kinsta-bot',
		'lcc',
		'letsencrypt',
		'libwww',
		'link+',
		'link5',
		'linkedin',
		'linklinklove',
		'linkpreview',
		'live',
		'm.tigo.com',
		'MailChimp',
		'mailpoet.com',
		'mainwp.com',
		'MastoPeek',
		'mclinkface',
		'mediapartners-google',
		'microsoft',
		'mobilink',
		'mollie.nl',
		'monitage',
		'monsido',
		'moosaico',
		'moreover',
		'msn bot/1.0',
		'msnbot',
		'myspace.com',
		'nonli',
		'newsbank.com',
		'nsoftware.com',
		'ohdear',
		'oercommons.org',
		'opendns',
		'orcabrowser',
		'orderdesk',
		'ostermiller.org',
		'overcast.fm',
		'pagosonline.com',
		'pantechp8010',
		'paypal.com/ipn',
		'pear.php',
		'picofeed',
		'pingdom.com',
		'pinterest.com',
		'PleskBot',
		'plukkie',
		'plurk',
		'printfriendly',
		'question2answer.org',
		'quickpay',
		'Register.Com.GR',
		'rtbtr.com',
		'ridder.co',
		'riseofglory',
		'rss',
		'sansanbot',
		'sarafanbot',
		'savepagenow',
		's2member.com',
		'salesforce.com',
		'scoutjet',
		'searchbutton',
		'secondlife.com',
		'security.ipip',
		'semrush',
		'seoul',
		'seznam',
		'sendcloud',
		'shareaholic',
		'shopping.com',
		'shoppingnotes',
		'shopwiki',
		'silk',
		'sismics.com',
		'symprex',
		'sitemap',
		'siteuptime.com',
		'slurp',
		'snowhaze.com',
		'socialmediaposterbot',
		'spip.net',
		'Statically-Imgpx',
		'statically.io',
		'stripe.com',
		'swanson',
		'tbrss.com',
		'telegram',
		'thelounge',
		'tigo.com',
		'tripadvisor',
		'tulipchain',
		'twitter',
		'twieve.net',
		'unoeuro.com',
		'unfurlist',
		'uptime.com',
		'Uptime-Bot',
		'uptimerobot',
		'url.com',
		'utmon.com',
		'vagabondo_wiseguys',
		'VBA-Web',
		'voila',
		'vuhuv.com',
		'xbmc.org',
		'watchful',
		'webcron.o',
		'webgazer',
		'webmastersite',
		'webtorrent.io',
		'WLMHttpTranspor',
		'wikimedia',
		'windows nt',
		'WorldPay',
		'wp-rocket.me',
		'wprocketbot',
		'yahoo',
		'yandex',
		'yerl.org',
		'yellowpages',
		'yeti_naver',
	);
	for ($i = 0; $i < count($agentsok); $i++) {
		$foundit = stripos($stopbadbots_userAgentOri, $agentsok[$i]);
		if ($foundit !== false) {
			return false;
		}
	}
	// Especificos
	$auako2 = array(
		'Ant',
		'2ip',
		'AHC',
		'bot',
		'git',
	);
	for ($i = 0; $i < count($auako2); $i++) {
		if (trim($stopbadbots_found) == trim($auako2[$i])) {
			return false;
		}
	}
	$nickname = (string) time();
	$myarray  = array(
		'ua'       => $stopbadbots_userAgentOri,
		'botip'    => $stopbadbots_ip,
		'nickname' => $nickname,
		'version'  => STOPBADBOTSVERSION,
	);
	if (empty($stopbadbots_userAgentOri) or empty($stopbadbots_ip) or empty($nickname)) {
		return false;
	}
	ob_start();
	$url      = 'https://stopbadbots.com/api/httpapi.php';
	$response = wp_remote_post(
		$url,
		array(
			'method'      => 'POST',
			'timeout'     => 10,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => array(),
			'body'        => $myarray,
			'cookies'     => array(),
		)
	);
	if (is_wp_error($response)) {
		stopbadbots_confail();
	}
	ob_end_clean();
	return false;
}
function stopbadbots_tablexist($table)
{
	global $wpdb;
	$table_name = $table;
	if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
		return true;
	} else {
		return false;
	}
}
add_filter('plugin_row_meta', 'stopbadbots_custom_plugin_row_meta', 10, 2);
function stopbadbots_custom_plugin_row_meta($links, $file)
{
	global $stopbadbots_checkversion;
	if (strpos($file, 'stopbadbots.php') !== false) {
		$new_links = array(
			'OnLine Guide' => '<a href="https://stopbadbots.com/help/" target="_blank">OnLine Guide</a>',
		);
		if (empty($stopbadbots_checkversion)) {
			$new_links['Pro'] = '<a href="https://stopbadbots.com/premium/" target="_blank"><b><font color="#FF6600">Go Pro</font></b></a>';
		} else {

			$url = STOPBADBOTSHOMEURL . 'plugin-install.php?s=sminozzi&tab=search&type=author';

			if (is_multisite()) {
				$url = esc_url_raw(STOPBADBOTSHOMEURL) . 'plugin-install.php?s=sminozzi&tab=search&type=author';
			} else {
				$url = esc_url_raw(STOPBADBOTSHOMEURL) . 'admin.php?page=stopbadbots_new_more_plugins';
			}

			$new_links['Other'] = '<a href="' . $url . '" target="_blank"><b><font color="#FF6600">Click To see more plugins from same author</font></b></a>';
		}

		$links = array_merge($links, $new_links);
	}
	return $links;
}
function stopbadbots_bill_ask_for_upgrade()
{
	global $stopbadbots_checkversion;
	if (!empty($stopbadbots_checkversion)) {
		return;
	}
	$time = date('Ymd');
	if ($time == '20191129') {
		$x = 3; // rand(0, 3);
		// $x = 3;
	} else {
		$x = rand(0, 3);
	}
	// $x = 3;
	if ($x == 0) {
		$banner_image          = STOPBADBOTSIMAGES . '/eating.png';
		$bill_banner_bkg_color = 'orange';
		$banner_txt            = esc_attr__('Bad Bots can do all sorts of nasty stuff and waste server resources.', 'stopbadbots');
	} elseif ($x == 1) {
		$banner_image          = STOPBADBOTSIMAGES . '/monitor-com-maca3.png';
		$bill_banner_bkg_color = 'orange';
		$banner_txt            = esc_attr__('Bad bots don’t play by the rules.', 'stopbadbots');
	} elseif ($x == 2) {
		$banner_image          = STOPBADBOTSIMAGES . '/unlock-icon-red-small.png';
		$bill_banner_bkg_color = 'turquoise';
		$banner_txt            = esc_attr__('Bad Bots stresses, harm and slowly your Web servers.', 'stopbadbots');
	} elseif ($x == 3) {
		$banner_image          = STOPBADBOTSIMAGES . '/5stars.png';
		$bill_banner_bkg_color = 'turquoise';
		$banner_txt            = esc_attr__('Show support with a 5-star rating.', 'stopbadbots');
	} elseif ($x == 4) {
		$banner_image          = STOPBADBOTSIMAGES . '/special-offer.png';
		$bill_banner_bkg_color = 'turquoise';
		$banner_txt            = esc_attr__('BLACK FRIDAY 30% OFF! Use the coupon code: special-black_2019. Limited time!', 'stopbadbots');
	} else {
		$banner_image          = STOPBADBOTSIMAGES . '/keys_from_left.png';
		$bill_banner_bkg_color = 'orange';
		$banner_txt            = esc_attr__('Become Pro: Your bad bots table is always updated.', 'stopbadbots');
	}
	$banner_tit = esc_attr__('Stop Bad Bots Plugin. Its time to Get Pro Protection!', 'stopbadbots');
	/*
	echo '<script type="text/javascript" src="' . STOPBADBOTSURL .
		'assets/js/c_o_o_k_i_e.js' . '"></script>';
	*/
?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			<?php
			if (empty($stopbadbots_checkversion)) {
				echo 'jQuery("#stopbadbots_firewall_1").attr("disabled", true);';
				echo 'jQuery("#stopbadbots_firewall_1").prop("checked", false);';
				echo 'jQuery("#stopbadbots_firewall_2").prop("checked", true);';
				echo 'jQuery("#stopbadbots_block_false_google_1").attr("disabled", true);';
				echo 'jQuery("#stopbadbots_block_false_google_1").prop("checked", false);';
				echo 'jQuery("#stopbadbots_block_false_google_2").prop("checked", true);';
				echo 'jQuery("#stopbadbots_block_http_tools_1").attr("disabled", true);';
				echo 'jQuery("#stopbadbots_block_http_tools_1").prop("checked", false);';
				echo 'jQuery("#stopbadbots_block_http_tools_2").prop("checked", true);';
				echo 'jQuery("#stopbadbots_radio_limit_visits_1").attr("disabled", true);';
				echo 'jQuery("#stopbadbots_radio_limit_visits_1").prop("checked", false);';
				echo 'jQuery("#stopbadbots_radio_limit_visits_2").prop("checked", true);';
				echo 'jQuery("#stopbadbots_enable_whitelist_1").attr("disabled", true);';
				echo 'jQuery("#stopbadbots_enable_whitelist_1").prop("checked", false);';
				echo 'jQuery("#stopbadbots_enable_whitelist_2").prop("checked", true);';
				echo 'jQuery("#stop_bad_bots_engine_option_3").prop("checked", false);';
			}
			?>
			jQuery(".sbb_bill_go_pro_dismiss").click(function(event) {
				jQuery(".sbb_bill_go_pro_message").css("display", "none");
				event.preventDefault()
				jQuery(".sbb_bill_go_pro_container").css("display", "none");
				jQuery.ajax({
					method: 'post',
					url: ajaxurl,
					data: {
						action: "stopbadbots_go_pro_hide"
					},
					success: function(data) {
						//alert('OK');
						return data;
					},
					error: function(jqXHR, textStatus, errorThrown) {
						alert('error' + errorThrown + ' ' + textStatus);
					}
				});
			});
		}); // end (jQuery);
	</script>
	<style type="text/css">
		.sbb_bill_go_pro_close_icon {
			width: 31px;
			height: 31px;
			border: 0px solid red;
			/* background: url("http://xxxxxx.com/wp-content/plugins/cardealer/assets/images/close_banner.png") no-repeat center center; */
			box-shadow: none;
			float: right;
			margin: 8px;
			margin: 60px 40px 8px 8px;
		}

		.sbb_bill_hide_settings_notice:hover,
		.sbb_bill_hide_premium_options:hover {
			cursor: pointer;
		}

		.sbb_bill_hide_premium_options {
			position: relative;
		}

		.sbb_bill_go_pro_image {
			float: left;
			margin-right: 20px;
			max-height: 90px !important;
		}

		.sbb_bill_image_go_pro {
			max-width: 200px;
			max-height: 88px;
		}

		.sbb_bill_go_pro_text {
			font-size: 18px;
			padding: 10px;
			margin-bottom: 5px;
		}

		.sbb_bill_go_pro_button_primary_container {
			float: left;
			margin-top: 0px;
		}

		.sbb_bill_go_pro_dismiss_container {
			margin-top: 0px;
		}

		.sbb_bill_go_pro_buttons {
			display: flex;
			max-height: 30px;
			margin-top: -10px;
		}

		.sbb_bill_go_pro_container {
			border: 1px solid darkgray;
			height: 88px;
			padding: 0;
			margin: 10px 0px 15px 0px;
			background: <?php echo esc_attr($bill_banner_bkg_color); ?>
		}

		.sbb_bill_go_pro_dismiss {
			margin-left: 15px !important;
		}

		.button {
			vertical-align: top;
		}

		@media screen and (max-width:900px) {
			.sbb_bill_go_pro_text {
				font-size: 16px;
				padding: 5px;
				margin-bottom: 10px;
			}
		}

		@media screen and (max-width:800px) {
			.sbb_bill_go_pro_container {
				display: none !important;
			}
		}
	</style>
	<div class="notice notice-success sbb_bill_go_pro_container" style="display: none;">
		<div class="sbb_bill_go_pro_message sbb_bill_banner_on_plugin_page sbb_bill_go_pro_banner">
			<div class="sbb_bill_go_pro_image">
				<img class="sbb_bill_image_go_pro" title="" src="<?php echo esc_html($banner_image); ?>" alt="" />
			</div>
			<div class="sbb_bill_go_pro_text">
				<!-- <strong>
								Weekly Updates!
							</strong> -->
				<span>
					<strong>
						<?php echo esc_html($banner_txt); ?>
					</strong>
				</span>
				<br />
				<?php
				if ($x != '3') {
					echo esc_html($banner_tit);
				} else {
					echoesc_attr__('Help keep Stop Bad Bots plugin going strong!', 'stopbadbots');
				}
				?>
			</div>
			<div class="sbb_bill_go_pro_buttons">
				<div class="sbb_bill_go_pro_button_primary_container">
					<?php
					if ($x != '3') {
						echo '<a class="button button-primary" target="_blank" href="https://stopbadbots.com/premium/">';
						echo esc_attr__('Learn More', 'stopbadbots');
						echo '</a>';
					} else {
						echo '<a class="button button-primary" target="_blank" href="https://wordpress.org/support/plugin/stopbadbots/reviews/#new-post">';
						echo esc_attr__('Go to WordPress', 'stopbadbots');
						echo '</a>';
					}
					?>
				</div>
				<div class="sbb_bill_go_pro_dismiss_container">
					<a class="button button-secondary sbb_bill_go_pro_dismiss" target="_blank" href="https://stopbadbots.com/premium/">
						<?php
						esc_attr_e(
							'Dismiss',
							'stopbadbots'
						);
						?>
					</a>
				</div>
			</div>
		</div>
	</div>
<?php
} // end Bill ask for upgrade
$when_installed = get_option('bill_installed');
$now            = time();
$delta          = $now - $when_installed;
// $delta = 999999999;
if ($delta > (3600 * 24 * 8)) {
	$stopbadbotsurl = sanitize_text_field($_SERVER['REQUEST_URI']);
	if (strpos($stopbadbotsurl, 'sbb_') !== false and empty($stopbadbots_checkversion)) {
		if (strpos($stopbadbotsurl, 'settings') === false) {
			// add_action('admin_notices', 'stopbadbots_bill_ask_for_upgrade');
		}
	}
}
	// add_action('admin_notices', 'stopbadbots_bill_ask_for_upgrade');



;

function stopbadbots_sbb_populate_stats()
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_stats';
	$my_query   = $wpdb->get_results("SELECT * FROM $table_name");



	if ($wpdb->num_rows > 360) {
		return;
	}




	for ($i = 01; $i < 13; $i++) {
		for ($k = 01; $k < 32; $k++) {
			// insert in table iikk
			// $intval = (int) $string;
			// $string = (string) $intval;
			$year = 2020;
			if (!checkdate($i, $k, $year)) {
				continue;
			}
			$mdata = (string) $i;
			if (strlen($mdata) < 2) {
				$mdata = '0' . $mdata;
			}
			$ddata = (string) $k;
			if (strlen($ddata) < 2) {
				$ddata = '0' . $ddata;
			}
			$data  = $mdata . $ddata;

			/*
			$query = 'select COUNT(*) from ' . $table_name . " WHERE date = '" . $data .
				"' LIMIT 1";
			*/

			$results90  = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM `$table_name` 
				 WHERE date = %s limit 1",
					$data
				)
			);


			//var_dump($results90);
			//die();


			if ($results90 > 0) {
				continue;
			}


			/*
			$r = $wpdb->get_results(
				$wpdb->prepare(
					"INSERT INTO `$table_name` 
                (date) 
                VALUES (%s)",
					$data
				)
			);
			*/





			// error_log(var_export($r,true));

			/*
			`date` varchar(4) COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qnick` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qip` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qfire` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qref` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qping` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`quenu` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qlogin` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qcom` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qcon` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qua` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qfalseg` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qtools` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qrate` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qbrowser` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qother` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
			`qtotal` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
			*/

			$r = $wpdb->get_results(
				$wpdb->prepare(
					"INSERT INTO `$table_name` 
					(date, qnick, qip, qfire, qref, qping, quenu, qlogin, qcom, qcon, qua, qfalseg, qtools, qrate, qbrowser, qother, qtotal)
					VALUES (%s, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')",
					$data
				)
			);
		}
	}
}

function stopbadbots_stats_moreone($qtype)
{
	global $wpdb;

	//	error_log($qtype);

	/*
	`id` mediumint(9) NOT NULL,
	`date` varchar(4) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qnick` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qip` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qtotal` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qfire` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qref` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qua` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qping` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`quenu` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qother` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qlogin` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qcom` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qcon` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qfalseg` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qtools` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qrate` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
	`qbrowser` text COLLATE utf8mb4_unicode_520_ci NOT NULL

	*/





	if (
		$qtype != 'qnick'
		and $qtype != 'qip'
		and $qtype != 'qfire'
		and $qtype != 'qref'
		and $qtype != 'qua'
		and $qtype != 'qping'
		and $qtype != 'quenu'
		and $qtype != 'qlogin'
		and $qtype != 'qcom'
		and $qtype != 'qcon'
		and $qtype != 'qfalseg'
		and $qtype != 'qother'
		and $qtype != 'qtotal'
		and $qtype != 'qtools'
		and $qtype != 'qrate'
		and $qtype != 'qbrowser'
	) {
		error_log('99999 - wrong qtype');
		return;
	}

	// var_dump($qtype);




	$qtoday = date('m') + date('d');
	$mdata  = date('m');
	$ddata  = date('d');
	$mdata  = (string) $mdata;
	if (strlen($mdata) < 2) {
		$mdata = '0' . $mdata;
	}
	$ddata = (string) $ddata;
	if (strlen($ddata) < 2) {
		$ddata = '0' . $ddata;
	}
	$qtoday = $mdata . $ddata;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table_name = $wpdb->prefix . 'sbb_stats';




	/*
	$query      = "UPDATE $table_name SET $qtype  =  $qtype + 1, qtotal = qtotal+1 WHERE date = $qtoday";
	$wpdb->query( sanitize_text_field( $query ) );
	//error_log($query);
	*/


	$r = $wpdb->query(
		$wpdb->prepare(
			"UPDATE `$table_name` 
			SET $qtype = $qtype + 1, qtotal = qtotal + 1
			WHERE date = %s LIMIT 1",
			$qtoday
		)
	);

	if (!$r)
		stopbadbots_sbb_populate_stats();
}
function stopbadbots_create_db_stats()
{
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	// creates my_table in database if not exists
	$table = $wpdb->prefix . 'sbb_stats';
	global $wpdb;
	$table_name = $wpdb->prefix . 'sbb_stats';
	if (stopbadbots_tablexist($table_name)) {
		return;
	}
	$charset_collate = $wpdb->get_charset_collate();
	$sql             = 'CREATE TABLE ' . $table . " (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `date` varchar(4) NOT NULL,
        `qnick` text NOT NULL,
        `qip` text NOT NULL,
        `qfire` text NOT NULL,
        `qref` text NOT NULL,
        `qping` text NOT NULL,
        `quenu` text NOT NULL,
        `qlogin` text NOT NULL,
        `qcom` text NOT NULL,  
        `qcon` text NOT NULL,         
        `qua` text NOT NULL,
        `qfalseg` text NOT NULL,
        `qtools` text NOT NULL,
        `qrate` text NOT NULL,  
        `qbrowser` text NOT NULL,           
        `qother` text NOT NULL,
        `qtotal` varchar(100) NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`date`)
    ) $charset_collate;";
	dbDelta($sql);
}
function stopbadbots_response($stopbadbots_why_block)
{
	global $stopbadbots_active;
	if ($stopbadbots_active == 'yes' and !stopbadbots_block_whitelist_IP()) {


		//http_response_code(403);
		if (!headers_sent()) {
			http_response_code(403);
		} else {
			error_log("[Warning] Another plugin has prematurely sent headers to the browser, which prevents Stop Bad Bots plugin from effectively blocking the visit.");
		}





		stopbadbots_record_log($stopbadbots_why_block);

		if (!headers_sent()) {
			header('HTTP/1.1 403 Forbidden');
			header('Status: 403 Forbidden');
			header('Connection: Close');
		}
		exit();
	}
}
function stopbadbots_ReferDetect($stopbadbots_referer)
{
	global $wpdb, $badreferer;

	if ($stopbadbots_referer == '') {
		return false;
	}

	if (stopbadbots_isourserver()) {
		return false;
	}

	$current_table = $wpdb->prefix . 'sbb_badref';

	//$query         = "SELECT botname, id FROM $current_table WHERE `botstate` =  'Enabled' ";

	//$result        = $wpdb->get_results( sanitize_text_field( $query ) );
	$result = $wpdb->get_results($wpdb->prepare("SELECT botname, id FROM %i WHERE `botstate` = 'Enabled'", $current_table));



	$stopbadbots_found     = '';
	foreach ($result as $results) {
		$name = trim($results->botname);
		if (strlen($name) < 3) {
			continue;
		}
		if (stripos($stopbadbots_referer, $name) !== false) {
			$badreferer = $name;
			return true;
		}
	}
	return false;
}
function stopbadbots_check_memory_old()
{
	global $stopbadbots_memory;
	$stopbadbots_memory['limit'] = (int) ini_get('memory_limit');
	$stopbadbots_memory['usage'] = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 0) : 0;
	if (!defined('WP_MEMORY_LIMIT')) {
		$stopbadbots_memory['msg_type'] = 'notok';
		return;
	}
	$stopbadbots_memory['wp_limit'] = trim(WP_MEMORY_LIMIT);
	if ($stopbadbots_memory['wp_limit'] > 9999999) {
		$stopbadbots_memory['wp_limit'] = ($stopbadbots_memory['wp_limit'] / 1024) / 1024;
	}
	if (!is_numeric($stopbadbots_memory['usage'])) {
		$stopbadbots_memory['msg_type'] = 'notok';
		return;
	}
	if (!is_numeric($stopbadbots_memory['limit'])) {
		$stopbadbots_memory['msg_type'] = 'notok';
		return;
	}
	if ($stopbadbots_memory['usage'] < 1) {
		$stopbadbots_memory['msg_type'] = 'notok';
		return;
	}
	$wplimit                = $stopbadbots_memory['wp_limit'];
	$wplimit                = substr($wplimit, 0, strlen($wplimit) - 1);
	$stopbadbots_memory['wp_limit'] = $wplimit;
	$stopbadbots_memory['percent']  = $stopbadbots_memory['usage'] / $stopbadbots_memory['wp_limit'];
	$stopbadbots_memory['color']    = 'font-weight:normal;';
	if ($stopbadbots_memory['percent'] > .7) {
		$stopbadbots_memory['color'] = 'font-weight:bold;color:#E66F00';
	}
	if ($stopbadbots_memory['percent'] > .85) {
		$stopbadbots_memory['color'] = 'font-weight:bold;color:red';
	}
	$stopbadbots_memory['msg_type'] = 'ok';
	// 2023
	$stopbadbots_memory['free'] = $stopbadbots_memory['wp_limit'] - $stopbadbots_memory['usage'];

	return $stopbadbots_memory;
}


/*
function stopbadbots_check_memory() {
		global $stopbadbots_memory;
		$stopbadbots_memory["color"] = "font-weight:normal;";
		try {
	
	        // PHP $stopbadbots_memory["limit"]
			if(!function_exists('ini_get')){
				$stopbadbots_memory["msg_type"] = "notok";
				return $stopbadbots_memory;
			}
			else{
				$stopbadbots_memory["limit"] = (int) ini_get("memory_limit");
			}

			if (!is_numeric($stopbadbots_memory["limit"])) {
				$stopbadbots_memory["msg_type"] = "notok";
				return $stopbadbots_memory;
			} else {
				if ($stopbadbots_memory["limit"] > 9999999) {
					$stopbadbots_memory["limit"] =
						$stopbadbots_memory["limit"] / 1024 / 1024;
				}
			}
	
	
	        // usage
			if(!function_exists('memory_get_usage')){
				$stopbadbots_memory["msg_type"] = "notok";
				return $stopbadbots_memory;
			}
			else{
				// $bill_install_memory["usage"] = round(memory_get_usage() / 1024 / 1024, 0);
				$stopbadbots_memory["usage"] = (int) memory_get_usage();
			}
	
	
			if ($stopbadbots_memory["usage"] < 1) {
				$stopbadbots_memory["msg_type"] = "notok";
				return $stopbadbots_memory;
			}
			else{
				$stopbadbots_memory["usage"] = round($stopbadbots_memory["usage"] / 1024 / 1024, 0);
	
			}
	
			if (!is_numeric($stopbadbots_memory["usage"])) {
				$stopbadbots_memory["msg_type"] = "notok";
				return $stopbadbots_memory;
			}

	
			// WP
			if (!defined("WP_MEMORY_LIMIT")) {
				$stopbadbots_memory['wp_limit'] = 40;
			} else {
				$stopbadbots_memory['wp_limit'] = (int) WP_MEMORY_LIMIT;

			}		
	

	
	
			$stopbadbots_memory["percent"] =
				$stopbadbots_memory["usage"] / $stopbadbots_memory["wp_limit"];
			$stopbadbots_memory["color"] = "font-weight:normal;";
			if ($stopbadbots_memory["percent"] > 0.7) {
				$stopbadbots_memory["color"] = "font-weight:bold;color:#E66F00";
			}
			if ($stopbadbots_memory["percent"] > 0.85) {
				$stopbadbots_memory["color"] = "font-weight:bold;color:red";
			}
			$stopbadbots_memory["msg_type"] = "ok";
			return $stopbadbots_memory;
		} catch (Exception $e) {
			$stopbadbots_memory["msg_type"] = "notok(7)";
			return $stopbadbots_memory;
		}
	}
*/

function stopbadbots_block_pingback_hook($call)
{
	global $stopbadbots_ip;
	global $stopbadbots_my_radio_report_all_visits;

	if (stopbadbots_isourserver()) {
		return;
	}

	if ($call == 'pingback.ping') {
		stopbadbots_stats_moreone('qping');
		if ($stopbadbots_my_radio_report_all_visits == 'yes') {
			stopbadbots_alertme6($stopbadbots_ip);
		}
		stopbadbots_response('Pingback Blocked');
	}
}
function stopbadbots_block_enumeration()
{
	global $stopbadbots_block_enumeration;
	global $stopbadbots_ip;
	global $stopbadbots_my_radio_report_all_visits;

	if (stopbadbots_isourserver()) {
		return;
	}

	// wp-json/contact-form-7/v1/contact-forms/571/feedback
	$workurl = sanitize_text_field($_SERVER['REQUEST_URI']);
	if (stripos($workurl, 'contact-form-7') !== false) {
		return;
	}
	if (isset($_SERVER['REQUEST_URI'])) {
		if (!preg_match('/(wp-comments-post)/', sanitize_text_field($_SERVER['REQUEST_URI'])) && !empty($_REQUEST['author']) && (int) sanitize_text_field($_REQUEST['author'])) { {
				if ($stopbadbots_block_enumeration == 'yes') {
					stopbadbots_stats_moreone('quenu');
					if ($stopbadbots_my_radio_report_all_visits == 'yes') {
						stopbadbots_alertme7($stopbadbots_ip);
					}
					stopbadbots_response('User Enumeration');
				}
			}
		}
	}
}
function stopbadbots_find_perc()
{
	$stopbadbots_option_name[] = 'stop_bad_bots_active';
	$stopbadbots_option_name[] = 'stop_bad_bots_ip_active';
	$stopbadbots_option_name[] = 'stop_bad_bots_referer_active';
	$stopbadbots_option_name[] = 'stopbadbots_firewall';
	$stopbadbots_option_name[] = 'stop_bad_bots_network';
	$stopbadbots_option_name[] = 'stop_bad_bots_blank_ua';
	$stopbadbots_option_name[] = 'stopbadbots_block_pingbackrequest';
	$stopbadbots_option_name[] = 'stopbadbots_block_enumeration';
	$stopbadbots_option_name[] = 'stopbadbots_block_false_google';
	$stopbadbots_option_name[] = 'stopbadbots_block_spam_comments';
	$stopbadbots_option_name[] = 'stopbadbots_block_spam_contacts';
	$stopbadbots_option_name[] = 'stopbadbots_block_spam_login';
	$stopbadbots_option_name[] = 'stopbadbots_limit_visits';
	// $stopbadbots_option_name[] = 'stopbadbots_rate_limiting_day';
	$stopbadbots_option_name[] = 'stopbadbots_block_http_tools';
	$stopbadbots_option_name[] = 'stopbadbots_install_anti_hacker';

	$wnum = count($stopbadbots_option_name);
	$ctd  = 0;
	for ($i = 0; $i < $wnum; $i++) {
		$yes_or_not = trim(sanitize_text_field(get_site_option($stopbadbots_option_name[$i], '')));
		if (strtoupper($yes_or_not) == 'YES') {
			$ctd++;
		}
		// else
		// die($stopbadbots_option_name[$i]);

	}
	/*
	var_dump($ctd);
	var_dump($wnum);
	die();
	*/

	$perc = ($ctd / $wnum) * 100;
	$perc = round($perc, 0, PHP_ROUND_HALF_UP);
	if ($perc > 100) {
		$perc = 100;
	}
	if (trim(sanitize_text_field(get_site_option('stopbadbots_checkversion', ''))) == '') {
		if ($perc > 60) {
			$perc = 60;
		}
		update_option('stopbadbots_block_false_google', '');
		update_option('stopbadbots_firewall', '');
	}
	if ($ctd < $wnum and $perc > 99) {
		$perc = 90;
	}
	if ($ctd == $wnum and $perc < 100) {
		$perc = 100;
	}
	return $perc;
}



function stopbadbots_cron_function_clear_debug()
{
	global $wpdb;
	global $stopbadbots_rate_penalty;
	global $stopbadbots_keep_log;
	error_log('stopbadbots_cron_function_clear iniciado'); // Log de início
	if (get_transient('stopbadbots_doing_clear_database')) {
		error_log('Transiente stopbadbots_doing_clear_database existe. Saindo da função.'); // Log se o transiente existir
		return;
	}
	set_transient('stopbadbots_doing_clear_database', true, MINUTE_IN_SECONDS);
	error_log('Transiente stopbadbots_doing_clear_database definido'); // Log ao definir o transiente
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$quant = 60 * 24;
	error_log('Valor de stopbadbots_rate_penalty 0: ' . print_r($stopbadbots_rate_penalty, true)); // Log do valor de rate_penalty
	switch ($stopbadbots_rate_penalty) {
		case 1:
			$quant = 9999999999;
			break;
		case 2:
			$quant = 5;
			break;
		case 3:
			$quant = 30;
			break;
		case 4:
			$quant = 60;
			break;
		case 5:
			$quant = 120;
			break;
		case 6:
			$quant = 360;
			break;
		case 7:
			$quant = 60 * 24;
			break;
	}
	error_log('Valor de quant calculado: ' . $quant); // Log do valor calculado de quant
	$table_name = $wpdb->prefix . 'sbb_badips';
	// $result = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE `added` = 'Temp' AND `botdate` < DATE_SUB(NOW(), INTERVAL %d MINUTE)", $quant));
	// Prepara a consulta
	$query = $wpdb->prepare("DELETE FROM $table_name WHERE `added` = 'Temp' AND `botdate` < DATE_SUB(NOW(), INTERVAL %d MINUTE)", $quant);
	error_log('Consulta preparada 1: ' . $query);
	// Executa a consulta
	$result = $wpdb->query($query);
	if ($result === false) {
		error_log('Erro ao executar a consulta de exclusão na tabela sbb_badips: ' . $wpdb->last_error); // Log do erro na consulta
	} else {
		error_log('Registro(s) excluído(s) na tabela sbb_badips: ' . $result); // Log do número de registros excluídos
	}
	$sbb_mytable_name = $wpdb->prefix . 'sbb_fingerprint';
	$result = $wpdb->query($wpdb->prepare('UPDATE ' . $sbb_mytable_name . ' SET deny = 0'));
	if ($result === false) {
		error_log('Erro ao atualizar a tabela sbb_fingerprint: ' . $wpdb->last_error); // Log do erro na atualização
	}
	$wdata = date('md', strtotime('tomorrow'));
	$table_name = $wpdb->prefix . 'sbb_stats';
	$result = $wpdb->get_results($wpdb->prepare("UPDATE $table_name SET qnick='', qip='', qtotal='', qfire='', qref='', qua='', qping='', quenu='', qother='', qlogin='', qcom='', qcon='', qfalseg='', qtools='', qbrowser='', qrate='' WHERE `date` = %s", $wdata));
	if ($result === false) {
		error_log('Erro ao atualizar a tabela sbb_stats: ' . $wpdb->last_error); // Log do erro na atualização
	}
	if (get_transient('stopbadbots_cron_clear_frequency')) {
		error_log('Transiente stopbadbots_cron_clear_frequency existe. Saindo da função.'); // Log se o transiente existir
		return;
	} else {
		set_transient('stopbadbots_cron_clear_frequency', true, DAY_IN_SECONDS);
		error_log('Transiente stopbadbots_cron_clear_frequency definido'); // Log ao definir o transiente
	}
	//$stopbadbots_keep_log = sanitize_text_field(get_option('stopbadbots_keep_log', '30'));
	//error_log('puxei do get options agora ' . $stopbadbots_keep_log);
	$days_to_keep = intval($stopbadbots_keep_log);
	error_log('days to keep ' . $days_to_keep);
	if ($days_to_keep < 1) {
		error_log('Nenhum registro para manter. Saindo da função.'); // Log se não houver registros para manter
		return;
	}
	// Log the value of days_to_keep for debugging
	error_log('Valor de days_to_keep::::::::: ' . $days_to_keep);
	if ($days_to_keep < 1) {
		error_log('days_to_keep é menor que 1, saindo da função.');
		return;
	}
	$table_name = $wpdb->prefix . 'sbb_visitorslog';
	// Log the current records in the table before deletion
	$current_records = $wpdb->get_results("SELECT * FROM $table_name WHERE `date` < DATE_SUB(CURDATE(), INTERVAL $days_to_keep DAY)");
	error_log('Registros atuais a serem excluídos na tabela::: ' . $table_name . ': ' . print_r($current_records, true));
	// Execute the delete query
	//$result = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE `date` < DATE_SUB(CURDATE(), INTERVAL %d DAY)", $days_to_keep));
	// Prepara a consulta
	$query = $wpdb->prepare("DELETE FROM $table_name WHERE `date` < DATE_SUB(CURDATE(), INTERVAL %d DAY)", $days_to_keep);
	error_log('Consulta preparada: ' . $query);
	// Executa a consulta
	$result = $wpdb->query($query);
	// Log the result of the deletion
	if ($result === false) {
		error_log('Erro ao executar a consulta de exclusão: ' . $wpdb->last_error);
	} else {
		error_log('Registro(s) excluído(s) na tabela ' . $table_name . ': ' . $result);
	}
	/*
	$result = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE `date` < DATE_SUB(CURDATE(), INTERVAL %d DAY)", $days_to_keep));
	if ($result === false) {
		error_log('Erro ao excluir registros na tabela sbb_visitorslog: ' . $wpdb->last_error); // Log do erro na exclusão
	} else {
		error_log('Registro(s) excluído(s) na tabela sbb_visitorslog: ' . $result); // Log do número de registros excluídos
	}
		*/
	$table_name = $wpdb->prefix . 'sbb_fingerprint';
	$result = $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE `data` < DATE_SUB(CURDATE(), INTERVAL %d DAY)", $days_to_keep));
	if ($result === false) {
		error_log('Erro ao excluir registros na tabela sbb_fingerprint: ' . $wpdb->last_error); // Log do erro na exclusão
	} else {
		error_log('Registro(s) excluído(s) na tabela sbb_fingerprint: ' . $result); // Log do número de registros excluídos
	}
}


// 5 minutes...
function stopbadbots_cron_function_clear()
{
	global $wpdb;
	global $stopbadbots_rate_penalty;
	global $stopbadbots_keep_log;

	if (get_transient('stopbadbots_doing_clear_database')) {
		return;
	}
	set_transient('stopbadbots_doing_clear_database', true, MINUTE_IN_SECONDS);

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$quant = 60 * 24;
	switch ($stopbadbots_rate_penalty) {
		case 1:
			$quant = 9999999999;
			break;
		case 2:
			$quant = 5;
			break;
		case 3:
			$quant = 30;
			break;
		case 4:
			$quant = 60;
			break;
		case 5:
			$quant = 120;
			break;
		case 6:
			$quant = 360;
			break;
		case 7:
			$quant = 60 * 24;
			break;
	}
	/*
	$table_name = $wpdb->prefix . 'sbb_badips';
	$sql        = 'delete from ' . $table_name . " WHERE `added` = 'Temp' and `botdate` <  CURDATE() - interval " . $quant . ' minute';
	// dbDelta($sql);
	$wpdb->query( $sql );
	*/
	$table_name = $wpdb->prefix . 'sbb_badips';
	/*
	$sql = $wpdb->prepare(
		"DELETE FROM {$table_name} WHERE `added` = 'Temp' AND `botdate` < DATE_SUB(NOW(), INTERVAL %d MINUTE)",
		$quant
	);
	$wpdb->query($sql);
	*/
	$wpdb->query($wpdb->prepare("DELETE FROM %i WHERE `added` = 'Temp' AND `botdate` < DATE_SUB(NOW(), INTERVAL %d MINUTE)", $table_name, $quant));



	// Reset Deny 1 each Hour...
	$sbb_mytable_name = $wpdb->prefix . 'sbb_fingerprint';
	//$query = 'UPDATE ' . $sbb_mytable_name . ' set deny = 0';
	//$wpdb->query($query);

	$wpdb->query($wpdb->prepare('UPDATE %i SET deny = 0', $sbb_mytable_name));

	//$r = $wpdb->query($wpdb->prepare('UPDATE ' . $sbb_mytable_name . ' SET deny = 0'));

	/*
	// warning...
	// Function wpdb::prepare was called incorrectly. The query argument of wpdb::prepare() must have a placeholder. 

	$wpdb->query(
		$wpdb->prepare(
			"UPDATE {$sbb_mytable_name} SET deny = 0"
		)
	);
	*/

	$wdata      = date('md', strtotime('tomorrow'));
	$table_name = $wpdb->prefix . 'sbb_stats';

	/*
	$wpdb->get_results(
		$wpdb->prepare(
			"UPDATE `$table_name` 
			SET qnick='', qip='', qtotal='', qfire='', qref='', qua='', qping='', quenu='',
			qother='', qlogin='', qcom='', qcon='', qfalseg='', qtools='', qbrowser='', qrate=''
			WHERE `date` = %s",
			$wdata
		)
	);
	*/

	$wpdb->get_results($wpdb->prepare("UPDATE %i SET qnick='', qip='', qtotal='', qfire='', qref='', qua='', qping='', quenu='', qother='', qlogin='', qcom='', qcon='', qfalseg='', qtools='', qbrowser='', qrate='' WHERE `date` = %s", $table_name, $wdata));



	/*
	//$wdata = esc_sql($wdata); // Sanitizing the variable
	$wpdb->get_results(
		"UPDATE `$table_name` 
		SET qnick='', qip='', qtotal='', qfire='', qref='', qua='', qping='', quenu='',
		qother='', qlogin='', qcom='', qcon='', qfalseg='', qtools='', qbrowser='', qrate=''
		WHERE `date` = $wdata"
	);
	*/

	// once a day 
	if (get_transient('stopbadbots_cron_clear_frequency'))
		return;
	else
		set_transient('stopbadbots_cron_clear_frequency', true, DAY_IN_SECONDS);



	//$sql        = 'delete from ' . $table_name . ' WHERE `data` <  CURDATE() - interval ' . $stopbadbots_keep_log   . ' day';
	// dbDelta($sql);
	//$wpdb->query( $sql );



	$days_to_keep = intval($stopbadbots_keep_log);

	if ($days_to_keep < 1)
		return;


	$table_name = $wpdb->prefix . 'sbb_visitorslog';
	/*
	$r = $wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$table_name} 
			WHERE `date` < DATE_SUB(CURDATE(), INTERVAL %d DAY)",
			$days_to_keep
		)
	);
	*/
	$r = $wpdb->query($wpdb->prepare("DELETE FROM %i WHERE `date` < DATE_SUB(CURDATE(), INTERVAL %d DAY)", $table_name, $days_to_keep));


	$table_name = $wpdb->prefix . 'sbb_fingerprint';
	/*
	if ($days_to_keep > 0) {
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$table_name} 
				WHERE `data` < DATE_SUB(CURDATE(), INTERVAL %d DAY)",
				$days_to_keep
			)
		);
    }
	*/
	$wpdb->query($wpdb->prepare("DELETE FROM %i WHERE `data` < DATE_SUB(CURDATE(), INTERVAL %d DAY)", $table_name, $days_to_keep));
}


// stop_bad_bots_autoupdate
function stopbadbots_auto_update($update, $item)
{
	// Array of plugin slugs to always auto-update
	// use textdomain...
	$plugins = array(
		'stopbadbots',
	);
	// var_dump($item->slug);
	if (in_array($item->slug, $plugins)) {
		// Always update plugins in this array
		return true;
	} else {
		// Else, use the normal API response to decide whether to update or not
		return $update;
	}
}
/*
function stopbadbots_include_jquery()
{
	wp_enqueue_script("jquery");
	wp_enqueue_script('jquery-ui-core');
	wp_register_script('sbb-cookies', STOPBADBOTSURL .
		'assets/js/stopbadbots_cookies.js', array('jquery'), null, true);
	wp_enqueue_script('sbb-cookies');
}
*/
//

function stopbadbots_cached_gethostbyaddr($ip)
{
	// Generate transient key based on IP address
	$transient_key = 'stopbadbots_dns_cache_' . md5($ip);
	$dns_cache_ttl = 3600; // 1 hour

	// Check if transient exists and has not expired
	if (false !== ($hostname = get_transient($transient_key))) {
		return $hostname;
	} else {
		// Perform DNS lookup
		$hostname = gethostbyaddr($ip);

		// Sanitize hostname to ensure it's safe for storage
		$hostname = sanitize_text_field($hostname);

		// Store hostname in transient with TTL
		set_transient($transient_key, $hostname, $dns_cache_ttl);

		return $hostname;
	}
}

function stopbadbots_maybe_search_engine($ua)
{
	global $stopbadbots_ip;

	// Check if the user agent matches Facebook or Twitter bots
	if (stripos($ua, 'facebookexternalhit') !== false || stripos($ua, 'twitterbot') !== false) {
		// If it matches Facebook or Twitter bots, return true
		return true;
	}

	// List of known search engine bots for host verification
	$search_bots_host = array(
		'googlebot',
		'Google-InspectionTool',
		'msn.com',
		'slurp',
	);

	// Check if the origin host matches any known search engine bots
	if (!empty($stopbadbots_ip) && filter_var($stopbadbots_ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE)) {
		try {
			// Get sanitized hostname using cached DNS lookup
			$host = stopbadbots_cached_gethostbyaddr($stopbadbots_ip);

			// If the hostname is valid, perform the host checks
			if ($host !== false && $host !== '' && is_string($host)) {
				$host = strtolower(trim($host));

				foreach ($search_bots_host as $bot) {
					if (stripos($host, $bot) !== false) {
						return true;
					}
				}
			}
		} catch (Exception $e) {
			return true;
		}
	} else {
		return false;
	}

	// If no search engine bot or Facebook/Twitter bot is found, return false
	return false;
}

/*
function stopbadbots_maybe_search_engine($ua) {
    global $stopbadbots_ip;

    // Check if the user agent matches Facebook or Twitter bots
    if (stripos($ua, 'facebookexternalhit') !== false || stripos($ua, 'twitterbot') !== false) {
        // If it matches Facebook or Twitter bots, return true
        return true;
    }

    // List of known search engine bots for host verification
    $search_bots_host = array(
        'googlebot',
        'Google-InspectionTool',
        'msn.com',
        'slurp',
    );

    // Check if the origin host matches any known search engine bots


	if (!empty($stopbadbots_ip) and filter_var($stopbadbots_ip, FILTER_VALIDATE_IP)) {
		//$host = strtolower(trim(strip_tags(gethostbyaddr($stopbadbots_ip))));

		try {

			
			//$host = gethostbyaddr($stopbadbots_ip);
			$host = strip_tags(gethostbyaddr($stopbadbots_ip));

			if ($host !== false && $host !== '' && is_string($host) ) {
                $host = trim($host);
            } 

			// $host = !empty($host) ? trim($host) : '';
			$host = strtolower($host);
		} catch (Exception $e) {
			 return true;
		}
	} else {
		return false;
	}
	
    foreach ($search_bots_host as $bot) {
		if ($host !== null && stripos($host, $bot) !== false) {
			return true;
		}
    }

    // If no search engine bot or Facebook/Twitter bot is found, return false
    return false;
}
*/

/*
function stopbadbots_maybe_search_engine( $ua ) {
	global $stopbadbots_ip;
	// crawl-66-249-73-151.googlebot.com
	// msnbot-157-55-39-204.search.msn.com
	$ua       = trim( strtolower( $ua ) );
	$mysearch = array(
		'googlebot',
		'Google-InspectionTool',
		'bingbot',
		'slurp',
		'Twitterbot',
		'facebookexternalhit',
	);
	
	for ( $i = 0; $i < count( $mysearch ); $i++ ) {
		if ( stripos( $ua, $mysearch[ $i ] ) !== false ) {
			if ( $mysearch[ $i ] == 'facebookexternalhit' ) {
				return true;
			}
			if ( $mysearch[ $i ] == 'Twitterbot' ) {
				return true;
			}

			$mysearch1 = array(
				'googlebot',
				'Google-InspectionTool',
				'msn.com',
				'slurp',
			);

			// $host      =       strip_tags( gethostbyaddr( $stopbadbots_ip ) );
			$host      = trim( strip_tags( gethostbyaddr( $stopbadbots_ip ) ) );
			if ( $host == trim( $stopbadbots_ip ) ) {
				return false;
			}
			if ( stripos( $host, $mysearch1[ $i ] ) !== false ) {
				return true;
			}
		}
	}
	return false;
} */






function stopbadbots_howmany_bots_visit()
{
	global $wpdb;
	global $stopbadbots_ip;
	global $stopbadbots_rate_limiting;
	if ($stopbadbots_rate_limiting < '1') {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_visitorslog';

	/*
	$query = "select count(*) FROM " . $table_name .
		" WHERE ip = '" . $stopbadbots_ip . "'
				AND `bot` = '1'
				AND `date` >= CURDATE() - interval 1 minute
				ORDER BY `date` DESC";
				*/

	// return $wpdb->get_var(sanitize_text_field($query));
	/*
	return $wpdb->get_var(
		$wpdb->prepare("SELECT  count(*) FROM `$table_name` 
     WHERE ip = %s
      AND `bot` = '1'
      AND `date` >=  CURDATE() - interval 1 minute ORDER BY `date` DESC",
			$stopbadbots_ip
		)
	);
	*/
	return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE ip = %s AND `bot` = '1' AND `date` >= CURDATE() - INTERVAL 1 MINUTE ORDER BY `date` DESC", $table_name, $stopbadbots_ip));
}
function stopbadbots_howmany_bots_visit2()
{
	global $wpdb;
	global $stopbadbots_ip;
	global $stopbadbots_rate_limiting_day;
	if ($stopbadbots_rate_limiting_day < '1') {
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_visitorslog';

	/*
	$query = "select count(*) FROM " . $table_name .
		" WHERE ip = '" . $stopbadbots_ip . "'
				AND `bot` = '1'
				AND `date` >= CURDATE() - interval 1 hour
				ORDER BY `date` DESC";
				*/

	// return $wpdb->get_var(sanitize_text_field($query));

	/*
	return $wpdb->get_var(
		$wpdb->prepare(
			"
    SELECT  count(*) FROM `$table_name` 
    WHERE ip =  %s
      AND `bot` = '1'
      AND `date` >=  CURDATE() - interval 1 hour ORDER BY `date` DESC",
			$stopbadbots_ip
		)
	);
	*/
	return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE ip = %s AND `bot` = '1' AND `date` >= CURDATE() - INTERVAL 1 HOUR ORDER BY `date` DESC", $table_name, $stopbadbots_ip));
}
function stopbadbots_first_time()
{
	global $wpdb;
	global $stopbadbots_ip;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_visitorslog';

	/*
	$query = "select count(*) FROM " . $table_name .
		" WHERE ip = '" . $stopbadbots_ip . "'
			AND `date` >= CURDATE() - interval 7 day ORDER BY `date` DESC";
	*/
	// return $wpdb->get_var(sanitize_text_field($query));

	/*
	return $wpdb->get_var(
		$wpdb->prepare(
			"
    SELECT  count(*) FROM `$table_name`
      WHERE ip = %s
        AND `date` >=  CURDATE()- interval 7 day ORDER BY `date` DESC",
			$stopbadbots_ip
		)
	);
	*/
	return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE ip = %s AND `date` >= CURDATE() - INTERVAL 7 DAY ORDER BY `date` DESC", $table_name, $stopbadbots_ip));
}
function stopbadbots_update_httptools($astopbadbots_http_tools)
{
	// Load into table
	global $wpdb;

	$stopbadbots_http_tools  = trim(get_site_option('stopbadbots_http_tools', ''));
	$astopbadbots_http_tools = explode(PHP_EOL, $stopbadbots_http_tools);

	if (count($astopbadbots_http_tools) < 1) {
		return;
	}
	$table_name = $wpdb->prefix . 'sbb_http_tools';
	$query      = 'SELECT name FROM ' . $table_name;
	// testar se table tem zero...
	$results9 = $wpdb->get_results(sanitize_text_field($query));
	// $results10 = json_decode(json_encode($results9), true);
	$names = array();
	foreach ($results9 as $array) {
		$names[] = trim($array->name);
	}
	$total = count($astopbadbots_http_tools);
	for ($i = 0; $i < $total; $i++) {
		$needle = trim($astopbadbots_http_tools[$i]);
		if (array_search($needle, $names, true) === false) {

			$needle = str_replace("'", '', $needle);



			// $query = 'select COUNT(*) from ' . $table_name . " WHERE name = '$needle'";


			$results99  = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT  COUNT(*) FROM `$table_name` 
			 WHERE name = %s limit 1",
					$needle
				)
			);




			if ($results99 > 0) {
				continue;
			}






			/*
			$r = $wpdb->get_results(
				$wpdb->prepare(
					"INSERT INTO `$table_name` 
                (name) 
                VALUES (%s)",
					$needle
				)
			);

			CREATE TABLE `wp_sbb_http_tools` (
				`id` mediumint(9) NOT NULL,
				`name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
				`quant` int(11) NOT NULL,
				`flag` varchar(1) COLLATE utf8mb4_unicode_520_ci NOT NULL

			*/

			$r = $wpdb->get_results(
				$wpdb->prepare(
					"INSERT INTO `$table_name` 
						(name, quant, flag)
						VALUES (%s, 0, '')",
					$needle
				)
			);
		}
	}
}
function stopbadbots_grava_fingerprint()
{
	global $stopbadbots_ip;
	global $wpdb;

	if (isset($_REQUEST)) {
		$fingerprint = sanitize_text_field($_REQUEST['fingerprint']);
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$mytable_name = $wpdb->prefix . 'sbb_fingerprint';

		// $query = "SELECT * from " . $mytable_name . "
		// WHERE ip = '$stopbadbots_ip' limit 1";

		$result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * from `$mytable_name` 
            WHERE ip = %s limit 1",
				$stopbadbots_ip
			)
		);

		if ($result) {

			$fingerprintDb = trim($result->fingerprint);

			if (empty($fingerprintDb) and !empty($fingerprint)) {

				/*
				$query = "UPDATE " . $mytable_name .
					" set fingerprint = " . $fingerprint . "
					WHERE  ip = '" . $stopbadbots_ip . "' LIMIT 1";

				$r = $wpdb->get_results(sanitize_text_field($query));
				*/

				$r = $wpdb->get_results(
					$wpdb->prepare(
						"UPDATE  `$mytable_name`
                    set fingerprint = %s 
                    WHERE ip = %s limit 1",
						$fingerprint,
						$stopbadbots_ip
					)
				);
			}

			die();
		}

		$r = $wpdb->get_results(
			$wpdb->prepare(
				"INSERT INTO `$mytable_name` 
            (ip, fingerprint)
            VALUES (%s, %s)",
				$stopbadbots_ip,
				$fingerprint
			)
		);
	}

	die();
}
function stopbadbots_addfieldlogin()
{
	echo '<input type="hidden" id="stopbadbots_key" name="stopbadbots_key" value="1"  />';
}
if ($stopbadbots_block_spam_login == 'yes') {
	add_action('login_form', 'stopbadbots_addfieldlogin');
}
function stopbadbos_validate_login($user, $password)
{
	if (!isset($_POST['stopbadbots_key'])) {
		global $stopbadbots_my_radio_report_all_visits, $stopbadbots_ip;
		stopbadbots_stats_moreone('qlogin');
		if ($stopbadbots_my_radio_report_all_visits == 'yes') {
			stopbadbots_alertme11($stopbadbots_ip);
		}
		stopbadbots_response('Login Blocked');
	}
	return $user;
}
function stopbadbots_check_for_spam()
{
	$stopbadbots_spam_flag = false;
	$stopbadbots_key        = '';
	if (isset($_POST['stopbadbots_key'])) {
		$stopbadbots_key = trim(sanitize_text_field($_POST['stopbadbots_key']));
	}
	if ($stopbadbots_key != '1') {
		$stopbadbots_spam_flag = true;
	}
	return $stopbadbots_spam_flag;
}
function stopbadbots_gocom()
{
	global $stopbadbots_now;
	$stopbadbots_con = get_option('stopbadbots_con', $stopbadbots_now);
	if ($stopbadbots_con > $stopbadbots_now) {
		return false;
	} else {
		return true;
	}
}
function stopbadbots_confail()
{
	global $stopbadbots_after;
	add_option('stopbadbots_con', $stopbadbots_after);
	update_option('stopbadbots_con', $stopbadbots_after);
}
function stopbadbots_check_4spammer($result, $tag)
{
	global $stopbadbots_ip;
	if (stopbadbots_check_for_spam()) {

		if (is_object($tag) && isset($tag->name)) {
			$name = $tag->name;
		} elseif (is_array($tag) && isset($tag['name'])) {
			$name = $tag['name'];
		} else {
			return $result;
		}

		//$name = $tag->name;

		add_filter('wpcf7_validation_error', 'cf7_add_custom_class', 10, 2);
		add_filter('wpcf7_display_message', 'stopbadbots_validation_messages_fail2', 10, 2);
		// add_action(“wpcf7_ajax_json_echo”, “cf7_change_response_message”,10,2);
		$result['valid']  = false;
		$result['reason'] = array($name => wpcf7_get_message('Spam'));
		return $result;
	}
	// $stopbadbots_ip = '175.139.165.216';
	if (stopbadbots_is_spammer($stopbadbots_ip)) {
		$name = $tag->name;
		add_filter('wpcf7_validation_error', 'cf7_add_custom_class', 10, 2);
		add_filter('wpcf7_display_message', 'stopbadbots_validation_messages_fail', 10, 2);
		// add_action(“wpcf7_ajax_json_echo”, “cf7_change_response_message”,10,2);
		$result['valid']  = false;
		$result['reason'] = array($name => wpcf7_get_message('Spam'));
	}
	return $result;
}
function stopbadbots_validation_messages_fail($message, $status)
{
	$message = esc_attr__('Your IP is blacklisted on Internet Public Databases. Please, use another way to contact us.', 'stopbadbots');
	return $message;
}
function stopbadbots_validation_messages_fail2($message, $status)
{
	$message = esc_attr__("Looks Like This Message doesn't come from our site. Please, use another way to contact us.", 'stopbadbots');
	return $message;
}
// ------------------------------------
function stopbadbots_check_comment($commentdata)
{
	global $stopbadbots_ip, $stopbadbots_my_radio_report_all_visits;
	// global $withcomments; // WP flag to show comments on all pages
	extract($commentdata);
	if (!is_user_logged_in() && $comment_type != 'pingback' && $comment_type != 'trackback') {
		// if ((is_singular() || $withcomments) && comments_open()) {
		if (stopbadbots_check_for_spam()) {
			stopbadbots_stats_moreone('qcom');
			if ($stopbadbots_my_radio_report_all_visits == 'yes') {
				stopbadbots_alertme10($stopbadbots_ip);
			}
			stopbadbots_response('Comment Blocked');
		}
		// }
	}
	// $stopbadbots_ip = '175.139.165.216';
	if (stopbadbots_is_spammer($stopbadbots_ip)) {
		stopbadbots_stats_moreone('qcom');
		if ($stopbadbots_my_radio_report_all_visits == 'yes') {
			stopbadbots_alertme10($stopbadbots_ip);
		}
		stopbadbots_response('Spammer Blocked');
	}
	return $commentdata;
}
function stopbadbots_is_spammer($ip)
{
	return false;

	// spammer...
	// $stopbadbots_ip = '1.0.133.100';

	//$urlcurl = 'https://api.stopforumspam.org/api';

	/*
	$data = array(
		'method'      => 'POST',
		'timeout'     => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking'    => true,
		'headers'     => array(),
		'body'        => array(
			'username' => 'bob',
			'password' => '1234xyz'
		),
		'cookies'     => array()
	);
	*/

	$data = array(
		'ip'      => $ip,
		'timeout' => 5,
		'method'  => 'POST',
		'body'    => array(
			'ip' => $ip,
		),

	);

	/////$result = wp_remote_post( $urlcurl, $data );

	///// $http_code = wp_remote_retrieve_response_code( $result );

	if ($http_code <> 200) {
		return false;
	}

	if (strpos($result['body'], 'yes')) {
		return true;
	} else {
		return false;
	}
}
function stopbadbots_check_false_googlebot()
{
	// crawl-66-249-73-151.googlebot.com
	// msnbot-157-55-39-204.search.msn.com
	// msnbot-157-55-39-143.search.msn.com
	global $stopbadbots_ip;
	$ua        = stopbadbots_get_ua();
	$mysearch  = array(
		'googlebot',
		'bingbot',
		'msn.com',
	);
	$mysearch1 = array(
		'googlebot',
		'msnbot',
		'msnbot',
	);
	for ($i = 0; $i < count($mysearch); $i++) {
		if (stripos($ua, $mysearch[$i]) !== false) {
			try {
				if (!empty($stopbadbots_ip) and filter_var($stopbadbots_ip, FILTER_VALIDATE_IP)) {
					$stopbadbots_ip = trim($stopbadbots_ip);
					$host = gethostbyaddr($stopbadbots_ip);
					if ($host !== null) {
						$host = strip_tags($host);
					}
					$host = !empty($host) ? trim($host) : '';
					$host = strtolower($host);
					if ($host == $stopbadbots_ip) {
						return true;
					}

					if (stripos($host, $mysearch1[$i]) === false) {
						return true;
					}
				}
			} catch (Exception $e) {
				return false;
			}
		}
	}
	return false;
}

function stopbadbots_record_log($stopbadbots_why_block = '')
{
	global $wpdb;
	global $stopbadbots_ip;
	global $stopbadbots_is_human;
	global $stopbadbots_method;
	global $stopbadbots_request_url;
	global $stopbadbots_referer;
	global $stopbadbots_userAgentOri;
	// global $stopbadbots_access;
	global $stopbadbots_amy_whitelist;
	global $stopbadbots_maybe_search_engine;
	// if (is_admin() or is_super_admin())
	// return;

	// debug2($stopbadbots_is_human);

	if (stopbadbots_block_whitelist_IP()) {
		return;
	}

	if ($stopbadbots_request_url == '/favicon.ico')
		return;


	if (@is_404()) {
		$stopbadbots_response = '404';
	} else {
		$stopbadbots_response = http_response_code();
	}

	if ($stopbadbots_is_human == '0') {
		$bot                  = '1';
		$stopbadbots_is_human = 'Bot';
	} elseif ($stopbadbots_is_human == '1') {
		$bot                  = '0';
		$stopbadbots_is_human = 'Human';
	} else {
		$bot                  = '?';
		$stopbadbots_is_human = 'Maybe';
	}

	if (!empty(trim($stopbadbots_why_block))) {
		$stopbadbots_response = 403;
	}
	if ($stopbadbots_response == 403) {
		$stopbadbots_access = 'Denied';
	} else {
		$stopbadbots_access = 'OK';
	}

	if (!$stopbadbots_maybe_search_engine) {
		if ($stopbadbots_is_human == 'Bot' and $stopbadbots_access == 'Denied' and $stopbadbots_response != 403)
			$stopbadbots_access = 'Masked';

		//if($stopbadbots_is_human == '?' and $stopbadbots_access != 'Denied' )
		//  $stopbadbots_access = 'Masked';

		if ($stopbadbots_is_human != 'Human' and $stopbadbots_access != 'Denied')
			$stopbadbots_access = 'Masked';
	}


	$table_name = $wpdb->prefix . 'sbb_visitorslog';

	$stopbadbots_userAgentOri = str_replace("'", "\'", $stopbadbots_userAgentOri);

	$stopbadbots_request_url = str_replace("'", "\'", $stopbadbots_request_url);
	$stopbadbots_referer     = str_replace("'", "\'", $stopbadbots_referer);

	//debug2(debug_backtrace());

	/*
	CREATE TABLE `wp_sbb_visitorslog` (
		`id` mediumint(9) NOT NULL,
		`ip` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
		`human` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		`response` varchar(5) COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`bot` varchar(1) COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`method` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`url` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`referer` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`ua` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`access` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`reason` text COLLATE utf8mb4_unicode_520_ci NOT NULL
	  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

	  
	$r = $wpdb->query(
		$wpdb->prepare(
			"INSERT INTO `$table_name`
			(reason,ip, response, human, bot, method, url, referer, access, ua)
			VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			$stopbadbots_why_block,
			$stopbadbots_ip,
			$stopbadbots_response,
			$stopbadbots_is_human,
			$bot,
			$stopbadbots_method,
			$stopbadbots_request_url,
			$stopbadbots_referer,
			$stopbadbots_access,
			$stopbadbots_userAgentOri
		)
	);
	*/


	$r = $wpdb->query(
		$wpdb->prepare(
			"INSERT INTO `$table_name`
			(ip, date, human, response, bot, method, url, referer, ua, access, reason)
			VALUES (%s, CURRENT_TIMESTAMP(), %s, %s, %s, %s, %s, %s, %s, %s, %s)",
			$stopbadbots_ip,
			$stopbadbots_is_human,
			$stopbadbots_response,
			$bot,
			$stopbadbots_method,
			$stopbadbots_request_url,
			$stopbadbots_referer,
			$stopbadbots_userAgentOri,
			$stopbadbots_access,
			$stopbadbots_why_block
		)
	);



	return;
}


function stopbadbots_add_whitelist()
{

	global $stopbadbots_ip_whitelist;
	global $astopbadbots_ip_whitelist;



	if (!isset($_POST['stopbadbots_nonce_table']) || !wp_verify_nonce(sanitize_text_field($_POST['stopbadbots_nonce_table']), 'stopbadbots_view_visits')) {
		wp_die('Nonce Fail.');
	}

	if (!current_user_can('administrator'))
		wp_die('Fail by Administration Permissions');


	$stopbadbots_ip_whitelist  = trim(sanitize_text_field(get_site_option('stopbadbots_ip_whitelist', '')));
	$astopbadbots_ip_whitelist = explode(' ', $stopbadbots_ip_whitelist);
	if (!isset($_REQUEST['ip'])) {
		die(' 1');
	}
	$ip = trim(filter_var(sanitize_text_field($_REQUEST['ip']), FILTER_VALIDATE_IP));
	if (empty($ip)) {
		die(' 2');
	}
	if (count($astopbadbots_ip_whitelist) < 1) {
		die('  3');
	}
	for ($i = 0; $i < count($astopbadbots_ip_whitelist); $i++) {
		$ip_address = $astopbadbots_ip_whitelist[$i];
		if (stripos($ip_address, $ip) !== false) {
			die(' 4');
		}
	}
	asort($astopbadbots_ip_whitelist);
	$text = '';
	for ($i = 0; $i < count($astopbadbots_ip_whitelist); $i++) {
		if (!empty($text)) {
			$text .= PHP_EOL;
		}
		$text .= $astopbadbots_ip_whitelist[$i];
	}
	$text .= PHP_EOL . $ip;
	if (!add_option('stopbadbots_ip_whitelist', $text)) {
		update_option('stopbadbots_ip_whitelist', $text);
	}
	die();
}

function stopbadbots_add_blacklist()
{
	global $wpdb;


	if (!isset($_POST['stopbadbots_nonce_table']) || !wp_verify_nonce(sanitize_text_field($_POST['stopbadbots_nonce_table']), 'stopbadbots_view_visits')) {
		wp_die('Nonce Fail.');
	}

	if (!current_user_can('administrator'))
		wp_die('Fail by Administration Permissions');



	$table_name = $wpdb->prefix . "sbb_badips";
	$botflag = '6';
	if (!isset($_REQUEST['ip'])) {
		die(' error -1');
	}
	$stopbadbots_ip = trim(filter_var(sanitize_text_field($_REQUEST['ip']), FILTER_VALIDATE_IP));
	if (empty($stopbadbots_ip)) {
		die(' error -2');
	}
	$results9  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT  * FROM `$table_name` 
     WHERE botip = %s limit 1",
			$stopbadbots_ip
		)
	);
	if (count($results9) > 0) {
		$r = $wpdb->query(
			$wpdb->prepare(
				"UPDATE `$table_name` 
				SET botstate = 'Enabled'
				WHERE botip = %s",
				$stopbadbots_ip
			)
		);
		die('!');
	} else {
		/*
		$r = $wpdb->get_results(
			$wpdb->prepare(
				"INSERT INTO `$table_name` 
				(botip, botstate, botflag, added)		
			VALUES (%s, 'Enabled' , %s , 'User')",
				$stopbadbots_ip,
				$botflag
			)
		);
		*/
		/*
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `botip` varchar(100) NOT NULL,
        `botobs` text NOT NULL,
        `botstate` varchar(10) NOT NULL,
        `botblocked` mediumint(9) NOT NULL,
        `botdate` timestamp NOT NULL,
        `added` varchar(30)NOT NULL,
        `botflag` varchar(1) NOT NULL,
        `botcountry` varchar(2) NOT NULL,
		*/

		$r = $wpdb->get_results(
			$wpdb->prepare(
				"INSERT INTO `$table_name` 
				(botip, botobs, botstate, botblocked, botdate, added, botflag, botcountry)
				VALUES (%s, '', 'Enabled', 0, CURRENT_TIMESTAMP(), 'User', %s, '')",
				$stopbadbots_ip,
				$botflag
			)
		);
	}
	die('!!!');
}



function stopbadbots_howmany_visit_200()
{
	global $wpdb;
	global $stopbadbots_ip;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_visitorslog';

	/*
	$query = 'select count(*) FROM ' . sanitize_text_field( $table_name ) .
		" WHERE ip = '" . sanitize_text_field( $stopbadbots_ip ) . "'
                AND `response` LIKE '200'";

	return $wpdb->get_var( sanitize_text_field( $query ) );
	*/

	return $wpdb->get_var(
		$wpdb->prepare(
			"SELECT count(*) FROM `$table_name` 
     WHERE ip = %s AND `response` LIKE '200'",
			$stopbadbots_ip
		)
	);
}

function stopbadbots_howmany_visit_404()
{
	global $wpdb;
	global $stopbadbots_ip;

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table_name = $wpdb->prefix . 'sbb_visitorslog';

	/*
	$query = 'select count(*) FROM ' . sanitize_text_field( $table_name ) .
		" WHERE ip = '" . sanitize_text_field( $stopbadbots_ip ) . "'
                AND `response` LIKE '404'";

	return $wpdb->get_var( sanitize_text_field( $query ) );
	*/

	return $wpdb->get_var(
		$wpdb->prepare(
			"SELECT count(*) FROM `$table_name` 
     WHERE ip = %s AND `response` LIKE '404'",
			$stopbadbots_ip
		)
	);
}

// mozilla/5.0 (linux; android 6.0.1; sm-j500m) applewebkit/537.36 (khtml, like gecko) chrome/91.0.4472.101 mobile safari/537.36

function stopbadbots_plugin_is_active($plugin_name)
{
	/*
	logplugin.php
	reCAPTCHA For All
	*/
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	$apl               = get_option('active_plugins');
	$plugins           = get_plugins();
	$activated_plugins = array();
	foreach ($apl as $p) {
		if (isset($plugins[$p])) {
			array_push($activated_plugins, $plugins[$p]);
		}
	}
	foreach ($activated_plugins as $ap) {
		if ($plugin_name == $ap['Name']) {
			return true;
		}
	}
	return false;
}

//Mozilla/5.0 (Linux; Android 12; SM-X200) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36


function stopbadbots_find_ua_os($agent)
{
	$ret_os = '';

	// Android ?
	if (stripos($agent, 'Android') !== false)
		return 'Android';

	/*
			Mozilla/5.0 
		(Linux; Android 12; SAMSUNG SM-A226BR) 
		AppleWebKit/537.36 
		(KHTML, like Gecko) 
		SamsungBrowser/20.0 Chrome/106.0.5249.126 Mobile Safari/537.36
		*/
	// // 'Mozilla/5.0 (X11; Linux x86_64; rv:102.0) Gecko/20100101 Firefox/102.0'



	$oss    = array(
		'Linux'         => array('linux', 'Linux'),
		'Android'       => array('Android'),
		'Mac OS X'      => array('Macintosh', 'Mac OS X'),
		'iOS'           => array('like Mac OS X'),
		'Windows'       => array('Windows NT', 'win32'),
		'Windows Phone' => array('Windows Phone'),
		'Chrome OS'     => array('CrOS'),
	);

	foreach ($oss as $os => $patterns) {
		foreach ($patterns as $pattern) {
			if (strpos($agent, $pattern) !== false) {
				return trim($os);
			}
		}
	}

	return '';
}
function stopbadbots_find_ua_browser($agent)
{
	$ret_browser = '';
	$browsers    = array(
		'Apple Safari'      => array('Safari'),
		'Google Chrome'     => array('Chrome'),
		'Edge'              => array('Edge'),
		'Internet Explorer' => array('MSIE'),
		'Mozilla Firefox'   => array('Firefox'),
		'Opera'             => array('OPR', 'Opera'),
		'Netscape'          => array('Netscape'),
		'cURL'              => array('curl'),
		'Wget'              => array('Wget'),
	);
	foreach ($browsers as $browser => $patterns) {
		foreach ($patterns as $pattern) {
			if (strpos($agent, $pattern) !== false) {
				return $pattern;
			}
		}
	}
	return '';
}
/*
function stopbadbots_find_ua_version( $agent, $browser ) {
	if ( empty( $agent ) or empty( $browser ) ) {
		return '';
	}
	$version = '';
	$pattern = '#(?<browser>' . join( '|', array( 'Version', $browser, 'other' ) ) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	preg_match_all( $pattern, $agent, $matches );
	if ( isset( $matches['version'][0] ) ) {
		$version = $matches['version'][0];
	}
	if ( count( $matches['browser'] ) != 1 ) {
		$version = strripos( $agent, 'Version' ) < strripos( $agent, $browser ) ? $matches['version'][0] : $matches['version'][1];
	}
	return $version;
}
*/

function stopbadbots_find_ua_version($agent, $browser)
{
	if (empty($agent) or empty($browser)) {
		return '';
	}
	$version = '';
	$pattern = '#(?<browser>' . join('|', array('Version', $browser, 'other')) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	preg_match_all($pattern, $agent, $matches);
	if (isset($matches['version'][0])) {
		$version = $matches['version'][0];
	}
	if (isset($matches['version'][1]) && isset($matches['browser'][1])) {
		$version = strripos($agent, 'Version') < strripos($agent, $browser) ? $matches['version'][0] : $matches['version'][1];
	}
	return $version;
}



/*
function stopbadbots_check_host_ripe($ip)
{

	$urlcurl = 'https://rdap.db.ripe.net/ip/' . $ip;

	try {

		$data      = array();
		$result    = wp_remote_get($urlcurl, $data);
		$http_code = wp_remote_retrieve_response_code($result);

		if ($http_code <> 200) {
			return false;
		}

		if (gettype($result) == 'array') {
			return json_decode($result['body'], true);
		} else {
			return false;
		}
	} catch (Exception $e) {
		// echo 'Caught exception: ',  $e->getMessage(), "\n";
		return false;
	}
}
*/


function stopbadbots_check_host_ripe($ip)
{
	// Validate the IP address format
	$ip = filter_var($ip, FILTER_VALIDATE_IP);
	if (!$ip) {
		return false; // Invalid IP
	}

	// Check if the data is already cached in a transient
	$cache_key = 'stopbadbots_host_' . md5($ip);
	$cached_data = get_transient($cache_key);

	// If cached data exists return it
	if ($cached_data !== false) {
		return $cached_data;
	}
	// Construct the RDAP API URL
	$urlcurl = 'https://rdap.db.ripe.net/ip/' . $ip;

	try {
		// Set up request options with timeout
		$request_options = array(
			'timeout'   => 5, // Set a timeout of 5 seconds
			'sslverify' => true, // Verify SSL for security
		);

		// Perform the HTTP request
		$response = wp_remote_get($urlcurl, $request_options);

		// Check if the request was successful
		$http_code = wp_remote_retrieve_response_code($response);
		if ($http_code !== 200) {
			return false; // API did not return a successful response
		}

		// Ensure the response is an array and contains a body
		if (is_array($response) && isset($response['body'])) {
			$decoded_response = json_decode($response['body'], true);

			// Ensure the JSON decoding succeeded
			if (json_last_error() === JSON_ERROR_NONE) {
				// Cache the decoded response in a transient for 1 hour
				// set_transient($cache_key, $decoded_response, HOUR_IN_SECONDS);
				set_transient($cache_key, $decoded_response, 3 * MINUTE_IN_SECONDS);

				return $decoded_response; // Process and return the result
			}
		}

		return false; // Fallback if the response or decoding is invalid
	} catch (Exception $e) {
		// Log the exception message for debugging
		error_log('Exception in stopbadbots_check_host_ripe: ' . $e->getMessage());
		return false; // Return false in case of an exception
	}
}


/*
function stopbadbots_check_host_ripe($ip)
{
	// Validate the IP address format
	$ip = filter_var($ip, FILTER_VALIDATE_IP);
	if (!$ip) {
		return false; // Invalid IP
	}

	// Check if the data is already cached in a transient
	$cache_key = 'stopbadbots_host_' . md5($ip);
	$cached_data = get_transient($cache_key);

	// If cached data exists return it
	if ($cached_data !== false) {
		return $cached_data;
	}
	// Construct the RDAP API URL
	$urlcurl = 'https://rdap.db.ripe.net/ip/' . $ip;

	try {
		// Set up request options with timeout
		$request_options = array(
			'timeout'   => 5, // Set a timeout of 5 seconds
			'sslverify' => true, // Verify SSL for security
		);

		// Perform the HTTP request
		$response = wp_remote_get($urlcurl, $request_options);

		// Check if the request was successful
		$http_code = wp_remote_retrieve_response_code($response);
		if ($http_code !== 200) {
			return false; // API did not return a successful response
		}

		// Ensure the response is an array and contains a body
		if (is_array($response) && isset($response['body'])) {
			$decoded_response = json_decode($response['body'], true);

			// Ensure the JSON decoding succeeded
			if (json_last_error() === JSON_ERROR_NONE) {
				// Cache the decoded response in a transient for 1 hour
				// set_transient($cache_key, $decoded_response, HOUR_IN_SECONDS);
				set_transient($cache_key, $decoded_response, 3 * MINUTE_IN_SECONDS);

				return stopbadbots_process_response($decoded_response); // Process and return the result
			}
		}

		return false; // Fallback if the response or decoding is invalid
	} catch (Exception $e) {
		// Log the exception message for debugging
		error_log('Exception in stopbadbots_check_host_ripe: ' . $e->getMessage());
		return false; // Return false in case of an exception
	}
}
*/


function stopbadbots_process_response($response)
{
	// Example processing logic for the RDAP response
	if (isset($response['entities']) && is_array($response['entities'])) {
		foreach ($response['entities'] as $entity) {
			if (isset($entity['roles']) && in_array('abuse', $entity['roles'])) {
				return true; // Mark as bad host if an abuse role is found
			}
		}
	}

	return false; // Return false if no relevant data indicates a bad host
}



function stopbadbots_find_email($item)
{
	global $_email;

	if (strpos($item, '@')) {
		$_email = $item;
	}
}

function stopbadbots_is_bad_hosting2($ip)
{
	global $stopbadbots_bad_host;

	$ret = stopbadbots_check_host_ripe($ip);
	if (!isset($ret['body'])) {
		// A chave 'body' não existe no array $ret
		return false;
	}
	$bodyArray = json_decode($ret['body'], true);
	if ($bodyArray === null) {
		// Falha ao decodificar o JSON
		return false;
	}

	for ($i = 0; $i < count($stopbadbots_bad_host); $i++) {
		if (stopbadbots_searchInArray($bodyArray, $stopbadbots_bad_host[$i])) {
			// Host encontrado
			return true;
		}
	}
	return false;
}


/*
function stopbadbots_is_bad_hosting2($ip)
{
	global $stopbadbots_bad_host;

	$ret = stopbadbots_check_host_ripe($ip);
	if (!isset($ret['body'])) {
		// A chave 'body' não existe no array $ret
		return false;
	}
	$bodyArray = json_decode($ret['body'], true);
	if ($bodyArray === null) {
		// Falha ao decodificar o JSON
		return false;
	}

	for ($i = 0; $i < count($stopbadbots_bad_host); $i++) {
		if (stopbadbots_searchInArray($bodyArray, $stopbadbots_bad_host[$i])) {
			// Host encontrado
			return true;
		}
	}
	return false;
}
*/
function stopbadbots_searchInArray($bodyArray, $badHost)
{
	foreach ($bodyArray as $element) {
		if (is_string($element) && stripos($element, $badHost) !== false) {
			// Host encontrado
			return true;
		} elseif (is_array($element)) {
			// Busca recursiva em subarrays
			if (searchInArray($element, $badHost)) {
				return true;
			}
		}
	}
	return false;
}


function stopbadbots_is_bad_hosting($ip)
{
	global $stopbadbots_bad_host;
	try {
		if (PHP_OS_FAMILY == 'Linux') {
			putenv('RES_OPTIONS=retrans:1 retry:1 timeout:1 attempts:1');
		}
		$ip = filter_var($ip, FILTER_VALIDATE_IP);
		if ($ip) {
			// Tenta obter o host a partir do transiente
			$host = get_transient('host_for_ip_2' . $ip);
			// Se não encontrou no transiente, chama gethostbyaddr e salva no transiente
			if (!$host) {
				if (function_exists('gethostbyaddr')) {
					$host = @gethostbyaddr($ip);
					// Armazena o resultado por 3 minutos
					set_transient('host_for_ip_2' . $ip, $host, 3 * MINUTE_IN_SECONDS);
				} else {
					return false;
				}
			}
		} else {
			return false; // era true em 2024;
		}
	} catch (Exception $e) {
		// echo 'Caught exception: ',  $e->getMessage(), "\n";
		return false;
	}
	if ($host === false) {
		return false;
	} else {
		$host = trim(sanitize_text_field($host));
	}
	if ($host == trim($ip) or empty($host)) {
		return false;
	}
	for ($i = 0; $i < count($stopbadbots_bad_host); $i++) {
		// O primeiro parâmetro $host é a string onde será feita a busca.
		if (stripos($host, $stopbadbots_bad_host[$i]) !== false) {
			return true;
		}
	}
	return false;
}


/*
function stopbadbots_is_bad_hosting($ip)
{

	global $stopbadbots_bad_host;
	try {
		if (PHP_OS_FAMILY == 'Linux') {
			putenv('RES_OPTIONS=retrans:1 retry:1 timeout:1 attempts:1');
		}
		$ip = filter_var($ip, FILTER_VALIDATE_IP);
		if ($ip) {
			// Tenta obter o host a partir do transiente
			$host = get_transient('host_for_ip' . $ip);
			// Se não encontrou no transiente, chama gethostbyaddr e salva no transiente
			if (!$host) {
				if (function_exists('gethostbyaddr')) {
					$host = @gethostbyaddr($ip);
					// Armazena o resultado por 3 minutos
					set_transient('host_for_ip' . $ip, $host, 3 * MINUTE_IN_SECONDS);
				} else {
					return false;
				}
			}
		} else {
			return false; // era true em 2024;
		}
	} catch (Exception $e) {
		// echo 'Caught exception: ',  $e->getMessage(), "\n";
		return false;
	}
	if ($host === false) {
		return false;
	} else {
		$host = trim(sanitize_text_field($host));
	}
	if ($host == trim($ip) or empty($host)) {
		return false;
	}
	for ($i = 0; $i < count($stopbadbots_bad_host); $i++) {
		// O primeiro parâmetro $host é a string onde será feita a busca.
		if (stripos($host, $stopbadbots_bad_host[$i]) !== false) {
			return true;
		}
	}
	return false;
}
*/








function stopbadbots_sizeFilter($bytes)
{
	$label = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $bytes >= 1024 && $i < (count($label) - 1); $bytes /= 1024, $i++);
	return (round($bytes, 2) . " " . $label[$i]);
}

/*
function stopbadbots_read_file($file, $lines)
{
    try {
        $handle = fopen($file, "r");
    } catch (Exception $e) {
        return "";
    }
    if (!$handle) {
        return "";
    }

    $linecounter = $lines;
    $pos = -2;
    $beginning = false;
    $text = [];

    while ($linecounter > 0) {
        $t = " ";
        // acha ultima quebra de linha indo para traz... 
        // partindo da ultima posicao menos 1.
        while ($t != "\n") {
            if (fseek($handle, $pos, SEEK_END) == -1) {
                // chegou no inicio?
                $beginning = true;
                break;
            }
            $t = fgetc($handle);
            $pos--;
        }

        $linecounter--;

        // chegou no inicio?
        if ($beginning) {
            rewind($handle);
        }

        $line = fgets($handle);
        if ($line === false) {
            break; // Não há mais linhas para ler
        }
        $text[] = $line;

        if ($beginning) {
            break;
        }
    }

    fclose($handle);
    return $text;
}
*/

/*
function stopbadbots_errors_today($onlytoday)
{
	$stopbadbots_count = 0;

	//define('STOPBADBOTSPATH', plugin_dir_path(__file__));
	//STOPBADBOTSPATH
	$stopbadbots_themePath = get_theme_root();
	$error_log_path = trim(ini_get('error_log'));
	if (!is_null($error_log_path) and $error_log_path != trim(ABSPATH . "error_log")) {
		$stopbadbots_folders = array(
			$error_log_path,
			ABSPATH . "error_log",
			ABSPATH . "php_errorlog",
			STOPBADBOTSPATH . "/error_log",
			STOPBADBOTSPATH . "/php_errorlog",
			$stopbadbots_themePath . "/error_log",
			$stopbadbots_themePath . "/php_errorlog"
		);
	} else {
		$stopbadbots_folders = array(
			ABSPATH . "error_log",
			ABSPATH . "php_errorlog",
			STOPBADBOTSPATH . "/error_log",
			STOPBADBOTSPATH . "/php_errorlog",
			$stopbadbots_themePath . "/error_log",
			$stopbadbots_themePath . "/php_errorlog"
		);
	}
	$stopbadbots_admin_path = str_replace(get_bloginfo('url') . '/', ABSPATH, get_admin_url());
	array_push($stopbadbots_folders, $stopbadbots_admin_path . "/error_log");
	array_push($stopbadbots_folders, $stopbadbots_admin_path . "/php_errorlog");
	$stopbadbots_plugins = array_slice(scandir(STOPBADBOTSPATH), 2);
	foreach ($stopbadbots_plugins as $stopbadbots_plugin) {
		if (is_dir(STOPBADBOTSPATH . "/" . $stopbadbots_plugin)) {
			array_push($stopbadbots_folders, STOPBADBOTSPATH . "/" . $stopbadbots_plugin . "/error_log");
			array_push($stopbadbots_folders, STOPBADBOTSPATH . "/" . $stopbadbots_plugin . "/php_errorlog");
		}
	}
	$stopbadbots_themes = array_slice(scandir($stopbadbots_themePath), 2);
	foreach ($stopbadbots_themes as $stopbadbots_theme) {
		if (is_dir($stopbadbots_themePath . "/" . $stopbadbots_theme)) {
			array_push($stopbadbots_folders, $stopbadbots_themePath . "/" . $stopbadbots_theme . "/error_log");
			array_push($stopbadbots_folders, $stopbadbots_themePath . "/" . $stopbadbots_theme . "/php_errorlog");
		}
	}



	foreach ($stopbadbots_folders as $stopbadbots_folder) {


		//// if (gettype($stopbadbots_folder) != 'array')
		//	continue;

		if(trim(empty($stopbadbots_folder)))
			continue;



		foreach (glob($stopbadbots_folder) as $stopbadbots_filename) {
			if (strpos($stopbadbots_filename, 'backup') != true) {
				$stopbadbots_count++;
				$marray = stopbadbots_read_file($stopbadbots_filename, 20);

				if (gettype($marray) == 'array' and count($marray) > 0) {
					for ($i = 0; $i < count($marray); $i++) {
						// [05-Aug-2021 08:31:45 UTC]

						if (substr($marray[$i], 0, 1) != '[' or empty($marray[$i]))
							continue;
						$pos = strpos($marray[$i], ' ');
						$string = trim(substr($marray[$i], 1, $pos));
						if (empty($string))
							continue;
						// $data_array = explode('-',$string,);
						$last_date = strtotime($string);
						// var_dump($last_date);
                        
						if ((time() - $last_date) < (60 * 60 * ($onlytoday * 24)))
						    return true;

					}
				}
			}
		}
	}
	return false;
}

*/

/*
function stopbadbots_javascript_errors_today($onlytoday)
{
	$stopbadbots_count = 0;



	//define('STOPBADBOTSPATH', plugin_dir_path(__file__));
	//STOPBADBOTSPATH
	$stopbadbots_themePath = get_theme_root();
	$error_log_path = trim(ini_get('error_log'));
	if (!is_null($error_log_path) and $error_log_path != trim(ABSPATH . "error_log")) {
		$stopbadbots_folders = array(
			$error_log_path,
			ABSPATH . "error_log",
			ABSPATH . "php_errorlog",
			STOPBADBOTSPATH . "/error_log",
			STOPBADBOTSPATH . "/php_errorlog",
			$stopbadbots_themePath . "/error_log",
			$stopbadbots_themePath . "/php_errorlog"
		);
	} else {
		$stopbadbots_folders = array(
			ABSPATH . "error_log",
			ABSPATH . "php_errorlog",
			STOPBADBOTSPATH . "/error_log",
			STOPBADBOTSPATH . "/php_errorlog",
			$stopbadbots_themePath . "/error_log",
			$stopbadbots_themePath . "/php_errorlog"
		);
	}
	$stopbadbots_admin_path = str_replace(get_bloginfo('url') . '/', ABSPATH, get_admin_url());
	array_push($stopbadbots_folders, $stopbadbots_admin_path . "/error_log");
	array_push($stopbadbots_folders, $stopbadbots_admin_path . "/php_errorlog");
	$stopbadbots_plugins = array_slice(scandir(STOPBADBOTSPATH), 2);
	foreach ($stopbadbots_plugins as $stopbadbots_plugin) {
		if (is_dir(STOPBADBOTSPATH . "/" . $stopbadbots_plugin)) {
			array_push($stopbadbots_folders, STOPBADBOTSPATH . "/" . $stopbadbots_plugin . "/error_log");
			array_push($stopbadbots_folders, STOPBADBOTSPATH . "/" . $stopbadbots_plugin . "/php_errorlog");
		}
	}
	$stopbadbots_themes = array_slice(scandir($stopbadbots_themePath), 2);
	foreach ($stopbadbots_themes as $stopbadbots_theme) {
		if (is_dir($stopbadbots_themePath . "/" . $stopbadbots_theme)) {
			array_push($stopbadbots_folders, $stopbadbots_themePath . "/" . $stopbadbots_theme . "/error_log");
			array_push($stopbadbots_folders, $stopbadbots_themePath . "/" . $stopbadbots_theme . "/php_errorlog");
		}
	}



	foreach ($stopbadbots_folders as $stopbadbots_folder) {


		//// if (gettype($stopbadbots_folder) != 'array')
		//	continue;

		if(trim(empty($stopbadbots_folder)))
			continue;



		foreach (glob($stopbadbots_folder) as $stopbadbots_filename) {
			if (strpos($stopbadbots_filename, 'backup') != true) {
				$stopbadbots_count++;
				$marray = stopbadbots_read_file($stopbadbots_filename, 20);

				if (gettype($marray) == 'array' and count($marray) > 0) {
					for ($i = 0; $i < count($marray); $i++) {
						// [05-Aug-2021 08:31:45 UTC]


						if ((substr($marray[$i], 0, 1) != '[' || stripos($marray[$i], 'javascript') === false) || empty($marray[$i]))
	 						continue;



						$pos = strpos($marray[$i], ' ');
						$string = trim(substr($marray[$i], 1, $pos));
						if (empty($string))
							continue;
						// $data_array = explode('-',$string,);
						$last_date = strtotime($string);
						// var_dump($last_date);
                        
						//if($onlytoday == 1) {
							if ((time() - $last_date) < (60 * 60 * ($onlytoday * 24)))
							  return true;
						//}
						//else {
						//	return true;	
						//}
					}
				}
			}
		}
	}
	return false;
}
*/
function stopbadbots_really_search_engine($ua)
{
	$ua       = trim(strtolower($ua));
	$mysearch = array(
		'googlebot',
		'bingbot',
		'slurp',
	);

	for ($i = 0; $i < count($mysearch); $i++) {
		if (stripos($ua, $mysearch[$i]) !== false)
			return true;
	}
	return false;
}


/*
if(stopbadbots_javascript_errors_today(2) or stopbadbots_errors_today(2)){
    add_action('admin_bar_menu', 'stopbadbots_alert_errors2', 999);
	add_action('admin_notices', 'stopbadbots_show_dismissible_notification');
}
*/

/*
$stopbadbots_memory = stopbadbots_check_memory();
if ( $stopbadbots_memory['msg_type'] == 'notok' ) {
	// return;
}
else{
	$sbb_memory_free = $stopbadbots_memory['wp_limit']  - $stopbadbots_memory['usage']; 
	if ( $stopbadbots_memory['percent'] > .7  or $sbb_memory_free < 30 ) {
		add_action('admin_bar_menu', 'stopbadbots_alert_errors3', 999);
		add_action('admin_notices', 'stopbadbots_show_dismissible_notification2');
	}
}
*/

function stopbadbots_show_dismissible_notification()
{
	// Check if the notification was already shown today
	$last_notification_date = get_option('stopbadbots_last_notification_date');
	$today = date('Y-m-d');

	if ($last_notification_date === $today) {
		return; // Notification already shown today
	}

	$message = __('Errors have been detected on this site. ', 'stopbadbots') . '<a href="' . esc_url(STOPBADBOTSHOMEURL . "admin.php?page=stop_bad_bots_plugin&tab=errors") . '">' . __('Learn more', 'stopbadbots') . '</a>';

	// Display the notification HTML
	echo '<div class="notice notice-error is-dismissible">';
	echo '<p style="color: red;">' . wp_kses_post($message) . '</p>';
	echo '</div>';

	// Update the last notification date
	update_option('stopbadbots_last_notification_date', $today);
}

// add_action('admin_notices', 'stopbadbots_show_dismissible_notification');

function stopbadbots_show_dismissible_notification2()
{
	// Check if the notification was already shown today
	$last_notification_date = get_option('stopbadbots_last_notification_date2');
	$today = date('Y-m-d');

	if ($last_notification_date === $today) {
		return; // Notification already shown today
	}

	$message = __('Memory issues have been detected on this site. ', 'stopbadbots') . '<a href="' . esc_url(STOPBADBOTSHOMEURL . "admin.php?page=stop_bad_bots_plugin&tab=memory") . '">' . __('Learn more', 'stopbadbots') . '</a>';

	// Display the notification HTML
	echo '<div class="notice notice-error is-dismissible">';
	echo '<p style="color: red;">' . wp_kses_post($message) . '</p>';
	echo '</div>';

	// Update the last notification date
	update_option('stopbadbots_last_notification_date2', $today);
}

function stopbadbots_show_logo()
{
	echo '<div id="stopbadbots_logo" style="margin-top:10px;">';
	// echo '<br>';
	echo '<img src="';
	echo esc_url(STOPBADBOTSIMAGES) . '/logo.png';
	// https://boatplugin.com/wp-content/plugins/stopbadbots/assets/images/logo.png
	echo '">';
	echo '<br>';
	echo '</div>';
}


if (!defined('STOPBADBOTS_FAIL2BAN_SECRET_TOKEN')) {
	define('STOPBADBOTS_FAIL2BAN_SECRET_TOKEN', AUTH_KEY);
}

//if (is_admin())
//	die(var_export(STOPBADBOTS_FAIL2BAN_SECRET_TOKEN));



add_action('wp_ajax_nopriv_log_sbb_fail2ban_event', 'stopbadbots_handle_fail2ban_event');
add_action('wp_ajax_log_sbb_fail2ban_event', 'stopbadbots_handle_fail2ban_event'); // Opcional


function stopbadbots_handle_fail2ban_event()
{
	// 1. Check the request method (must be POST)
	if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {


		error_log('Invalid request method.');


		wp_send_json_error(
			['message' => 'Invalid request method.'],
			405 // Method Not Allowed
		);
		wp_die();
	}

	// 2. Check the security token
	$submitted_token = isset($_POST['security_token']) ? sanitize_text_field($_POST['security_token']) : '';
	if (empty($submitted_token) || !hash_equals(STOPBADBOTS_FAIL2BAN_SECRET_TOKEN, $submitted_token)) {
		// Log an attempt if you want, but don't give away too much info
		// error_log('Fail2Ban Logger: Unauthorized access attempt. IP: ' . stopbadbots_get_ip_address());

		error_log('submited token ' . $submitted_token);
		error_log('token ' . STOPBADBOTS_FAIL2BAN_SECRET_TOKEN);

		error_log('Invalid fail2ban token.');

		wp_send_json_error(
			['message' => 'Unauthorized.'],
			403 // Forbidden
		);
		wp_die();
	}

	$ip_address = isset($_POST['ip']) ? sanitize_text_field($_POST['ip']) : '';
	// Validate IP (optional, but recommended)
	if (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
		wp_send_json_error(['message' => 'Invalid IP provided.'], 400);
		wp_die();
	}

	$f2b_timestamp = isset($_POST['f2b_timestamp']) ? absint($_POST['f2b_timestamp']) : 0;
	$event_timestamp = ($f2b_timestamp > 0) ? date('Y-m-d H:i:s', $f2b_timestamp) : current_time('mysql', 1);

	$jail_name = isset($_POST['jail']) ? sanitize_text_field($_POST['jail']) : 'N/A';
	$attempts = isset($_POST['attempts']) ? absint($_POST['attempts']) : 0;
	$log_lines_raw = isset($_POST['log_line']) ? $_POST['log_line'] : ''; // Sanitize later, as it may be multiline

	// Sanitize log_lines: wp_kses_post removes dangerous HTML but allows some.
	// If you want plain text and line breaks as \n:
	$log_lines_sanitized = sanitize_textarea_field($log_lines_raw);

	// For the 'reason' field, we can take the first line of log_line or part of it
	$reason = !empty($log_lines_sanitized) ? mb_substr(strtok($log_lines_sanitized, "\n"), 0, 255) : 'N/A';
	if (empty($reason) && !empty($log_lines_sanitized)) { // In case strtok returns false
		$reason = mb_substr($log_lines_sanitized, 0, 255);
	}

	$server_host = isset($_POST['server_host']) ? sanitize_text_field($_POST['server_host']) : '';

	// Handle the 'port' field. Fail2Ban may send 'http', '80,443', or a number.
	// Your column is INT.
	$port_str = isset($_POST['port']) ? sanitize_text_field($_POST['port']) : '';
	$port_int = 0;
	if (is_numeric($port_str)) {
		$port_int = intval($port_str);
	} elseif (!empty($port_str)) {
		$port_parts = explode(',', $port_str); // Take the first if it's a list
		$first_port = trim($port_parts[0]);
		if (is_numeric($first_port)) {
			$port_int = intval($first_port);
		} elseif (strtolower($first_port) === 'http') {
			$port_int = 80;
		} elseif (strtolower($first_port) === 'https') {
			$port_int = 443;
		}
		// Add more service-to-port mappings if needed
	}

	$protocol = isset($_POST['protocol']) ? sanitize_text_field($_POST['protocol']) : '';
	$ban_duration = isset($_POST['ban_duration']) ? intval($_POST['ban_duration']) : 0;

	global $wpdb;
	$table_name = $wpdb->prefix . 'stopbadbots_fail2ban_logs';

	$data_to_insert = array(
		'ip'           => $ip_address,
		'timestamp'    => $event_timestamp,
		'jail'         => $jail_name,
		'reason'       => $reason,
		'attempts'     => $attempts,
		'log_line'     => $log_lines_sanitized,
		'host'         => $server_host,
		'port'         => $port_int,
		'protocol'     => $protocol,
		'ban_duration' => $ban_duration,
	);

	$format = array(
		'%s', // ip
		'%s', // timestamp (DATETIME string)
		'%s', // jail
		'%s', // reason
		'%d', // attempts
		'%s', // log_line
		'%s', // host
		'%d', // port
		'%s', // protocol
		'%d'  // ban_duration
	);

	$result = $wpdb->insert($table_name, $data_to_insert, $format);

	if ($result === false) {
		// For debugging, you may want to log $wpdb->last_error
		// error_log("Fail2Ban Logger: Error inserting into DB: " . $wpdb->last_error);
		wp_send_json_error(
			['message' => 'Failed to log event in the database.', 'db_error' => $wpdb->last_error],
			500 // Internal Server Error
		);
	} else {
		wp_send_json_success(
			['message' => 'Fail2Ban event logged successfully.', 'id' => $wpdb->insert_id],
			200 // OK
		);
	}

	wp_die();
}





// end fail2ban

/*
// acertar
// Grava Robots.txt
$stopbadbots_tmp = substr(STOPBADBOTSURL, 1);
$stopbadbots_tmp = trim(strtolower($stopbadbots_tmp));
if ($stopbadbots_tmp == 'robots.txt' or STOPBADBOTSPAGE == 'wp-login.php') {
	add_action('init', 'stopbadbots_record_log');
} else {
	// add_action('template_redirect', 'stopbadbots_record_log');
	// add_action('init', 'stopbadbots_record_log');
	//add_action('wp_loaded', 'stopbadbots_record_log');
	add_action('send_headers', 'stopbadbots_record_log');
}*/
