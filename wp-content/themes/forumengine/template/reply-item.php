<?php
	global $post,$thread,$current_user,$et_repliesData;
	$reply 				= FE_Replies::convert($post);
	$reply_author 		= apply_filters( 'fe_author', get_the_author(), $reply->post_author );
?>
	<div class="items-thread reply-item clearfix" id="post_<?php echo $reply->ID ?>" data-id="<?php echo $reply->ID ?>">						
		<ul class="control-thread">

			<?php 
				if (user_can_edit($reply)){ 
			?>
				<li><a href="#" class="edit-topic-thread control-edit" data-toggle="tooltip" title="<?php _e('Edit', ET_DOMAIN) ?>"><span class="icon" data-icon="p"></span></a></li>
			<?php } ?>

			<?php if (current_user_can('manage_threads')){ ?>								
				<li><a href="#" class="delete-topic-thread control-delete" data-toggle="tooltip" title="<?php _e('Delete', ET_DOMAIN) ?>"><span class="icon" data-icon="#"></span></a></li>
			<?php } ?>
				<li><a href="#" data-id="<?php echo $reply->ID ?>" class="control-quote" data-toggle="tooltip" title="<?php _e('Quote', ET_DOMAIN) ?>"><span class="icon" data-icon='"'></span></a></li>
				<!-- <li><a href="#" class="control-report" data-toggle="tooltip" title="<?php _e('Report', ET_DOMAIN) ?>"><span class="icon" data-icon="^"></span></a></li> -->
			<?php if(!$reply->reported){?>
				<li><a href="#" class="control-report" data-toggle="tooltip" title="<?php _e('Report', ET_DOMAIN) ?>"><span class="icon" data-icon='!'></span></a></li>
			<?php }?>
		</ul>						
		<div class="f-floatleft single-avatar">           
			<a href="<?php echo get_author_posts_url( $reply->post_author ) ?>">
				<?php echo et_get_avatar($reply->post_author);?>
				<?php do_action( 'fe_user_badge', $reply->post_author ); ?>
			</a>
		</div>
		<!-- end float left -->
		<div class="f-floatright">
			<div class="post-display">
				<div class="name">
					<a class="post-author" href="<?php echo get_author_posts_url( $reply->post_author ) ?>"><?php echo $reply_author; //the_author() ?></a>
					<span class="comment">
						<a href="#replies_<?php echo $reply->ID ?>" class="show-replies <?php if ( $reply->replied && $user_ID ) echo 'active' ?>">
							<span data-icon="w" class="icon"></span>
							<span class="count"><?php echo $reply->et_replies_count ?></span>
						</a>
					</span>
					<span class="like">
						<a href="#" class="like-post <?php if ( $reply->liked ) echo 'active' ?>" data-id="<?php echo $reply->ID ?>">
							<span data-icon="k" class="icon"></span>
							<span class="count"><?php echo $reply->et_likes_count ?></span>
						</a>
					</span>
					<span class="date"><?php echo et_the_time( strtotime( $reply->post_date ) )  ?></span>     
				</div>
				<div  itemprop="itemListElement" class="content"><?php et_the_content(); ?></div>
				<div id="replies_<?php echo $reply->ID ?>" data-id="<?php echo $reply->ID ?>" data-page="1" class="reply-children">
					<?php
						$replies_child = FE_Replies::get_replies(array(
							'reply_parent' => $reply->ID,
							'order' => 'ASC',
							)) ;
					?>
					<div class="replies-container">
						<?php 
						if(get_option('et_auto_expand_replies')):
							if($replies_child->have_posts()){
								global $post;
								while ( $replies_child->have_posts() ) {
									$replies_child->the_post();
									$reply_child = FE_Replies::convert($post);
									$isLike = $reply_child->liked ? 'active' : '';
									$et_repliesData[] 	= $reply_child;
									$reply_child_author = apply_filters( 'fe_author', get_the_author(), $reply_child->post_author );
								?>
									<div class="items-thread reply-item clearfix child" data-parent="<?php echo $reply->ID ?>" id="post_<?php echo $reply_child->ID ?>" data-parent="<?php echo $reply->ID ?>" data-id="<?php echo $reply_child->ID ?>">
										<ul class="control-thread">
											<?php if (user_can_edit($reply_child)){ ?>
												<li><a href="#" class="edit-topic-thread child control-edit" data-toggle="tooltip" title="<?php _e('Edit', ET_DOMAIN) ?>"><span class="icon" data-icon="p"></span></a></li>
											<?php } ?>

											<?php if (current_user_can('manage_threads')){ ?>
												<li><a href="#" class="delete-topic-thread control-delete" data-toggle="tooltip" title="<?php _e('Delete', ET_DOMAIN) ?>"><span class="icon" data-icon="#"></span></a></li>
											<?php } ?>

												<li><a href="#" data-id="<?php echo $reply->ID ?>" class="control-quote child" data-toggle="tooltip" title="<?php _e('Quote', ET_DOMAIN) ?>"><span class="icon" data-icon='"'></span></a></li>
											<?php if(!$reply->reported)	{?>
												<li><a href="#" class="control-report" data-toggle="tooltip" title="<?php _e('Report', ET_DOMAIN) ?>"><span class="icon" data-icon='!'></span></a></li>
											<?php } ?>
										</ul>														
										<div class="f-floatleft single-avatar avatar-child">
											<?php echo et_get_avatar($reply_child->post_author) ?>
											<?php do_action( 'fe_user_badge', $reply_child->post_author ); ?>
										</div>
										<div class="f-floatright clearfix">
											<div class="post-display">
												<div class="name">
													<a class="post-author" href="<?php echo get_author_posts_url( $reply_child->post_author ) ?>"><?php echo $reply_child_author; //the_author(); ?></a>
													<span class="like">
														<a href="#" class="like-post <?php echo $isLike ?>" data-id="<?php echo $reply_child->ID ?>">
															<span data-icon="k" class="icon"></span>
															<span class="count"><?php echo $reply_child->et_likes_count ?></span>
														</a>
													</span>
													<span class="date"><?php echo et_the_time( strtotime( $reply_child->post_date ) )  ?></span>
												</div>
												<div class="content">
													<?php echo apply_filters( 'et_the_content', $reply_child->post_content ) ?>
												</div>
											</div><!-- end post display child -->
											<!-- Form Edit Reply Child -->
											<div class="post-edit collapse">
												<form class="form-post-edit child" action="" method="post">
													<input type="hidden" name="fe_nonce" value="<?php echo wp_create_nonce( 'edit_reply' ) ?>">
													<input type="hidden" name="ID" value="<?php echo $reply_child->ID ?>">
													<div class="form-detail">
														<div id="wp-<?php echo 'edit_post_content' . $reply_child->ID ?>-editor-container" class="wp-editor-container">
															<textarea name="post_content" id="<?php echo 'edit_post_content' . $reply_child->ID?>"><?php echo nl2br($reply_child->post_content) ?></textarea>
														</div>
														<div class="row line-bottom">
															<div class="col-md-6 col-sm-6">
																<div class="show-preview"></div>
															</div>
															<div class="col-md-6 col-sm-6">
																<div class="button-event">
																	<input type="submit" value="<?php _e('Update', ET_DOMAIN) ?>" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" class="btn">
																	<a href="#" class="cancel child control-edit-cancel"><span class="btn-cancel"><span class="icon" data-icon="D"></span><?php _e('Cancel', ET_DOMAIN) ?></span></a>
																</div>
															</div>
														</div>
													</div>
												</form>
											</div>
											<!-- Form Edit Reply Child -->			
										</div>
									</div>												
								<?php
								}// end while replies child
							}// end if replies child
						endif;
						?>
					</div>
					<a class="btn-more-reply <?php if(!get_option('et_auto_expand_replies') || $replies_child->max_num_pages <= 1) echo 'hide'; ?>" data-id="<?php echo $reply->ID ?>">
						<?php _e('Show more replies', ET_DOMAIN) ?>
					</a>
				</div>
				<!-- end items threads child -->
				<div class="linke-by clearfix <?php if($thread->post_status == "closed" && count($reply->et_likes) == 0 ){?>collapse <?php } ?>">
					<ul class="user-discuss <?php echo count($reply->et_likes) > 0 ? '' : 'collapse' ?>">
						<li class="text"><?php _e('Liked by', ET_DOMAIN) ?></li>
						<?php 
						$count = 0;
						foreach ($reply->et_likes as $user_id) { 
							if ($count < 5) {
							$avatar = et_get_avatar($user_id);
							$user 	= FE_Member::get($user_id);
							$name 	= $user->display_name;
						?>
							<li <?php if ( $user_id == $current_user->ID ) echo 'class="me"' ?>>
								<a href="<?php echo get_author_posts_url( $user_id ) ?>" data-toggle="tooltip" title="<?php echo $name ?>"><?php echo $avatar ?></a>
							</li>
						<?php 
							}
						$count++;										
						} 
						?>
						<?php 
						if ( $count > 5 ) 
							echo '<li class="img-circle more-img">' . ($count - 5) . '</li>'
						?>   
					</ul>
					<?php if($thread->post_status != "closed") { ?>
					<a href="#reply_<?php echo $reply->ID ?>" data-id="<?php echo $reply->ID ?>" class="btn-reply open-reply"><?php _e('Reply', ET_DOMAIN) ?><span class="icon" data-icon="R"></span></a>
					<?php } ?>
				</div>
				<div id="reply_<?php echo $reply->ID ?>" class="edit-reply form-reply items-thread clearfix child collapse">
					<div class="f-floatleft single-avatar">
						<?php echo et_get_avatar($current_user->ID, 40); ?>  
						<?php do_action( 'fe_user_badge', $current_user->ID ); ?>
					</div>
					<div class="f-floatright clearfix">
						<form class="ajax-reply" action="" method="post">
							<input type="hidden" name="fe_nonce" value="<?php echo wp_create_nonce( 'ajax_insert_reply' ) ?>">
							<input type="hidden" name="post_parent" value="<?php echo $thread_id ?>">
							<input type="hidden" name="et_reply_parent" value="<?php echo $reply->ID ?>">
							<div id="wp-<?php echo 'edit_post_content' . $reply->ID ?>-editor-container" class="wp-editor-container">
								<textarea name="post_content" id="<?php echo 'reply_content' . $reply->ID ?>"></textarea>
							</div>
							<div class="button-event">
								<input class="btn" type="submit" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" value="<?php _e('Reply', ET_DOMAIN) ?>">
								<span data-target="#reply_<?php echo $reply->ID ?>" class="btn-cancel"><span class="icon" data-icon="D"></span><?php _e('Cancel', ET_DOMAIN) ?></span>
							</div>
						</form>
					</div> 
				</div>
			</div>
			<div class="post-edit collapse">
				<form class="form-post-edit" action="" method="post">
					<input type="hidden" name="fe_nonce" value="<?php echo wp_create_nonce( 'edit_reply' ) ?>">
					<input type="hidden" name="ID" value="<?php echo $reply->ID ?>">
					<div class="form-detail">
						<div id="wp-<?php echo 'edit_post_content' . $reply->ID ?>-editor-container" class="wp-editor-container">
							<textarea name="post_content" id="<?php echo 'edit_post_content' . $reply->ID?>"><?php echo nl2br($reply->post_content) ?></textarea>
						</div>
						<div class="row line-bottom">
							<div class="col-md-6 col-sm-6">
								<div class="show-preview">
									<!--<div class="skin-checkbox">
										<span class="icon" data-icon="3"></span>
										<input type="checkbox" class="checkbox-show" id="show_topic_item" style="display:none" />
									</div>
									<a href="#"><?php _e('Show preview', ET_DOMAIN) ?></a>-->
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
			</div>    
		</div> <!-- end float right -->
	</div>