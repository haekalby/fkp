<?php
get_header();
global $wp_query,$et_query, $wp_rewrite, $post,$current_user , $user_ID;

$data = et_get_unread_follow();
?>

<div class="header-bottom header-filter">
	<div class="main-center">
		<ul class="nav-link">
			<li <?php if(is_home() || is_front_page()){ ?> class="active" <?php }?>>
				<a href="<?php echo home_url() ?>">
					<span class="icon" data-icon="W"></span>
					<span class="text"><?php _e('ALL POSTS',ET_DOMAIN) ?></span>
					<?php
						if(!empty($data) && count($data['unread']['data']) > 0){
					?>
					<span class="number"><?php echo count($data['unread']['data']) ?></span>
					<?php } ?>
				</a>
			</li>
			<li>
				<a
			<?php
				if($user_ID){
					echo 'href="'.et_get_page_link("following").'"';
				} else {
					echo 'id="open_login" data-toggle="modal" href="#modal_login"';
				}
			?>>
				<span class="icon" data-icon="&"></span>
				<span class="text"><?php _e('Following',ET_DOMAIN) ?></span>
				<?php if($user_ID && count($data['follow']) > 0){ ?>
				<span class="number"><?php echo count($data['follow']) ;?></span>
				<?php } ?>
				</a>
			</li>
			<?php if ( et_get_option("pending_thread") && (et_get_counter('pending') > 0) &&(current_user_can("manage_threads") || current_user_can( 'trash_threads' )) ) {?>
			<li>
				<a href="<?php echo et_get_page_link("pending");?>">
					<span class="icon" data-icon="N"></span>
					<span class="text"><?php _e('PENDING POSTS',ET_DOMAIN) ?></span>
				</a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<div class="mo-menu-toggle visible-sm visible-xs">
		<a class="icon-menu-tablet" href="#"><?php _e('open',ET_DOMAIN) ?></a>
	</div>
</div>
<!--end header Bottom-->
<div class="container main-center">
	<div class="row">
		<div class="col-md-9 marginTop30">
			<?php get_template_part('template/post', 'thread'); ?>
			<ul id="main_list_post" class="list-post">
			<?php
			if (  have_posts() ){ ?>

					<?php
					/**
					 * Display regular threads
					 */
					while (have_posts()){
						the_post();
						get_template_part( 'template/thread', 'loop' );
					} // end while
					?>

				<?php
			} else {
				$s = !empty($et_query) ? implode(' ', $et_query['s']) : '';
			?>
				<div class="notice-noresult">
					<span class="icon" data-icon="!"></span><?php echo sprintf( __( "0 results found for %s. Please try again.", ET_DOMAIN ), $s );?>
				</div>
				<?php
			} // end if
			?>
			</ul>

			<?php
				global $et_query;
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
			<div id="loading" class="hide" data-fetch="<?php echo $fetch ?>" data-status="scroll-search" data-s="<?php echo implode(' ', $et_query['s']); ?>" data-check="<?php echo $check ?>" <?php if(isset($_GET['tax_cat'])){ echo 'data-term="'.$_GET['tax_cat'].'"'; } ?>>
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
		</div>
		<div class="col-md-3 hidden-sm hidden-xs">
			<?php get_sidebar( ) ?>
			<!-- end widget -->
		</div>
	</div>
</div>

<?php get_footer(); ?>

