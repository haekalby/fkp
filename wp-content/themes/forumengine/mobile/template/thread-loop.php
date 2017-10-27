<?php
global $post, $et_sticky_pagename;
	$thread 			= FE_Threads::convert($post);
	$et_updated_date 	= et_the_time(strtotime($thread->et_updated_date));
	if ( !empty($thread->thread_category[0]) )
		$color = FE_ThreadCategory::get_category_color($thread->thread_category[0]->term_id);
	else
		$color = 0;
?>
<article class="fe-post <?php if($thread->post_status == "pending"){?> fe-pending <?php } echo et_is_highlight($thread->ID); ?>" data-id="<?php echo $thread->ID;?>">
	<div class="fe-post-panel">
		<div class="fe-actions fe-actions-2">
			<a href="#" class="fe-act fe-act-approve" data-act="approve" data-id="<?php echo $thread->ID;?>">
				<div class="fe-act-icon-container">
					<span class="fe-act-icon fe-icon fe-sprite fe-icon-approve"></span>
					<span class="fe-act-text"><?php _e('APPROVE', ET_DOMAIN) ?></span>
				</div>
			</a>
			<a href="#" class="fe-act fe-act-reject" data-act="delete" data-id="<?php echo $thread->ID;?>">
				<div class="fe-act-icon-container">
					<span class="fe-act-icon fe-icon fe-sprite fe-icon-reject"></span>
					<span class="fe-act-text"><?php _e('REJECT', ET_DOMAIN) ?></span>
				</div>
			</a>
		</div>
	</div>
	<div class="fe-post-container">
		<a href="" class="fe-post-edit"><span class="fe-sprite fe-icon fe-icon-edit"></span></a>
		<a class="fe-post-avatar" href="<?php the_permalink() ?>">
			<span class="thumb avatar">
				<?php echo  et_get_avatar($post->post_author);?>
				<?php do_action( 'fe_user_badge', $post->post_author ); ?>
			</span>
		</a>
		<div class="fe-post-content">
			<div class="fe-post-title">
				<?php
				if(!is_user_logged_in() && get_option('user_view', false) && get_option('authorize_to_view') && !in_array($thread->thread_category[0]->term_id, get_option('authorize_to_view')))
					echo '<a class="login_to_view" href="#">'.get_the_title().'</a>';
				else
					echo '<a target="_self" href="'.get_permalink().'">'.get_the_title().'</a>';
				?>
			</div>
			<div class="fe-post-info">
				<span class="fe-post-time"><?php printf( __( 'Updated %s', ET_DOMAIN ),$et_updated_date); ?></span>
				<span class="fe-post-cat">
					<?php _e('in',ET_DOMAIN);?>
					<a href="<?php if($thread->thread_category){ echo get_term_link( $thread->thread_category[0]->slug, 'thread_category' );}else{echo '#';} ?>">
						<span class="flags color-<?php echo $color ?>"></span>
						<?php
							if($thread->thread_category){
								echo $thread->thread_category[0]->name;
							} else {
								_e('No category', ET_DOMAIN);
							}
						?>
					</a>.
				</span>
				<!-- <span class="fe-post-author">
					<?php if ( $thread->et_last_author == false ){
							_e( 'No reply yet', ET_DOMAIN );
						} else {
					?>
						<span class="last-reply"><a href="<?php echo et_get_last_page($thread->ID) ?>"><?php _e('Last reply',ET_DOMAIN);?></a></span> by <?php echo '<span class="semibold"><a href="'.get_author_posts_url($thread->et_last_author->ID).'">'. $thread->et_last_author->display_name .'</a></span>.' ?>
					<?php
						}
					?>
				</span> -->
				<span class="comment <?php if($thread->replied) echo 'active';?>">
					<span class="fe-icon fe-icon-comment fe-sprite" data-icon="w"></span><?php echo $thread->et_replies_count ?>
				</span>
				<span class="like <?php if($thread->liked) echo 'active';?>">
					<span class="fe-icon fe-icon-like fe-sprite" data-icon="k"></span><?php echo $thread->et_likes_count ?>
				</span>
				<span class="undo-action">
					<?php printf( __('Want to %s ?', ET_DOMAIN), '<a href="#" class="act-undo">' . __('undo', ET_DOMAIN) . '</a>' ) ?>

				</span>
			</div>
		</div>
	</div>
	<?php if ( (et_is_sticky_thread($post->ID, true) && $et_sticky_pagename == 'home') || (et_is_sticky_thread($post->ID) && $et_sticky_pagename == 'thread_category') ){
		echo '<div class="sticky-bar color-' . $color . '"></div>';
	} ?>
</article>