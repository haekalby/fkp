<?php
/**
 * Template Name: Reset Password
 */

get_header();
global $wp_query, $wp_rewrite, $post,$user_ID;

?>
<!--end header Bottom-->
<div class="container main-center">
	<div class="row">
		<div class="marginTop30 new-pass-page">
				<div class="title">
					<h1><?php _e('new password',ET_DOMAIN) ?></h1>
					<span class="sub-h1"><?php _e('Type your new password on the fields below',ET_DOMAIN) ?></h1>
				</div>
				<div class="main-edit-profile">
					<form class="form-horizontal" id="reset_pass" method="POST">
						<span class="clearfix"></span>
						<div class="control-group">
							<label class="control-label" for="new_pass"><?php _e('New password',ET_DOMAIN) ?></label>
							<div class="controls">
								<input type="password" name="new_pass" id="new_pass" placeholder="" value="">
								<span class="icon collapse" data-icon="D"></span>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="re_pass"><?php _e('Retype',ET_DOMAIN) ?></label>
							<div class="controls">
								<input type="password" name="re_pass" id="re_pass" placeholder="" value="">
								<span class="icon collapse" data-icon="D"></span>
							</div>
						</div>
						<div class="control-group">
    						<div class="controls">
								<div class="button-event">
									<input type="submit" class="btn" value="Submit">
								</div>
							</div>
						</div>
						<input type="hidden" id="user_login" name="user_login" value="<?php echo $_GET['user_login'] ?>" />
						<input type="hidden" id="user_key" name="user_key" value="<?php echo $_GET['key'] ?>">
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>

