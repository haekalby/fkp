<?php 
et_get_mobile_header();

get_template_part( 'mobile/template', 'header' );

global $post,$user_ID,$wp_rewrite,$wp_query;
?>
		<div data-role="content" class="fe-content">
			<div class="fe-nav">
				<a href="#fe_category" class="fe-nav-btn fe-btn-cats"><span class="fe-sprite"></span></a>
				<?php if(!$user_ID){?>
				<a href="<?php echo et_get_page_link('login') ?>" class="fe-nav-btn fe-btn-profile"><span class="fe-sprite"></span></a>
				<?php } else {?>
				<a href="<?php echo get_author_posts_url($user_ID) ?>" class="fe-head-avatar toggle-menu"><?php echo  et_get_avatar($user_ID);?></a>
				<?php } ?>
			</div>
			<?php get_template_part( 'mobile/template', 'profile-menu' ) ?>
			<?php if (have_posts()) { the_post(); ?>
			<div class="fe-post-single">
				<div class="fe-post-heading">
					<div class="fe-entry-meta">
						<?php
							$categories = get_the_category();
							$separator = ' ';
							$output = '';
							if($categories){
								foreach($categories as $category) {
									$output .= '<a class="fe-entry-cat" href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s",ET_DOMAIN), $category->name ) ) . '">'.$category->cat_name.'</a>'.$separator;
								}
							echo trim($output, $separator);
							}
						?>				
						<a href="<?php the_permalink() ?>#comments" class="fe-entry-comments icon fe-icon-b" data-icon="q"><?php echo get_comments_number() ?></a>
					</div>
					<a href="<?php the_permalink() ?>"><h2 class="fe-entry-title"><?php the_title(); ?></h2></a>
				</div>
				<div class="fe-post-section fe-single-content" id="posts_container">
					<div class="fe-entry-left">
						<a class="fe-entry-thumbnail" href="<?php echo get_author_posts_url($post->post_author) ?>">
							<?php echo et_get_avatar($post->post_author);?>
						</a>
					</div>
					<div class="fe-entry-right">
						<div class="fe-entry-author">
							<span class="fe-entry-time pull-right" href="#"><?php the_time('M jS Y'); ?></span>
							<?php the_author_posts_link(); ?>
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
				</div>
				<?php 
					add_filter('comments_template' , function(){return dirname(__FILE__).'/comments.php';});
					comments_template();
				?>
			</div>
			<?php } ?>
		</div>		
<?php 
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>
