<?php
/**
 * @author William Sergio Minossi
 * @copyright 2012-30-07
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
/*
CREATE TABLE `wp_sbb_stats` (
  `id` mediumint(9) NOT NULL,
  `date` varchar(4) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `qnick` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `qip` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `qtotal` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL
) 
*/
global $wpdb;
$table_name = $wpdb->prefix . "sbb_stats";
/*
$query = "SELECT date,qtotal FROM `$table_name`";
$results9 = $wpdb->get_results($query);
*/
$results9 = $wpdb->get_results($wpdb->prepare("SELECT date,qtotal FROM %i", $table_name));


$results8 = json_decode(json_encode($results9), true);

unset($results9);

 
      
$x = 0; 
$d = 15;
for ($i = $d ; $i > 0; $i--)
{
    $timestamp = time();
    $tm = 86400 * ($x); // 60 * 60 * 24 = 86400 = 1 day in seconds
    $tm = $timestamp - $tm;

    $the_day = date("d", $tm);
    
    $this_month = date('m', $tm);


    
    $array30d[$x] = $this_month.$the_day ;
    //$_dia = 'dia_';
    $key = array_search(trim($array30d[$x]), array_column($results8, 'date'));
    if($key)
    {
        // $awork = array_column( $results8 , 'qtotal');
        // $array30[$x] = $awork[$key];
        // objeto:
        // $array30[$x] = $results9[$key]->qtotal;
        // 
        
        $awork = $results8[$key]['qtotal'];
        $array30[$x] = $awork;
    }
    else
      $array30[$x] = 0;
    $x++;
}


$array30 = array_reverse($array30);
$array30d = array_reverse($array30d);


/*
echo '<pre>';
print_r($array30);
echo '</pre>';
*/

/*
$d2 = array();
for($i=0; $i < 7; $i++ )
{
     array_push($d2, array($array30d[$i],$array30[$i]));
}
*/
?>
