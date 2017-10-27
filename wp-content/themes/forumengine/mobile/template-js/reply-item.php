<script type="text/template" id="mobile_reply_template">
	<article class="fe-th-post" id="reply_{{=ID}}">
		<a class="fe-avatar" href="{{= author_url }}">
			{{= avatar }}
			{{= user_badge }}
		</a>
		<div class="fe-th-container">
			<div class="fe-th-heading">
				<div class="fe-th-info">
					<a href="#" class="show-comment-child" data-id="{{=ID}}">
						<span class="comment <# if(replied && currentUser.ID) { #> active <# } #>">
							<span class="fe-icon fe-icon-comment  fe-sprite" data-icon="w"></span>
							{{= et_replies_count }}
						</span>
					</a>
					<a href="#" class="like" data-id="{{=ID}}">
						<span class="like <# if(liked) { #> active <# } #>">
							<span class="fe-icon fe-icon-like fe-sprite" data-icon="k"></span>
							<span class="count">{{=et_likes_count}}</span>
						</span>
					</a>
					<span class="time">
						{{= human_date  }}
					</span>
				</div>
				<span class="title">{{= reply_author }}</span>
			</div>
			<div class="fe-th-content">
				{{= content_filter }}
			</div>
			<!-- form edit -->
			<div class="fe-topic-form clearfix">
				<input type="hidden" name="fe_nonce" id="fe_nonce" value="{{= edit_reply_nonce }}">
				<div class="fe-topic-content" style="display:block;">
					<?php if(get_option('upload_images')){ ?>
					<div class="insert-image-wrap" data-id="{{=ID}}">
						<div class="form-post" id="{{=ID}}_images_upload_container">
							<a href="javascript:void(0)" id="{{=ID}}_images_upload_browse_button"><img src="<?php echo get_template_directory_uri();?>/mobile/img/ico-pic.png" /><?php _e("Insert Image", ET_DOMAIN); ?></a>
							<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'et_upload_images' ); ?>"></span>
							<span id="images_upload_text">
							<?php
								printf(__("Size must be less than < %sMB.", ET_DOMAIN),$max_file_size ); ?>

							</span>
						</div>
					</div>
					<?php } ?>
					<div class="textarea">
						<textarea id="thread_content">
							{{= content_edit }}
						</textarea>
					</div>
					<div class="fe-form-actions pull-right">
						<a href="#reply_{{=ID}}" class="fe-btn update-reply" data-id="{{=ID}}" data-role="button">
							<?php _e('Save', ET_DOMAIN) ?>
						</a>
						<a href="#" class="fe-btn-cancel fe-icon-b fe-icon-b-cancel cancel-modal ui-link">
							<?php _e('Cancel', ET_DOMAIN) ?>
						</a>
					</div>
				</div>
			</div>
			<!-- form edit -->
			<div class="fe-th-replies">
				<a href="#" class="btn-more-reply hidden" data-role="button">
					<?php _e('Show more replies',ET_DOMAIN); ?>
				</a>
			</div>
			<div class="fe-th-ctrl">
				<div class="fe-th-ctrl-right">
					<a href="#reply_{{=ID}}" class="fe-icon fe-icon-edit"></a>
					<a href="#reply_{{=ID}}" class="fe-icon fe-icon-quote"></a>
					<!-- <a href="" class="fe-icon fe-icon-report"></a> -->
				</div>
				<div class="fe-th-ctrl-left">
					<a href="#reply_{{=ID}}" class="fe-reply ui-link">
						<?php _e('Reply', ET_DOMAIN) ?> <span class="fe-icon fe-icon-reply"></span>
					</a>
				</div>
			</div>
			<div class="child-reply-box hidden">
				<div class="fe-reply-box expand reply-small">
					<textarea class="reply_child_content"></textarea>
					<div class="fe-reply-actions">
						<a href="#" class="reply-child fe-btn" data-id="{{=ID}}" data-role="button">
							<?php _e('Reply', ET_DOMAIN) ?>
						</a>
						<a href="#" class="fe-btn-cancel fe-icon-b fe-icon-b-cancel cancel-modal ui-link">
							<?php _e('Cancel', ET_DOMAIN) ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</article>
</script>