<?php
/**
 * @author    William Sergio Minozzi
 * @copyright 2021
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly 
}


//debug3();


global $stopbadbots_active;
global $stopbadbots_ip_active;
global $stopbadbots_referer_active;
global $stopbadbots_Report_Blocked_Firewall;
global $stopbadbots_notif_level;
global $wpdb;
$stopbadbots_prot_perc = stopbadbots_find_perc();
if (isset($_GET['notif'])) {
    $notif = sanitize_text_field($_GET['notif']);
    if ($notif == 'level') {
        update_option('stopbadbots_notif_level', time());
        $stopbadbots_notif_level = time();
    }
}
$timeout_level = time() > ($stopbadbots_notif_level + 60 * 60 * 24 * 7);
//$timeout_level = time() > ($stopbadbots_notif_level + 10);
$site = STOPBADBOTSHOMEURL . "admin.php?page=stop_bad_bots_plugin&tab=notifications&notif=";
?>
<div id="stopbadbots-notifications-page">
   <div class="stopbadbots-block-title">
        <?php esc_attr_e("Notifications","stopbadbots"); ?>
   </div>
   <div id="notifications-tab">
       <br>
      <?php
        $empty_notif = true;
        if ($stopbadbots_active != 'yes') {
            $empty_notif = false; ?>
         <b><?php esc_attr_e("Plugin Stop Bad Bots It is not active!","stopbadbots"); ?></b>
         <br>
         <?php esc_attr_e("Go to Dashboard => Stop Bad Bots => Settings => General Settings (tab) and activate it. ","stopbadbots"); ?>
         <br>
         <?php esc_attr_e('Mark: "Block all Bots included at Bad Bots Table?" with yes.',"stopbadbots"); ?>
         <br>
         <hr>
            <?php
        }
        if ($stopbadbots_ip_active != 'yes') {
            $empty_notif = false; ?>
         <b> <?php esc_attr_e("Plugin Stop Bad Bots (Block Ips) It is not active!","stopbadbots"); ?></b>
         <br>
         <?php esc_attr_e("Go to Dashboard => Stop Bad Bots => Settings => General Settings (tab) and activate it.","stopbadbots"); ?>
         <br>
         <?php esc_attr_e('Mark: "Block all IPs included at Bad IPs Table?" with yes.',"stopbadbots"); ?>
         <hr>
            <?php
        }
        if ($stopbadbots_referer_active != 'yes') {
            $empty_notif = false; ?>
         <b> <?php esc_attr_e("Plugin Stop Bad Bots (Block Bad Refer Table) It is not active!","stopbadbots"); ?></b>
         <br>
         <?php esc_attr_e("Go to Dashboard => Stop Bad Bots => Settings => General Settings (tab) and activate it.","stopbadbots"); ?>
         <br>
         <?php esc_attr_e('Mark: "Block all bots included at Bad Referer Table?" with yes.',"stopbadbots"); ?>
         <hr>
            <?php
        }
        if ($timeout_level and $stopbadbots_prot_perc < 80) {
            $empty_notif = false;
            ?>
         <b> <?php esc_attr_e("Improve your protection level.","stopbadbots"); ?> </b>
         <br>
         <?php esc_attr_e("Protection Status level:","stopbadbots"); ?>&nbsp;
            <?php echo esc_attr($stopbadbots_prot_perc); ?>%
         <br>
         <?php esc_attr_e("To increase, go to","stopbadbots"); ?>
         <br>
         <?php esc_attr_e("Stop Bad Bots => Setting => General Settings","stopbadbots"); ?>
         <br>
         <?php esc_attr_e("and mark all with yes.","stopbadbots"); ?>
         <br>
         <a href="<?php echo esc_url($site) ?>level"> <?php esc_attr_e("Dismiss","stopbadbots"); ?></a>
         <hr>
        <?php }
        if ($empty_notif) {
            echo  '<br>';
            echo '<b>'.esc_attr_e("No notifications at this time!","stopbadbots").'</b>';
        }
        ?>
   </div>
</div>