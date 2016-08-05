<?php /**
 * @author Bill Minozzi
 * @copyright 2016
 */


if (is_admin()) {
    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        if ($page == 'stop-bad-bots' or $page == 'sbb_my-custom-submenu-page') 
        {
            add_filter('contextual_help', 'stopbadbots_contextual_help', 10, 3);

            function stopbadbots_contextual_help($contextual_help, $screen_id, $screen)
            {

                $myhelp = '<br> Stop Bad Bots from stealing you.';
                $myhelp .= '<br />Read the StartUp guide at Stop Bad Bots Settings page.';
                $myhelp .= '<br />Visit the <a href="http://stopbadbots.com" target="_blank">plugin site</a> for more details, demo video and online guide.';

                $screen->add_help_tab(array(
                    'id' => 'sbb-overview-tab',
                    'title' => __('Overview', 'stopbadbots'),
                    'content' => '<p>' . $myhelp . '</p>',
                    ));
                return $contextual_help;
            }

        }
    }

}
function sbb_add_menu_items() {
    $sbb_table_page =  add_submenu_page(
        'stop-bad-bots', // $parent_slug
        'Bad Bots Table', // string $page_title
        'Bad Bots Table', // string $menu_title
        'manage_options', // string $capability
        'sbb_my-custom-submenu-page',
        'sbb_render_list_page' );
     add_action( "load-$sbb_table_page", 'stopbadbots_screen_options' );   
}

function stopbadbots_screen_options() {
	global $sbb_table_page;
 
	$screen = get_current_screen();
 
    if(trim($screen->id) != 'stop-bad-bots_page_sbb_my-custom-submenu-page')
    		return;
            
	$args = array(
		'label' => __('Bots per page', 'stopbadbots'),
		'default' => 10,
		'option' => 'stopbadbots_per_page'
	);
	add_screen_option( 'per_page', $args );
}

  
function stopbadbots_set_screen_options($status, $option, $value) {
    
    
	if ( 'stopbadbots_per_page' == $option ) 
      return $value;
}

function sbbalertme($userAgentOri, $table)
{
    global $sbbserver, $sbb_found, $sbb_admin_email;



    $ip = sbb_findip();
    
    $subject = "Detected Bot on $sbbserver";
    $message = "Bot was detected from " . $table . " Table \n\n 
    Date ..............: " . date("F j, Y, g:i a") . " 
    User Agent ........: " . $userAgentOri . " 
    Robot IP Address ..: " . $ip . " 
    String Found: ...... " . $sbb_found . "
    ";

    // mail($to, $server, $subject);
    wp_mail($sbb_admin_email, $subject, $message);
}


function sbb_findip()
{

    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    $ip = trim($ip);

    if (!empty($ip))
        return $ip;
    else
        return 'unknow';
}


function sbbcrawlerDetect($userAgent)
{

    global $wpdb, $sbb_found;
    $current_table = $wpdb->prefix . 'sbb_blacklist';
    $results = $wpdb->get_results("SELECT * FROM $current_table WHERE `botstate` LIKE 'Enabled' ");

    $data = array();
    $i = 0;
    $crawlers_agents = '';


    foreach ($results as $querydatum) {

        array_push($data, (array )$querydatum);

        $data[$i]['botnickname'] = strtolower(trim($data[$i]['botnickname']));
        $data[$i]['botnickname'] = str_replace('|', '', $data[$i]['botnickname']);

        if (strlen($data[$i]['botnickname']) > 2) {

            if (!empty($crawlers_agents))
                $crawlers_agents .= '|';

            $crawlers_agents .= $data[$i]['botnickname'];
        }
        $i++;
    }


    if (empty($crawlers_agents))
        return false;

    preg_match("/$crawlers_agents/i", $userAgent, $matches);


    if (isset($matches[0]))
        $sbb_found = trim($matches[0]);
    else
        $sbb_found = '';

    if (empty($sbb_found))
        return false;
    else
        return true;

}

function sbbcrawlerDetect1($userAgent)
{

    $stopbadbots_my_blacklist = trim(get_site_option('stopbadbots_my_blacklist', ''));

    if (empty($stopbadbots_my_blacklist))
        return;

    $stopbadbots_my_blacklist = explode(PHP_EOL, $stopbadbots_my_blacklist);

    $q = count($stopbadbots_my_blacklist);

    $crawlers_agents = '';

    for ($i = 0; $i < $q; $i++) {

        $stopbadbots_my_blacklist[$i] = trim($stopbadbots_my_blacklist[$i]);
        $stopbadbots_my_blacklist[$i] = str_replace('|', '', $stopbadbots_my_blacklist[$i]);


        if (!empty($stopbadbots_my_blacklist[$i])) {

            if (strlen($stopbadbots_my_blacklist[$i]) > 2) {

                if (!empty($crawlers_agents))
                    $crawlers_agents .= '|';

                $crawlers_agents .= strtolower($stopbadbots_my_blacklist[$i]);

            }

        }

    }

    if (empty($crawlers_agents))
        return false;


    preg_match("/$crawlers_agents/i", $userAgent, $matches);

    if (isset($matches[0]))
        $sbb_found = trim($matches[0]);
    else
        $sbb_found = '';


    if (empty($sbb_found))
        return false;
    else
        return true;
}

