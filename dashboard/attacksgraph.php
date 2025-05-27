<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
require "calcula_stats.php";
echo '<script type="text/javascript">';
echo 'jQuery(function() {';
  echo 'var d2 = [';
for($i=0; $i<15; $i++)
{
    //graph.push([i,demand[i]]);   
    echo '[';
    echo esc_attr($i);
    echo ',';
    echo esc_attr($array30[$i]);
    echo ']';
    if($i < 14) {
        echo ',';
    }
}
  echo '];';
    echo 'var ticks = [';
for($i=0; $i<15; $i++)
{
    //        ticks.push([i,dates[i]]);    
    echo '[';
    echo esc_attr($i);
    echo ',';
    echo esc_attr(substr($array30d[$i], 2));
    echo ']';
    if($i < 14) {
        echo ',';
    }
}
  echo '];';
?>
        var options = {
            series: {
                lines: { show: true },
                points: { show: true },
                color: "#ff0000"
            },
            grid: { hoverable: true, 
            clickable: true,
            borderColor: "#CCCCCC",
            color: "#333333",
            backgroundColor: { colors: ["#fff", "#eee"]}           
            },
            xaxis:{
               font:{
                  size:6,
                  style:"italic",
                  weight:"normal",
                  family:"sans-serif",
                  color: "#ff0000",
                  variant:"small-caps"
                                 },
                  ticks: ticks,
                 /* minTickSize: [1, "day"] */
            },
                   <?php
                    echo 'yaxis: {
                                  font:{
                                  size:8,
                                  style:"italic",
                                  weight:"bold",
                                  family:"sans-serif",
                                  color: "#616161",
                                  variant:"small-caps"
                                 },'; 
                    echo 'stopbadbots_tickFormatter: function stopbadbots_suffixFormatter(val, axis) {return (val.toFixed(0)); }';             
                    echo '},';
                    ?>
            };
  <?php          
    echo 'jQuery.plot("#placeholder", [ d2 ], options);';
    // echo 'jQuery.plot("#placeholder"), [d2], options);';
    echo '});';
    echo '</script>';
    $placeholder='#placeholder'; 
    echo '<div id="placeholder" style="min-width:250px; max-width:100% !important;height:165px; margin-top: -20px;"></div>';
    ?>