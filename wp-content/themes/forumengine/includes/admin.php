<?php
define('ADMIN_PATH', TEMPLATEPATH . '/admin');

/**
 * Handle admin features
 * Adding admin menus
 */
class ET_ForumAdmin extends ET_ForumEngine{
	/**
	 * Constructor for backend development
	 */
	public function __construct(){
		// declare new admin menus
		parent::__construct();
		$ajax_classes = apply_filters( 'et_ajax_classes', array(
			'ET_ForumAjax',
			'ET_UserAjax',
			'FE_LanguageAjax',
			'FE_ThreadCategoryAjax'
		) );
		foreach ((array)$ajax_classes as $class) {
			if (class_exists($class))
				new $class();
		}

		// adding thread & reply columns
		$this->add_filter('manage_posts_columns', 'add_columns', 10, 2);
		$this->add_filter('manage_posts_custom_column', 'display_columns', 10, 2);
		$this->add_action('init', 'add_menus');

		// kick subscriber user
		if ( !current_user_can( 'manage_options' ) && basename($_SERVER['SCRIPT_FILENAME']) != 'admin-ajax.php' ){
			wp_redirect( home_url(  ) );
			exit;
		}
		$this->add_filter('author_link', 'modify_author_link', 10, 3);
	}
	public function modify_author_link($link, $author_id, $author_nicename){
		$return = str_replace('/'.'author/', '/'.'member/', $link);
		return $return;
	}
	public function add_menus(){
		global $et_admin_page;
		do_action('et_before_admin_menu');
		// menus
		new ET_AdminOverview();
		new ET_AdminSetting();
		new ET_AdminMember();
		new ET_AdminBadges();
		new ET_AdminExtensions();
		$et_admin_page = new ET_EngineAdminMenu();
		do_action('et_admin_menu');
	}

	public function add_columns($columns, $post_type){
		switch ($post_type) {
			case 'thread':
				$columns = $this->array_put($columns, array(
					'replies' => __('Replies', ET_DOMAIN)
				), 4);
				break;

			case 'reply':
				$columns = $this->array_put($columns, array(
					'reply_to' => __('Rely to', ET_DOMAIN),
					'thread_in' => __('In thread', ET_DOMAIN)
				), 4);
				break;

			default:
				break;
		}
		return $columns;
	}

	public function display_columns($column, $post_id){
		switch ($column) {
			case 'replies':
				$count = get_post_meta( $post_id, 'et_replies_count', true );
				echo $count ? $count : 0;
				break;

			case 'reply_to':
				$parent_reply = get_post_meta( $post_id, 'et_reply_parent', true );
				if ( $parent_reply ){
					$parent = get_post($parent_reply);
					echo "<a href='" . admin_url( 'post.php?post=' . $parent_reply . '&action=edit' ) . "'>{$parent->post_title}</a>";
				}
				else
					echo 'N/A';
				break;

			case 'thread_in' :
				$post = get_post($post_id);
				$parent = get_post($post->post_parent);
				if($parent){
					echo "<a href='" . admin_url( 'post.php?post=' . $parent->ID . '&action=edit' ) . "'>{$parent->post_title}</a>";
				} else {
					echo 'N/A';
				}

				break;

			default:
				# code...
				break;
		}
	}

	private function array_put($array, $element, $pos){
		$new = array_slice($array, 0, $pos, true) + $element + array_slice($array, $pos);
		return $new;
	}

	// scripts
	public function on_add_scripts(){
		parent::on_add_scripts();
		$this->add_script('modernizr', TEMPLATEURL . '/js/libs/modernizr.js', array('jquery'));
	}

	// styles
	public function on_add_styles(){

	}
}


/**
 * Class template for Admin menus
 */
abstract class ET_AdminMenuItem extends ET_Base{

	abstract public function menu_view($args);
	abstract public function on_add_scripts();
	abstract public function on_add_styles();

	function __construct($menu_name, $args = array()){
		parent::__construct();
		$this->menu_name = $menu_name;
		$this->menu_args = wp_parse_args( $args, array(
			'menu_title' => 'Menu title',
			'page_title' => 'Menu title',
			'slug' 			=> 'menu-slug',
			'callback' 	=> array($this, 'menu_view')
		) );

		// actions
		$this->add_action('et_admin_menu', 'add_option_page');
		$this->add_action('et_admin_enqueue_scripts-' . $this->menu_args['slug'], 'on_add_scripts');
		$this->add_action('et_admin_enqueue_styles-' . $this->menu_args['slug'], 'on_add_styles');
	}

	public function add_option_page(){
		// default args
		et_register_menu_section($this->menu_name, $this->menu_args);
	}
}

$includes = array( 'overview', 'settings', 'members', 'badges','extensions');
foreach ($includes as $file) {
	require_once ADMIN_PATH . "/" . $file .  ".php";
}

function et_add_ajax_class($classname){
	global $et_ajax_classes;
	if ( empty($ajax_classes) ) $ajax_classes = array();

	$ajax_classes[] = $classname;
}

?>