function sbb_plugin_was_activated()
{
    global $wp_sbb_blacklist;

    require_once (STOPBADBOTSPATH . "functions/aBots.php");

    sbb_create_db();
    sbb_fill_db_froma($wp_sbb_blacklist);
    sbb_upgrade_db();
}


function sbb_fill_db_froma($wp_sbb_blacklist)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "sbb_blacklist";
    $charset_collate = $wpdb->get_charset_collate();
    $z = count($wp_sbb_blacklist);
 
 
    for ($i = 0; $i < $z; $i++) {
        $a = $wp_sbb_blacklist[$i];


        $botnickname = trim($a['botnickname']);
        $botname = trim($a['botname']);
        $boturl = trim($a['boturl']);

        $results9 = $wpdb->get_results("SELECT * FROM $table_name where botnickname = '$botnickname' limit 1");


        if (count($results9) > 0 or empty($botnickname))
            continue;

/*
        $r = $wpdb->insert($table_name, array(
            'botnickname' => $botnickname,
            'botname' => $a['botname'],
            'boturl' => $a['boturl'],
            'botstate' => "Enabled",
            ), array(
            '%s',
            '%s',
            '%s',
            '%s'));
*/

$query = "INSERT INTO ".$table_name.
         " (botnickname, botname, boturl, botstate)
          VALUES ('"
         .$botnickname.
         "', '".
         $botname .
         "', '"
         .$boturl .
         "', 'Enabled')";
         
    $r = $wpdb->get_results($query);            
    }
}


function sbb_create_db()
{
    global $wpdb;
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    // creates my_table in database if not exists
    $table = $wpdb->prefix . "sbb_blacklist";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `botnickname` varchar(30) NOT NULL,
        `botname` text NOT NULL,
        `boturl` text NOT NULL,
        `botip` varchar(100) NOT NULL,
        `botobs` text NOT NULL,
        `botstate` varchar(10) NOT NULL,
        `botblocked` mediumint(9) NOT NULL,
        
    UNIQUE (`id`),
    UNIQUE (`botnickname`)

    ) $charset_collate;";

    // KEY `botnickname` (`botnickname`)

    dbDelta($sql);

}


function sbb_plugin_db_update()
{

    global $wp_sbb_blacklist, $wpdb;
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');

    require_once (STOPBADBOTSPATH . "functions/aBots.php");

    $z = count($wp_sbb_blacklist);

    $table_name = $wpdb->prefix . "sbb_blacklist";

    $results9 = $wpdb->get_results("SELECT * FROM $table_name");

    if (count($results9) >= $z)
        return;

    sbb_create_db();
    sbb_fill_db_froma($wp_sbb_blacklist);
}



function sbb_upgrade_db()
{
    global $wpdb;
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "sbb_blacklist";
    $query = "SHOW COLUMNS FROM " . $table_name . " LIKE 'botblocked'";
    $wpdb->query($query);
    if(empty($wpdb->num_rows)) { 
      $alter = "ALTER TABLE " . $table_name . " ADD botblocked mediumint(9) NOT NULL"; 
      $wpdb->query($alter); 
    }
    
    
    // Upgrade to new names
    //$stopbadbots_option_name[0] = 'stop_bad_bots_active';
    $stopbadbots_option_name[1] = 'my_blacklist';
    $stopbadbots_option_name[2] = 'my_email_to';
    $stopbadbots_option_name[3] = 'my_radio_report_all_visits';   
    for ($i = 1; $i < 4; $i++)
    {
     
     $stopbadbots_option = get_site_option($stopbadbots_option_name[$i]);
     $stopbadbots_new_name = 'stopbadbots_'.$stopbadbots_option_name[$i];
     add_site_option($stopbadbots_new_name,$stopbadbots_option);
     // update_site_option();
     
     delete_option( $stopbadbots_option_name[$i] );
     // For site options in Multisite
     delete_site_option( $stopbadbots_option_name[$i] );

         
    }   
     
}

function sbbmoreone($userAgentOri){
 
           global $sbb_found, $wpdb;
           require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
           $table_name = $wpdb->prefix . "sbb_blacklist"; 
           $query = "UPDATE " . $table_name . " SET botblocked = botblocked+1 WHERE botnickname = '".$sbb_found."'";
           $wpdb->query($query);
        }
 ?>