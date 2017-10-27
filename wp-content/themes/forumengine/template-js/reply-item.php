<script type="text/template" id="reply_item_template">
		<ul class="control-thread">
				<li>
					<a href="#" class="edit-topic-thread control-edit" data-toggle="tooltip" title="<?php _e('Edit', ET_DOMAIN) ?>">
						<span class="icon" data-icon="p"></span>
					</a>
				</li>
			<?php if (current_user_can('manage_threads')){ ?>
				<li>
					<a href="#" class="delete-topic-thread control-delete" data-toggle="tooltip" title="<?php _e('Delete', ET_DOMAIN) ?>">
						<span class="icon" data-icon="#"></span>
					</a>
				</li>
			<?php } ?>
				<li>
					<a href="#" data-id="{{= ID }}" class="control-quote" data-toggle="tooltip" title="<?php _e('Quote', ET_DOMAIN) ?>">
						<span class="icon" data-icon='"'></span>
					</a>
				</li>
				<li>
					<a href="#" data-id="{{= ID }}" class="control-report" data-toggle="tooltip" title="<?php _e('Report', ET_DOMAIN) ?>">
						<span class="icon" data-icon='!'></span>
					</a>
				</li>
		</ul><!-- END CONTROL THREAD -->
		<div class="f-floatleft single-avatar">
			<a href="{{= author_url }}">
				{{= avatar }}
				{{= user_badge }}
			</a>
		</div>

		<!-- end float left -->
		<div class="f-floatright">
			<div class="post-display">
				<div class="name">
					<a class="post-author" href="{{= author_url }}">{{= reply_author }}</a>
					<span class="comment">
						<a href="#replies_{{= ID }}" class="show-replies <# if(replied && currentUser.ID) { #> active <# } #>">
							<span data-icon="w" class="icon"></span>
							<span class="count">{{= et_replies_count }}</span>
						</a>
					</span>
					<span class="like">
						<a href="#" class="like-post <# if(liked) { #> active <# } #>" data-id="{{= ID }}">
							<span data-icon="k" class="icon"></span>
							<span class="count">{{= et_likes_count }}</span>
						</a>
					</span>
					<span class="date">{{= human_date  }}</span>
				</div>
				<div itemprop="itemListElement" class="content">{{= content_filter }}</div>

				<!-- REPLY CHILD BLOCK -->
				<div id="replies_{{= ID }}" data-id="{{= ID }}" data-page="1" class="reply-children">
					<div class="replies-container">

					</div>
					<a class="btn-more-reply hide" data-id="{{= ID }}">
						<?php _e('Show more replies', ET_DOMAIN) ?>
					</a>
				</div>
				<!-- REPLY CHILD BLOCK -->

				<!-- LIKE BLOCK -->
				<div class="linke-by clearfix">

					<ul class="user-discuss <# if (et_likes.length == 0) { #> collapse <# } #>">
						<li class="text"><?php _e('Liked by', ET_DOMAIN) ?></li>
					</ul>

					<a href="#reply_{{= ID }}" data-id="{{= ID }}" class="btn-reply open-reply">
						<?php _e('Reply', ET_DOMAIN) ?><span class="icon" data-icon="R"></span>
					</a>

				</div>
				<!-- END LIKE BLOCK -->

				<div id="reply_{{= ID }}" class="edit-reply form-reply items-thread clearfix child collapse">
					<div class="f-floatleft single-avatar">
						{{= avatar }}
						{{= user_badge }}
					</div>
					<div class="f-floatright clearfix">
						<form class="ajax-reply" action="" method="post">
							<input type="hidden" name="fe_nonce" value="{{= ajax_insert_reply_nonce }}">
							<input type="hidden" name="post_parent" value="{{= post_parent }}">
							<input type="hidden" name="et_reply_parent" value="{{= ID }}">
							<div id="wp-edit_post_content{{= ID }}-editor-container" class="wp-editor-container">
								<textarea name="post_content" id="reply_content{{= ID }}"></textarea>
							</div>
							<div class="button-event">
								<input class="btn" type="submit" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" value="<?php _e('Reply', ET_DOMAIN) ?>">
								<span data-target="#reply_{{= ID }}" class="btn-cancel"><span class="icon" data-icon="D"></span><?php _e('Cancel', ET_DOMAIN) ?></span>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="post-edit collapse">
				<form class="form-post-edit" action="" method="post">
					<input type="hidden" name="fe_nonce" value="{{= edit_reply_nonce }}">
					<input type="hidden" name="ID" value="{{= ID }}">
					<div class="form-detail">
						<div id="wp-edit_post_content{{= ID }}-editor-container" class="wp-editor-container">
							<textarea name="post_content" id="edit_post_content{{= ID }}">
								{{= content_edit }}
							</textarea>
						</div>
						<div class="row line-bottom">
							<div class="col-md-6 col-sm-6">
								<div class="show-preview">
								</div>
							</div>
							<div class="col-md-6 col-sm-6">
								<div class="button-event">
									<input type="submit" value="<?php _e('Update', ET_DOMAIN) ?>" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" class="btn">
									<a href="#" class="cancel control-edit-cancel"><span class="btn-cancel"><span class="icon" data-icon="D"></span><?php _e('Cancel', ET_DOMAIN) ?></span></a>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div><!-- END REPLY EDIT -->
		</div>
</script>