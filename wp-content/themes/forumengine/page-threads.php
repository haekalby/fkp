<?php
/**
 * Template Name: Threads Index
 */
get_header();
global $wp_query, $wp_rewrite, $post,$current_user , $user_ID;

$data = et_get_unread_follow();
$term = get_term_by( 'slug' , get_query_var( "term" ), 'thread_category') ;
?>

<div class="header-bottom header-filter">
	<div class="main-center container">
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
		<div class="col-md-9 col-sm-12 marginTop30">
			<div id="form_thread" class="thread-form auto-form new-thread">
				<form action="" method="post">
					<input type="hidden" name="fe_nonce" class="fe_nonce" value="<?php echo wp_create_nonce( 'insert_thread' ) ?>">
					<div class="text-search">
						<div class="input-container">
							<input class="inp-title" id="thread_title" maxlength="90" name="post_title" type="text" autocomplete="off" placeholder="<?php _e('Click here to start your new topic' , ET_DOMAIN) ?>">
						</div>
						<div class="btn-group cat-dropdown dropdown category-search-items collapse">
							<span class="line"></span>
							<button class="btn dropdown-toggle" data-toggle="dropdown">
								<span class="text-select"></span>
								<span class="caret"></span>
							</button>
							<?php
							$categories = FE_ThreadCategory::get_categories();
							?>
							<select class="collapse" name="thread_category" id="thread_category">
								<option value=""><?php _e('Please select' , ET_DOMAIN) ?></option>
								<?php et_the_cat_select($categories) ?>
							</select>
						</div>
				  	</div>
					<div class="form-detail collapse">
						<?php wp_editor( '' , 'post_content' , editor_settings() ); ?>
						<?php
							// $useCaptcha = et_get_option('google_captcha') ;
							// if($useCaptcha){
								do_action( 'fe_custom_fields_form' );
							// }
						?>
						<div class="row line-bottom">
							<div class="col-md-6">
								<div class="show-preview">
									<div class="skin-checkbox">
										<span class="icon" data-icon="3"></span>
										<input type="checkbox" name="show_preview" class="checkbox-show" id="show_topic_item" style="display:none" />
									</div>
									<a href="#"><?php _e('Show preview' , ET_DOMAIN) ?></a>
								</div>
							</div>
							<div class="col-md-6">
								<div class="button-event">
									<input type="submit" value="
									<?php
										if($user_ID){
											_e('Create Topic', ET_DOMAIN);
										} else {
											_e('Login and Create Topic', ET_DOMAIN);
										}
									?>
									" class="btn">
									<a href="#" class="cancel"><span class="btn-cancel"><span class="icon" data-icon="D"></span><?php _e('Cancel' , ET_DOMAIN) ?></span></a>
								</div>
							</div>
						</div>
					</div>
				</form>
				<div id="thread_preview">
					<div class="name-preview"><?php _e('YOUR PREVIEW' , ET_DOMAIN) ?></div>
			        <ul class="detail-preview list-post">
			            <li>
			              <span class="thumb"><?php echo  et_get_avatar($user_ID);?></span>
			              <span class="title f-floatright" id="preview_title"><a href="#"><?php _e('Click here to start your new topic' , ET_DOMAIN) ?></a></span>
			              <div class="post-information f-floatright">
			                <span class="times-create"><?php _e('Just now in',ET_DOMAIN) ?></span>
			                <span class="type-category"><span class="flags color-2"></span><?php _e('Please select.',ET_DOMAIN) ?></span>
			                <!-- <span class="author"><span class="last-reply">Last reply</span> by <span class="semibold"><?php echo $current_user->user_login;?></span>.</span> -->
			                <span class="comment"><span class="icon" data-icon="w"></span>0</span>
			                <span class="like"><span class="icon" data-icon="k"></span>0</span>
			              </div>
			              <div class="text-detail f-floatright"></div>
			            </li>
			        </ul>
				</div><!-- End Preview Thread -->
			</div> <!-- End Form Thread -->
			<?php

			if( get_query_var('paged') ){
				$paged = get_query_var('paged');
			} else if( get_query_var('page') ){
				$paged = get_query_var('page');
			} else {
				$paged = 1;
			}

			$threads = new WP_Query( array(
				'post_type' 	=> 'thread',
				'paged' 		=> $paged
			) );
			if (  $threads->have_posts() ){ ?>
				<ul id="main_list_post" class="list-post">
					<?php
					/**
					 * Display regular threads
					 */
					while ($threads->have_posts()){
						$threads->the_post();
						get_template_part( 'template/thread', 'loop' );
					} // end while
					?>
				</ul>
				<?php
			} else { ?>
				<div class="notice-noresult">
					<span class="icon" data-icon="!"></span><?php _e('No topic has been created yet.', ET_DOMAIN) ?> <a href="#" id="create_first"><?php _e('Create the first one', ET_DOMAIN) ?></a>.
				</div>
				<?php
			} // end if
			?>

			<div class="pagination pagination-centered" id="main_pagination">
				<?php
				//wp_reset_query();
				echo paginate_links( array(
					'base' 		=> str_replace('99999', '%#%', esc_url(get_pagenum_link( 99999 ))),
					'format' 	=> $wp_rewrite->using_permalinks() ? 'page/%#%' : '?paged=%#%',
					'current' 	=> max(1, $paged),
					'total' 	=> $threads->max_num_pages,
					'prev_text' => '<',
					'next_text' => '>',
					'type' 		=> 'list'
				) ); ?>
			</div>
		</div>
		<div class="col-md-3 hidden-sm hidden-xs sidebar">
			<?php get_sidebar( ) ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>

