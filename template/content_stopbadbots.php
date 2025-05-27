<?php if (!defined('ABSPATH')) {
	die('Invalid request.');
}?>
<!DOCTYPE html>
<html>
<head>
	
	<?php wp_head(); ?>

<script>
	setTimeout(function(){ location.reload(); }, 3000);
</script>

</head>
<body>


<div class="container" id="main-content">

<style>
.verticalhorizontal {
	width: 100px; 
	height: 100px;   
	top: 40%;
	left: 50%;
	bottom: 50%;
	right: 50%;
	position: absolute;
}
body {background-color: white;}
</style>


<?php define( 'STOPBADBOTSURL2', plugin_dir_url( __file__ ) ); ?>

<div class="verticalhorizontal">
	<img src="<?php echo esc_attr( STOPBADBOTSURL2 ); ?>ajax-loader.gif" alt="centered image" />
</div>


</div>


<?php wp_footer(); ?> 



</body>
</html>
