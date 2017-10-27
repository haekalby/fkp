<?php 
et_get_mobile_header();
get_template_part( 'mobile/template', 'header' );
global $user_ID,$wp_query;

$author = get_user_by( 'id', get_query_var( 'author' ) );
$user_location = get_user_meta($author->ID,'user_location',true);
$user_mobile = (get_user_meta($author->ID,'user_mobile',true)) ;
$user_facebook = (get_user_meta($author->ID,'user_facebook',true)) ;
$user_twitter = (get_user_meta($author->ID,'user_twitter',true)) ;
$user_gplus = (get_user_meta($author->ID,'user_gplus',true)) ;
$user_hide_info = get_user_meta($author->ID,'user_hide_info',true);
?>
<div data-role="content" class="fe-content fe-content-auth">
	<div class="fe-page-heading">
		<div class="fe-avatar">
			<a href="#" class="ui-link toggle-menu">
				<?php echo  et_get_avatar($user_ID);?>
			</a>
		</div>
		<?php //if ($user_ID != $author->ID) {?>
		<!-- <ul class="fe-thread-actions fe-author-actions nav nav-tabs">
			<li>
				<a class="ui-link" rel="external" href="#more_info" data-toggle="tab"><span class="fe-icon fe-icon-doc"></span> <?php _e('Show info', ET_DOMAIN) ?></a>
			</li>
			<li class="active">
				<a class="fe-act-w ui-link"  rel="external" data-toggle="tab" href="#basic_info"><span class="fe-icon fe-icon-doc-w"></span> <?php _e('Hide info', ET_DOMAIN) ?></a>
			</li>
		</ul> -->
		<?php //} else {?>
		<ul class="fe-thread-actions fe-author-actions nav nav-tabs">
			<li>
				<a class="fe-act ui-link" href="<?php echo et_get_page_link('edit-profile') ?>"><span class="fe-icon fe-icon-doc"></span> <?php _e('Edit Profile', ET_DOMAIN) ?></a>
			</li>
			<li>
				<a class="ui-link" rel="external" href="#more_info" data-toggle="tab"><span class="fe-icon fe-icon-doc"></span> <?php _e('Show info', ET_DOMAIN) ?></a>
			</li>
			<li class="active">
				<a class="fe-act-w ui-link"  rel="external" data-toggle="tab" href="#basic_info"><span class="fe-icon fe-icon-doc-w"></span> <?php _e('Hide info', ET_DOMAIN) ?></a>
			</li>
		</ul>
		<?php //} ?>
	</div>
	<?php get_template_part( 'mobile/template', 'profile-menu' ) ?>
	<div class="tab-content">
		<div id="more_info" class="tab-pane fe-author-detail">
			<?php if ( $user_hide_info ) { ?>
			<div class="fe-info fe-container fe-info-contact">
				<div class="fe-info-title">
					<?php _e('CONTACT',ET_DOMAIN) ?>
				</div>
				<ul class="fe-info-items">
					<li class="fe-info-item fe-icon-b fe-icon-b-mail fe-info-mail"><?php echo $author->user_email; ?></li>
					<li class="fe-info-item fe-icon-b fe-icon-b-phone fe-info-phone"><?php echo $user_mobile ?></li>
				</ul>
			</div>
			<?php } ?>
			<div class="fe-info fe-container fe-info-contact">
				<div class="fe-info-title">
					<?php _e('SOCIAL',ET_DOMAIN) ?>
				</div>
				<ul class="fe-info-items">
					<li class="fe-info-item fe-icon-b fe-icon-b-fb fe-info-fb"><?php echo $user_facebook ?></li>
					<li class="fe-info-item fe-icon-b fe-icon-b-twitter fe-info-twitter"><?php echo $user_twitter ?></li>
					<li class="fe-info-item fe-icon-b fe-icon-b-google fe-info-google"><?php echo $user_gplus ?></li>
				</ul>
			</div>
			<div class="fe-info fe-container fe-info-contact">
				<div class="fe-info-title">
					<?php _e('STATISTICS',ET_DOMAIN) ?>
				</div>
				<ul class="fe-info-items">
					<li class="fe-info-item fe-icon-b fe-icon-b-thread fe-info-thread"><?php echo et_count_user_posts($author->ID);?></li>
					<li class="fe-info-item fe-icon-b fe-icon-b-comment fe-info-comment"><?php echo et_count_user_posts($author->ID,"reply");?></li>
				</ul>
			</div>
		</div>
		<div id="basic_info" class="fe-container-author tab-pane active">
			<div class="fe-container fe-author-info">
				<div class="fe-info-top">
					<div class="fe-avatar">
						<a href="<?php echo get_author_posts_url($author->ID) ?>" class="ui-link">
							<?php echo  et_get_avatar($author->ID);?>
						</a>
					</div>
					<div class="fe-info-right">
						<h2><?php echo $author->display_name; ?></h2>
						<p class="info">@<?php echo $author->user_login; ?> <?php _e('joined',ET_DOMAIN) ?> <?php echo date("dS, M, Y", strtotime($author->user_registered));?></p>
						<?php //if($user_location){ ?>
						<p class="info"><span class="fe-icon fe-icon-loc"></span><?php echo empty($user_location) ? __('NA', ET_DOMAIN) : $user_location; ?></p>
						<?php //} ?>
					</div>
				</div>
				<div class="fe-info-bottom">
					<?php echo wpautop( $author->description); ?>
				</div>
			</div>
			<div class="fe-posts fe-author-list">
				<div class="fe-posts" id="posts_container">
					<!-- Loop Thread -->
					<?php 
						if (have_posts()){
							while (have_posts()){
								the_post();
								load_template( apply_filters( 'et_mobile_template_thread', dirname(__FILE__) . '/mobile-template-thread.php'), false); 
							} //end while
						}//end if
					?>			
					<!-- Loop Thread -->						
				</div>
				<!-- button load more -->
				<?php 
					wp_reset_query();
					$current_page = (get_query_var('paged')) ? get_query_var('paged') : 1;
					if($current_page < $wp_query->max_num_pages) {
				?>			
				<a href="#" id="more_thread_author" class="fe-btn-primary"  term="<?php echo get_query_var('term');?>" data-author="<?php echo $author->ID ?>" data-status="author" data-page="<?php echo $current_page ?>" data-theme="d" data-role="button"><?php _e('Load More Threads',ET_DOMAIN) ?></a>			
				<?php } ?>		
				<!-- button load more -->				
			</div>
		</div>
	</div>
</div>
<?php 
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer(); 
?>