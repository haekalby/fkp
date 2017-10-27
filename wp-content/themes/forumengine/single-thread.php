<?php
get_header();
global $post, $current_user, $user_ID, $thread, $wp_rewrite;

// FOR PENDING JOB
if ( $post->post_status == 'pending' && current_user_can( 'manage_threads' )){
?>
<div class="head-notice">
	<div class="main-center">
		<h1><?php _e('THIS THREAD IS PENDING. YOU CAN APPROVE OR REJECT IT.',ET_DOMAIN)?><span class="arrow-down"></span></h1>
	</div>
</div>
<?php } ?>
<?php
// FOR CLOSED JOB
if ( $post->post_status == 'closed'){
?>
<div class="head-notice closed">
	<div class="main-center">
		<h1><?php _e('THIS THREAD HAS BEEN CLOSED.',ET_DOMAIN)?></h1>
	</div>
</div>
<?php } ?>
<!--end header Bottom-->
<div class="container main-center">
	<div itemscope itemtype="http://schema.org/Article">
		<div class="row">
			<div class="col-md-9 marginTop18">

				<?php
				$threadData = array();
				if (have_posts()) {
					the_post();

					global $thread;
					$thread 	 = FE_Threads::convert($post);
					$threadData  = $thread;
					// query replies
					$thread_id = $post->ID;
					$thread_author = apply_filters( 'fe_author', get_the_author(), $post->post_author );
					$et_updated_date = et_the_time(strtotime($thread->et_updated_date));

					$replies_query = FE_Replies::get_replies(array(
						'paged' => get_query_var( 'page' ),
						'post_parent' => $post->ID,
						'order' => 'ASC'
					)) ;

					if ( !empty($thread->thread_category[0]) )
						$color = FE_ThreadCategory::get_category_color($thread->thread_category[0]->term_id);
					else
						$color = 0;

					?>
				<div class="row">
					<div class="col-md-9 title-thread-header col-sm-9 marginBottom4">
						<h1 itemprop="name" class="title-thread"><?php the_title() ?></h1>
						<div class="thread-information">
							<span class="times-create"><?php printf( __( 'Updated %s in', ET_DOMAIN ),$et_updated_date );?></span>
							<span class="type-category">
								<?php if ( $thread->has_category ) {  ?>
									<a href="<?php echo get_term_link( $thread->thread_category[0], 'thread_category' ) ?>">
										<span class="flags color-<?php echo $color ?>"></span>
										<span class="thread-cat-name"><?php echo $thread->thread_category[0]->name ?></span>
									</a>
								<?php } else {  ?>
									<span class="flags color-<?php echo 0 ?>"></span><?php _e('No Category', ET_DOMAIN) ?>
								<?php } ?>
							</span>
							<?php if(current_user_can( 'manage_threads' )){?>
							<div class="contronl-thread-single">
								<?php if($post->post_status != 'closed'){ ?>
								<a href="<?php echo add_query_arg(array('action' => 'close','thread_id' => $post->ID,'fe_nonce' => wp_create_nonce( 'close_thread' )), get_permalink($post->ID) );?>" class="close-thread " data-toggle="tooltip" title="<?php _e('Close thread', ET_DOMAIN) ?>" data-original-title="Close"><span class="icon" data-icon="("></span></a>
								<?php } elseif($post->post_status == 'closed') { ?>
								<a href="<?php echo add_query_arg(array('action' => 'approve','thread_id' => $post->ID,'fe_nonce' => wp_create_nonce( 'approve_thread' )), get_permalink($post->ID) );?>" class="unclose-thread" data-toggle="tooltip" title="<?php _e('Reopen thread', ET_DOMAIN) ?>"><span class="icon" data-icon=")"></span></a>
								<?php  } ?>
								<a href="<?php echo add_query_arg(array('action' => 'delete','thread_id' => $post->ID,'fe_nonce' => wp_create_nonce( 'delete_thread' )), get_permalink($post->ID) );?>" class="delete-thread" data-toggle="tooltip" title="<?php _e('Delete thread', ET_DOMAIN) ?>" data-original-title="Delete"><span class="icon" data-icon="#"></span></a>
							</div>
							<?php } ?>
						</div>
					</div>
					<div class="col-md-3 col-md-3 thread-infor">
						<?php
						$user_following = explode(',', (string) get_post_meta( $post->ID, 'et_users_follow',true));
						$is_followed = in_array($current_user->ID, $user_following);
						?>
						<a class="tog-follow unfollow <?php if (!$is_followed) echo 'collapse' ?>" href="#"><span class="icon" data-icon="_"></span> <?php _e('Unfollow', ET_DOMAIN) ?></a>
						<a class="tog-follow follow <?php if ($is_followed) echo 'collapse' ?>" href="#"><span class="icon" data-icon="&"></span> <?php _e('Follow', ET_DOMAIN) ?></a>
					</div>
				</div>
				<?php if(!get_option( 'et_infinite_scroll' )){ ?>

					<?php if($replies_query->max_num_pages > 1) { ?>

					<div class="row" style="padding: 0 15px">
						<div class="col-md-12 pagination pagination-centered">
							<?php
							wp_reset_query();
							echo paginate_links( array(
								'base' 		=> @esc_url(add_query_arg( 'page', '%#%' )),
								'current' 	=> max(1, get_query_var('page')),
								'total' 	=> $replies_query->max_num_pages,
								'prev_text' => '<',
								'next_text' => '>',
								'type' 		=> 'list'
							) ); ?>
						</div>
					</div>

					<?php } ?>

				<?php } ?>

				<?php
					if($replies_query->query_vars['paged'] <= 1){
				?>

				<div class="items-thread item-thread clearfix" data-id="<?php echo $post->ID ?>">
					<div class="f-floatleft single-avatar">
						<a href="<?php echo get_author_posts_url( $post->post_author ) ?>">
							<?php echo et_get_avatar($post->post_author);?>
							<?php do_action( 'fe_user_badge', $post->post_author ); ?>
						</a>
					</div>
					<!-- end float left -->
					<div class="f-floatright topic-thread">
						<div class="topic-thread">
							<div class="post-display">
								<ul class="control-thread">
									<?php
										if (user_can_edit($thread)){
									?>
										<li><a href="#" class="edit-topic-thread control-edit" data-toggle="tooltip" title="<?php _e('Edit', ET_DOMAIN) ?>"><span class="icon" data-icon="p"></span></a></li>
									<?php } ?>
										<li><a href="#" class="control-quote" data-toggle="tooltip" title="<?php _e('Quote', ET_DOMAIN) ?>"><span class="icon" data-icon='"'></span></a></li>
									<?php if ( !$thread->reported ){?>
										<li><a href="#" class="control-report" data-toggle="tooltip" title="<?php _e('Report', ET_DOMAIN) ?>"><span class="icon" data-icon='!'></span></a></li>
									<?php } ?>
								</ul>
								<div class="name">
									<a  class="post-author" href="<?php echo get_author_posts_url( $post->post_author ) ?>"><span itemprop="author"><?php echo $thread_author; //the_author() ?></span></a>
									<span class="comment">
										<span class="<?php if ( $thread->replied ) echo 'active' ?>">
											<span data-icon="w" class="icon"></span>
											<span itemprop="interactionCount" class="count"><?php echo $thread->et_replies_count ?></span>
										</span>
									</span>
									<span class="like">
										<a href="#" class="like-post <?php if ($thread->liked) echo 'active' ?>" data-id="<?php echo $thread->ID ?>">
											<span data-icon="k" class="icon"></span>
											<span  itemprop="interactionCount" class="count"><?php echo $thread->et_likes_count ?></span>
										</a>
									</span>
									<span class="date"><?php echo et_the_time( strtotime( $thread->post_date ) ); ?></span>
								</div>
								<div class="content">
									<?php the_content(); ?>
								</div>
								<!-- custom fields data go here -->
								<div class="custom-fields-data">
									<?php do_action( 'custom_fields_data', $thread->ID );?>
								</div>
								<!-- custom fields data go here -->
								<div class="linke-by clearfix <?php if($thread->post_status == "closed" && count($thread->et_likes) == 0 ){?>collapse <?php } ?>">
									<ul class="user-discuss <?php echo count($thread->et_likes) > 0 ? '' : 'collapse' ?>">
										<li class="text"><?php _e('Liked by', ET_DOMAIN) ?></li>
										<?php
										$count = 0;
										foreach ($thread->et_likes as $user_id) {
											if ($count < 5) {
											$user 	= FE_Member::get($user_id);
											$avatar = $user->et_avatar;
											$name 	= $user->display_name;
										?>
											<li <?php if ( $user_id == $current_user->ID ) echo 'class="me"' ?>>
												<a href="<?php echo get_author_posts_url( $user_id ) ?>" data-toggle="tooltip" title="<?php echo $name ?>"><?php echo $avatar ?></a>
											</li>
										<?php
											}//end if
											$count++;
										}//end foreach
										?>
										<?php
										if ( $count > 5 ) {
											echo '<li class="img-circle more-img">' . '+' . ($count - 5) . '</li>';
										}
										?>
									</ul>
									<?php if($thread->post_status != "closed") { ?>
									<a href="#reply_thread" data-id="<?php echo $thread->ID ?>" class="goto-reply"><?php _e('Reply', ET_DOMAIN) ?><span class="icon" data-icon="R">
									</span></a>
									<?php } ?>
								</div>
							</div>
							<!-- EDITOR -->
							<div id="form_thread" class="post-edit thread-form edit-thread collapse">
								<form class="form-post-edit" action="" method="post">
									<input type="hidden" name="fe_nonce" value="<?php echo wp_create_nonce( 'edit_thread' ) ?>">
									<div class="text-search">
										<div class="input-container">
											<input class="inp-title" name="post_title" maxlength="90" type="text" value="<?php echo $post->post_title ?>" placeholder="<?php _e('Click here to start your new topic' , ET_DOMAIN) ?>">
										</div>
										<input type="hidden" name="ID" value="<?php echo $post->ID ?>">
										<div class="btn-group cat-dropdown dropdown category-search-items">
											<span class="line"></span>
											<button class="btn dropdown-toggle" data-toggle="dropdown">
												<span class="text-select"></span>
												<span class="caret"></span>
											</button>
											<?php $current_cat = empty($thread->thread_category[0]) ? false : $thread->thread_category[0]->term_id ?>
											<?php
											$categories = FE_ThreadCategory::get_categories();;//FE_Threads::get_categories(array('hide_empty'=>false));
											?>
											<select class="collapse" name="thread_category" id="thread_category">
												<option value=""><?php _e('Please select' , ET_DOMAIN) ?></option>
												<?php et_the_cat_select($categories, $current_cat); ?>
											</select>
										</div>
								  	</div>
									<div class="form-detail">
										<div id="wp-<?php echo 'edit_post_content' . $post->ID ?>-editor-container" class="wp-editor-container">
											<textarea name="post_content" id="<?php echo 'edit_post_content' . $post->ID?>"><?php echo $post->post_content ?></textarea>
										</div>
										<?php do_action( 'fe_custom_fields_form' ); ?>
										<div class="row line-bottom">
											<div class="col-md-6 col-sm-6">
												<!-- <div class="show-preview">
													<div class="skin-checkbox">
														<span class="icon" data-icon="3"></span>
														<input type="checkbox" class="checkbox-show" id="show_topic_item" style="display:none" />
													</div>
													<a href="#"><?php _e('Show preview', ET_DOMAIN) ?></a>
												</div> -->
											</div>
											<div class="col-md-6 col-sm-6">
												<div class="button-event">
													<input type="submit" value="<?php _e('Update', ET_DOMAIN) ?>" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" class="btn">
													<a href="#" class="cancel control-edit-cancel"><span class="btn-cancel"><span class="icon" data-icon="D"></span><?php _e('Cancel', ET_DOMAIN) ?></span></a>
												</div>
											</div>
										</div>
									</div>
								</form> <!-- end EDITOR -->
							</div> <!-- end form -->
						</div>
					</div>
					<!-- end float right -->
				</div>
				<?php
						}
					}
				?>
				<!-- end items threads -->

				<?php
				/**
				 * List replies
				 */
				?>
				<div id="replies_list">
					<div itemscope itemtype="http://schema.org/ItemList">
					<meta itemprop="itemListOrder" content="Descending" />
					<?php
						global $et_repliesData;
						$et_repliesData = array();
						if ( $replies_query->have_posts() ){
							while ( $replies_query->have_posts() ) {
								global $post;
								$replies_query->the_post();
								$reply 				= FE_Replies::convert($post);
								$et_repliesData[] 	= $reply;
								get_template_part( 'template/reply', 'item' );
							}// end while
						} //end if
					?>
					</div>
				</div>
				<!-- end items replies -->
				<script type="text/javascript">
					var threadData 	= <?php echo json_encode($threadData) ?>;
					var repliesData = <?php echo json_encode($et_repliesData) ?>;
				</script>

				<?php
					wp_reset_query();
					$page = get_query_var('page') ? get_query_var('page') : 1;
				?>

				<?php if(!get_option( 'et_infinite_scroll' )){ ?>
				<?php ///if(1==1){ ?>
				<!-- Normal Paginations -->
				<?php if ( $replies_query->max_num_pages > 1 ){ ?>
				<div class="pagination pagination-centered">
					<?php
					echo paginate_links( array(
						'base' 		=> @add_query_arg( 'page', '%#%' ),
						'current' 	=> max(1, $page),
						'total' 	=> $replies_query->max_num_pages,
						'prev_text' => '<',
						'next_text' => '>',
						'type' 		=> 'list'
					) ); ?>
				</div>
				<?php } ?>
				<!-- Normal Paginations -->

				<?php } else { ?>

				<!-- Infinite Scroll -->
				<?php
					$fetch = ($page < $replies_query->max_num_pages) ? 1 : 0 ;
					//$check = round((int) 10 / (int) get_option( 'posts_per_page' ) , 0 , PHP_ROUND_HALF_DOWN);
					$check = floor((int) 10 / (int) get_option( 'posts_per_page' ));
				?>
				<div id="reply_loading" class="hide" data-fetch="<?php echo $fetch ?>" data-parent="<?php echo $thread->ID ?>" data-status="scroll-index" data-check="<?php echo $check ?>">
					<!-- <img src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader.gif"> -->
					<div class="bubblingG">
						<span id="bubblingG_1">
						</span>
						<span id="bubblingG_2">
						</span>
						<span id="bubblingG_3">
						</span>
					</div>
					<?php _e( 'Loading more replies', ET_DOMAIN ); ?>
					<input type="hidden" value="<?php echo $page ?>" id="current_page">
					<input type="hidden" value="<?php echo $replies_query->max_num_pages ?>" id="max_page">
				</div>
				<!-- Infinite Scroll -->

				<?php } ?>

				<!-- Check if thread is closed or not -->
				<?php if($thread->post_status != "closed") { ?>
				<div class="thread-reply items-thread clearfix no-border" id="thread_reply">
					<div class="f-floatleft single-avatar">
						<?php echo et_get_avatar($current_user->ID);?>
						<?php do_action( 'fe_user_badge', $current_user->ID ); ?>
					</div>
					<div class="f-floatright clearfix" id="reply_thread_container">
						<a class="reply-overlay"><?php _e( 'Click here to start your discussion', ET_DOMAIN ); ?></a>
						<form id="reply_thread" action="" method="post" style="display:none;">
							<input type="hidden" name="fe_nonce" value="<?php echo wp_create_nonce( 'insert_reply' ) ?>">
							<input type="hidden" name="parent" value="<?php echo $post->ID ?>">
							<div id="wp-<?php echo 'post_content' . $post->ID ?>-editor-container" class="wp-editor-container">
								<textarea id="<?php echo 'post_content' . $post->ID ?>" name="post_content"></textarea>
							</div>
							<?php do_action( 'fe_reply_form_fields' );?>
							<div class="row line-bottom">
								<div class="col-md-6 col-sm-6">
									<div class="show-preview">
										<div class="skin-checkbox">
											<span class="icon" data-icon="3"></span>
											<input type="checkbox" class="checkbox-show" id="show_topic_item" style="display:none" />
										</div>
										<a href="#"><?php _e( 'Show preview', ET_DOMAIN ) ?></a>
									</div>
								</div>
								<div class="col-md-6 col-sm-6">
									<div class="button-event">
										<input type="submit" data-loading-text="<?php _e( 'Loading...', ET_DOMAIN ) ?>" value="<?php _e('Reply', ET_DOMAIN) ?>" class="btn one-btn">
										<a href="javascript:void(0);" class="cancel cancel-reply"><span class="btn-cancel"><span class="icon" data-icon="D"></span><?php _e('Cancel' , ET_DOMAIN) ?></span></a>
									</div>
								</div>
							</div>
						</form>
					</div>
					<!-- Preview Reply -->
					<div id="thread_preview" class="reply-preview">
						<div class="name-preview"><?php _e('YOUR PREVIEW' , ET_DOMAIN) ?></div>
				        <div class="reply-item items-thread clearfix preview-item">
							<div class="f-floatleft">
								<?php echo  et_get_avatar($user_ID);?>
							</div>
							<div class="f-floatright">
								<div class="post-display">
									<div class="post-information">
										<div class="name">
											<span  class="post-author"><?php echo $current_user->display_name;?></span>
											<span  class="comment"><span class="icon" data-icon="w"></span>0</span>
											<span  class="like"><span class="icon" data-icon="k"></span>0</span>
										</div>
									</div>
									<div class="text-detail content"></div>
								</div>
							</div>
				        </div>
					</div><!-- End Preview Reply -->
					<!-- Preview Reply -->
				</div>
				<?php } //else { ?>
				<!-- Check if thread is closed or not -->
			</div>
			<?php
			/**
			  * Pending jobs
			  */
			?>
			<div class="col-md-3 marginTop18 thread-discuss-right hidden-sm hidden-xs">
				<?php
				wp_reset_query();
				if ( $post->post_status == 'pending' && current_user_can( "manage_threads" )){
					$approve_link = add_query_arg(array(
						'action' => 'approve',
						'thread_id' => $post->ID,
						'fe_nonce' => wp_create_nonce( 'approve_thread' )), home_url() );
					$delete_link = add_query_arg(array(
						'action' => 'delete',
						'thread_id' => $post->ID,
						'fe_nonce' => wp_create_nonce( 'delete_thread' )), home_url() );
				?>
				<ul class="control-approve">
					<li class="reject"><a class="delete-thread" href="<?php echo $delete_link ?>"><span class="icon" data-icon="#"></span><?php _e('Delete', ET_DOMAIN) ?></a></li>
					<li><a href="<?php echo $approve_link ?>"><span class="icon" data-icon="3"></span><?php _e('Approve', ET_DOMAIN) ?></a></li>
				</ul>
				<?php } else { ?>

				<?php } ?>

				<?php do_action( 'forumengine_before_single_thread_sidebar' ) ?>
				<?php get_sidebar( 'thread' ); ?>
				<?php do_action( 'forumengine_after_single_thread_sidebar' ) ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var currentThread = <?php echo json_encode($thread) ?>;
</script>

<?php get_footer() ?>