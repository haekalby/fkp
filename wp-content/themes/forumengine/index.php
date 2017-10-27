<?php
get_header();
?>
<!--end header Bottom-->
<div class="container main-center">
	<div class="row">
		<div class="col-md-9 col-sm-12 marginTop30 blog-listing" id="main_list_post">
			<?php
			if (  have_posts() ){ ?>
				<?php
				/**
				 * Display regular threads
				 */
				while (have_posts()){
					the_post();
					get_template_part( 'content' );
				} // end while
				?>
				<?php
			} else { ?>
				<div class="notice-noresult">
					<span class="icon" data-icon="!"></span><?php _e('No post has been created yet.', ET_DOMAIN) ?> <a href="#" id="create_first"><?php _e('Create the first one', ET_DOMAIN) ?></a>.
				</div>
				<?php
			} // end if
			?>

			<?php if(!get_option( 'et_infinite_scroll' )){ ?>

			<!-- Normal Paginations -->
			<div class="pagination pagination-centered" id="main_pagination">
				<?php
					$page = get_query_var('paged') ? get_query_var('paged') : 1;
					echo paginate_links( array(
						'base' 		=> str_replace('99999', '%#%', esc_url(get_pagenum_link( 99999 ))),
						'format' 	=> $wp_rewrite->using_permalinks() ? 'page/%#%' : '?paged=%#%',
						'current' 	=> max(1, $page),
						'total' 	=> $wp_query->max_num_pages,
						'prev_text' => '<',
						'next_text' => '>',
						'type' 		=> 'list'
					) );
				?>
			</div>
			<!-- Normal Paginations -->

			<?php } else { ?>

			<!-- Infinite Scroll -->
			<?php $fetch = ($page < $wp_query->max_num_pages) ? 1 : 0 ; ?>
			<div id="post_loading" class="hide" data-fetch="<?php echo $fetch ?>" data-status="scroll-blog" data-cat="<?php echo get_query_var('cat' );?>">
				<!-- <img src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader.gif"> -->
				<div class="bubblingG">
					<span id="bubblingG_1">
					</span>
					<span id="bubblingG_2">
					</span>
					<span id="bubblingG_3">
					</span>
				</div>
				<?php _e( 'Loading more posts', ET_DOMAIN ); ?>
				<input type="hidden" value="<?php echo $page ?>" id="current_page">
				<input type="hidden" value="<?php echo $wp_query->max_num_pages ?>" id="max_page">
			</div>
			<!-- Infinite Scroll -->

			<?php } ?>
		</div>
		<div class="col-md-3 hidden-sm hidden-xs sidebar">
			<?php get_sidebar( 'blog' ) ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>

