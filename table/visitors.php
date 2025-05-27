<?php
/**
 * @author William Sergio Minossi
 * @copyright 2020
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;


add_action( 'admin_menu', 'stopbadbots_add_menu_items2' );
// add_action('wp_head', 'antibots_ajaxurl');


function stopbadbots_add_menu_items2() {
	$stopbadbots_table_page = add_submenu_page(
		'stop_bad_bots_plugin', // $parent_slug
		'Visits Log', // string $page_title
		'Visits Log', // string $menu_title
		'manage_options', // string $capability
		'stopbadbots_my-custom-submenu-page',
		'stopbadbots_render_list_page9'
	);

}
function stopbadbots_render_list_page9() {
	require_once STOPBADBOTSPATH . 'table/visitors_render.php';
}
