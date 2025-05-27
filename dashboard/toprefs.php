<?php /**
       * @author    William Sergio Minossi
       * @copyright 2018
       */
/*
CREATE TABLE `wp_sbb_badref` (
`id` mediumint(9) NOT NULL,
`botname` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
`botstate` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
`botblocked` mediumint(9) NOT NULL,
`botdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`added` varchar(30) COLLATE utf8mb4_unicode_520_ci NOT NULL,
`botobs` text COLLATE utf8mb4_unicode_520_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
 */
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
global $wpdb;
$table_name = $wpdb->prefix . "sbb_badref";

//$query = "SELECT * FROM `$table_name` WHERE botblocked > 0 order by botblocked DESC limit 10";
//$results9 = $wpdb->get_results($query);

$results9 = $wpdb->get_results($wpdb->prepare("SELECT * FROM %i WHERE botblocked > %d ORDER BY botblocked DESC LIMIT %d", $table_name, 0, 10));


if ($wpdb->num_rows < 1) {
    echo 'No bots blocked by bad Referer. Please, try again tomorrow';
    return;
}
echo '<table class="greyGridTable">';
echo '<thead>';
echo "<tr><th>Bot <br />Referer</th>  <th>";
echo esc_attr__("Num","stopbadbots");
echo '<br />';
echo esc_attr__("Blocked","stopbadbots");
echo "</th></tr>";
echo '</thead>';

$count = 0;
foreach ($results9 as $bot) {
    if ($count > 0) {
        echo "</tr>";
    }
    echo "<tr>";
    echo "<td>";
    echo esc_attr($bot->botname);
    echo "</td>";
  
    echo "<td>";
    echo esc_attr($bot->botblocked);
    echo "</td>";
    echo "</tr>";
    $count++;
}
echo "</table>";
