<article id="post-<?php the_ID(); ?>" <?php post_class('fe-entry'); ?>>
	<div class="fe-entry-left">
		<a class="fe-entry-thumbnail" href="<?php echo get_author_posts_url($post->post_author) ?>">
			<?php echo  et_get_avatar($post->post_author);?>
		</a>
	</div>
	<div class="fe-entry-right">
		<div class="fe-entry-head">
			<div class="fe-entry-meta">
				<?php
					$categories = get_the_category();
					$separator = ' ';
					$output = '';
					if($categories){
						foreach($categories as $category) {
							$output .= '<a class="fe-entry-cat" href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s",ET_DOMAIN ), $category->name ) ) . '">'.$category->cat_name.'</a>'.$separator;
						}
					echo trim($output, $separator);
					}
				?>				
				<a href="<?php the_permalink() ?>#comments" class="fe-entry-comments icon fe-icon-b" data-icon="q"><?php echo get_comments_number() ?></a>
			</div>
			<a href="<?php the_permalink() ?>"><h2 class="fe-entry-title"><?php the_title(); ?></h2></a>
		</div>
		<div class="fe-entry-content">
			<?php if(!is_single()) {?>

			<?php the_excerpt(); //the_content( __('Read more', ET_DOMAIN) . '&nbsp;&nbsp;<span class="icon" data-icon="]"></span>' ) ?>
			<a class="more-link" href="<?php the_permalink(); ?>"><?php _e('Read more', ET_DOMAIN) ?><span class="icon fe-icon fe-icon-more" data-icon="]"></span></a>

			<?php } else {?>
			
			<?php the_content();?>

			<?php }?>
		</div>
	</div>
	<div class="clearfix"></div>
</article><!-- #post -->