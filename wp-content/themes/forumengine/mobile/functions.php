<?php
define( 'MOBILE_PATH', dirname(__FILE__) );
/* Disable WordPress Admin Bar for all users. */
if(et_load_mobile()){
	add_filter('show_admin_bar', '__return_false');
}

add_action('wp_ajax_et_mobile_logout','et_ajax_mobile_logout');
function et_ajax_mobile_logout(){
	wp_logout();
	$response = array(
		'success' => true,
		'redirect_url' => home_url(),
		'msg'			=> __('You have been logged out successfully.',ET_DOMAIN)
	);

	wp_send_json( $response );
}

/**
 * Handle mobile post
 */
add_action('wp_ajax_fe_get_posts' , 'fe_get_posts');
add_action('wp_ajax_nopriv_fe_get_posts' , 'fe_get_posts' );
function fe_get_posts() {
	try{
		global $post,$user_ID;

		$posts_data = array();
		$params = $_POST['content'];

		$args = array(
		'post_type'	  => 'post',
		'paged' 	  => $params['paged']+1,
		'cat'	  => $params['category'],
		'post_status' => apply_filters('fe_thread_status', array('publish')),
		);

		$posts_query = new WP_Query($args);

		if($posts_query->have_posts()){
			while($posts_query->have_posts()){
				$posts_query->the_post();
				$posts_data[] 	= post_mobile_template($post);
			}
		}
		$resp = array(
			'success' 	=> true,
			'data' 		=> array(
				'posts'			=> $posts_data,
				'paged' 		=> $params['paged'] +1,
				'total_pages' 	=> $posts_query->max_num_pages
				),
			'msg' 		=> 'successfully'
		);

	} catch (Exception $e) {
		$resp = array(
			'success' 	=> false,
			'msg' 		=> $e->getMessage()
		);
	}
	wp_send_json( $resp );
}

function post_mobile_template($post){
	$avatar 		= et_get_avatar($post->post_author);
	$post_class 	= get_post_class('fe-entry',$post->ID);
	$post_class  	= implode(' ', $post_class);
	$categories 	= get_the_category();
	$separator 		= ' ';
	$output 		= '';

	if($categories){
		foreach($categories as $category) {
			$output .= '<a class="fe-entry-cat" href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s",ET_DOMAIN ), $category->name ) ) . '">'.$category->cat_name.'</a>'.$separator;
		}
	$category =   trim($output, $separator);
	}

	$author_link 	= get_author_posts_url($post->post_author);
	$permalink 		= get_permalink( $post->ID );
	$content 		= apply_filters('the_content' , $post->post_excerpt );
	$comments 		= get_comments_number( $post->ID );
	$template = <<<HTML
<article id="post-{$post->ID}" class="{$post_class}">
	<div class="fe-entry-left">
		<a class="fe-entry-thumbnail" href="{$author_link}">
			{$avatar}
		</a>
	</div>
	<div class="fe-entry-right">
		<div class="fe-entry-head">
			<div class="fe-entry-meta">
				{$category}
				<a href="{$permalink}" class="fe-entry-comments icon fe-icon-b" data-icon="q">{$comments}</a>
			</div>
			<a href="{$permalink}"><h2 class="fe-entry-title">{$post->post_title}</h2></a>
		</div>
		<div class="fe-entry-content">
			{$content}
			<a class="more-link" href="{$permalink}">Read more<span class="icon fe-icon fe-icon-more" data-icon="]"></span></a>
		</div>
	</div>
	<div class="clearfix"></div>
</article>
HTML;

	return $template;
}

add_action('wp_ajax_fe_get_comments' , 'fe_get_comments');
add_action('wp_ajax_nopriv_fe_get_comments' , 'fe_get_comments' );
function fe_get_comments() {
	try{
		global $post,$user_ID;

		$comments_data = array();
		$params = $_POST['content'];

		$args = array(
			'post_id' 	=> $params['id'],
			'order' 	=> 'ASC',
		);

		$comments = get_comments( $args );

		ob_start();
		wp_list_comments(array(
			'page' 			=> $params['paged']+1,
			'per_page'		=> get_option( 'comments_per_page' ),
			'type' 			=> 'comment',
			'callback' 		=> 'je_comment_template_mobile',
			'avatar_size' 	=> 40,
			'reply_text'	=> __('Reply ',ET_DOMAIN).'<span class="icon" data-icon="R"></span>',
		), $comments);
		$returns = ob_get_clean();

		$resp = array(
			'success' 	=> true,
			'data' 		=> array(
				'comments'			=> $returns,
				'paged' 		=> $params['paged'] +1,
				),
			'msg' 		=> 'successfully'
		);

	} catch (Exception $e) {
		$resp = array(
			'success' 	=> false,
			'msg' 		=> $e->getMessage()
		);
	}
	wp_send_json( $resp );
}
add_action('wp_ajax_fe_insert_comment' , 'fe_insert_comment');
add_action('wp_ajax_nopriv_fe_insert_comment' , 'fe_insert_comment' );
function fe_insert_comment() {
	try{
		global $user_ID, $current_user;

		if (is_user_logged_in()){
			$data = wp_parse_args( $_POST['content'] , array(
				'comment_type'		=> 'comment',
				'user_id'  			=> $current_user->ID,
				'comment_content' 	=> $_POST['content']['comment']
			));
		} else {
			$data = wp_parse_args( $_POST['content'] , array(
				'comment_type'	 		=> 'comment',
				'comment_author' 		=> $_POST['content']['author'],
				'comment_author_email'  => $_POST['content']['email'],
				'comment_author_url' 	=> $_POST['content']['url'],
				'comment_content' 		=> $_POST['content']['comment']
			));
		}

		$ID = wp_insert_comment( $data );

		$resp = array(
			'success' 	=> true,
			'data' 		=> $ID,
			'msg' 		=> 'successfully'
		);

	} catch (Exception $e) {
		$resp = array(
			'success' 	=> false,
			'msg' 		=> $e->getMessage()
		);
	}
	wp_send_json( $resp );
}
/**
 * Handle mobile post
 */
