	<script src="<?php echo TEMPLATEURL ?>/js/libs/bootstrap.min.js"></script>
	<script src="<?php echo TEMPLATEURL ?>/includes/core/js/lib/underscore-min.js"></script>
	<script src="<?php echo TEMPLATEURL ?>/includes/core/js/lib/backbone-min.js"></script>
	<?php do_action('wp_enqueue_scripts'); ?>
	<script src="<?php echo TEMPLATEURL ?>/js/functions.js"></script>
	
	<script src="<?php echo TEMPLATEURL ?>/mobile/js/main.js"></script>
	<script src="<?php echo TEMPLATEURL ?>/mobile/js/index.js"></script>
	<?php if(!is_page_template('page-edit-profile.php' )){?>
	<script src="<?php echo TEMPLATEURL ?>/mobile/js/single-thread.js"></script>
	<?php } ?>
	<script src="<?php echo TEMPLATEURL ?>/mobile/js/authorize.js"></script>
	<script src="<?php echo TEMPLATEURL ?>/mobile/js/reset-password.js"></script>
	<script src="<?php echo TEMPLATEURL ?>/mobile/js/author.js"></script>
	<script src="<?php echo TEMPLATEURL ?>/mobile/js/edit-profile.js"></script>

	<?php
		global $fe_confirm,$current_user;
		if($fe_confirm == 1)
			echo '<script type="text/javascript">
	        jQuery(document).ready(function() {
	            ForumMobile.app.notice("success", "'.__("Your account has been confirmed successfully!",ET_DOMAIN).'");
	        });
	    </script>';
	?>
	<script type="text/javascript" id="current_user">
	 	var currentUser = <?php
	 	if ($current_user->ID)
	 		echo json_encode(FE_Member::convert($current_user));
	 	else
	 		echo json_encode(array('id' => 0, 'ID' => 0));
	 	?>;
	 	var loginUrl = "<?php echo et_get_page_link('login'); ?>";
	</script>
	<script type="text/javascript" id="translation_text">
	var translation_text = <?php
	 		$text = array(
	 			'fill_out' => __('please fill out all fields', ET_DOMAIN),
	 		);
	 		echo json_encode($text);
	 	?>;
	</script>
	<?php
		//if( is_singular( 'thread' ) ){
			get_template_part( 'mobile/template-js/reply', 'item' );
	?>

	<script type="text/javascript">
		_.templateSettings = {
			evaluate: /\<\#(.+?)\#\>/g,
			interpolate: /\{\{=(.+?)\}\}/g,
			escape: /\{\{-(.+?)\}\}/g
		};
	</script>

	<?php  wp_footer(); ?>
	</body>
</html>