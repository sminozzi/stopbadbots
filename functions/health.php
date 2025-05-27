<?php
if (!defined('ABSPATH')) {
	die('Invalid request.');
}
global $wp_version;

if ( version_compare( $wp_version, '5.2' ) >= 0 ) {
	stopbadbots_health();
} else {
	return;
}
/*
function stopbadbots_check_memory()
    {
        try {


            if(!function_exists('ini_get')){
                $memory["msg_type"] = "notok";
                return $memory;;
            }
            else{
                $memory["limit"] = (int) ini_get("memory_limit");
            }

            if(!function_exists('memory_get_usage')){
                $memory["msg_type"] = "notok";
                return $memory;;
            }
            else{
                $memory["usage"] = memory_get_usage();
            }

            if ($memory["usage"] == 0) {
                $memory["msg_type"] = "notok";
                return $memory;;
            }
            else{
                $memory["usage"] = round($memory["usage"] / 1024 / 1024, 0);
            }


            if (!defined("WP_MEMORY_LIMIT")) {
                $memory["wp_limit"] = 40;
                define('WP_MEMORY_LIMIT', '40M');
            } else {
                $wp_memory_limit = WP_MEMORY_LIMIT;
                $wp_memory_limit = rtrim($wp_memory_limit, 'M');
                $memory["wp_limit"] = (int) $wp_memory_limit;
            }


            if ($memory["limit"] > 9999999) {
                // $memory['msg_type'] = 'notok(5)';
                $memory["wp_limit"] =
                    $memory["wp_limit"] / 1024 / 1024;
            }

            if ($memory["usage"] < 1) {
                $memory["msg_type"] = "notok";
                return $memory;;
            }




            if (!is_numeric($memory["usage"])) {
                $memory["msg_type"] = "notok";
                return $memory;;
            }
            


            
            if (!is_numeric($memory["limit"])) {
                $memory["msg_type"] = "notok";
                return $memory;;
            }

            //if ($memory["limit"] > 9999999) {
            //    $memory["limit"] = $memory["limit"] / 1024 / 1024;
            // }



            if ($memory["usage"] < 1) {
                $memory["msg_type"] = "notok";
                return $memory;;
            }

            //$wplimit = $memory["wp_limit"];
            //$wplimit = substr($wplimit, 0, strlen($wplimit) - 1);
            //$memory["wp_limit"] = $wplimit;



            $memory["percent"] = $memory["usage"] / $memory["wp_limit"];
            $memory["color"] = "font-weight:normal;";
            if ($memory["percent"] > 0.7) {
                $memory["color"] = "font-weight:bold;color:#E66F00";
            }
            if ($memory["percent"] > 0.85) {
                $memory["color"] = "font-weight:bold;color:red";
            }
            $memory["free"] = $memory["wp_limit"] - $memory["usage"];
            $memory["msg_type"] = "ok";

        } catch (Exception $e) {
            $memory["msg_type"] = "notok";
            return $memory;;
        }
        return $memory;
}
*/


function stopbadbots_health() {
	 global $stopbadbots_memory_result;

	 $memory = stopbadbots_check_memory();


     if (isset($memory["msg_type"]) && $memory["msg_type"] == "notok"){
        $memory = 'Unable to Check!';
        return;
     }

    /*
    if (preg_match('/(\d+)\s*([A-Za-z]+)$/', WP_MEMORY_LIMIT, $matches)) {
        $mb = $matches[1]; // Valor em MB
        $unit = strtoupper($matches[2]); // Unidade (por exemplo, M para MB)
    }
    else
        $unit = "MB";
    */
      

    // die(var_export($memory['wp_limit']));
    

	ob_start();
	echo esc_attr__('Current memory WordPress Limit:','stopbadbots');
	
	echo ' '.esc_attr( $memory['wp_limit'] ) . 
		'MB &nbsp;&nbsp;&nbsp;  |&nbsp;&nbsp;&nbsp;';

	echo '<span style="color:red;">';
	echo  esc_attr__('Your usage now:','stopbadbots');
	echo ' '. esc_attr( $memory['usage'] ) .
		'MB &nbsp;&nbsp;&nbsp;';
	echo '</span>';
	echo '<br />';
	echo '</strong>';
	$stopbadbots_memory_result = ob_get_contents();
	ob_end_clean();



	function stopbadbots_add_memory_test( $tests ) {
		$tests['direct']['memory_plugin'] = array(
			'label' => __( 'My Memory Test', 'stopbadbots' ),
			'test'  => 'stopbadbots_memory_test',
		);
		return $tests;
	}
	$perc = $memory['usage'] / $memory['wp_limit'];
	if ( $perc > .7 ) {
		add_filter( 'site_status_tests', 'stopbadbots_add_memory_test' );
	}

	function stopbadbots_memory_test() {
		global $stopbadbots_memory_result;
		$result = array(
			'badge'       => array(
				'label' => __( 'Critical', 'stopbadbots' ), // Performance
				'color' => 'red', // orange',
			),
			'test'        => 'Bill_plugin',
			'status'      => 'critical',
			'label'       => __( 'Low WordPress Memory Limit in wp-config file', 'stopbadbots' ),
			'description' => $stopbadbots_memory_result . '  ' . sprintf(
				'<p>%s</p>',
				__( 'Run your site with low memory available, can result in behaving slowly, or pages fail to load, you get random white screens of death or 500 internal server error. Basically, the more content, features and plugins you add to your site, the bigger your memory limit has to be. Increase the WP Memory Limit is a standard practice in WordPress. You can manually increase memory limit in WordPress by editing the wp-config.php file. You can find instructions in the official WordPress documentation (Increasing memory allocated to PHP). Just click the link below: ', 'stopbadbots' )
			),
			'actions'     => sprintf(
				'<p><a href="%s">%s</a></p>',
				'https://codex.wordpress.org/Editing_wp-config.php',
				__( 'WordPress Help Page', 'stopbadbots' )
			),
		);
		return $result;
	}
}
