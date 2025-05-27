<?php
/**
 * Tools
 * 2022/01/11
 *
 * */
// Exit if accessed directly
if (! defined('ABSPATH') ) { exit;
}
// debug3();
if(! current_user_can('activate_plugins') ) {
        return;
}
?>
<br>
<big><strong>
<?php
echo esc_attr__('If you need support, please, copy and paste the info below in our','stopbadbots'); ?> &nbsp;
<a href="https://BillMinozzi.com/support"><?php echo esc_attr__('Support Site','stopbadbots'); ?></a>
<br><br>
</strong>
<textarea style="height:60vh"; readonly="readonly" onclick="this.focus(); this.select()"><?php echo esc_attr(stopbadbots_sysinfo_get()); ?></textarea>
</big>
<?php
