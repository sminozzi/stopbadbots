<?php
/**
 * @ Author: Bill Minozzi -
 * @ Copyright: 2023 www.BillMinozzi.com
 * @ Modified time: 2023-07-17 2024-02-27
 */
if (!defined("ABSPATH")) {
    exit();
} // Exit if accessed directly
global $wpdb;


if (current_user_can('manage_options')) 
   add_action("admin_menu", "stopbadbots_add_menu_stats");
   
// add_action('wp_head', 'antibots_ajaxurl');
function stopbadbots_add_menu_stats()
{
    $stopbadbots_table_page = add_submenu_page(
        "stop_bad_bots_plugin", // $parent_slug
        "Visits Analytics", // string $page_title
        "Visits Analytics", // string $menu_title
        "manage_options", // string $capability
        "stopbadbots_my-custom-submenu-page-stats",
        "stopbadbots_render_stats_page"
    );
}
function stopbadbots_render_stats_page()
{
    global $stopbadbots_checkversion;
    global $wpdb;
    ?>
    <div id="stopbadbots-logo">
    <img alt="logo" src="<?php echo esc_attr(
        STOPBADBOTSIMAGES
    ); ?>/logo.png" width="250px" />
    </div>
    <center>
         <img id="stopbadbots_spinner" alt="stopbadbots_spinner" src="<?php echo esc_attr(STOPBADBOTSIMAGES);?>/spinner.gif" width="100px" style="opacity:.5"; />
    </center>
    <center>
    <?php
    esc_attr_e(
        "These data depend on the number of days you keep in the visitors-log file. Refer to the plugin settings page for more details.",
        "stopbadbots"
    );
    echo "<br>";
    /*
    esc_attr_e(
        "To access detailed analytics, visit the Visits Log page. Also, remember to explore the Help button located in the top right corner of that page.",
        "stopbadbots"
    );
    */
    echo "</center>";
    /* --------------------------- */
    ?>
     </center>
<div style="max-width: 100%; padding-right: 20px;">
 <br><br><br><br>
 <div id="stopbadbots_help_title">
 Page View by days
 </div> 
 <br>
 <center>
 <?php
 esc_attr_e(
     "Analysis of total visitor count by unique IP addresses.",
     "stopbadbots"
 );
 echo "</center>";
 //require_once  STOPBADBOTSPATH . 'includes/visits_stats/calcula_stats2.php';
 //require_once  STOPBADBOTSPATH . 'includes/visits_stats/visits_graph2.php';
 require_once STOPBADBOTSPATH . "includes/visits_stats/analytics-30days.php";
 /* --------------------------- */
    ?>
    <br> <br><br> <br><br> <br>
    <div id="stopbadbots_help_title">
    Page View by month
    </div> 
    <br>
    <center>
    <?php
    esc_attr_e(
      "Analysis of total visitor count by unique IP addresses.",
      "stopbadbots"
    );
    echo "<br><br>";
    //  $type_access = 'Total';
    //require_once  STOPBADBOTSPATH . 'includes/visits_stats/calcula_stats.php';
    //require_once  STOPBADBOTSPATH . 'includes/visits_stats/visits_graph.php';
    require_once STOPBADBOTSPATH . "includes/visits_stats/analytics-12m.php";
    /* --------------------------- */
    ?>
 <div id="stopbadbots_help_title">
   <br><br><br><br><br> <br>
   Average Pages Viewed per IP per Month
   </div> 
   <br>
   <center>
   <?php
   //esc_attr_e("Analysis of total visitor count by unique IP addresses.","stopbadbots");
   echo "</center>";
   require_once STOPBADBOTSPATH .
       "includes/visits_stats/analytics-pages-by-ip.php";
   /* --------------------------- */
    //echo '<table class="wp-list-table widefat striped" id="visitors-table">';
    ?>
   <div id="visitors-table">
   <div id="stopbadbots_help_title">
   <br><br> <br><br><br> <br>
  Pages Visited Table
   </div> 
   <br>
   <center>
   <?php
   //esc_attr_e("Analysis of total visitor count by unique IP addresses.","stopbadbots");
   echo "</center>";
   require_once STOPBADBOTSPATH . "includes/visits_stats/analytics-pages.php";
   /* --------------------------- */
    ?>
  </div> 
  <div id="visitors-table-ref_table">
   <div id="stopbadbots_help_title">
   <br><br>
   <hr>
 Referer Table
  </div> 
  <br>
  <center>
  <?php
  //esc_attr_e("Analysis of total visitor count by unique IP addresses.","stopbadbots");
  echo "</center>";
  require_once STOPBADBOTSPATH . "includes/visits_stats/analytics-referer.php";
  echo '</div>'; 
  /* --------------------------- */
  echo "</div>";
}
return;