add_action( 'template_redirect', 'prevent_user_mobile' );
function prevent_user_mobile() {
	if(is_page_template( 'page-login.php' )){
		global $user_ID;
		if($user_ID){
			wp_redirect( home_url() );
			exit;
		}
	}
}
/**
 * Handle mobile here
 */
add_filter('template_include', 'et_template_mobile');
function et_template_mobile($template){
	global $user_ID, $wp_query, $wp_rewrite;
	$new_template = $template;

	// no need to redirect when in admin
	if ( is_admin() ) return $template;

	/***
	  * Detect mobile and redirect to the correlative layout file
	  */

	//if ( et_load_mobile() || (isset($_COOKIE['demo_is_mobile']) && $_COOKIE['demo_is_mobile'] == 1) ){
	if ( et_load_mobile() ){
		$filename 		= basename($template);

		$child_path		= get_stylesheet_directory() . '/mobile' . '/' . $filename;
		$parent_path 	= get_template_directory() . '/mobile' . '/' . $filename;

		if ( file_exists($child_path) ){
			$new_template = $child_path;
		} else if ( file_exists( $parent_path )){
			$new_template = $parent_path;
		} else {
			$new_template = get_template_directory() . '/mobile/unsupported.php';
		}
		// else if (is_page_template('page-following.php')){
		// 	$new_template = get_template_directory() . '/mobile/page-following.php';
		// } else if (is_page_template('page-pending.php')){
		// 	$new_template = get_template_directory() . '/mobile/page-pending.php';
		// } else {
		// 	$new_template = get_template_directory() . '/mobile/unsupported.php';
		// }

		// some special page which are existed in main template
		// if(!in_array($filename, array('header-mobile.php' , 'footer-mobile.php', 'header.php', 'footer.php')) ) {
		// 	if (is_page_template('page-login.php')){
		// 		$new_template = get_template_directory() . '/mobile/page-login.php';
		// 	} else if (is_page_template('page-register.php')){
		// 		$new_template = get_template_directory() . '/mobile/page-register.php';
		// 	}
		// }
	}

	return $new_template;
}

/**
 *
 */
function et_load_mobile(){
	global $isMobile;
	$detector = new ET_MobileDetect();
	$isMobile = $detector->isMobile() && (!$detector->isAndroidtablet()) && (!$detector->isIpad());
	$isMobile = apply_filters( 'et_is_mobile', $isMobile ? true : false );
	if ( $isMobile && (!isset($_COOKIE['mobile']) || md5('disable') != $_COOKIE['mobile'] )){
		return true;
	} else {
		return false;
	}
}

/**
 * Get mobile version header template
 * @author toannm
 * @param name of the custom header template
 * @version 1.0
 * @copyright enginethemes.com team
 * @license enginethemes.com team
 */
function et_get_mobile_header( $name = null ){
	do_action( 'get_header', $name );

	//$templates = array();
	$templates = MOBILE_PATH . '/' . 'header.php';
	if ( isset($name) )
		$templates = MOBILE_PATH . '/' . "header-{$name}.php";
	$templates = apply_filters( 'template_include', $templates );

	if ('' == locate_template($templates, true))
		//load_template( ABSPATH . WPINC . '/theme-compat/header.php');
		load_template( $templates);
}

/**
 * Get mobile version header template
 * @author toannm
 * @param name of the custom header template
 * @version 1.0
 * @copyright enginethemes.com team
 * @license enginethemes.com team
 */
