<?php 
et_get_mobile_header();
// header part
get_template_part( 'mobile/template', 'header' );

global $post,$user_ID,$wp_rewrite,$wp_query,$current_user;

et_get_user_following_threads();

$data = et_get_unread_follow();
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
			<div class="fe-tab">
				<ul class="fe-tab-items">
					<li class="fe-tab-item">
						<a href="<?php echo home_url() ?>">
							<span class="fe-tab-name"><?php _e('ALL POSTS',ET_DOMAIN) ?>
							<?php 
								if(!empty($data) && count($data['unread']['data']) > 0){
							?>								
								<span class="count <?php if ( et_get_option("pending_thread") && (et_get_counter('pending') > 0) &&(current_user_can("manage_threads") || current_user_can( 'trash_threads' )) || is_tax( 'thread_category' )) { echo 'mana'; }?>"><?php echo count($data['unread']['data']) ?></span>
							<?php } ?>								
							</span>
						</a>
					</li>
					<li class="fe-tab-item fe-current current">
						<a href="<?php echo et_get_page_link("following") ?>">
							<span class="fe-tab-name"><?php _e('FOLLOWING',ET_DOMAIN) ?>
							<?php if($user_ID && count($data['follow']) > 0){ ?>
								<span class="count <?php if ( et_get_option("pending_thread") && (et_get_counter('pending') > 0) &&(current_user_can("manage_threads") || current_user_can( 'trash_threads' )) || is_tax( 'thread_category' )) { echo 'mana'; }?>"><?php echo count($data['follow']) ;?></span>
							<?php } ?>
							</span>
						</a>
					</li>
					<?php if ( et_get_option("pending_thread") && (et_get_counter('pending') > 0) &&(current_user_can("manage_threads") || current_user_can( 'trash_threads' )) ) {?>
					<li class="fe-tab-item">
						<a href="<?php echo et_get_page_link("pending");?>">
							<span class="fe-tab-name"><?php _e('PENDING',ET_DOMAIN) ?>
								<!-- <span class="count">3</span> -->
							</span>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
			<!-- <div class="fe-new-topic fe-container">
				<div class="fe-topic-form">
					<div class="fe-topic-input">
						<div class="fe-topic-dropbox">
							<select name="" id="">
								<option value="">Please select</option>
								<option value="">Fashion</option>
								<option value="">Tech</option>
								<option value="">Sport</option>
							</select>
						</div>
						<input type="text" name="" id="" placeholder="Touch here">
					</div>
					<div class="fe-topic-content">
						<div class="textarea">
							
						</div>
					</div>
				</div>
			</div> -->
			<div class="fe-post-space"></div>
			<div class="fe-posts" id="posts_container">
				<!-- Loop Thread -->
				<?php 
					$follows = get_user_meta( $user_ID, 'et_following_threads',true);
					$posts_in = ($follows) ? $follows : array(0);
					$args = array(
						'post_type' => 'thread',
						'post_status' => apply_filters('fe_thread_status', array('publish','pending','closed')),
						'paged' => get_query_var('paged'),
						'post__in' => $posts_in
					);

					add_filter( 'posts_join', 'ET_ForumFront::_thread_join' );
					add_filter( 'posts_orderby', 'ET_ForumFront::_thread_orderby');

					$following  = new WP_Query($args);

					if (  $following->have_posts() ){
						while ($following->have_posts()){ $following->the_post(); 
							//load_template( apply_filters( 'et_mobile_template_thread', dirname(__FILE__) . '/mobile-template-thread.php'), false);
							get_template_part( 'mobile/template/thread', 'loop' );
						}
					} 

					remove_filter('posts_join' , 'ET_ForumFront::_thread_join');
					remove_filter('posts_orderby' , 'ET_ForumFront::_thread_orderby');					
				?>
				<!-- Loop Thread -->	
			</div>
			<!-- button load more -->
			<?php 
				wp_reset_query();
				$current_page = (get_query_var('paged')) ? get_query_var('paged') : 1;
				if($current_page < $following->max_num_pages) {
			?>			
			<a href="#" id="more_thread" class="fe-btn-primary"  term="<?php echo get_query_var('term');?>" data-status="follow" data-page="<?php echo $current_page ?>" data-theme="d" data-role="button"><?php _e('Load More Threads',ET_DOMAIN) ?></a>			
			<?php } ?>		
			<!-- button load more -->		
		</div>
<?php 
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>
