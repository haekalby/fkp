<?php
/**
 * Template Name: Authentication
 */
global $wp_query, $wp_rewrite, $post, $et_data;
if ( session_id() == '' ) session_start();
get_header(); 
?>

<!--end header Bottom-->
<div class="container-fluid main-center">
	<div class="row">        
		<div class="col-md-12 marginTop30">
			<?php 
			$labels = $et_data['auth_labels'];
			$auth = unserialize($_SESSION['et_auth'])
			?>
			<div class="twitter-auth social-auth social-auth-step1">
				<p class="text-page-not social-big"><?php echo $labels['title'] ?></p>
				<p class="social-small"><?php echo $labels['content'] ?></p>
				<form id="form_auth" method="post" action="">
					<div class="social-form">
						<input type="hidden" name="et_nonce" value="<?php echo wp_create_nonce( 'authentication' ) ?>">
						<input type="text" name="user_email" placeholder="<?php _e('Email', ET_DOMAIN) ?>">
						<input type="password" name="user_pass"  placeholder="<?php _e('Password', ET_DOMAIN) ?>">
						<input type="submit" value="<?php _e('Submit', ET_DOMAIN) ?>">
					</div>
				</form>
			</div>
			<div class="social-auth social-auth-step2">
				<p class="text-page-not social-big"><?php echo $labels['title'] ?></p>
				<p class="social-small"><?php echo $labels['content_confirm'] ?></p>
				<form id="form_username" method="post" action="">
					<div class="social-form">
						<input type="hidden" name="et_nonce" value="<?php echo wp_create_nonce( 'authentication' ) ?>">
						<input type="text" name="user_login" value="<?php echo isset($auth['user_login']) ? $auth['user_login'] : "" ?>" placeholder="<?php _e('Username', ET_DOMAIN) ?>">
						<input type="submit" value="<?php _e('Submit', ET_DOMAIN) ?>">
					</div>
				</form>
			</div>
		</div>		
	</div>
</div>
 
<?php get_footer(); ?>