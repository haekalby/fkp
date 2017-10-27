<?php
/**
 * Template Name: Edit Profile
 */
global $wp_query, $wp_rewrite, $post,$current_user;
$author = get_user_by( 'id', $current_user->ID );

$user_mobile 	= get_user_meta($author->ID,'user_mobile',true);
$user_location 	= get_user_meta($author->ID,'user_location',true);
$user_hide_info = get_user_meta($author->ID,'user_hide_info',true);
$user_facebook 	= get_user_meta($author->ID,'user_facebook',true);
$user_twitter 	= get_user_meta($author->ID,'user_twitter',true);
$user_gplus 	= get_user_meta($author->ID,'user_gplus',true);

get_header();
?>

<!--end header Bottom-->
<div class="container-fluid main-center">
	<div class="row">
		<div class="col-md-12 marginTop30">
			<div class="content-edit-profile">
				<div class="head-edit-profile">
					<a href="javascript:history.back();" class="back"><span class="icon" data-icon="["></span><?php _e('Back',ET_DOMAIN) ?></a>
					<div id="user_logo_container">
						<span class="img-profile" id="user_logo_thumbnail"><?php echo  et_get_avatar($author->ID);?></span>
						<span class="btn-profile" id="user_logo_browse_button"><a href="#" class="no-underline"><span class="icon" data-icon="p"></span><br /><?php _e('New Look',ET_DOMAIN) ?></a></span>
						<span class="et_ajaxnonce" id="<?php echo wp_create_nonce('user_logo_et_uploader'); ?>"></span>
						<span class="hide" id="user_id" user-data="<?php echo $author->ID; ?>"></span>
					</div>
					<a href="<?php echo et_get_page_link('change-pass'); ?>" class="text-change-password"><?php _e('Change password',ET_DOMAIN) ?></a>
				</div>

				<div class="main-edit-profile">
					<form class="form-horizontal" id="edit_profile" method="POST">
					<!--<div class="intro-edit-profile">
						<span class="text"><?php _e('About',ET_DOMAIN) ?> @<?php echo $author->user_login ?></span>
						<textarea name="description" class="edit-about-me"><?php echo $author->description ?></textarea>
					</div>-->
						<input type="hidden" value="<?php echo wp_create_nonce( 'update_profile' ) ?>" name="fe_nonce" />
						<input type="hidden" value="<?php echo $author->ID; ?>" name="ID" />
						<!--<span class="text">Pengaturan profil</span>-->
						<span class="clearfix"></span>
						<div class="control-group">
							<label class="control-label" for="display_name">Nama Anda</label>
							<div class="controls">
								<input type="text" name="display_name" id="display_name" placeholder="<?php _e('e.g. John Smith',ET_DOMAIN) ?>" value="<?php echo $author->display_name ?>">
								<span class="icon hide" data-icon="D"></span>
							</div>
						</div>
						<!--<div class="control-group">
							<label class="control-label" for="location"><?php _e('Satuan Kerja',ET_DOMAIN) ?></label>
							<div class="controls">
								<input type="text" name="user_location" id="location" placeholder="<?php _e('contoh: (528743) KPPN Merauke',ET_DOMAIN) ?>" value="<?php echo $author->user_location ?>">
								<span class="icon hide" data-icon="D"></span>
							</div>
						</div>-->
						<div class="control-group">
							<label class="control-label" for="email">Email Anda</label>
							<div class="controls">
								<input type="text" name="user_email" id="email" placeholder="<?php _e('e.g. email@email.com',ET_DOMAIN) ?>" value="<?php echo $author->user_email ?>">
								<span class="icon hide" data-icon="D"></span>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="phone">No telepon</label>
							<div class="controls">
								<input type="text" name="user_mobile" id="phone" placeholder="<?php _e('e.g. 0123 456 789',ET_DOMAIN) ?>" value="<?php echo $user_mobile ?>">
								<span class="icon hide" data-icon="D"></span>
							</div>
						</div>
						<div class="control-group">
						<div class="controls">
							<div class="hide-info">
								<div class="checkbox-hide <?php if($user_hide_info == 1) {echo 'checked';}?>" id="checkbox_hide" >
									<span data-icon="3" class="icon"></span>
									<input type="hidden" id="hide_info" value="<?php echo $user_hide_info ?>" class="hide" name="user_hide_info">
								</div>
								<a href="#">Klik disini untuk menampilkan Email dan No telepon</a>
							</div>
						</div>
						</div>
						<!--<span class="text text1"><?php _e('Social Setting',ET_DOMAIN) ?></span>-->
						<span class="clearfix"></span>
						<!--<div class="control-group">
							<label class="control-label" for="facebook">Facebook</label>
							<div class="controls">
								<input type="text" name="user_facebook" id="facebook" class="profile-input-url" placeholder="e.g. http://facebook.com/someone" value="<?php echo $user_facebook ?>">
								<span class="icon hide" data-icon="D"></span>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="twitter">Twitter</label>
							<div class="controls">
								<input type="text" name="user_twitter" id="twitter" class="profile-input-url" placeholder="e.g. http://twitter.com/someone" value="<?php echo $user_twitter ?>">
								<span class="icon hide" data-icon="D"></span>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="google">Google Plus</label>
							<div class="controls">
								<input type="text" name="user_gplus" id="google" class="profile-input-url" placeholder="e.g. http://plus.google.com/someone" value="<?php echo $user_gplus ?>">
								<span class="icon hide" data-icon="D"></span>
							</div>
						</div>-->

						<div class="button-event">
							<input type="submit" class="btn" value="<?php _e('Submit',ET_DOMAIN) ?>">
						</div>

					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?php get_footer(); ?>

