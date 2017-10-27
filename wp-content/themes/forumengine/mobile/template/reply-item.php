<?php
	global $post, $thread,$max_file_size;
	$reply = FE_Replies::convert($post);
?>
<article class="fe-th-post" id="reply_<?php echo $reply->ID ?>">
	<div itemscope itemtype="http://schema.org/ItemList">
	<meta itemprop="itemListOrder" content="Descending" />
		<a class="fe-avatar" href="<?php echo get_author_posts_url( $post->post_author ) ?>">
			<?php echo et_get_avatar($post->post_author);?>
			<?php do_action( 'fe_user_badge', $post->post_author ); ?>
		</a>

		<div class="fe-th-container">
			<div class="fe-th-heading">
				<div class="fe-th-info">
					<a href="#" class="show-comment-child" data-id="<?php echo $reply->ID ?>">
						<span class="comment <?php if ( $reply->replied && $user_ID ) echo 'active' ?>">
							<span class="fe-icon fe-icon-comment  fe-sprite" data-icon="w"></span><span class="c-count"><?php echo $reply->et_replies_count ?></span>
						</span>
					</a>
					<a href="#" class="like" data-id="<?php echo $reply->ID ?>">
						<span class="like <?php if ( $reply->liked ) echo 'active' ?>">
							<span class="fe-icon fe-icon-like fe-sprite" data-icon="k"></span><span class="count"><?php echo $reply->et_likes_count ?></span>
						</span>
					</a>
					<span class="time">
						<?php echo et_the_time( strtotime( $reply->post_date ) )  ?>
					</span>
				</div>
				<span class="title"><?php the_author() ?></span>
			</div>
			<div class="fe-th-content" itemprop="itemListElement">
				<?php et_the_content(); ?>
			</div>
			<!-- form edit -->
			<div class="fe-topic-form clearfix">
				<input type="hidden" name="fe_nonce" id="fe_nonce" value="<?php echo wp_create_nonce( 'edit_reply' ) ?>">
				<div class="fe-topic-content" style="display:block;">
					<?php if(get_option('upload_images')){ ?>
					<div class="insert-image-wrap" data-id="<?php echo $reply->ID ?>">
						<div class="form-post" id="<?php echo $reply->ID ?>_images_upload_container">
							<a href="javascript:void(0)" id="<?php echo $reply->ID ?>_images_upload_browse_button"><img src="<?php echo get_template_directory_uri();?>/mobile/img/ico-pic.png" /><?php _e("Insert Image", ET_DOMAIN); ?></a>
							<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'et_upload_images' ); ?>"></span>
							<span id="images_upload_text">
							<?php
							printf(__("Size must be less than < %sMB.", ET_DOMAIN), $max_file_size);
							?>

							</span>
						</div>
					</div>
					<?php } ?>
					<div class="textarea">
						<textarea id="thread_content"><?php echo strip_tags(get_the_content()) ?></textarea>
					</div>
					<div class="fe-form-actions pull-right">
						<a href="#reply_<?php echo $reply->ID; ?>" class="fe-btn update-reply" data-id="<?php echo $reply->ID; ?>" data-role="button"><?php _e('Save',ET_DOMAIN) ?></a>
						<a href="#" class="fe-btn-cancel fe-icon-b fe-icon-b-cancel cancel-modal ui-link"><?php _e('Cancel', ET_DOMAIN) ?></a>
					</div>
				</div>
			</div>
			<!-- form edit -->
			<?php
				$replies_child = FE_Replies::get_replies(array(
					'reply_parent' => $post->ID,
					'order'        => 'ASC',
				));
			?>
			<!-- button more child replies -->
			<a href="#" class="btn-more-reply <?php if(!get_option('et_auto_expand_replies') || $replies_child->max_num_pages <= 1) echo 'hidden'; ?>" data-id="<?php echo $reply->ID ?>"  data-role="button"><?php _e('Show more replies',ET_DOMAIN) ?></a>
			<!-- button more child replies -->
			<div class="fe-th-replies">
				<?php
				if(get_option('et_auto_expand_replies')):
					if($replies_child->have_posts()){
						global $post;
						while ( $replies_child->have_posts() ) {
							$replies_child->the_post();
							$reply_child        = FE_Replies::convert($post);
							$isLike             = $reply_child->liked ? 'active' : '';
							$replies_data[]     = $reply_child;
							$reply_child_author = apply_filters( 'fe_author', get_the_author(), $reply_child->post_author );
						?>
				<div class="fe-reply-item">
					<a href="#" class="fe-avatar">
						<?php echo et_get_avatar($reply_child->post_author) ?>
						<?php do_action( 'fe_user_badge', $reply_child->post_author ); ?>
					</a>
					<div class="fe-th-container">
						<div class="fe-th-heading">
							<div class="fe-th-info">
							<a href="#" class="like <?php echo $isLike ?>" data-id="<?php echo $reply_child->ID ?>">
								<span class="like ">
									<span class="fe-icon fe-icon-like fe-sprite" data-icon="k"></span><span class="count"><?php echo $reply_child->et_likes_count ?></span>
								</span>
							</a>
							</div>
							<span class="title"><?php echo $reply_child_author; ?></span>
						</div>
						<div class="fe-th-content">
							<?php echo apply_filters( 'et_the_content', $reply_child->post_content ) ?>
						</div>
					</div>
				</div>
						<?php
						}
					}
				endif;
				?>
			</div>
			<div class="fe-th-ctrl">
				<div class="fe-th-ctrl-right">
					<?php if(user_can_edit($reply)){ ?>
					<a href="#reply_<?php echo $reply->ID ?>" class="fe-icon fe-icon-edit"></a>
					<?php } ?>
					<?php if($thread->post_status != "closed"){ ?>
					<a href="#reply_<?php echo $reply->ID ?>" class="fe-icon fe-icon-quote"></a>
					<?php } ?>
					<!-- <a href="" class="fe-icon fe-icon-report"></a> -->
				</div>
				<?php if($thread->post_status != "closed"){ ?>
				<div class="fe-th-ctrl-left">
					<a href="#reply_<?php echo $reply->ID ?>" class="fe-reply">
						<?php _e('Reply',ET_DOMAIN) ?> <span class="fe-icon fe-icon-reply"></span>
					</a>
				</div>
				<?php } ?>
			</div>
			<div class="child-reply-box hidden">
				<div class="fe-reply-box expand reply-small">
					<textarea class="reply_child_content"></textarea>
					<div class="fe-reply-actions">
						<a href="#" class="reply-child fe-btn" data-id="<?php echo $reply->ID; ?>" data-role="button"><?php _e('Reply',ET_DOMAIN) ?></a>
						<a href="#" class="fe-btn-cancel fe-icon-b fe-icon-b-cancel cancel-modal ui-link"><?php _e('Cancel', ET_DOMAIN) ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</article>