function et_get_mobile_footer( $name = null ) {

	do_action( 'get_footer', $name );

	//$templates = array();
	$templates = MOBILE_PATH . '/' . 'footer.php';
	if ( isset($name) )
		$templates = MOBILE_PATH . '/' . "footer-{$name}.php";
	$templates = apply_filters( 'template_include', $templates );

	//$templates = apply_filters( 'template_include', $templates );
	// Backward compat code will be removed in a future release
	if ('' == locate_template($templates, true))
		//load_template( ABSPATH . WPINC . '/theme-compat/footer.php');
		load_template($templates);
}

/**
 *
 **/
function et_mobile_categories($parent = 0, $level = 1, $categories = false){

	$current_cat = get_query_var( 'term' );
	if ( !$categories )
		$cats = FE_ThreadCategory::get_categories();
	else
		$cats = $categories;

	if ( !empty($cats) ){
		foreach ($cats as $cat) {
			if ( $cat->parent != $parent ) continue;

			$cat_link = get_term_link( $cat, 'thread_category' );

			$has_child = false;
			foreach ($cats as $child) {
				if ( $child->parent == $cat->term_id ){
					$has_child = true;
					break;
				}
			}
			$color = FE_ThreadCategory::get_category_color($cat->term_id);
			?>
			<li class="<?php echo $current_cat == $cat->slug ? "fe-current" : '' ?> <?php if ($has_child) echo 'fe-has-child' ?>">
				<a href="<?php echo get_term_link( $cat, 'thread_category' ) ?>">
					<span class="arrow"><span class="fe-sprite"></span></span>
					<span class="name"><?php echo $cat->name ?></span>
					<span class="flags color-<?php echo $color ?>"></span>
				</a>
				<?php if ( $has_child ) { ?>
					<ul>
						<?php et_mobile_categories($cat->term_id, $level + 1, $cats) ?>
					</ul>
				<?php } ?>
			</li>
			<?php
		}
	}
}

/**
 *
 */
function fe_edit_modals($args){
	foreach ($args as $modal) {
		$params = wp_parse_args( $modal, array(
			'id' 	=> 'modal_',
			'title' => '',
			'name' 	=> '',
			'value' => '',
			'placeholder' => '',
			'type' 	=> 'text',
			'hidden_fields' => array()
		) );
		?>
		<div class="modal-edit" id="<?php echo $params['id'] ?>">
			<form class="form-edit-profile" action="" data-target="<?php echo '#content_' . $params['name'] ?>">
				<div class="fe-page-heading">
					<ul class="fe-thread-actions pull-right">
						<li class="">
							<a class="submit-modal" href="#"><?php _e('Submit', ET_DOMAIN) ?></a>
						</li>
					</ul>
					<ul class="fe-thread-actions">
						<li class="" style="">
							<a class="fe-icon-b fe-icon-b-cancel cancel-modal" href="#"><?php _e('Cancel', ET_DOMAIN) ?></a>
						</li>
					</ul>
				</div>
				<div class="fe-edit-block fe-container">
					<div class="fe-block-title">
						<?php echo $params['title'] ?>
					</div>
					<?php foreach ($params['hidden_fields'] as $name => $value) {
						echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
					 } ?>
					<div class="fe-edit-area">
						<div class="fe-area-block">
							<?php if ( $params['type'] == 'text' ){ ?>
								<input class="fe-input-text" data-role="none" name="<?php echo $params['name'] ?>" value="<?php echo esc_attr($params['value']) ?>" data-role="none"
								 type="text">
							<?php } else if ( $params['type'] == 'textarea' ) { ?>
								<textarea class="fe-input-text" name="<?php echo $params['name'] ?>"><?php echo $params['value'] ?></textarea>
							<?php } ?>
						</div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}
}

function je_comment_template_mobile($comment, $args, $depth){
	$GLOBALS['comment'] = $comment;
?>
	<li class="fe-comment" id="comment_<?php echo $comment->comment_ID ?>">
		<div class="fe-comment-left">
			<div class="fe-comment-thumbnail">
				<?php echo et_get_avatar($comment->user_id); ?>
			</div>
		</div>
		<div class="fe-comment-right">
			<div class="fe-comment-header">
				<a href="<?php comment_author_url() ?>"><strong class="fe-comment-author"><?php comment_author() ?></strong></a>
				<span class="fe-comment-time icon" data-icon="t"><?php comment_date() ?></span>
			</div>
			<div class="fe-comment-content">
				<?php echo wpautop(strip_tags( get_comment_text($comment->comment_ID), '<p><br>' )) ?>
				<!-- <p class="fe-comment-reply">
					<a class="comment-reply" href="#" data-id="<?php echo $comment->comment_ID ?>">Reply <span class="icon" data-icon="R"></span></a>
				</p> -->
				<p>
					<a class="fe-comment-reply" data-id="<?php echo $comment->comment_ID ?>" href="#"><?php _E('Reply', ET_DOMAIN) ?> <span class="icon fe-icon fe-icon-reply" data-icon="R"></span></a>
				</p>
			</div>
		</div>
		<div class="clearfix"></div>
<?php
}