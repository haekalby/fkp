<div id="setting-mailing" class="inner-content et-main-main clearfix hide">
<?php
	$FE_MailTemplate       = 	new FE_MailTemplate();
	$register_mail         = 	$FE_MailTemplate->get_register_mail();
	$confirm_mail          = 	$FE_MailTemplate->get_confirm_mail();
	$confirmed_mail        = 	$FE_MailTemplate->get_confirmed_mail();
	$twitter_mail          = 	$FE_MailTemplate->get_twitter_mail();
	$facebook_mail         = 	$FE_MailTemplate->get_facebook_mail();
	$forgot_pass_mail      = 	$FE_MailTemplate->get_forgot_pass_mail();
	$reset_pass_mail       =	$FE_MailTemplate->get_reset_pass_mail();
	$following_thread_mail =	$FE_MailTemplate->get_following_thread_mail();
	$pending_thread_mail   = 	$FE_MailTemplate->get_pending_thread_mail();
	$ban_member_mail       =	$FE_MailTemplate->get_ban_member_mail();
	$report_mail           =	$FE_MailTemplate->get_report_mail();
?>
	<div class="title font-quicksand mail-template-title" id="auth-mail-template-title">
		<?php _e("Authentication Mail Template",ET_DOMAIN);?>
		
	</div>
	<div class="desc" id="authentication-mail-template">
		<?php _e("Email templates for authentication process. You can use placeholders to include some specific content.",ET_DOMAIN);?> 
		<a class="icon btn-template-help" data-icon="?" href="#" title="<?php  _e("View more details",ET_DOMAIN) ?>"></a>
		<div class="cont-template-help">
			[user_login],[display_name],[user_email] : <?php _e("user's details you want to send mail", ET_DOMAIN) ?><br />
			[activate_url] : <?php _e("activate link is require for user to renew their pass", ET_DOMAIN) ?> <br />
			[confirm_link] : <?php _e("activate link is require for user to active their account", ET_DOMAIN) ?> <br />
			[site_url],[blogname],[admin_email] :<?php _e(" site info, admin email", ET_DOMAIN) ?>
		</div>
		<div class="inner email-template" >
			<div class="item">
				<div class="payment">
					<?php _e("User Register Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $register_mail, 'register_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>							
							<div class="mail-control-btn">
								<a href="#" rel="register_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>
						
					</div>
				</div>    						
			</div>

			<div class="item">
				<div class="payment">
					<?php _e("User Confirmation Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $confirm_mail, 'confirm_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>							
							<div class="mail-control-btn">
								<a href="#" rel="confirm_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>
						
					</div>
				</div>    						
			</div>

			<div class="item">
				<div class="payment">
					<?php _e("User Confirmationed Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $confirmed_mail, 'confirmed_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>							
							<div class="mail-control-btn">
								<a href="#" rel="confirmed_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>
						
					</div>
				</div>    						
			</div>

			<div class="item">
				<div class="payment">
					<?php _e("User Facebook Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $facebook_mail, 'facebook_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>							
							<div class="mail-control-btn">
								<a href="#" rel="facebook_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>
						
					</div>
				</div>    						
			</div>	

			<div class="item">
				<div class="payment">
					<?php _e("User Twitter Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $twitter_mail, 'twitter_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>							
							<div class="mail-control-btn">
								<a href="#" rel="twitter_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>
						
					</div>
				</div>    						
			</div>

			<div class="item">
				<div class="payment">
					<?php _e("Forgot Password Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $forgot_pass_mail, 'forgot_pass_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>
							<div class="mail-control-btn">
								<div>(*)[activate_url] : activate url is require for user to renew their pass, you must have it in your mail </div>
								<a href="#" rel="forgot_pass_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>						
					</div>
				</div>    						
			</div>
			
			<div class="item">
				<div class="payment">
					<?php _e("Reset Password Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $reset_pass_mail, 'reset_pass_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>
							<div class="mail-control-btn">
								<a href="#" rel="reset_pass_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>
					</div>
				</div>    						
			</div>

			<div class="item">
				<div class="payment">
					<?php _e("Following Thread Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $following_thread_mail, 'following_thread_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>
							<div class="mail-control-btn">
								<a href="#" rel="following_thread_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>
					</div>
				</div>    						
			</div>
			<div class="item">
				<div class="payment">
					<?php _e("Pending Thread Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $pending_thread_mail, 'pending_thread_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>
							<div class="mail-control-btn">
								<a href="#" rel="pending_thread_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>
					</div>
				</div>    						
			</div>

			<div class="item">
				<div class="payment">
					<?php _e("Ban member Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $ban_member_mail, 'ban_member_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>
							<div class="mail-control-btn">
								<a href="#" rel="ban_member_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>
					</div>
				</div>    						
			</div>

			<div class="item">
				<div class="payment">
					<?php _e("Report Mail Template",ET_DOMAIN);?>
				</div>	    
				<div class="form payment-setting hide">
					<div class="form-item">
						<div class="mail-template">
							<div class="mail-input">
								<?php wp_editor( $report_mail, 'report_mail', backend_editor_settings() );?>
								<span class="icon" data-icon="3"></span>
							</div>
							<div class="mail-control-btn">
								<a href="#" rel="report_mail" class="trigger-editor" ><?php  _e('Toggle Editor',ET_DOMAIN) ?></a> | <a href="#" class="reset-default" ><?php _e('Reset to default', ET_DOMAIN)?></a>
							</div>
						</div>
					</div>
				</div>    						
			</div>

		</div>
	</div>
</div>