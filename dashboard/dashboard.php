<?php

/**
 * @author    William Sergio Minozzi
 * @copyright 2017
 * @ Modified time: 2020-02-03 16:00:57
 * */
if (!defined('ABSPATH')) {
    exit;
}
//
?>
<div id="stopbadbots-steps3">
    <div class="stopbadbots-block-title">
        <?php esc_attr_e("Stop Bad Bots Plugin Activated","stopbadbots"); ?>

    </div>
    <div class="stopbadbots-help-container1">

        <!-- "Column1">  -->
        <div class="stopbadbots-help-2column stopbadbots-help-column-2">
            <h3><?php esc_attr_e("Protection Status","stopbadbots"); ?></h3>
            <?php


            $perc = stopbadbots_find_perc();

            /*
            $color = '#ff0000';
            if ($perc > 80) {
            $color = '#029E26';
            // verde
            }
            */

            $nivel = round($perc / 10, 0, PHP_ROUND_HALF_UP);

            /*
            echo '<p><li style="max-width:50%;font-weight:bold;padding:5px 15px;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;background-color:#0073aa;margin-left:13px;color:white;">' .
            'Protection Level' .
            '<div style="border:1px solid #ccc;width:100%;background:white;margin:2px 5px 2px 0;padding:1px">' .
            '<div style="width: ' . ($perc) . '%;background-color:' . $color .
            ';height:6px"></div></div>' . 'Level: ' . $nivel . ' of 10  Protected' .
            '</li>';
            */
            $initValue = stopbadbots_find_perc();
            require_once "circle_status.php";


            global $stopbadbots_engine_option;
            echo 'Engine Option: <strong>'. esc_attr($stopbadbots_engine_option). '</strong> Change it on settings page.';

            echo '<br />';
            echo '<br />';

            $msg = '';

            if ($stopbadbots_block_enumeration == 'no') {
                $ms = esc_attr__("Activate Block User Enumeration in Settings Page.","stopbadbots");
            }

            if ($stopbadbots_checkversion == '') {
                $ms = esc_attr__("Upgrade to Premium for weekly updates, enhanced firewall protection, and more, ensuring a level 100% of security.","stopbadbots");
            }


            if ($stopbadbots_block_pingbackrequest == 'no') {
                $ms = esc_attr__("Activate Block PingBack Requests in Settings Page.","stopbadbots");
            }



            if ($stopbadbots_active == 'no') {
                $ms = esc_attr__("Activate Block All Bots in Settings Page.", "stopbadbots");
            }
            if ($stopbadbots_ip_active == 'no') {
                $ms = esc_attr__("Activate Block All IPs in Settings Page.", "stopbadbots");
            }
            if ($stopbadbots_referer_active == 'no') {
                $ms = esc_attr__("Activate Block all bots included at Bad Referer Table", "stopbadbots");
            }

            if ($stopbadbots_firewall != 'yes' and $stopbadbots_checkversion != '') {
                $ms = esc_attr__("Activate Firewall to increase protection.", "stopbadbots");
            }
            if (empty($ms)) {
                echo esc_attr__('Protection level currently configured.', "stopbadbots");
            } else {
                echo esc_attr($ms);
            }
            ?>
            <br /> <br />
        </div> <!-- "columns 2">  -->
        <div class="stopbadbots-help-2column stopbadbots-help-column-3">
            <?php
            if (!empty($stopbadbots_checkversion)) {

                echo '<img src="' . esc_attr(STOPBADBOTSURL) . '/assets/images/lock-xxl.png" style="text-align:center; width: 40px;margin: 10px 0 auto;"  />';
            ?>

                <h3 style="color:green; margin-top:10px;"><?php esc_attr_e("Pro Protection Enabled", "stopbadbots"); ?></h3>
                <?php esc_attr_e("With weekly database updates and Firewall protection.", "stopbadbots"); ?>
                <br />
                <?php $site = 'https://stopbadbots.com'; ?>
                <a href="<?php echo esc_url($site); ?>" class="button button-primary"><?php esc_attr_e("Learn More", "stopbadbots"); ?></a>
            <?php } else {

                echo '<center>';

                echo '<img src="' . esc_attr(STOPBADBOTSURL) . '/assets/images/unlock-icon-red-small.png" style="text-align:center; max-width: 40px;margin: 10px 0 auto;"  />';

                echo '</center>';
            ?>
                <h3 style="color:red; margin-top:10px;"><?php esc_attr_e("Only Partial Protection enabled!", "stopbadbots"); ?>
                </h3>
                <!-- Get weekly database updates and Firewall Protection. -->
                <?php esc_attr_e("Bad bots consume bandwidth, slow down servers, hack into your system, create spam, steal your content to sell to competitors - harm SEO - , search for vulnerabilities, and ruin the customer experience.", "stopbadbots"); ?>

                <br />
                <?php $site = 'https://stopbadbots.com/premium/'; ?>
                <a href="<?php echo esc_url($site); ?>" class="button button-primary"><?php esc_attr_e("Learn More", "stopbadbots"); ?></a>

            <?php
            }

            $stopbadbots_link_panel = false;

            $plugin = 'recaptcha-for-all/recaptcha.php';

            if (!is_plugin_active($plugin)) {

                $stopbadbots_link_panel = true;

                echo '<br>';
                echo '<br>';
                echo esc_attr__('reCAPTCHA extension disabled!', 'stopbadbots');

               // echo '<br>';


            }

            $plugin = 'antihacker/antihacker.php';

            if (!is_plugin_active($plugin)) {

                $stopbadbots_link_panel = true;

                echo '<br>';
                echo '<br>';
                echo esc_attr__('Anti Hacker extension disabled!', 'stopbadbots');
                // echo '<br>';

            }
            echo '<br>';
            if($stopbadbots_link_panel == true){

                echo '<a href="' . esc_attr( STOPBADBOTSHOMEURL ) . 'plugin-install.php?s=sminozzi&tab=search&type=author">Only 2 clicks to open the panel and install the extension.</a>';


            }





            ?>

        </div>
        <!-- "Column 3">  -->
    </div> <!-- "Container 1 " -->
