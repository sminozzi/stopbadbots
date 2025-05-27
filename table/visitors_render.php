<?php
/**
 * @author William Sergio Minossi
 * @copyright 2020 2023
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


global $wpdb;
global $stopbadbots_checkversion;

$stopbadbots_table = $wpdb->prefix . 'sbb_visitorslog';

// $recordsTotal = $wpdb->get_var("SELECT  COUNT(*)  FROM $stopbadbots_table");
$recordsTotal = $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*) FROM %i",
        $stopbadbots_table
    )
);



if ( $recordsTotal < 1 ) {
	echo '<br>';
	echo '<br><h3>';
	echo esc_attr__( 'Empty Table. Please, try again later.', 'stopbadbots' );
	sleep( 5 );
	echo '<br></h3>';
	return;

}
?>

<style>
div.dataTables_wrapper div.dataTables_processing {
   top: 0;
}
</style>

<div id="stopbadbots-logo" style="float:left; margin-bottom: -30px">
	<img alt="logo" src="<?php echo esc_attr( STOPBADBOTSIMAGES ); ?>/logo.png" width="250px" />
	</div>

<div id='stopbadbots-twrap'>




	<div id="stopbadbots_help_title">
	<?php esc_attr_e( 'Visits Log', 'stopbadbots' ); ?>
	</div>

	<div id="stopbadbots_help_subtitle">
	<?php
		global $stopbadbots_engine_option;
		echo '<small>Engine Option: '.esc_attr($stopbadbots_engine_option). '. Change it on settings page. </small>';
	?>
	</div>	

	<?php

	if(!$stopbadbots_checkversion ){
		$site = 'https://stopbadbots.com/premium'; 
		echo '<div id="stopbadbots_table_upgrade_button">';
		echo '<a href="'. esc_url($site).'" class="button button-primary">'.esc_attr__("Upgrade for Max Protection", "stopbadbots"). '</a>';
		echo '</div>';
	} 

	?>
	<div id="stopbadbots_reload_button">
			<button id="reloadButton">Reload Only Content</button>
	
	</div>
</div>



<div class="table-responsive" style="margin-right:20px; width: 99%; max-width:99%;">



<table style="margin-right:20px; cellpadding=" 0" cellspacing="0" border="1px"  max-width=100%; class="dataTable" id="dataTableVisitorsSBB">



<form action="" method="post">
  <input type="hidden" name="stopbadbots_view_visits" id="stopbadbots_view_visits" value="<?php echo esc_attr( wp_create_nonce( 'stopbadbots_view_visits' ) ); ?>">

</form>

	<thead>
	  <tr>
		<th></th>
		<th></th>
		<th>date</th>
		<th>access</th>
		<th>ip</th>
		<th>reason</th>
		<th>response</th>
		<th>method</th>
		<th>user agent</th>
		<th>url</th>
		<th>referer</th>
	  </tr>
	</thead>
	<tfoot>
	  <tr>
		<th></th>
		<th></th>
		<th>date</th>
		<th>access</th>
		<th>ip</th>
		<th>reason</th>
		<th>response</th>
		<th>method</th>
		<th>user agent</th>
		<th>url</th>
		<th>referer</th>
	  </tr>
	</tfoot>
	<tbody>
	</tbody>
</table>
  
  <?php
  if($stopbadbots_checkversion ){ ?>
  <div id="dialog-confirm" title="Confirm">
	<div id="modal-body">
	</div>
  </div>
  <div id="dialog-confirm-black" title="Confirm">
	<div id="modal-body-black">
	</div>
  </div>
  <?php
  }
  else { ?>
  <div id="dialog-confirm" title="Alert">
	<div id="modal-body2">
	</div>
  </div>
  <div id="dialog-confirm-black" title="Alert">
	<div id="modal-body-black2">
	</div>
  </div>
  <?php
  }

