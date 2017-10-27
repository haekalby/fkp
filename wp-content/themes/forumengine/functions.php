<?php
if (WP_DEBUG && WP_DEBUG_DISPLAY)
{
   ini_set('error_reporting', E_ALL & ~E_STRICT & ~E_NOTICE);
}
if(is_admin()){
	/** Absolute path to the WordPress directory. */
	if ( !defined('ABSPATH') )
	    define('ABSPATH', dirname(__FILE__) . '/');

	define('CONCATENATE_SCRIPTS', false);
}
define("ET_UPDATE_PATH",    "//www.enginethemes.com/forums/?do=product-update");
define("ET_VERSION", '1.6.9');

if(!defined('ET_URL'))
	define('ET_URL', '//www.enginethemes.com/');

if(!defined('ET_CONTENT_DIR'))
	define('ET_CONTENT_DIR', WP_CONTENT_DIR.'/et-content/');

define ( 'TEMPLATEURL', get_bloginfo('template_url') );
define('THEME_NAME', 'forumengine');

define('THEME_CONTENT_DIR', WP_CONTENT_DIR . '/et-content' . '/' . THEME_NAME );
define('THEME_CONTENT_URL', content_url() . '/et-content' . '/' . THEME_NAME );

if(!defined('ET_LANGUAGE_PATH') )
	define('ET_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang');

if(!defined('ET_CSS_PATH') )
	define('ET_CSS_PATH', THEME_CONTENT_DIR . '/css');

require_once TEMPLATEPATH . '/includes/index.php';
//google captcha class
require_once TEMPLATEPATH . '/includes/google-captcha.php';

try {
	if ( is_admin() ){
		new ET_ForumAdmin();
	} else {
		new ET_ForumFront();
	}

} catch (Exception $e) {
	echo $e->getMessage();
}

add_theme_support( 'automatic-feed-links');
add_theme_support('post-thumbnails');

function et_prevent_user_access_wp_admin ()  {
	if(!current_user_can('manage_options')) {
		wp_redirect(home_url());
		exit;
	}
}

/// for test purpose
add_action( 'init', 'test_oauth' );
function test_oauth(){
	if ( isset($_GET['test']) && $_GET['test'] == 'twitter' ){
		require dirname(__FILE__) . '/auth.php';
		exit;
	}
}
function je_comment_template($comment, $args, $depth){
	$GLOBALS['comment'] = $comment;
?>
	<li class="et-comment" id="comment-<?php echo $comment->comment_ID ?>">
		<div class="et-comment-left">
			<div class="et-comment-thumbnail">
				<?php echo et_get_avatar($comment->user_id); ?>
			</div>
		</div>
		<div class="et-comment-right">
			<div class="et-comment-header">
				<a href="<?php comment_author_url() ?>"><strong class="et-comment-author"><?php comment_author() ?></strong></a>
				<span class="et-comment-time icon" data-icon="t"><?php comment_date() ?></span>
			</div>
			<div class="et-comment-content">
				<?php echo esc_attr( get_comment_text($comment->comment_ID) ) ?>
				<p class="et-comment-reply"><?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></p>
			</div>
		</div>
		<div class="clearfix"></div>
<?php
}
/**
 *Check to load mobile version
 *
 *@return true if load mobile version / false if don't load
 *@since version 1.6.1
 */
if(!function_exists('et_load_mobile')) {
	function et_load_mobile() {
		global $isMobile;
		$detector = new ET_MobileDetect();
		$isMobile = $detector->isMobile() && ( ! $detector->isAndroidtablet() ) && ( ! $detector->isIpad() );
		$isMobile = apply_filters( 'et_is_mobile', $isMobile ? TRUE : FALSE );
		if ( $isMobile && ( ! isset( $_COOKIE[ 'mobile' ] ) || md5( 'disable' ) != $_COOKIE[ 'mobile' ] ) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
/**
 * Remove desktop style and script when load mobile version
 */
add_action('wp_head', 'et_wp_head');
function et_wp_head() {
	if(et_load_mobile()){
		return;
	}
}
add_action('wp_footer', 'et_wp_footer');
function et_wp_footer() {
	if(et_load_mobile()){
		return;
	}
}
/*
add_action('init', 'cng_author_base');
function cng_author_base() {
    global $wp_rewrite;
    $author_slug = 'user'; // change slug name
    $wp_rewrite->author_base = $author_slug;
}
*/

/**
 * Add closed status to search query.
 *
 * @param $query
 *
 * @return void
 *
 * @author nguyenvanduocit
 */
function add_status_to_search_query( $query ) {
	if ( $query->is_search() && $query->is_main_query() ) {
		$query->query_vars['post_status'] = apply_filters('fe_thread_status', array('publish', 'closed'));
	}
}

add_action( 'pre_get_posts', 'add_status_to_search_query' );