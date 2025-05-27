<?php

/**
 * @ Author: Bill Minozzi
 * @ Copyright: 2020 www.BillMinozzi.com
 * @ Modified time: 2020-02-03 15:24:59
 */
//  http://ignitersworld.com/lab/radialIndicator.html#example
if (!defined('ABSPATH')) {
	die('Invalid request.');
}
?>
<style>
    prg-cont.canvas {
        width: 125px !important;
    }
</style>
<center>
    <div class="prg-cont rad-prg" id="indicatorContainer" style="width:125px; height:125px"></div>
</center>
<?php
// $initValue = 90;
?>
<script>
    jQuery('#indicatorContainer').radialIndicator({
        barColor: 'red',
        /*  '#87CEEB', */
        barWidth: 10,
        initValue: <?php echo esc_attr($initValue); ?>,
        roundCorner: true,
        percentage: true,
        radius: 50,
        barWidth: 10,
        barColor: {
            0: '#FF0000',
            99: '#FF0000',
            100: '#33CC33'
        },
    });
</script>