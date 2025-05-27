<?php
/**
 * @author William Sergio Minossi
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpdb;


$table_name = $wpdb->prefix . "sbb_visitorslog";

//$query = "SELECT COUNT(*) FROM `$table_name` WHERE `bot` = '1'";
//$quantos_bots = $wpdb->get_var(sanitize_text_field($query));
$quantos_bots = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE `bot` = %d", $table_name, 1));


//$query = "SELECT COUNT(*) FROM `$table_name` WHERE `bot` = '0'";
//$quantos_humanos = $wpdb->get_var(sanitize_text_field($query));
$quantos_humanos = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM %i WHERE `bot` = %d", $table_name, 0));


if($quantos_humanos < 1)
  $quantos_humanos = 1;

if($quantos_bots < 1 or $quantos_humanos < 1) {

    esc_attr_e("Just give us a little time to collect data so we can display it for you here.","stopbadbots");

    return;

}



$total = $quantos_bots +  $quantos_humanos;


$stopbadbots_results10[0]['Bots'] = $quantos_bots/$total;
$stopbadbots_results10[0]['Humans'] = $quantos_humanos/$total; 



