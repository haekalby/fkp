<?php 
et_get_mobile_header();

get_template_part( 'mobile/template', 'header' );

global $post,$user_ID,$wp_rewrite,$wp_query,$current_user;

$data = et_get_unread_follow();
?>
		<div data-role="content" class="fe-content">
			<div class="fe-nav">
				<a href="#fe_category" class="fe-nav-btn fe-btn-cats"><span class="fe-sprite"></span></a>
				<?php if(!$user_ID){?>
				<a href="<?php echo et_get_page_link('login') ?>" class="fe-nav-btn fe-btn-profile"><span class="fe-sprite"></span></a>
				<?php } else {?>
				<a href="<?php echo get_author_posts_url($user_ID) ?>" class="fe-head-avatar"><?php echo  et_get_avatar($user_ID);?></a>
				<?php } ?>
			</div>
			<div class="fe-tab">
				<ul class="fe-tab-items">
					<li class="fe-tab-item fe-tab-item-3">
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
						<a href="<?php echo et_get_page_link("following") ?>">
							<span class="fe-tab-name"><?php _e('FOLLOWING',ET_DOMAIN) ?>
							<?php if($user_ID && count($data['follow']) > 0){ ?>
								<span class="count"><?php echo count($data['follow']) ;?></span>
							<?php } ?>
							</span>
						</a>
					</li>
					<?php if ( et_get_option("pending_thread") && (et_get_counter('pending') > 0) &&(current_user_can("manage_threads") || current_user_can( 'trash_threads' )) ) {?>
					<li class="fe-tab-item fe-tab-item-3">
						<a href="<?php echo et_get_page_link("pending");?>">
							<span class="fe-tab-name"><?php _e('PENDING',ET_DOMAIN) ?>
								<!-- <span class="count">3</span> -->
							</span>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
			<div class="fe-404">
				<img src="<?php echo TEMPLATEURL ?>/mobile/img/404-img.png">
				<h1>PAGE NOT FOUND</h1>
				<p>Look like something wrong here. The page you were looking for is not here.</p>
				<a class="fe-btn-primary fe-back-home" href="<?php echo home_url() ?>">Back to Home</a>
				<a class="fe-previous" onclick="history.back();return false;" href="#">Previous Page</a>
			</div>
		</div>
<?php 
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>
