<?php
/**
 * @ Author: Bill Minozzi - AH
 * @ Copyright: 2020 www.BillMinozzi.com
 * @ Modified time: 2021-03-19 10:20:31
 */
// http://codylindley.com/thickbox/
if (!defined('ABSPATH'))
  exit; // Exit if accessed directly

add_thickbox();
/*
function stopbadbots_add_thickbox2() {
    wp_enqueue_script( 'thickbox' );
    wp_enqueue_style( 'thickbox' );
 
    if ( is_network_admin() ) {
        add_action( 'admin_head', '_thickbox_path_admin_subfolder' );
    }
}
*/


$is_modal = "&modal=true";
$is_modal = "&modal=false";
$is_modal = "";
if (empty($is_modal))
  $wheight = "400";
else
  $wheight = "300";



?>
<div style="display:none; max-width:400px !important;">
    <a href="#TB_inline?&width=400&height=320&inlineId=stopbadbots-vendor-id<?php echo esc_attr($is_modal); ?>" id="stopbadbots-vendor-ok"
        class="thickbox" title="Stop Bad Bots Plugin"  style="display:none;">xx---xxx</a>
</div>


<div id="stopbadbots-vendor-id" style="display:none;">

            <video id="bill-banner-sbb" style="margin: 0px; padding:0px;" width="400" height="240" muted>
                <source src="<?php echo esc_url(STOPBADBOTSURL); ?>assets/videos/security11.mp4" type="video/mp4">
            </video>
            <br> 
            <div id="stopbadbots-wait" style="display:none;text-align:center">

            <h3><?php esc_attr_e("Please, wait...  Dismissing...","stopbadbots");?></h3>
            </div>
            
            <div class="bill-navpanel">
                <a href="#" id="bill-vendor-button-ok-sbb" style="margin-top: 0px !important; margin-right:10px;"
                    class="button button-primary bill-navitem"><?php esc_attr_e("Learn More","stopbadbots"); ?></a>
                <a href="#" id="bill-vendor-button-again-sbb" style="margin-top: 0px !important;" class="button bill-navitem"><?php esc_attr_e("Watch Again","stopbadbots"); ?></a>
                <a href="#" id="bill-vendor-button-dismiss-sbb" class="button bill-navitem" style="margin-left:10px !important;align:right;"><?php esc_attr_e("Dismiss","stopbadbots"); ?></a>
                <span class="spinner" style="display:none;"></span>
            </div>
</div> 

<?php
return;