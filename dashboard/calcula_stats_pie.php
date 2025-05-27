<?php
/**
 * @author William Sergio Minossi
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $wpdb;
$table_name = $wpdb->prefix . "sbb_stats";





/*
`id` mediumint(9) NOT NULL,
`date` varchar(4) COLLATE utf8mb4_unicode_520_ci NOT NULL,
`qlogin` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
`qfire` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
`qenum` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
`qplugin` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
`qtema` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
`qfalseg` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
`qblack` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
`qtotal` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL
qbrowser
*/


$month_day = date('md');

if($month_day < '0115') {

    /*
    $stopbadbots_results8 = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT 
    date, 
    qnick as nick,
    qip as ip,
    qlogin as brute, 
    qfire as firewall,
    quenu as enumeration,
    qref as referrer,
    qua as agent,
    qping as pingback,
    qcom as comment,
    qcon as contact,
    qtools as httptools,
    qbrowser as browser,
    qrate as rating,
    qfalseg as false_se
    FROM `$table_name`
    WHERE
    `date` <= %s OR `date`  > '1215'", $month_day
        )
    );
    */
    $stopbadbots_results8 = $wpdb->get_results($wpdb->prepare("SELECT 
    date, 
    qnick as nick,
    qip as ip,
    qlogin as brute, 
    qfire as firewall,
    quenu as enumeration,
    qref as referrer,
    qua as agent,
    qping as pingback,
    qcom as comment,
    qcon as contact,
    qtools as httptools,
    qbrowser as browser,
    qrate as rating,
    qfalseg as false_se
    FROM %i
    WHERE
    `date` <= %s OR `date`  > '1215'", $table_name, $month_day));


}
else{
    /*
    $stopbadbots_results8 = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT 
    date, 
    qnick as nick,
    qip as ip,
    qlogin as brute, 
    qfire as firewall,
    quenu as enumeration,
    qref as referrer,
    qua as agent,
    qping as pingback,
    qcom as comment,
    qcon as contact,
    qtools as httptools,
    qbrowser as browser,
    qrate as rating,
    qfalseg as false_se
    FROM `$table_name`
    WHERE
    `date` <= %s", $month_day
        )
    );
    */
    $stopbadbots_results8 = $wpdb->get_results($wpdb->prepare("SELECT 
    date, 
    qnick as nick,
    qip as ip,
    qlogin as brute, 
    qfire as firewall,
    quenu as enumeration,
    qref as referrer,
    qua as agent,
    qping as pingback,
    qcom as comment,
    qcon as contact,
    qtools as httptools,
    qbrowser as browser,
    qrate as rating,
    qfalseg as false_se
    FROM %i
    WHERE
    `date` <= %s", $table_name, $month_day));

}


$stopbadbots_results9 = json_decode(json_encode($stopbadbots_results8), true);
unset($stopbadbots_results8);
$stopbadbots_results10[0]['nick'] = 0;
$stopbadbots_results10[0]['ip'] = 0;
$stopbadbots_results10[0]['brute'] = 0;
$stopbadbots_results10[0]['firewall'] = 0;
$stopbadbots_results10[0]['enumeration'] = 0;
$stopbadbots_results10[0]['false_se'] = 0;
$stopbadbots_results10[0]['referrer'] = 0;
$stopbadbots_results10[0]['agent'] = 0;
$stopbadbots_results10[0]['pingback'] = 0;
$stopbadbots_results10[0]['comment'] = 0;
$stopbadbots_results10[0]['browser'] = 0;
$stopbadbots_results10[0]['contact'] = 0;


$stopbadbots_results10[0]['httptools'] = 0;
$stopbadbots_results10[0]['rating'] = 0;






for($i = 0; $i < count($stopbadbots_results9); $i++)
{
    $stopbadbots_results10[0]['nick'] = $stopbadbots_results10[0]['nick'] + intval($stopbadbots_results9[$i]['nick']);
    $stopbadbots_results10[0]['ip'] = $stopbadbots_results10[0]['ip'] + intval($stopbadbots_results9[$i]['ip']); 
    $stopbadbots_results10[0]['brute'] = $stopbadbots_results10[0]['brute'] + intval($stopbadbots_results9[$i]['brute']);
    $stopbadbots_results10[0]['firewall'] = $stopbadbots_results10[0]['firewall'] + intval($stopbadbots_results9[$i]['firewall']);
    $stopbadbots_results10[0]['enumeration'] = $stopbadbots_results10[0]['enumeration'] + intval($stopbadbots_results9[$i]['enumeration']);
    //  $stopbadbots_results10[0]['plugin'] = $stopbadbots_results10[0]['plugin'] + intval( $stopbadbots_results9[$i]['plugin']);
    //  $stopbadbots_results10[0]['theme'] =  $stopbadbots_results10[0]['theme'] + intval( $stopbadbots_results9[$i]['theme']);
    $stopbadbots_results10[0]['false_se'] = $stopbadbots_results10[0]['false_se'] + intval($stopbadbots_results9[$i]['false_se']);
    $stopbadbots_results10[0]['referrer'] = $stopbadbots_results10[0]['referrer'] + intval($stopbadbots_results9[$i]['referrer']);
    $stopbadbots_results10[0]['agent'] = $stopbadbots_results10[0]['agent'] + intval($stopbadbots_results9[$i]['agent']);
    $stopbadbots_results10[0]['pingback'] = $stopbadbots_results10[0]['pingback'] + intval($stopbadbots_results9[$i]['pingback']);
    $stopbadbots_results10[0]['comment'] = $stopbadbots_results10[0]['comment'] + intval($stopbadbots_results9[$i]['comment']);
    $stopbadbots_results10[0]['contact'] = $stopbadbots_results10[0]['contact'] + intval($stopbadbots_results9[$i]['contact']);

    $stopbadbots_results10[0]['httptools'] = $stopbadbots_results10[0]['httptools'] + intval($stopbadbots_results9[$i]['httptools']);
    $stopbadbots_results10[0]['rating'] = $stopbadbots_results10[0]['rating'] + intval($stopbadbots_results9[$i]['rating']);
    $stopbadbots_results10[0]['browser'] = $stopbadbots_results10[0]['browser'] + intval($stopbadbots_results9[$i]['browser']);





}




return;