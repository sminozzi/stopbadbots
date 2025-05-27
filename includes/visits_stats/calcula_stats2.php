<?php
/**
 * @author William Sergio Minossi
 * @copyright 2012-30-07
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $wpdb;
//die($type_access);
$table_name = $wpdb->prefix . "sbb_visitorslog";
$type_access = 'OK';

/*
$query = "SELECT DATE(date) AS data, COUNT(DISTINCT ip) AS quantidade
FROM `$table_name`
WHERE DATE(date) > DATE_SUB(CURDATE(), INTERVAL 31 DAY) AND DATE(date) < CURDATE() AND access = '$type_access' AND human = 'Human'
GROUP BY DATE(date)
ORDER BY DATE(date) DESC";
$results = $wpdb->get_results($query);
*/

/*
$query = $wpdb->prepare("
    SELECT DATE(date) AS data, COUNT(DISTINCT ip) AS quantidade
    FROM %s
    WHERE DATE(date) > DATE_SUB(CURDATE(), INTERVAL 31 DAY) AND DATE(date) < CURDATE() AND access = %s AND human = 'Human'
    GROUP BY DATE(date)
    ORDER BY DATE(date) DESC", $table_name, $type_access);
$results = $wpdb->get_results($query);
*/

$query = $wpdb->prepare("
    SELECT 
        DATE(date) AS data, 
        COUNT(DISTINCT ip) AS quantidade
    FROM 
        %i
    WHERE 
        DATE(date) > DATE_SUB(CURDATE(), INTERVAL 31 DAY) 
        AND DATE(date) < CURDATE() 
        AND access = %s 
        AND human = 'Human'
    GROUP BY 
        DATE(date)
    ORDER BY 
        DATE(date) DESC", $table_name, $type_access);

$results = $wpdb->get_results($query);



$json_string =wp_json_encode($results);
$results9 = json_decode($json_string, true);
$array30 = array();
$array30d = array();
// Obter os últimos 15 dias
$last15Days = array();
// Preencher os últimos 15 dias, exceto para o dia atual
for ($i = 1; $i <= 30; $i++) {
    $last15Days[] = date('Y-m-d', strtotime("-$i days"));
}
// Loop para adicionar os elementos à nova array com 15 elementos
foreach ($last15Days as $currentDate) {
    $found = false;
    // Verificar se a data está presente na array original
    foreach ($results9 as $registro) {
        if ($registro['data'] === $currentDate) {
            $array30[] = $registro['quantidade'];
            $array30d[] = date('md', strtotime($registro['data']));
            $found = true;
            break;
        }
    }
    // Se a data não for encontrada e não for o dia atual, adicionar quantidade 0
    if (!$found && $currentDate !== date('Y-m-d')) {
        $array30[] = 0;
        $array30d[] = date('md', strtotime($currentDate));
    }
}
//print_r($array30);
//print_r($array30d);
$ok_array30 = array_reverse($array30);
$ok_array30d = array_reverse($array30d);
?>
