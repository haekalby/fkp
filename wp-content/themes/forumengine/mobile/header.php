<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<title><?php echo et_get_option("blogname") ; ?>  | MOBILE VERSION</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

	<link rel="stylesheet" href="//code.jquery.com/mobile/1.3.2/jquery.mobile.structure-1.3.2.css" />
	<link rel="stylesheet" href="<?php echo TEMPLATEURL ?>/mobile/css/main.css" />
	<?php wp_head(); ?>
	<?php do_action( 'mobile_header' ); ?>
	<?php
		if ( is_multisite() )
			$file = THEME_CONTENT_DIR . '/css/customization_mobile_' . $wpdb->blogid . '.css';
		else
			$file = THEME_CONTENT_DIR . '/css/customization_mobile.css';
		if ( file_exists( $file ) ){
			$url = THEME_CONTENT_URL . '/css' . '/' . basename($file);
			echo '<link rel="stylesheet" href="'.$url.'" />';
		}
	?>
	<script src="<?php echo TEMPLATEURL ?>/includes/core/js/lib/jquery.min.js"></script>
	<script src="<?php echo TEMPLATEURL ?>/js/libs/jquery.validate.min.js"></script>
	<?php 	if(isset($_COOKIE['demo_is_mobile']) && $_COOKIE['demo_is_mobile'] == 1){ ?>
	<script type="text/javascript">
		$(document).bind("mobileinit", function () {
			console.log('ci');
		    $.mobile.ajaxEnabled = false;
		});
	</script>
	<?php } ?>
	<script src="<?php echo TEMPLATEURL ?>/js/libs/jquery.mobile-1.3.2.min.js"></script>
	<?php echo et_get_option('et_google_analytics'); ?>
	<script type="text/javascript">
        var fe_front = {
        	'login_2_view'  			: '<?php _e("You need to sign in to view this thread.", ET_DOMAIN) ?>',
        	'form_login_error_msg'		: '<?php _e("Please fill out all fields required.", ET_DOMAIN); ?>',
        	'form_login_error_repass'	: '<?php _e("Please enter the same password as above.", ET_DOMAIN); ?>',
        	'redirect_reset_pass'		: '<?php echo et_get_page_link("login") ?>',
        };
    </script>
</head>
<body>
