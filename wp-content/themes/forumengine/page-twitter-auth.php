<?php
global $wp_query, $wp_rewrite, $post;

get_header();
?>

<!--end header Bottom-->
<div class="container-fluid main-center">
	<div class="row">
		<div class="col-md-12 marginTop30">
			<div class="twitter-auth social-auth">
				<p class="text-page-not social-big"><?php _e('SIGNING IN WITH TWITTER',ET_DOMAIN);?></p>
				<p class="social-small"><?php _e('Please provide your email for the last step',ET_DOMAIN) ?></p>
				<form method="post" action="<?php echo add_query_arg('action', 'twitterauth_login', home_url()) ?>">
					<div class="social-form">
						<input type="text" name="user_email" placeholder="">
						<input type="submit" value="Submit">
					</div>
					<?php global $et_error; ?>
					<?php if ( $et_error ) echo '<p class="error">' . $et_error . '</p>'; ?>
				</form>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>

