<?php /*
Plugin Name: StopBadBots 
Plugin URI: http://stopbadbots.com
Description: The easiest way to stop bad bots.
Version: 1.4
Text Domain: stopbadbots
Domain Path: /language
Author: Bill Minozzi
Author URI: http://stopbadbots.com
License:     GPL2
Copyright (c) 2016 Bill Minozzi

 
Stopbadbots is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
BoatDealer is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with BoatDealer. If not, see {License URI}.


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


if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

define('STOPBADBOTS', '1.4' );
define('STOPBADBOTSPATH', plugin_dir_path(__file__) );
define('STOPBADBOTSURL', plugin_dir_url(__file__));
define('STOPBADBOTSDOMAIN', get_site_url() );
$sbbserver = $_SERVER['SERVER_NAME'];


// Add settings link on plugin page
function stopbadbots_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=stop-bad-bots">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'stopbadbots_plugin_settings_link' );


require_once (STOPBADBOTSPATH . "settings/load-plugin.php");
require_once (STOPBADBOTSPATH . "settings/options/plugin_options_tabbed.php");
require_once (STOPBADBOTSPATH . "functions/functions.php");
require_once(ABSPATH . 'wp-includes/pluggable.php');

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

require dirname( __FILE__ ) . '/includes/list-tables/class-sbb-list-table.php';

add_action( 'admin_menu', 'sbb_add_menu_items' );


add_filter('set-screen-option', 'stopbadbots_set_screen_options', 10, 3);


$userAgentOri = $_SERVER['HTTP_USER_AGENT'];
$userAgent = strtolower(trim(strtolower($userAgentOri)));

//$userAgent = 'Acoon';


$stop_bad_bots_active = get_site_option('stop_bad_bots_active','');
$stopbadbots_my_radio_report_all_visits = get_site_option('stopbadbots_my_radio_report_all_visits','');
$stopbadbots_version = get_site_option('stopbadbots_version');


$sbb_admin_email = trim(get_option( 'stopbadbots_my_email_to' ));
if( ! empty($sbb_admin_email)) {
    if ( ! is_email($sbb_admin_email)) {

        $sbb_admin_email = '';
        update_option('stopbadbots_my_email_to', '');

    }
}

if(empty($sbb_admin_email))
     $sbb_admin_email = get_option( 'admin_email' ); 
     
     
     
if( ! empty($userAgent) and ( ! is_admin())) 
 { 
    if( sbbcrawlerDetect($userAgent) and $stop_bad_bots_active == 'yes' )
     { 
        sbbmoreone($userAgentOri); // +1
        if( $stopbadbots_my_radio_report_all_visits == 'Yes')
           sbbalertme($userAgentOri, 'Default');
           
        exit;
        die();
     }
     
    if( sbbcrawlerDetect1($userAgent) and $stop_bad_bots_active == 'yes' )
     { 
        sbbmoreone($userAgentOri); // +1
        if( $stopbadbots_my_radio_report_all_visits == 'Yes')
           sbbalertme($userAgentOri, 'My Blacklist');

        exit;
        die();
     }     
 }
function sbb_render_list_page() {
	$test_list_table = new sbb_List_Table();
	$test_list_table->sbb_prepare_items();
    require dirname( __FILE__ ) . '/includes/list-tables/page.php';
}
register_activation_hook( __FILE__, 'sbb_plugin_was_activated');
add_action( 'plugins_loaded', 'sbb_plugin_db_update' ); 

if(!$stopbadbots_version)
{ 

   sbb_upgrade_db();
   if(! add_site_option('stopbadbots_version', STOPBADBOTS))
      update_site_option('stopbadbots_version', STOPBADBOTS);
   
}
//die($stopbadbots_version);
?>