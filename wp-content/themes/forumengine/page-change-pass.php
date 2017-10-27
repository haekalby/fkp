<?php
/**
 * Template Name: Change Password
 */

get_header();
global $wp_query, $wp_rewrite, $post,$user_ID;

?>

<!--end header Bottom-->
<div class="container main-center">
	<div class="row">
		<div class="marginTop30">
			<div class="content-edit-profile">
				<div class="head-edit-profile">
					<a href="javascript:history.back();" class="back"><span class="icon" data-icon="["></span><?php _e('Back',ET_DOMAIN) ?></a>
					<div id="user_logo_container">
						<span class="img-profile" id="user_logo_thumbnail"><?php echo  et_get_avatar($user_ID);?></span>
						<span class="btn-profile" id="user_logo_browse_button"><a href="#"><span class="icon" data-icon="p"></span><br />New Look</a></span>
						<span class="et_ajaxnonce" id="<?php echo wp_create_nonce('user_logo_et_uploader'); ?>"></span>
						<span class="hide" id="user_id" user-data="<?php echo $user_ID; ?>"></span>
					</div>
				</div>

				<div class="main-edit-profile">
					<form class="form-horizontal" id="change_pass" method="POST">
						<input type="hidden" value="<?php echo wp_create_nonce( 'change_password' ) ?>" name="fe_nonce" />
						<span class="text"><?php _e('Change password',ET_DOMAIN) ?></span>
						<span class="clearfix"></span>
						<div class="control-group">
							<label class="control-label" for="display_name"><?php _e('Old password',ET_DOMAIN) ?></label>
							<div class="controls">
								<input type="password" name="old_pass" id="old_pass" placeholder="" value="">
								<span class="icon hide" data-icon="D"></span>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="display_name"><?php _e('New password',ET_DOMAIN) ?></label>
							<div class="controls">
								<input type="password" name="new_pass" id="new_pass" placeholder="" value="">
								<span class="icon hide" data-icon="D"></span>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="display_name"><?php _e('Retype',ET_DOMAIN) ?></label>
							<div class="controls">
								<input type="password" name="re_pass" id="re_pass" placeholder="" value="">
								<span class="icon hide" data-icon="D"></span>
							</div>
						</div>
						<div class="control-group">
    						<div class="controls">
								<div class="button-event">
									<input type="submit" class="btn" value="<?php _e("Submit", ET_DOMAIN) ?>">
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>

