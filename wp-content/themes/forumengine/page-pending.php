<?php
/**
 * Template Name: Pending Threads Page
 */
get_header();
global $wp_query, $wp_rewrite, $post,$user_ID;

$data = et_get_unread_follow();

?>

<div class="header-bottom header-filter">
	<div class="main-center">
		<?php
			fe_navigations();
		?>
	</div>
	<div class="mo-menu-toggle visible-sm visible-xs">
		<a class="icon-menu-tablet" href="#"><?php _e('open',ET_DOMAIN) ?></a>
	</div>
</div>
<!--end header Bottom-->
<div class="container main-center">
	<div class="row">
		<div class="col-md-9 marginTop30">
			<ul class="list-post" id="main_list_post">
			<?php
			/**
			 * Display threads
			 */
			$args = array(
				'post_type' 	=> 'thread',
				'post_status' 	=> 'pending',
				'paged' 		=> get_query_var('paged'),
			);

			$pending  = FE_Threads::get_threads($args);

			if ($pending->have_posts()){
				while ($pending->have_posts()){
					$pending->the_post();
					get_template_part( 'template/thread', 'loop' );
				} // end while
			} else { ?>
					<div class="notice-noresult">
						<span class="icon" data-icon="!"></span><?php _e('No pending thread found.', ET_DOMAIN) ?>
					</div>
			<?php } // end if ?>
			</ul>
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
						'total' 	=> $pending->max_num_pages,
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
				$fetch = ($page < $pending->max_num_pages) ? 1 : 0 ;
				$check = floor((int) 10 / (int) get_option( 'posts_per_page' ));
			?>
			<div id="loading" class="hide" data-fetch="<?php echo $fetch ?>"  data-check="<?php echo $check ?>" data-status="scroll-pending">
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
				<input type="hidden" value="<?php echo $pending->max_num_pages ?>" id="max_page">
			</div>
			<!-- Infinite Scroll -->

			<?php } ?>
		</div>
		<div class="col-md-3 hidden-sm hidden-xs">
			<?php get_sidebar( ) ?>
			<!-- end widget -->
		</div>
	</div>
</div>

<?php get_footer(); ?>

