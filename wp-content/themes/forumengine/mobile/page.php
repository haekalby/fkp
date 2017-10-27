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
					<a href="#"><h2 class="fe-entry-title"><?php the_title(); ?></h2></a>
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
							<?php the_content();?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<?php } ?>
		</div>		
<?php 
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>
