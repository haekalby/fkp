<?php 
/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>
<div class="fe-post-section fe-comment-section">
	<div class="fe-comment-title"><?php comments_number( __('Be the first to post a comment.',ET_DOMAIN), __('1 Comment on this article',ET_DOMAIN) , __('% Comments on this article',ET_DOMAIN) ); ?></div>
</div>
<div class="fe-post-section fe-comments">
	<ul class="fe-comment-list" id="comments_list">
		<?php 
			$comments = wp_list_comments(array(
				'type' 			=> 'comment',
				'callback' 		=> 'je_comment_template_mobile',
				'avatar_size' 	=> 40,
				//'reply_text'	=> __('Reply <span class="icon" data-icon="R"></span>',ET_DOMAIN), 
			));
			$max_cmt_pages 		= (int)get_comment_pages_count( $comments );
			$current_cmnt_pages = get_query_var( 'cpage' ) ?  get_query_var( 'cpage' ) : 1;
		?>		
	</ul>
	<?php if($current_cmnt_pages < $max_cmt_pages){ ?>
		<a href="#" id="more_comment" class="fe-btn-primary" data-id="<?php the_ID();?>" data-max-page="<?php echo $max_cmt_pages ?>" data-page="<?php echo $current_cmnt_pages ?>" data-theme="d" data-role="button"><?php _e('Load more comments',ET_DOMAIN) ?></a>
	<?php } ?>
</div>
<div class="fe-post-section fe-comment-form">
	<div id="comment_form_wrap" class="fe-reply-form" action="">
		<?php 
		comment_form(array(
			'title_reply' 			=> __('Add a comment', ET_DOMAIN),
			'comment_notes_before' 	=> '',
			'comment_notes_after' 	=> '<p class="submit-form"><button id="submit" class="et-submit-comment">' . __('Submit comment', ET_DOMAIN) . '<span class="icon fe-icon fe-icon fe-icon-edit-w" data-icon="p"></span></button></p>',
		)); 
		?>
	</div>
</div>