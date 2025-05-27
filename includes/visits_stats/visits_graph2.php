<?php namespace stopbadbots_graph2;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$array30 = $total_array30;
$array30d = $total_array30d;
$dt_ticks = '[';
for($i=0; $i<30; $i++)
{
    $dt_ticks .= '[';
    $dt_ticks .= $i;
    $dt_ticks .= ',';
    $dt_ticks .= substr($total_array30d[$i], 2);
    $dt_ticks .= ']';
    if($i < 29) {
      $dt_ticks .= ',';
    }
}
$dt_ticks .= ']';
// OK
$d_ok = '[';
for($i=0; $i<30; $i++)
{
  $d_ok .= '['.$i;
  $d_ok .= ',';
  $d_ok .= $ok_array30[$i];
  $d_ok .= ']';
  if($i < 29) 
     $d_ok .= ',';
}
$d_ok .= '];';
echo '<script type="text/javascript">';





?>
jQuery(document).ready(function() {

  <?php
  if(!empty($stopbadbots_checkversion))
    echo 'var ispro = true;';
  else
    echo 'var ispro = false';
  ?>

  var d0 = <?php echo esc_attr($d_ok);?>

 // console.log(d0);

  var dataset = [
    {
      label: 'Visits by Unique IP',
      data: d0,
      color: 'green'
    },
  ];
  var options = {
            series: {
                lines: { show: true },
                points: { show: true },
                color: "#ff0000",
                lines: {
                  show: true,
                  fill: false,
                  fillColor: { colors: [{ opacity: 0.2 }, { opacity: 0.2 }] }
                },
            },
            container: '#legend-container',
            grid: { hoverable: true, 
            clickable: true,
            borderColor: "#CCCCCC",
            color: "#333333",
            backgroundColor: { colors: ["#fff", "#eee"]}           
            },
            xaxis:{
               font:{
                  size:8,
                  style:"italic",
                  weight:"bold",
                  family:"sans-serif",
                  color: "#616161",
                  variant:"small-caps"
                },
                   ticks: <?php echo esc_attr($dt_ticks);?>, 
            },
            yaxis: {
                  font:{
                    size:10,
                    style:"italic",
                    weight:"bold",
                    family:"sans-serif",
                    color: "#616161",
                    variant:"small-caps"
                  }, 
                    stopbadbots_tickFormatter: function stopbadbots_suffixFormatter(val, axis) {return (val.toFixed(0)); }             
            },
        };
     var plot = jQuery.plot("#placeholder2",  dataset, options );
}); // end doc ready
</script>
<div id="placeholder2"  style="width:100% !important; max-width:99% !important; height:300px !important; margin-top: 0px; margin-right:20px !important;"></div>
