<?php namespace stopbadbots_graph;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$array30 = $total_array30;
$array30d = $total_array30d;
// total
$d_total = '[';
for($i=0; $i<30; $i++)
{
  // $d_total .= '[';
  $d_total .= '['.$i;
  $d_total .= ',';
  $d_total .= $total_array30[$i];
  $d_total .= ']';
  if($i < 29) 
      $d_total .= ',';
}
$d_total .= '];';
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
// dmask
$d_mask = '[';
for($i=0; $i<30; $i++)
{
  $d_mask .= '['.$i;
  $d_mask .= ',';
  $d_mask .= $masked_array30[$i];
  $d_mask .= ']';
  if($i < 29) 
     $d_mask .= ',';
}
$d_mask .= '];';
// Denied
$d_denied = '[';
for($i=0; $i<30; $i++)
{
  $d_denied .= '['.$i;
  $d_denied .= ',';
  $d_denied .= $denied_array30[$i];
  $d_denied .= ']';
  if($i < 29) 
     $d_denied .= ',';
}
$d_denied .= '];';
echo '<script type="text/javascript">';
?>

jQuery(document).ready(function() {


  <?php
  if(!empty($stopbadbots_checkversion))
    echo 'var ispro = true;';
  else
    echo 'var ispro = false';
  ?>


  var d0 = <?php echo esc_attr($d_total);?>
  var d1 = <?php echo esc_attr($d_ok);?>
  var d2 = <?php echo esc_attr($d_denied);?>
  var d3 = <?php echo esc_attr($d_mask);?>
  var dataset = [
    {
      label: 'Total',
      data: d0,
      color: 'gray'
    },
    {
      label: 'OK',
      data: d1,
      color: 'green' 
    },
    {
      label: 'Denied',
      data: d2,
      color: 'red' 
    },
    {
      label: 'Mask',
      data: d3,
      color: 'blue' 
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
     var plot = jQuery.plot("#placeholder",  dataset, options );
   // Adicionar checkboxes para cada série
   for (var i = 0; i < dataset.length; i++) {
      var seriesLabel = dataset[i].label;
      var checkbox = jQuery('<input type="checkbox" checked>');
      checkbox.attr('id', 'checkbox-' + i);
      checkbox.val(seriesLabel);
      checkbox.click(function() {
        var seriesLabel = jQuery(this).val();
        var series = plot.getData().find(s => s.label === seriesLabel);
        series.lines.show = !series.lines.show;
        series.points.show = !series.points.show; // Inverter a visibilidade dos pontos
        if(ispro) {
           plot.draw();
        }
        else {
           alert("Enhanced Functionality Exclusive to Pro Version!");
        }
      });
      var label = jQuery('<label>').text(seriesLabel);
      label.attr('for', 'checkbox-' + i);
      jQuery('#legend-container').append(checkbox);
      jQuery('#legend-container').append(label);
      jQuery('#checkbox-' + i).css('margin-left', '20px');
    } // end for next
}); // end doc ready
</script>
<div id="legend-container" style="text-align:center"></div>
<div id="placeholder"  style="width:100% !important; max-width:99% !important; height:300px !important; margin-top: 0px; margin-right:20px !important;"></div>
