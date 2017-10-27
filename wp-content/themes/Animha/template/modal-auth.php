<div class="modal fade" id="modal_login" style="display:none;" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="login-modal">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						<span class="icon" data-icon="D"></span>
					</button>
					<h4 class="modal-title"><?php _e( 'Login or Join', ET_DOMAIN ) ?></h4>
				</div>
				<div class="modal-body">
					<div class="login-fr">
						<form id="form_login" method="post" class="form-horizontal">
							<div class="form-group">
								<div class="col-lg-10">
									<span class="line-correct collapse"></span>
									<input type="text" name="user_name" class="form-control" id="user_name" title="<?php _e( 'Enter your username or email', ET_DOMAIN ) ?>" placeholder="<?php _e( 'Enter your username or email', ET_DOMAIN ) ?>">
									<span class="icon collapse" data-icon="D"></span>
								</div>
							</div>
							<div class="form-group">
								<div class="col-lg-10">
									<span class="line-correct collapse"></span>
									<input type="password" name="user_pass" class="form-control" id="user_pass" title="<?php _e( 'Password', ET_DOMAIN ) ?>" placeholder="<?php _e( 'Password', ET_DOMAIN ) ?>">
									<span class="icon  collapse" data-icon="D"></span>
								</div>
							</div>
					  		<div class="form-group">
								<div class="col-lg-10">
						  			<div class="btn-submit">
										<a href="#" class="bnt_forget"><?php _e( 'Forgotten password?', ET_DOMAIN ) ?></a>
										<button type="submit" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" class="btn"><?php _e( 'Login', ET_DOMAIN ) ?></button>
						  			</div>
								</div>
					  		</div>
						</form>
						<!--<p class="login-by"><?php _e( 'You can also sign in by', ET_DOMAIN ) ?> : <p>-->
						<ul class="social-icon clearfix">
						<!-- google plus login -->
						<?php if(et_get_option('gplus_login', false)){?>
							<li class="gp"><a id="signinButton" href="#">Google+</a></li>
						<?php } ?>
						<!-- twitter plus login -->
						<?php if(et_get_option('twitter_login', false)){?>
							<li class="tw"><a href="<?php echo add_query_arg('action', 'twitterauth', home_url()) ?>">Twitter</a></li>
						<?php } ?>
						<!-- facebook plus login -->
						<?php if(et_get_option('facebook_login', false)){?>
							<li class="fb"><a href="#" id="facebook_auth_btn">facebook</a></li>
						<?php } ?>

						</ul>
				  	</div> <!--form login -->
				  	<div class="join">
						<form id="form_register" method="post" class="form-horizontal">
					  		<div class="form-group">
								<div class="col-lg-10">
									<span class="line-correct collapse"></span>
						  			<input type="text" name="user_name" class="form-control" id="user_name" title="<?php _e( 'Username', ET_DOMAIN ) ?>" placeholder="<?php _e( 'Username', ET_DOMAIN ) ?>">
						  			<span class="icon collapse" data-icon="D"></span>
								</div>
					  		</div>
					  		<div class="form-group">
								<div class="col-lg-10">
									<span class="line-correct collapse"></span>
						  			<input type="text" name="email" class="form-control" id="email" title="<?php _e( 'Email', ET_DOMAIN ) ?>" placeholder="<?php _e( 'Email', ET_DOMAIN ) ?>">
						  			<span class="icon collapse" data-icon="D"></span>
								</div>
					  		</div>
							
					  		<div class="form-group">
								<div class="col-lg-10">
									<span class="line-correct collapse"></span>
						  			<input type="password" name="user_pass" class="form-control" id="user_pass_register" title="<?php _e( 'Password', ET_DOMAIN ) ?>" placeholder="<?php _e( 'Password', ET_DOMAIN ) ?>">
						  			<span class="icon collapse" data-icon="D"></span>
								</div>
					  		</div>
					  		<div class="form-group" style="margin-bottom: 0">
								<div class="col-lg-10">
									<span class="line-correct collapse"></span>
						  			<input type="password" name="re_pass" class="form-control" id="re_pass" title="<?php _e( 'Retype password', ET_DOMAIN ) ?>" placeholder="<?php _e( 'Retype password', ET_DOMAIN ) ?>">
						  			<span class="icon collapse" data-icon="D"></span>
						  		</div>
						  	</div>
					  		<div class="form-group">
								<div class="col-lg-10">
						  			<div class="btn-submit">
										<div class="fe-checkbox-container">
											<input type="checkbox" name="agree_terms" id="agree_terms" class="fe-checkbox" onfocus="blur(this)">
											<label for="agree_terms"><span data-icon="3" class="icon"></span><?php _e( 'I agree to', ET_DOMAIN ) ?> <a target="_blank" href="<?php echo et_get_page_link('term-condition'); ?>"><span class="color-blue"><?php _e( 'the terms', ET_DOMAIN ) ?></span></a>.</label>
							  				<!-- <div class="skin-checkbox">
												<span data-icon="3" class="icon"></span>
												<input type="checkbox" name="agree_terms" id="agree_terms" class="checkbox-show hide">
							  				</div> -->
							  				<!-- <a href="#">I agree to <span class="color-blue">the terms</span>.</a> -->
										</div>
										<button type="submit" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" class="btn"><?php _e( 'Join', ET_DOMAIN ) ?></button>
						  			</div>
								</div>
					  		</div>
						</form>
				  	</div>
				</div>
			</div>
			<div class="forget-modal" style="display:none">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						<span class="icon" data-icon="D"></span>
					</button>
					<h4 class="modal-title"><?php _e( 'Forgot your password?', ET_DOMAIN ) ?></h4>
				</div>
				<div class="modal-body">
					<span class="text"><?php _e( "Type your email and we'll send you a link to retrieve it.", ET_DOMAIN ) ?></span>
					<form id="form_forget" class="form-horizontal">
				  		<div class="form-group">
							<div class="form-field">
								<span class="line-correct  collapse"></span>
					  			<input type="text" name="user_login" class="form-control" autocomplete="off" id="user_login" placeholder="<?php _e( 'Enter your email address', ET_DOMAIN ) ?>">
					  			<span class="icon collapse" data-icon="D"></span>
							</div>
							<button type="submit" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" class="btn"><?php _e( 'Send', ET_DOMAIN ) ?></button>
				  		</div>
					</form>
				</div>
			</div>
	  	</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>