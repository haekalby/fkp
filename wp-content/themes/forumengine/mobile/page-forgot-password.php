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
					<span class="fe-tab-name"><?php _e('Forgot your password?', ET_DOMAIN) ?></span>
				</a>
			</li>
		</ul>
	</div>
	<div id="forgot_pass">
		<span class="text"><?php _e( "Type your email and we'll send you a link to retrieve it.", ET_DOMAIN ) ?></span>
		<form id="form_forgot_pass" class="form-horizontal">
	  		<div class="form-group">
				<div class="form-field fe-form-item">
					<span class="line-correct  collapse"></span>
		  			<input type="text" name="user_login" class="form-control" autocomplete="off" id="user_login" placeholder="<?php _e( 'Enter your email address', ET_DOMAIN ) ?>">
		  			<span class="icon collapse" data-icon="D"></span>
				</div>
				<button class="fe-form-btn" type="button" id="btn-forgot" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" class="btn"><?php _e( 'Send', ET_DOMAIN ) ?></button>
	  		</div>
		</form>
	</div>	
</div>
<?php 
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>
