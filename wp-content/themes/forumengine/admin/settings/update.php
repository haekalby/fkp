<div class="et-main-main clearfix inner-content hide" id="setting-update">
<?php 
?>
	<div class="desc">
		<?php _e('Enter your license key on Enginethemes.com', ET_DOMAIN) ?>:
		<div class="form no-margin no-padding no-background">
			<div class="form-item license-field">
				<input class="bg-grey-input" type="text" placeholder="<?php _e('Enter license key', ET_DOMAIN) ?>" value="<?php echo get_option('et_license_key', '') ?>" id="license_key" name="license_key">
			</div>
		</div>
	</div>
	<?php 
		$theme = wp_get_theme();
		if(!get_option( 'fe_update_users_likes' ) && $theme->get( 'Version' ) != "1.2.2"){
	?>
	<div class="desc" id="update_content_wrap">
		<?php _e('Update the missing contents (e.g. User Likes Count,...)', ET_DOMAIN) ?>:
		<div class="form no-margin no-padding no-background">
			<div class="btn-language padding-top10 f-left-all btn-update-content">
				<button class="primary-button" id="fe_update_content">Update Content</button>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

