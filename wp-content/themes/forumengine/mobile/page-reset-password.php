<?php 
et_get_mobile_header();
// header part
get_template_part( 'mobile/template', 'header' );
?>
<div data-role="content" class="fe-content fe-content-auth">
	<div class="fe-tab">
		<ul class="fe-tab-items nav nav-tabs">
			<li class="fe-tab-item active">
				<a href="#content_login" data-toggle="tab" class="ui-link">
					<span class="fe-tab-name"><?php _e('New password', ET_DOMAIN) ?></span>
				</a>
			</li>
		</ul>
	</div>
	<div class="reset-pass">
		<span class="text"><?php _e('Type your new password on the fields below:',ET_DOMAIN) ?></span>
		<div class="main-edit-profile">
			<form class="form-horizontal" id="reset_pass" method="POST">
				<div class="fe-form-item">
					<label><?php _e('New password',ET_DOMAIN) ?></label>
					<div class="controls">
						<input type="password" name="new_pass" id="new_pass" placeholder="" value="">
						<span class="icon collapse" data-icon="D"></span>
					</div>
				</div>
				<div class="fe-form-item">
					<label><?php _e('Retype',ET_DOMAIN) ?></label>
					<div class="controls">
						<input type="password" name="re_pass" id="re_pass" placeholder="" value="">
						<span class="icon collapse" data-icon="D"></span>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<div class="button-event">
							<input type="submit" class="btn fe-form-btn" value="Submit">
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
<?php 
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>
