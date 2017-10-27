
<?php if(current_user_can("edit_threads")) {?>


<div id="form_thread" class="thread-form auto-form new-thread">
	<form action="" method="post">
		<input type="hidden" name="fe_nonce" class="fe_nonce" value="<?php echo wp_create_nonce( 'insert_thread' ) ?>">
		<div class="text-search">
			<div class="input-container">
				<input class="inp-title" id="thread_title" maxlength="90" name="post_title" type="text" autocomplete="off" placeholder="<?php _e('Click here to start your new topic' , ET_DOMAIN) ?>">
			</div>
			<div class="btn-group cat-dropdown dropdown category-search-items collapse">
				<span class="line"></span>
				<button class="btn dropdown-toggle" data-toggle="dropdown">
					<span class="text-select"></span>
					<span class="caret"></span>
				</button>
				<?php
					$categories = FE_ThreadCategory::get_categories();
				?>
				<select class="collapse" name="thread_category" id="thread_category">
					<option value=""><?php _e('Please select' , ET_DOMAIN) ?></option>
					<?php
						$term = get_term_by( 'slug' , get_query_var( "term" ), 'thread_category') ;
						if(isset($term->term_id)){
							et_the_cat_select($categories, $term->term_id) ;
						} else {
							et_the_cat_select($categories) ;
						}
					?>
				</select>
			</div>
	  	</div>
		<div class="form-detail collapse">
			<div id="wp-post_content-editor-container" class="wp-editor-container">
				<textarea id="post_content" name="post_content"></textarea>
			</div>
			<?php
				// $useCaptcha = et_get_option('google_captcha') ;
				// if($useCaptcha){
					do_action( 'fe_custom_fields_form' );
				// }
			?>
			<div class="row line-bottom">
				<div class="col-md-6">
					<div class="show-preview">
						<div class="skin-checkbox">
							<span class="icon" data-icon="3"></span>
							<input type="checkbox" name="show_preview" class="checkbox-show" id="show_topic_item" style="display:none" />
						</div>
						<a href="#"><?php _e('Show preview' , ET_DOMAIN) ?></a>
					</div>
				</div>
				<div class="col-md-6">
					<div class="button-event">
						<input type="submit" id = "btn-create" value="
						<?php
							if($user_ID){
								_e('Create Topic', ET_DOMAIN);
							} else {
								_e('Login and Create Topic', ET_DOMAIN);
							}
						?>
						" class="btn">
						<a href="#" class="cancel"><span class="btn-cancel"><span class="icon" data-icon="D"></span><?php _e('Cancel' , ET_DOMAIN) ?></span></a>
					</div>
				</div>
			</div>
		</div>
	</form>
	<div id="thread_preview">
		<div class="name-preview"><?php _e('YOUR PREVIEW' , ET_DOMAIN) ?></div>
        <div class="reply-item items-thread clearfix preview-item">
			<div class="f-floatleft">
				<?php echo et_get_avatar($user_ID);?>
			</div>
			<div class="f-floatright">
				<div class="post-display">
					<div class="post-information">
						<div class="name">
							<span class="post-author"><?php echo $current_user->display_name;?></span>
							<span class="comment"><span class="icon" data-icon="w"></span>0</span>
							<span class="like"><span class="icon" data-icon="k"></span>0</span>
						</div>
					</div>
					<div class="text-detail content"></div>
				</div>
			</div>
        </div>
	</div><!-- End Preview Thread -->
</div> <!-- End Form Thread -->
<?php } ?>