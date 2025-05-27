<?php /**
	   * @ Author: Bill Minozzi
	   * @ Copyright: 2020 www.BillMinozzi.com
	   * @ Modified time: 2020-10-26 12:02:27
	   */

/*
error_reporting(E_ALL);
ini_set('display_errors', 1);
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wpdb;
global $table_prefix;
$table = $table_prefix . 'sbb_visitorslog';
// Table's primary key
$primaryKey = 'date';
$columns    = array(
	array(
		'db'        => 'date',
		'dt'        => 2,
		'formatter' => function ( $d, $row ) {
			return date( 'd-M-Y H:i:s', strtotime( $d ) );
		},
	),
	array(
		'db' => 'access',
		'dt' => 3,
	),
	array(
		'db' => 'ip',
		'dt' => 4,
	),
	// array('db' => 'human',  'dt' => 4),
	array(
		'db' => 'reason',
		'dt' => 5,
	),
	array(
		'db' => 'response',
		'dt' => 6,
	),
	array(
		'db' => 'method',
		'dt' => 7,
	),
	array(
		'db' => 'ua',
		'dt' => 8,
	),
	array(
		'db' => 'url',
		'dt' => 9,
	),
	array(
		'db' => 'referer',
		'dt' => 10,
	),
);
require '_ssp_sbb.class.php';
echo wp_json_encode(
	STOPBADBOTS_SSP::simple( $_GET, $table, $primaryKey, $columns )
);
