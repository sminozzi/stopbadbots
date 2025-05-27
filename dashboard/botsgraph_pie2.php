<?php
if (!defined('ABSPATH')) {
	die('Invalid request.');
}
require "calcula_stats_pie2.php";

if(!isset($stopbadbots_results10[0]['Bots']) or ! isset($stopbadbots_results10[0]['Humans'])) { 
    return;
}

  echo '<script type="text/javascript">';
  echo 'var stopbadbots_pie2 = [';

  $label = "Bots "; // . (round($stopbadbots_results10[0]['Bots'],2)) * 100;
  echo '{label: "'.esc_attr($label).'", data: '.esc_attr($stopbadbots_results10[0]['Bots']).', color: "#FF0000" },';
  $label = "Humans "; // . (round($stopbadbots_results10[0]['Humans'],2)) * 100;
 echo '{label: "'.esc_attr($label).'", data: '.esc_attr($stopbadbots_results10[0]['Humans']).', color: "#00A36A" }';

echo '];';


?>


function stopbadbots_labelFormatter(label, series) {
  return "<div style='font-size:12px;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
};

var stopbadbots_pie2_options = {
    series: {
        pie: {
            show: true,
            innerRadius: 0.3,
            label: {
                show: true,
                formatter: stopbadbots_labelFormatter,
                
            }
        }
    },

    legend: {
    show: false,

  }

};
jQuery(document).ready(function () {
  jQuery.plot(jQuery("#stopbadbots_flot-placeholder_pie2"), stopbadbots_pie2, stopbadbots_pie2_options);
});
</script>
<div id="stopbadbots_flot-placeholder_pie2" style="width:200px;height:150px;margin:-20px 0 auto"></div>
