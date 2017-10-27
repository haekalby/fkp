<?php
/**
 * Template name: Member
 */
get_header();
global $wp_query, $wp_rewrite, $post,$current_user;

$author = get_user_by( 'slug', get_query_var( 'member' ) );

$edit_link = esc_url(add_query_arg( 'uid', $author->ID , et_get_page_link('edit-profile') ));

$user_location = get_user_meta($author->ID,'user_location',true);
?>

<!--end header Bottom-->

<div class="container main-center">
	<div class="row">
		<div class="col-md-9 marginTop30">
			<div class="infor-profile clearfix">
				<span class="img-profile"><?php echo et_get_avatar($author->ID);?></span>
				<div class="text clearfix">
					<span class="name"><?php echo esc_attr( $author->display_name ); ?></span><br />
					<span class="address">@<?php echo $author->user_login; ?> <?php _e('joined',ET_DOMAIN) ?> <?php echo date_i18n(get_option('date_format'), strtotime($author->user_registered)); if($user_location){?>. <span class="icon location" data-icon="@"></span><?php echo esc_attr( $user_location ); } ?></span>
				</div>
				<div class="button-event">
					<?php if($current_user->ID == $author->ID){ ?>
					<a href="<?php echo et_get_page_link('edit-profile');?>" class="btn" ><?php _e('Edit Profile',ET_DOMAIN) ?></a>
					<?php } else { ?>
					<a href="#" id="open_contact" class="btn" ><?php _e('Contact me',ET_DOMAIN) ?></a>
					<?php } ?>
				</div>
				<div class="intro clearfix">
					<?php echo wpautop( esc_attr( $author->description ) ); ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php
				$user_mobile 	= (get_user_meta($author->ID,'user_mobile',true)) ;
				$user_facebook 	= (get_user_meta($author->ID,'user_facebook',true)) ;
				$user_twitter 	= (get_user_meta($author->ID,'user_twitter',true)) ;
				$user_gplus 	= (get_user_meta($author->ID,'user_gplus',true)) ;
				$user_hide_info = get_user_meta($author->ID,'user_hide_info',true);
				?>
			<div class="side-bar infor-contact-profile" style="display:none">
				<?php if($user_hide_info){ ?>
				<div class="contact-tablet col-3">
					<!-- Hide contact infomation -->
					<h2><?php _e('Contact',ET_DOMAIN) ?></h2>
					<p>
						<a rel="nofollow" target="_blank" href="mailto:<?php echo $author->user_email ?>">
							<span class="icon" data-icon="M"></span>
							<span class="text"><?php echo $author->user_email; ?></span>
						</a>
					</p>
					<p>
						<span class="icon-phone"></span>
						<span class="text"><?php echo esc_attr( $user_mobile ) ?></span>
					</p>
				</div>
				<?php } ?>
				<div class="fb-tablet col-3">
					<h2 class=""><?php _e('Social Links',ET_DOMAIN) ?></h2>
					<p>
						<a rel="nofollow" target="_blank" href="<?php echo $user_facebook ?>">
							<span class="icon-fb" ></span>
							<span class="text"><?php echo esc_attr( $user_facebook ) ?></span>
						</a>
					</p>
					<p>
						<a rel="nofollow" target="_blank" href="<?php echo $user_twitter ?>">
							<span class="icon-tw" ></span>
							<span class="text"><?php echo esc_attr( $user_twitter ) ?></span>
						</a>
					</p>
					<p>
						<a rel="nofollow" target="_blank" href="<?php echo $user_gplus ?>">
							<span class="icon-google" ></span>
							<span class="text"><?php echo esc_attr( $user_gplus ) ?></span>
						</a>
					</p>
				</div>
				<div class="statis-tablet col-3">
					<h2 class=""><?php _e('Statistics',ET_DOMAIN) ?></h2>
					<p>
						<span class="icon" data-icon="w"></span>
						<span class="text"><?php echo et_count_user_posts($author->ID);?></span>
					</p>
					<p>
						<span class="icon" data-icon="q"></span>
						<span class="text"><?php echo et_count_user_posts($author->ID,"reply");?></span>
					</p>
				</div>
			</div>
			<div class="clearfix"></div>
			<ul id="main_list_post" class="list-post list-post-profile">
				<?php
				/**
				 * Display threads
				 */

				$page = get_query_var('paged') ? get_query_var('paged') : 1;
				$thread_query = FE_Threads::get_threads(array(
					'post_type' 	=> 'thread',
					'author' 		=> $author->ID,
					'paged' 		=> $page
				));

				if ($thread_query->have_posts()){
					while ($thread_query->have_posts()){
						$thread_query->the_post();

						get_template_part( 'template/thread', 'loop' );

					} // end while
				} else {
				?>
				<div class="notice-noresult">
					<span class="icon" data-icon="!"></span><?php _e('0 topics created.',ET_DOMAIN) ?>
				</div>
	          	<?php
				} // end if
				?>
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
						'total' 	=> $thread_query->max_num_pages,
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
				$fetch = ($page < $thread_query->max_num_pages) ? 1 : 0 ;
				$check = floor((int) 10 / (int) get_option( 'posts_per_page' ));
			?>
			<div id="loading" class="hide" data-fetch="<?php echo $fetch ?>" data-check="<?php echo $check ?>" data-status="scroll-author" data-author="<?php echo $author->ID; ?>">
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
				<input type="hidden" value="<?php echo $thread_query->max_num_pages ?>" id="max_page">
			</div>
			<!-- Infinite Scroll -->

			<?php } ?>
		</div>
		<div class="col-md-3 hidden-sm hidden-xs">
			<div class="widget side-bar">
				<!-- Hide contact infomation -->
				<?php if($user_hide_info){ ?>
				<h2><?php _e('Contact',ET_DOMAIN) ?></h2>
				<p>
					<a rel="nofollow" target="_blank" href="mailto:<?php echo $author->user_email ?>">
						<span class="icon" data-icon="M"></span>
						<span class="text"><?php echo $author->user_email; ?></span>
					</a>
				</p>
				<?php if($user_mobile) {?>
				<p>
					<span class="icon-phone"></span>
					<span class="text"><?php echo esc_attr( $user_mobile ) ?></span>
				</p>
				<?php } ?>
				<?php } ?>
				<!-- Hide contact infomation -->
				<?php if($user_facebook || $user_twitter || $user_gplus ) { ?>
				<h2 class="padding-top-side"><?php _e('Social Links',ET_DOMAIN) ?></h2>
				<?php if($user_facebook) {?>
				<p>
					<a rel="nofollow" target="_blank" href="<?php echo $user_facebook ?>">
						<span class="icon-fb" ></span>
						<span class="text"><?php echo esc_attr( $user_facebook ) ?></span>
					</a>
				</p>
				<?php } ?>
				<?php if($user_twitter) {?>
				<p>
					<a rel="nofollow" target="_blank" href="<?php echo $user_twitter ?>">
						<span class="icon-tw" ></span>
						<span class="text"><?php echo esc_attr( $user_twitter ) ?></span>
					</a>
				</p>
				<?php } ?>
				<?php if($user_gplus) {?>
				<p>
					<a rel="nofollow" target="_blank" href="<?php echo $user_gplus ?>">
						<span class="icon-google" ></span>
						<span class="text"><?php echo esc_attr( $user_gplus ) ?></span>
					</a>
				</p>
				<?php } ?>
				<?php } ?>
				<h2 class="padding-top-side"><?php _e('Statistics',ET_DOMAIN) ?></h2>
				<p>
					<span class="icon" data-icon="w"></span>
					<span class="text"><?php echo et_count_user_posts($author->ID);?></span>
				</p>
				<p>
					<span class="icon" data-icon="q"></span>
					<span class="text"><?php echo et_count_user_posts($author->ID,"reply");?></span>
				</p>
			</div>
			<?php //get_sidebar() ?>
			<!-- end widget -->
		</div>
	</div>
</div>

<?php get_footer(); ?>

