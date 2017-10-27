<?php
et_get_mobile_header();

get_template_part( 'mobile/template', 'header' );

global $post,$user_ID,$wp_rewrite,$wp_query,$current_user,$max_file_size;
$term = get_term_by( 'slug' , get_query_var( "term" ), 'thread_category') ;
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
					<li class="fe-tab-item fe-tab-item-3 <?php //if (!is_tax( 'thread_category' ) || current_user_can( 'manage_threads' )) echo 'fe-current current'; ?>">
						<a href="<?php echo home_url() ?>">
							<span class="fe-tab-name"><?php _e('ALL POSTS',ET_DOMAIN) ?>
							<?php
								if(!empty($data) && count($data['unread']['data']) > 0){
							?>
								<span class="count"><?php echo count($data['unread']['data']) ?></span>
							<?php } ?>
							</span>
						</a>
					</li>
					<li class="fe-tab-item fe-tab-item-3">
						<?php if($user_ID){?>
						<a href="<?php echo et_get_page_link("following") ?>">
						<?php } else { ?>
						<a href="<?php echo et_get_page_link("login") ?>">
						<?php } ?>
							<span class="fe-tab-name"><?php _e('FOLLOWING',ET_DOMAIN) ?>
							<?php if($user_ID && count($data['follow']) > 0){ ?>
								<span class="count"><?php echo count($data['follow']) ;?></span>
							<?php } ?>
							</span>
						</a>
					</li>
					<?php if ( et_get_option("pending_thread") && (et_get_counter('pending') > 0) &&(current_user_can("manage_threads") || current_user_can( 'trash_threads' )) ) {?>
					<li class="fe-tab-item fe-tab-item-3 fe-tab-3">
						<a href="<?php echo et_get_page_link("pending");?>">
							<span class="fe-tab-name"><?php _e('PENDING',ET_DOMAIN) ?>
								<!-- <span class="count">3</span> -->
							</span>
						</a>
					</li>
					<?php } else if ( is_tax( 'thread_category' ) ){ ?>
						<li class="fe-tab-item fe-tab-item-3 fe-tab-3 current fe-current">
							<a href="#">
								<span class="fe-tab-name"><?php single_term_title( ) ?>
									<!-- <span class="count">3</span> -->
								</span>
							</a>
						</li>
					<?php } ?>
				</ul>
			</div>
			<div class="fe-new-topic fe-container">
				<div class="fe-topic-form">
					<div class="fe-topic-input">
						<div class="fe-topic-dropbox">
							<select name="thread_category" id="thread_category">
								<option value="">Please select</option>
								<?php
									$categories = FE_ThreadCategory::get_categories();
									et_the_cat_select($categories,$term->term_id);
								?>
							</select>
						</div>
						<input type="text" maxlength="90" name="thread_title" id="thread_title" placeholder="<?php _e('Touch here to start a new topic',ET_DOMAIN) ?>">
					</div>
					<div class="fe-topic-content">
						<?php if(get_option('upload_images')){ ?>
						<div class="insert-image-wrap">
							<div class="form-post" id="images_upload_container">
								<a href="javascript:void(0)" id="images_upload_browse_button"><img src="<?php echo get_template_directory_uri();?>/mobile/img/ico-pic.png" /><?php _e("Insert Image", ET_DOMAIN); ?></a>
								<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'et_upload_images' ); ?>"></span>
								<span id="images_upload_text"><?php printf(__("Size must be less than < %sMB.", ET_DOMAIN),$max_file_size); ?></span>
							</div>
						</div>
						<?php } ?>
						<div class="textarea">
							<textarea id="thread_content" class=""></textarea>
							<?php // wp_editor( '', 'thread_content' , editor_settings()) ?>
						</div>
						<div class="fe-submit">
							<a href="#" class="fe-btn-primary" id="create_thread" data-role="button"><?php _e('Create',ET_DOMAIN) ?></a>
							<a href="#" class="fe-btn-cancel fe-icon-b fe-icon-b-cancel cancel-modal ui-link"><?php _e('Cancel',ET_DOMAIN) ?></a>
						</div>
					</div>
				</div>
			</div>
			<div class="fe-posts" id="posts_container">
				<!-- Sticky threads -->

				<?php
				$sticky_threads = et_get_sticky_threads();
				if ( !empty( $sticky_threads[1] ) ){
					//load_template( apply_filters('et_mobile_template_sticky', dirname(__FILE__) . '/mobile-template-sticky.php' ), false );
					get_template_part( 'mobile/template/thread', 'sticky' );
				}
				?>

				<!-- Loop Thread -->
				<?php
					wp_reset_query();
					$page = get_query_var('paged') ? get_query_var('paged') : 1;
					if (  have_posts() ){
						while (have_posts()){ the_post();
							//load_template( apply_filters( 'et_mobile_template_thread', dirname(__FILE__) . '/mobile-template-thread.php'), false);
							get_template_part( 'mobile/template/thread', 'loop' );
						}
					}
				?>
				<!-- Loop Thread -->
				<?php if ( !empty($sticky_threads[1]) ){ ?>
					<script type="text/javascript">
					var threads_exclude = <?php echo json_encode($sticky_threads[1]) ?>;
					</script>
				<?php } ?>
			</div>
			<!-- button load more -->
			<?php
				wp_reset_query();
				if($page < $wp_query->max_num_pages) {
			?>
			<a href="#" id="more_thread" class="fe-btn-primary" data-term="<?php echo get_query_var('term');?>" data-status="index" data-page="<?php echo $page ?>" data-theme="d" data-role="button"><?php _e('Load more threads',ET_DOMAIN) ?></a>
			<?php } ?>
			<!-- button load more -->
		</div>
<?php
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>