</div> <!-- "stopbadbots-steps3"> -->


<div id="stopbadbots-services3">
    <div class="stopbadbots-help-container1">
        <div class="stopbadbots-help-column stopbadbots-help-column-1">
            <img alt="aux" src="<?php echo esc_attr(STOPBADBOTSURL) ?>assets/images/service_configuration.png" />
            <div class="bill-dashboard-titles"><?php echo esc_attr__("Start Up Guide and Settings","stopbadbots");?></div>
            <br /><br />
            <?php echo esc_attr__("Just click Settings in the left menu (Stop Bad Bots).","stopbadbots");?>
            <br />
            Dashboard => Stop Bad Bots => Settings
            <br />
            <?php $site = STOPBADBOTSHOMEURL . "admin.php?page=settings-stop-bad-bots"; ?>
            <a href="<?php echo esc_url($site); ?>" class="button button-primary"><?php echo esc_attr__("Go","stopbadbots");?></a>
            <br /><br />
        </div> <!-- "Column1">  -->
        <div class="stopbadbots-help-column stopbadbots-help-column-2">
            <img alt="aux" src="<?php echo esc_attr(STOPBADBOTSURL) ?>assets/images/support.png" />
            <div class="bill-dashboard-titles"><?php esc_attr_e("OnLine Guide, Support, Faq...","stopbadbots"); ?></div>
            <br /><br />
            <?php  esc_attr_e("You will find our complete and updated OnLine guide, faqs page, link to support and more in our site.","stopbadbots"); ?>
            <br />
            <?php $site = 'https://stopbadbots.com'; ?>
            <a href="<?php echo esc_url($site); ?>" class="button button-primary"><?php esc_attr_e("Go", "stopbadbots"); ?></a>
        </div> <!-- "columns 2">  -->
        <div class="stopbadbots-help-column stopbadbots-help-column-3">
            <img alt="aux" src="<?php echo esc_attr(STOPBADBOTSURL) ?>assets/images/system_health.png" />
            <div class="bill-dashboard-titles"><?php esc_attr_e("Troubleshooting Guide","stopbadbots"); ?></div>
            <br />
            <?php esc_attr_e("Bots showing in your statistics tool, Use old WP version, Low memory, some plugin with Javascript error are some possible problems.","stopbadbots"); ?>
            <br /><br />
            <a href="https://siterightaway.net/troubleshooting/" class="button button-primary"><?php esc_attr_e("Troubleshooting Page","stopbadbots"); ?></a>
        </div> <!-- "Column 3">  -->
    </div> <!-- "Container1 ">  -->
</div> <!-- "services"> -->



<div id="stopbadbots-services3">


    <div class="stopbadbots-help-container1">


        <div class="stopbadbots-help-2column stopbadbots-help-column-2">
            <h3><?php esc_attr_e("Total Bots Blocked Last 15 days","stopbadbots"); ?></h3>
            <br />
            <?php require_once "botsgraph.php"; ?>
            <center><?php esc_attr_e("Days","stopbadbots"); ?></center>
        </div> <!-- "Column 3">  -->



        <div style="margin-bottom: 20px; min-height: 240px;" class="stopbadbots-help-2column stopbadbots-help-column-2">
            <h3><?php esc_attr_e("Bots Blocked By Type","stopbadbots"); ?></h3>
            <br />
            <?php require_once "botsgraph_pie.php"; ?>
        </div> <!-- "Column 3">  -->


        <div class="stopbadbots-help-2column stopbadbots-help-column-2">
            <h3><?php esc_attr_e("Bots / Human Visits","stopbadbots"); ?></h3>
            <br />
            <?php require_once "botsgraph_pie2.php"; ?>
            <br /><br />
        </div> <!-- "Column 3">  -->


    </div> <!-- "Container1"> -->


</div> <!-- "Services"> -->
<div id="stopbadbots-services3">
    <div class="stopbadbots-help-container1">


        <div class="stopbadbots-help-2column stopbadbots-help-column-1">
            <h3><?php esc_attr_e("Top Bots Blocked by Name","stopbadbots"); ?></h3>
            <?php require_once "topbots.php"; ?>
        </div> <!-- "Column1">  -->

        <div class="stopbadbots-help-2column stopbadbots-help-column-1">
            <h3><?php esc_attr_e("Top Bots Blocked By IP","stopbadbots"); ?></h3>
            <?php require_once "topips.php"; ?>
        </div>


        <div class="stopbadbots-help-2column stopbadbots-help-column-2">
            <h3><?php esc_attr_e("Top Bots Bad Referer Blocked","stopbadbots"); ?></h3>
            <?php require_once "toprefs.php"; ?>
        </div>
    </div>
</div>
<center>
    <h4><?php esc_attr_e("With our plugin, many blocked bots will give up of attack your site!","stopbadbots"); ?>
    </h4>
</center>