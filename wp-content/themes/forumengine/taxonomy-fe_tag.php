<?php
get_header();
global $wp_query, $wp_rewrite, $post,$current_user , $user_ID;

$data = et_get_unread_follow();
$term = get_term_by( 'slug' , get_query_var( "term" ), 'fe_tag') ;
?>

<div class="header-bottom header-filter">
	<div class="main-center container">
		<?php fe_navigations(); ?>
	</div>
	<div class="mo-menu-toggle visible-sm visible-xs">
		<a class="icon-menu-tablet" href="#"><?php _e('open',ET_DOMAIN) ?></a>
	</div>
</div>
<!--end header Bottom-->
<div class="container main-center">
	<div class="row">
		<div class="col-md-9 col-sm-12 marginTop30">
			<?php get_template_part('template/post', 'thread'); ?>
			<?php
			do_action( 'forumengine_before_thread_category_list' );
			$sticky_threads = et_get_sticky_threads();

			if ( have_posts() || !empty( $sticky_threads[1] ) ){
			?>
				<ul id="main_list_post" class="list-post">
					<?php

					if ( !empty( $sticky_threads[1] ) ){
						// load sticky thread
						get_template_part( 'template/sticky', 'thread' );
					}

					// reset query
					wp_reset_query();

					/**
					 * Display regular threads
					 */
					while (have_posts()){
						the_post();
						get_template_part( 'template/thread', 'loop' );
					} // end while
					?>
				</ul>
				<?php if ( !empty($sticky_threads[1]) ){ ?>
					<script type="text/javascript">
					var threads_exclude = <?php echo json_encode($sticky_threads[1]) ?>;
					</script>
				<?php
				} //end if
			} else { ?>
				<div class="notice-noresult">
					<span class="icon" data-icon="!"></span><?php _e('No topic has been created yet.', ET_DOMAIN) ?> <a href="#" id="create_first"><?php _e('Create the first one', ET_DOMAIN) ?></a>.
				</div>
				<?php
			} // end if
			?>

			<?php
				$page = get_query_var('paged') ? get_query_var('paged') : 1;
				if(!get_option( 'et_infinite_scroll' )){
			?>
			<!-- Normal Paginations -->
			<div class="pagination pagination-centered" id="main_pagination">
				<?php
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
			<?php
				$fetch = ($page < $wp_query->max_num_pages) ? 1 : 0 ;
				$check = floor((int) 10 / (int) get_option( 'posts_per_page' ));
			?>
			<div id="loading" class="hide" data-fetch="<?php echo $fetch ?>" data-status="scroll-index" data-check="<?php echo $check ?>" data-tag="<?php echo get_query_var('term');?>">
				<div class="bubblingG">
					<span id="bubblingG_1">
					</span>
					<span id="bubblingG_2">
					</span>
					<span id="bubblingG_3">
					</span>
				</div>
				<?php _e( 'Loading more threads', ET_DOMAIN ); ?>
				<input type="hidden" value="<?php echo $page ?>" id="current_page">
				<input type="hidden" value="<?php echo $wp_query->max_num_pages ?>" id="max_page">
			</div>
			<!-- Infinite Scroll -->

			<?php } ?>

			<?php do_action( 'forumengine_after_thread_category_list' ); ?>

		</div>
		<div class="col-md-3 hidden-sm hidden-xs sidebar">
			<?php get_sidebar( ) ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>

