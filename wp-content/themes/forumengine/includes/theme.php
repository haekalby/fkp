<?php

class ET_ForumEngine extends ET_Base{

	// abstract function on_add_scripts();
	// abstract function on_add_styles();

	//
	// declare post_types, scripts, styles ... which are uses in theme
	function __construct(){
		parent::__construct();
		global $current_user;

		// disable admin bar if user can not manage options
		if (!current_user_can('manage_options')){
			show_admin_bar(false);
		};

		// register tag
		$this->add_action('init', 'init_theme');
		//$this->add_action('init', 'register_tag');
		//$this->add_filter('logout_url', 'logout_home', 10, 2);

		//filter email message template
		$this->add_filter('et_reset_password_link', 'reset_password_link', 10, 3);
		$this->add_filter('et_retrieve_password_message','retrieve_password_message',10,3);

		$this->add_action('et_after_register','user_register_mail', 20 , 2);
		$this->add_action('et_after_register','update_user_meta', 10 , 2);

		$this->add_action('et_password_reset', 'password_reset_mail',10,2);
		$this->add_action('widgets_init', 'et_widgets_init');

		$this->add_action('after_switch_theme', 'set_default_theme');

		$this->add_action('after_switch_theme', 'set_static_front_page');

		$this->add_filter('map_meta_cap', 'map_meta_cap', 10, 4);

		$this->add_filter('wp_title', 'fe_wp_title', 10, 2 );

		$this->add_action( 'add_meta_boxes', 'add_post_meta_box' );
		$this->add_action('fe_after_reported', 'fe_reported_email', 10, 2 );

		if( get_option('fe_live_notifications') ){
			// live notification
			$this->add_filter( 'heartbeat_settings', 'change_hearbeat_rate');
			$this->add_filter( 'heartbeat_send', 'send_data_to_heartbeat', 10, 2 );
			$this->add_action( 'et_insert_thread', 'store_new_question_to_DB');
		}

		//short codes
		new FE_Shortcodes();

		// enqueue script and styles
		if (is_admin()){
			$this->add_action('admin_enqueue_scripts', 'on_add_scripts');
			$this->add_action('admin_print_styles', 'on_add_styles');
		} else {
			$this->add_action('wp_enqueue_scripts', 'on_add_scripts');
			$this->add_action('wp_print_styles', 'on_add_styles');
		}

		// remove action deregister jquery
		//remove_action( 'wp_enqueue_scripts', 'et_deregister_jquery' );

	}

	/**
	 * Send email after report success
	 */
	public function fe_reported_email($thread_id, $report_message){
		global $current_user;
		if($thread_id && $report_message){
			$thread = get_post( $thread_id );
			$user_send 		= get_users( 'role=administrator' );
			foreach ( $user_send as $user ) {
				$user_email			=	$user->user_email;
				$FE_MailTemplate	=	new FE_MailTemplate();

				$message	=	$FE_MailTemplate->get_report_mail();

				/* ============ filter placeholder ============ */
				$message  	=	str_ireplace('[display_name]', $user->display_name, $message);
				$message  	=	str_ireplace('[thread_title]', $thread->post_title, $message);
				$message  	=	str_ireplace('[thread_content]', $thread->post_content, $message);
				$message  	=	str_ireplace('[thread_link]', get_permalink($thread_id), $message);
				$message  	=	str_ireplace('[report_message]',$report_message, $message);
				$message  	=	str_ireplace('[blogname]', get_option('blogname'), $message);
				$message	=	et_filter_authentication_placeholder ( $message, $current_user->ID);
				$subject	=	__("There's a new report.",ET_DOMAIN);

				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				$headers .= "From: ".get_option('blogname')." < ".get_option('admin_email') ."> \r\n";
				if($user_email ){
					wp_mail($user_email, $subject , $message, $headers) ;
				}
			}
		}
	}
	/**
	 * All about meta boxes in backend
	 */
	function add_post_meta_box(){
		add_meta_box( 'thread_info',
			__('Report Information', ET_DOMAIN),
			array($this, 'meta_box_view'),
			'report',
			'normal',
			'high' );
	}
	function meta_box_view($post){
		?>
		<p>Click this link below to view thread:</p>
		<p>
			<a href="<?php echo get_post_meta($post->ID, '_link_report', true) ?>">
				<?php echo get_post_meta($post->ID, '_link_report', true) ?>
			</a>
		</p>
		<?php
	}
	function fe_wp_title($title, $sep){
		global $paged, $page;

		if ( is_feed() )
			return $title;

		// Add the site name.
		$title .= get_bloginfo( 'name' );

		// Add the site description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			$title = "$title $sep $site_description";

		// Add a page number if necessary.
		if ( $paged >= 2 || $page >= 2 )
			$title = "$title $sep " . sprintf( __( 'Page %s', ET_DOMAIN ), max( $paged, $page ) );

		return $title;
	}
	/* ==================== LIVE NOTIFICATION ==================== */
	public function send_data_to_heartbeat($response, $data){

		global $wpdb, $current_user;

		$sql = $wpdb->prepare(
			"SELECT * FROM $wpdb->options WHERE option_name LIKE %s",
			'_transient_qa_notify_%'
		);

		$notifications = $wpdb->get_results( $sql );

		if(!empty($notifications)){
			foreach ( $notifications as $db_notification ) {

				$id = str_replace( '_transient_', '', $db_notification->option_name );

				if ( false !== ( $notification = get_transient( $id ) )  && $notification['user'] != md5( $current_user->user_login ) )
					$response['message'][ $id ] = $notification;
			}
		}

		return $response;
	}
	public function store_new_question_to_DB($post_id){
		$current_user = wp_get_current_user();
		if(get_post_type( $post_id ) != 'thread' && get_post_type( $post_id ) != 'reply' )
			return $post_id;
		if(get_post_type( $post_id ) == 'thread'){
			set_transient( 'qa_notify_' . mt_rand( 100000, 999999 ), array(
				'title'		=>		__( 'New Thread', ET_DOMAIN ),
				'content'	=>	 	__( 'There\'s a new post, why don\'t you give a look at', ET_DOMAIN ) .
									' <a href="' . get_permalink( $post_id ) . '">' . get_the_title( $post_id ) . '</a>',
				'type'		=>		'update',
				'user'		=>	md5( $current_user->user_login )
			), 20 );
		}
		else if(get_post_type( $post_id )  == 'reply'){
			$threads = get_option( 'fe_threads_new_reply' );
			$post = get_post( $post_id );
			$parent_id = $post->post_parent;
			if(!empty($threads)){
				foreach ($threads as $key => $id) {
					$last_author  = get_post_meta( $id, 'et_last_author', true );
					$users_follow = explode(',',get_post_meta($id,'et_users_follow',true));
					foreach ($users_follow as $key => $value) {
						$user 		= get_user_by('id',$value);
						if($value != $last_author){
							set_transient( 'qa_notify_' . mt_rand( 100000, 999999 ), array(
								'title'		=>		__( 'New Reply', ET_DOMAIN ),
								'content'	=>	 	__( 'There\'s a new reply, why don\'t you give a look at', ET_DOMAIN ) .
													' <a href="' . get_permalink( $parent_id ) . '">' . get_the_title( $parent_id ) . '</a>',
								'type'		=>		'update',
								'user'		=>	md5( $current_user->user_login )
							), 20 );
						}
					}
				}
			}
			else{
				return $post_id;
			}
		}
		return $post_id;
	}
	public function change_hearbeat_rate($settings){

		$settings['interval'] = 20;

		return $settings;
	}
	/* ==================== LIVE NOTIFICATION ==================== */
 	public function set_default_theme(){
		if(did_action( 'after_switch_theme' ) === 1){
			update_option( 'et_auto_expand_replies' , true );
			update_option( 'fe_send_following_mail' , true );
			update_option( 'avatar_default', 'identicon' );
			et_generate_templates();
		}
 	}

	public function set_static_front_page(){
		if(did_action( 'after_switch_theme' ) === 1){
			$front_id  = get_option('page_on_front');

			if ( empty($front_id) ){
				$front = wp_insert_post(array(
					'post_status' => 'publish',
					'post_type'   => 'page',
					'post_title'  => 'Threads Index'
				));
				update_option( 'page_on_front' , $front );
			}

			$posts_id  = get_option('page_for_posts');

			if (empty( $posts_id )){
				$post = wp_insert_post(array(
					'post_status' => "publish",
					'post_type'   => 'page',
					'post_title'  => 'Blog'
				));
				update_option( 'page_for_posts' , $post );
			}

			update_option( 'show_on_front' , "page" );
		}
	}

	public function et_widgets_init(){
		register_widget('FE_Thread_Category_Widget');
		register_widget('FE_Thread_Discuss_Widget');
		register_widget('FE_Thread_Hot_Widget');
		register_widget('FE_Statistic_Widget');
		register_widget('FE_Top_Users_Widget');
		register_widget('FE_Related_Threads_Widget');
		register_widget('FE_Hashtags_Widget');
	}
	public function retrieve_password_message($message , $active_key , $user_data) {
		$user_login 	=   $user_data->user_login;
		$FE_MailTemplate    = new FE_MailTemplate();
		$forgot_message	=	$FE_MailTemplate->get_forgot_pass_mail();
		$activate_url	= apply_filters('et_reset_password_link',  network_site_url("wp-login.php?action=rp&key=$active_key&login=" . rawurlencode($user_login), 'login'), $active_key, $user_login );

		$forgot_message	=	et_filter_authentication_placeholder ( $forgot_message, $user_data->ID );
		$forgot_message	=	str_ireplace('[activate_url]', $activate_url, $forgot_message);

		return $forgot_message;
	}
	public function password_reset_mail ( $user, $new_pass ) {
		$FE_MailTemplate    = new FE_MailTemplate();
		$new_pass_msg	=	$FE_MailTemplate->get_reset_pass_mail();

		$new_pass_msg	=	et_filter_authentication_placeholder($new_pass_msg, $user->ID);
		$new_pass_msg 	=	str_ireplace('[site_url]', home_url(), $new_pass_msg);
		//$new_pass_msg 	=	str_ireplace('[user_login]', $user->user_login, $new_pass_msg);

		$subject 		=	apply_filters('et_reset_pass_mail_subject',__('Password updated successfully!', ET_DOMAIN));

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "From: ".get_option('blogname')." < ".get_option('admin_email') ."> \r\n";

		//$new_pass_msg	=	et_get_mail_header().$new_pass_msg.et_get_mail_footer();
		wp_mail($user->user_email, $subject , $new_pass_msg, $headers);
	}
	public function update_user_meta( $user_id, $role = false) {
		$user = get_user_by( 'id',$user_id );
		update_user_meta( $user_id, 'et_like_count', 0 );
		if(get_option( 'user_confirm' ))
			update_user_meta( $user_id, 'register_status', 'unconfirm' );
		update_user_meta( $user_id, 'key_confirm', md5($user->user_email) );
	}
	public function user_register_mail( $user_id, $role = false) {

		$user			=   new WP_User($user_id);

		$user_email		=	$user->user_email;
		$FE_MailTemplate		=	new FE_MailTemplate();

		if(get_option( 'user_confirm' )){
			$message	=	$FE_MailTemplate->get_confirm_mail();
		} else {
			$message	=	$FE_MailTemplate->get_register_mail();
		}

		$message	=	et_filter_authentication_placeholder ( $message, $user_id );
		$subject		=	sprintf(__("Congratulations! You have successfully registered to %s.",ET_DOMAIN),get_option('blogname'));

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "From: ".get_option('blogname')." < ".get_option('admin_email') ."> \r\n";

		wp_mail($user_email, $subject , $message, $headers) ;

	}

	public function init_theme(){
		// post type
		FE_Threads::init();
		FE_Replies::init();
		FE_Member::init();

		//Register pages template
		et_register_page_template(array(
			'following' 		=> __('Following Threads', ET_DOMAIN),
			'pending' 			=> __('Pending Threads', ET_DOMAIN),
			'edit-profile' 		=> __('Edit Profile', ET_DOMAIN),
			'change-pass' 		=> __('Change Password', ET_DOMAIN),
			'new-pass' 			=> __('Create New Password', ET_DOMAIN),
			'social-connect'	=> __('Authentication', ET_DOMAIN),
			'member' 			=> __('Member', ET_DOMAIN),
			'term-condition'	=> __('Terms & Conditions', ET_DOMAIN),
		));

		// generate predefined template pages
		//et_generate_templates();
		// register footer menu
		register_nav_menus ( array (
			'et_header_menu'=>	__('Menu display on header',ET_DOMAIN),
			'et_footer'	=>	__('Menu display on the footer',ET_DOMAIN),
		));
		// register new post status: closed
		register_post_status( 'closed', array(
			'label' 				=> __('Closed',ET_DOMAIN),
			'public' 				=> true,
			'exclude_from_search' 	=> false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count'               =>  _n_noop( 'Closed <span class="count">(%s)</span>', 'Closed <span class="count">(%s)</span>',ET_DOMAIN ),
		) );

		//sidebars
		register_sidebar( array(
			'name' 			=> __('Homepage Sidebar', ET_DOMAIN),
			'id' 			=> 'fe-homepage-sidebar',
			'description' 	=> __("Display widgets in homepage's sidebar", ET_DOMAIN)
		) );
		register_sidebar( array(
			'name' 			=> __('Single Thread Sidebar', ET_DOMAIN),
			'id' 			=> 'fe-single-thread-sidebar',
			'description' 	=> __("Display widgets in single thread's sidebar", ET_DOMAIN)
		) );
		register_sidebar( array(
			'name' 			=> __('Single Post Sidebar', ET_DOMAIN),
			'id' 			=> 'fe-single-post-sidebar',
			'description' 	=> __("Display widgets in single post's sidebar", ET_DOMAIN)
		) );
		register_sidebar( array(
			'name' 			=> __('Blog Sidebar', ET_DOMAIN),
			'id' 			=> 'fe-blog-sidebar',
			'description' 	=> __("Display widgets in blog's sidebar", ET_DOMAIN)
		) );
		register_sidebar( array(
			'name' 	=> __('All Pages Sidebar', ET_DOMAIN),
			'id' 	=> 'fe-allpage-sidebar',
			'description' 	=> __("Displays widgets in every page's sidebar", ET_DOMAIN)
		) );


		// run activation in first run
		// update_option( 'forumengine_activation', 0 ); // reset activation
		// self::removeDefaulyRoles();
		if ( !get_option( 'forumengine_activation' ) ){

			// run functions for theme first activation
			$this->first_activation();

			// disable first activation hook
			update_option( 'forumengine_activation', 1 );
		}

		// check ban user
		global $current_user;
		$user_factory = FE_Member::get_instance();
		if ( $current_user->ID && $user_factory->is_ban( $current_user->ID ) ){
			wp_logout();
			wp_clear_auth_cookie();
		}
		/**
		 * create post type report
		*/
		$args = array(
			'labels' => array(
				'name'               => __('Reports', ET_DOMAIN ),
				'singular_name'      => __('Report', ET_DOMAIN ),
				'add_new'            => __('Add New', ET_DOMAIN ),
				'add_new_item'       => __('Add New Report', ET_DOMAIN ),
				'edit_item'          => __('Edit Report', ET_DOMAIN ),
				'new_item'           => __('New Report', ET_DOMAIN ),
				'all_items'          => __('All Reports', ET_DOMAIN ),
				'view_item'          => __('View Report', ET_DOMAIN ),
				'search_items'       => __('Search Reports', ET_DOMAIN ),
				'not_found'          => __('No Reports found', ET_DOMAIN ),
				'not_found_in_trash' => __('No Reports found in Trash', ET_DOMAIN ),
				'parent_item_colon'  => '',
				'menu_name'          => __('Reports', ET_DOMAIN )
			),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'report'),
			'capability_type'     => 'post',
			'has_archive'         => 'reports',
			'hierarchical'        => false,
			'menu_position'       => null,
			'supports'            => array( 'title', 'editor', 'author'),
			'taxonomies'          => array('report-taxonomy')
		);
		register_post_type( 'report', $args );

		$tax_labels = array(
			'name'                       => _x( 'Reports Type', ET_DOMAIN ),
			'singular_name'              => _x( 'Report Type', ET_DOMAIN ),
			'search_items'               => __( 'Search Reports Type', ET_DOMAIN ),
			'popular_items'              => __( 'Popular Reports Type', ET_DOMAIN ),
			'all_items'                  => __( 'All Reports Type', ET_DOMAIN ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Report Type', ET_DOMAIN ),
			'update_item'                => __( 'Update Report Type', ET_DOMAIN ),
			'add_new_item'               => __( 'Add New Report Type', ET_DOMAIN  ),
			'new_item_name'              => __( 'New Report Type Name', ET_DOMAIN ),
			'separate_items_with_commas' => __( 'Separate Reports Type with commas', ET_DOMAIN ),
			'add_or_remove_items'        => __( 'Add or remove Reports Type', ET_DOMAIN ),
			'choose_from_most_used'      => __( 'Choose from the most used Reports Type', ET_DOMAIN ),
			'not_found'                  => __( 'No Reports Type found.', ET_DOMAIN ),
			'menu_name'                  => __( 'Report Type', ET_DOMAIN ),
		);
		$tax_args = array(
			'hierarchical'          => true,
			'labels'                => $tax_labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'report-type' ),
		);
		register_taxonomy( 'report-taxonomy', 'report', $tax_args );

		//
		if(et_get_option('twitter_login', false))
			new ET_TwitterAuth();
		if(et_get_option('facebook_login', false)){
			new ET_FaceAuth();
		}
		if(et_get_option('gplus_login', false)){
			new ET_GoogleAuth();
		}
		global $max_file_size;
		$max_file_size = get_theme_mod('max_file_size', 5);
	}

	public function first_activation(){
		self::setupDefaultRoles();
	}

	public function map_meta_cap($caps, $cap, $user_id, $args){

		if ( 'edit_threads' == $cap || 'delete_threads' == $cap || 'read_threads' == $cap ) {
			if (empty($args[0])) return $caps;

			$post = get_post( $args[0] );
			$post_type = get_post_type_object( $post->post_type );
			$caps = array();
		}

		if ( 'edit_threads' == $cap ) {
			if ( $user_id == $post->post_author ){
				$caps[] = $post_type->cap->edit_posts;
				$caps[] = 'edit_threads';
			}
			else{
				$caps[] = $post_type->cap->edit_others_posts;
				$caps[] = 'edit_others_threads';
			}

		}

		elseif ( 'delete_threads' == $cap ) {
			if ( $user_id == $post->post_author )
				$caps[] = $post_type->cap->delete_posts;
			else
				$caps[] = $post_type->cap->delete_others_posts;
		}

		elseif ( 'read_threads' == $cap ) {
			if ( 'private' != $post->post_status )
				$caps[] = 'read';
			elseif ( $user_id == $post->post_author )
				$caps[] = 'read';
			else
				$caps[] = $post_type->cap->read_private_posts;
		}

		return $caps;

	}

	static function setupDefaultRoles(){
		// add role
		if ( !get_role('moderator')){
			// get subcribers capabilities
			$subscriber 	= get_role('subscriber');
			$caps 			= $subscriber->capabilities;

			// add some special cap
			$modcaps = array(
				'manage_threads' 		=> 1,
				'trash_threads' 		=> 1,
				'trash_others_threads' 	=> 1,
				'publish_threads' 		=> 1,
				'edit_others_threads' 	=> 1,
				'edit_thread' 			=> 1,
				'delete_threads' 		=> 1,
				'delete_others_threads' => 1,
				'delete_thread' 		=> 1,
				'read_private_threads' 	=> 1,

				'edit_reply' 			=> 1,
				'trash_others_replies' 	=> 1,
				'edit_others_replies' 	=> 1,
				'delete_other_replies' 	=> 1,
				'read_private_replies' 	=> 1,
				'read_replies'          => 1,
				'close_threads' 		=> 1,
			);
			$subcaps = array(
				'edit_threads' 		=> 1,
				'edit_thread' 		=> 1,
				'edit_replies' 		=> 1,
				'edit_reply' 		=> 1,
				'trash_replies' 	=> 1,
				'delete_replies' 	=> 1,
				'delete_reply' 		=> 1,
				'read_thread' 		=> 1,
				'read_reply' 		=> 1
			);

			$caps 		= (array)$caps + $modcaps + $subcaps;
			$newcaps 	= $modcaps + $subcaps;
			add_role( 'moderator', __('Moderator',ET_DOMAIN), $caps );

			// add caps to admin
			$admin_role = get_role('administrator');
			foreach ($newcaps as $cap => $value) {
				$admin_role->add_cap($cap);
			}

			// add user cap
			foreach ($subcaps as $cap => $value) {
				$subscriber->add_cap($cap);
			}
		}
	}

	static function removeDefaulyRoles(){
		// add some special cap
		$modcaps = array(
			'manage_threads' 		=> 1,
			'trash_threads' 		=> 1,
			'trash_others_threads' 	=> 1,
			'publish_threads' 		=> 1,
			'edit_others_threads' 	=> 1,
			'edit_thread' 			=> 1,
			'delete_threads' 		=> 1,
			'delete_others_threads' => 1,
			'delete_thread' 		=> 1,
			'read_private_threads' 	=> 1,

			'edit_reply' 			=> 1,
			'trash_others_replies' 	=> 1,
			'edit_others_replies' 	=> 1,
			'delete_other_replies' 	=> 1,
			'read_private_replies' 	=> 1,
			'close_threads' 		=> 1,
		);
		$subcaps = array(
			'edit_threads' 		=> 1,
			'edit_thread' 		=> 1,
			'edit_replies' 		=> 1,
			'edit_reply' 		=> 1,
			'trash_replies' 	=> 1,
			'delete_replies' 	=> 1,
			'delete_reply' 		=> 1,
			'read_thread' 		=> 1,
			'read_reply' 		=> 1
		);
		$allcaps = $modcaps + $subcaps;

		remove_role('moderator');

		$admin 		= get_role('administrator');
		$subscriber = get_role('subscriber');
		foreach ($allcaps as $cap => $value) {
			// remove cap in admin role
			$admin->remove_cap($cap);
			// remove cap in subscriber role
			if ( array_key_exists($cap, $subcaps) ){
				$subscriber->remove_cap($cap);
			}
		}
	}

	public function on_add_scripts(){
		global $current_user,$max_file_size;

		$isEditable = current_user_can( 'manage_threads' );
		$variables = array(
			'ajaxURL' 			=> admin_url('/admin-ajax.php'),
			'imgURL' 			=> TEMPLATEURL.'/img',
			'posts_per_page' 	=> get_option('posts_per_page'),
			'isEditable'	 	=> $isEditable,
			'isConfirm'			=> get_option( 'user_confirm' ),
			'gplus_client_id'	=> et_get_option("gplus_client_id"),
			'searchSummary' 	=> __('View all {{count}} results', ET_DOMAIN),
			'homeURL' 			=> home_url(),
			'plupload_config'			=> array(
				'max_file_size' 		=> $max_file_size.'mb',
				'url' 					=> admin_url('admin-ajax.php'),
				'flash_swf_url' 		=> includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url'	=> includes_url('js/plupload/plupload.silverlight.xap'),
				'filters' => array(
					'title' => 'Image Files',
					'extensions' => 'jpg,jpeg,gif,png,ico'
					),
				'msg' 	=> array ( 'FILE_EXTENSION_ERROR' => __('File extension error. Only allow  %s file extensions.',ET_DOMAIN),
						 'FILE_SIZE_ERROR'		=> __('Max file size %s.',ET_DOMAIN),
						 'FILE_DUPLICATE_ERROR'	=> __('File already present in the queue.', ET_DOMAIN),
						 'FILE_COUNT_ERROR' 	=> __('File count error.',ET_DOMAIN),
						 'IMAGE_FORMAT_ERROR' 	=> __('Image format either wrong or not supported.',ET_DOMAIN),
						 'IMAGE_MEMORY_ERROR' 	=> __('Runtime ran out of available memory',ET_DOMAIN),
						 'HTTP_ERROR' 			=> __('Upload URL might be wrong or doesn\'t exist.',ET_DOMAIN),
						)

			),
			'is_mobile' => et_load_mobile()
		);
		wp_enqueue_script('plupload-all');
		?>
		<script type="text/javascript">
			fe_globals = <?php echo json_encode($variables) ?>
		</script>
		<?php
	}
	public function on_add_styles(){}

	/**
	 * Write some method specified for forumengine only ...
	 */

	public function logout_home($logouturl, $redir)
	{
		$redir = get_option('siteurl');
		return $logouturl . '&amp;redirect_to=' . urlencode($redir);
	}
	public function reset_password_link($link, $key, $user_login){
		return et_get_page_link('reset-password', array('user_login' => $user_login, 'key' => $key));
	}
}

class FE_Shortcodes{
	public function __construct(){
		$this->add_shortcode( 'img', 'img' );
		$this->add_shortcode( 'quote', 'quote' );
		$this->add_shortcode( 'code', 'code' );

		do_action('et_add_shortcodes');
	}

	function img($atts, $content = ""){
		return '<a class="fe-zoom" href="'.$content.'"><img src="' . $content . '"></a>';
	}

	function code($atts, $content = ''){
		extract( shortcode_atts( array(
				'type' => 'php',
				'start' => 1,
				'highlight'=> ''
			), $atts ) );
		$content = preg_replace('#<br\s*/?>#i', "\n", $content);
		$content = str_replace("<br>", "\n", $content);
		$content = str_replace("<p></p>", "", $content);
		$content = str_replace("<p>", "", $content);
		$content = str_replace("</p>", "", $content);
		return '<pre class="ruler: true;brush: '.$type.';toolbar: false;highlight: ['.$highlight.'];first-line: '.$start.';">'.do_shortcode( $content ).'</pre>';
	}

	function quote($atts, $content = ''){
		extract( shortcode_atts( array(
				'author' => 'John Smith',
			), $atts ) );
		return '<blockquote>' . do_shortcode( $content ) . '<span class="quote-author">'.__('From',ET_DOMAIN).' <strong>'.$author.'</strong></span></blockquote>';
	}

	private function add_shortcode($name, $callback){
		add_shortcode( $name, array($this, $callback) );
	}
}

/**
 * process uploaded image: save to upload_dir & create multiple sizes & generate metadata
 * @param  [type]  $file     [the $_FILES['data_name'] in request]
 * @param  [type]  $author   [ID of the author of this attachment]
 * @param  integer $parent=0 [ID of the parent post of this attachment]
 * @param  array [$mimes] [array of supported file extensions]
 * @return [int/WP_Error]	[attachment ID if successful, or WP_Error if upload failed]
 * @author anhcv
 */
function et_process_file_upload( $file, $author=0, $parent=0, $mimes=array() ){

	global $user_ID;
	$author = ( 0 == $author || !is_numeric($author) ) ? $user_ID : $author;
	global $max_file_size;
	$max_size = $max_file_size*1024*1024;
	if( isset($file['name']) && $file['size'] > 0 && $file['size'] < $max_size){

		// setup the overrides
		$overrides['test_form']	= false;
		if( !empty($mimes) && is_array($mimes) ){
			$overrides['mimes']	= $mimes;
		}
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		// this function also check the filetype & return errors if having any
		$uploaded_file	=	wp_handle_upload( $file, $overrides );

		//if there was an error quit early
		if ( isset( $uploaded_file['error'] )) {
			return new WP_Error( 'upload_error', $uploaded_file['error'] );
		}
		elseif(isset($uploaded_file['file'])) {

			// The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload
			$file_name_and_location = $uploaded_file['file'];

			// Generate a title for the image that'll be used in the media library
			$file_title_for_media_library = preg_replace('/\.[^.]+$/', '', basename($file['name']));

			$wp_upload_dir = wp_upload_dir();

			// Set up options array to add this file as an attachment
			$attachment = array(
				'guid'				=> $uploaded_file['url'],
				'post_mime_type'	=> $uploaded_file['type'],
				'post_title'		=> $file_title_for_media_library,
				'post_content'		=> '',
				'post_status'		=> 'inherit',
				'post_author'		=> $author
			);

			// Run the wp_insert_attachment function. This adds the file to the media library and generates the thumbnails. If you wanted to attch this image to a post, you could pass the post id as a third param and it'd magically happen.
			$attach_id = wp_insert_attachment( $attachment, $file_name_and_location, $parent );

			$attach_data = wp_generate_attachment_metadata( $attach_id, $file_name_and_location );
			wp_update_attachment_metadata($attach_id,  $attach_data);
			return $attach_id;

		} else { // wp_handle_upload returned some kind of error. the return does contain error details, so you can use it here if you want.
			return new WP_Error( 'upload_error', __( 'There was a problem with your upload.', ET_DOMAIN ) );
		}
	}
	else { // No file was passed
		return new WP_Error( 'upload_error', __( 'Image\'s size upload must be less than %sMB!', ET_DOMAIN ), $max_file_size );
	}
}
/**
 * Print the content with shortcode
 */
function et_get_unread_follow(){
	global $user_ID;
	$data = array();
	if($user_ID){
		$unreads =  get_user_meta( $user_ID, 'et_unread_threads',true);
		$follows =  get_user_meta( $user_ID, 'et_following_threads',true);
		$follows_ur = array();

		if(!empty($follows)){
			foreach ($follows as $key => $value) {
				if(in_array($value, $unreads['data']))
					array_push($follows_ur, $value);
			}
		}
		$data['unread'] = (array)$unreads;
		$data['follow'] = $follows_ur;
	}
	return $data;
}
/**
 * Print the content with shortcode
 */
function et_the_content($more_link_text = null, $stripteaser = false){
	$content = get_the_content($more_link_text, $stripteaser);
	$content = apply_filters( 'et_the_content', $content );
	$content = str_replace(']]>', ']]&gt;', $content);
	echo $content;
}

add_filter('et_the_content', 'et_the_content_filter');
function et_the_content_filter($content){
	add_filter('the_content', 'do_shortcode', 11);
	$content = apply_filters( 'the_content', $content );
	remove_filter('the_content', 'do_shortcode');
	return $content;
}
add_filter( 'the_content', 'add_nofollow_blank_link');
function add_nofollow_blank_link( $content ) {

	$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>";
	if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) {
		if( !empty($matches) ) {

			$srcUrl = get_option('siteurl');
			for ($i=0; $i < count($matches); $i++)
			{

				$tag = $matches[$i][0];
				$tag2 = $matches[$i][0];
				$url = $matches[$i][0];

				$noFollow = '';

				$pattern = '/target\s*=\s*"\s*_blank\s*"/';
				preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
				if( count($match) < 1 )
					$noFollow .= ' target="_blank" ';

				$pattern = '/rel\s*=\s*"\s*[n|d]ofollow\s*"/';
				preg_match($pattern, $tag2, $match, PREG_OFFSET_CAPTURE);
				if( count($match) < 1 )
					$noFollow .= ' rel="nofollow" ';

				$pos = strpos($url,$srcUrl);
				if ($pos === false) {
					$tag = rtrim ($tag,'>');
					$tag .= $noFollow.'>';
					$content = str_replace($tag2,$tag,$content);
				}
			}
		}
	}

	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;

}
/**
 * Get editor default settings
 * @param array $args overwrite settings
 */
function editor_settings($args = array()){
	$buttons = apply_filters( 'fe_editor_buttons' , 'bold,|,italic,|,underline,|,bullist,|,link,unlink,|,feimage,fecode' );
	return array(
		'quicktags' 	=> false,
		'media_buttons' => false,
		'tabindex' 		=> 5,
		'wpautop' => false,
		'textarea_name' => 'post_content',
		'tinymce' 		=> array(
			'content_css' => TEMPLATEURL . '/css/tinyeditor-content.css',
			'height'                => 150,
			'autoresize_min_height' => 150,
			'force_p_newlines'      => false,
			'statusbar'             => false,
			'force_br_newlines'     => false,
			//'forced_root_block'     => '',
			'toolbar1'              => $buttons,
			'toolbar2'              => '',
			'toolbar3'              => '',
			'setup'                 => "function(ed) {
				ed.onChange.add(function(ed, l) {
                    var content = ed.getContent();
                    if(ed.isDirty() || content === '' ){
                        ed.save();
                        jQuery(ed.getElement()).blur(); // trigger change event for textarea
                    }

                });

                // We set a tabindex value to the iframe instead of the initial textarea
                ed.onInit.add(function() {
                    var editorId = ed.editorId,
                        textarea = jQuery('#'+editorId);
                    jQuery('#'+editorId+'_ifr').attr('tabindex', textarea.attr('tabindex'));
                    textarea.attr('tabindex', null);
                });
		   	}"
		)
	);
}

//add_filter( 'mce_buttons', 'fe_teeny_mce_buttons');
function fe_teeny_mce_buttons ($buttons) {
	if(!is_admin())
		return apply_filters( 'fe_editor_buttons' , array('bold','italic','underline','bullist','link','unlink','feimage','fecode') );
	else
		return $buttons;
}

function et_modify_breabcrumb($breadcrumb, $args){
	extract($args);
	if ( $class != '' ) $class = 'class="' . $class . '"';
	if ( $id != '' ) $id = 'id="' . $id . '"';
	if ( $item_class != '') $item_class = 'class="' . $item_class . '"';

	if ( is_tax( 'thread_category' ) || is_singular( 'thread' ) ){
		$breadcrumb = '';
		$breadcrumb .= '<ul class="breadcrumbs">';
		global $post;
		$terms = get_the_terms( $post->ID, 'thread_category' );

		if(!empty($terms)){
			foreach ($terms as $term) {
				$breadcrumb .= "<li $item_class><a href='" . get_term_link( $term, 'thread_category' ) . "'> $term->name</a></li>";
				break;
			}
			//the_terms( $post->ID, 'thread_category', '', '</li><li ' . $item_class .'> ' );
		} else {
			$breadcrumb .= "<li $item_class>" . __( 'No Category', ET_DOMAIN ) . '</li>';
		}

		if ( is_singular( 'thread' ) ){
			$breadcrumb .= '<li ' . $item_class .' >';
			$breadcrumb .= get_the_title();
			$breadcrumb .= '</li>';
		}
		$breadcrumb .= '</ul>';
	}
	return $breadcrumb;
}
//add_action('et_get_breadcrumb', 'et_modify_breabcrumb', 10, 2);

function et_filter_authentication_placeholder ($content, $user_id) {
		$user 		=	new WP_User ($user_id);

		$content 	=	str_ireplace('[user_login]'		, $user->user_login, $content);
		$content 	=	str_ireplace('[user_name]'		, $user->user_login, $content);
		$content 	=	str_ireplace('[user_nicename]'	, ucfirst( $user->user_nicename ), $content);
		$content 	=	str_ireplace('[user_email]'		, $user->user_email, $content);
		$content 	=	str_ireplace('[blogname]'		, get_bloginfo( 'name' ), $content);
		$content 	=	str_ireplace('[display_name]'	, ucfirst( $user->display_name ), $content);
		$content 	=	str_ireplace('[company]'		, ucfirst( $user->display_name ) , $content);
		$content 	=	str_ireplace('[dashboard]'		, et_get_page_link('dashboard'), $content);
		$content 	=	str_ireplace('[confirm_link]'	, add_query_arg(array('act' => 'confirm', 'key'=>md5($user->user_email)),home_url()), $content);

		return $content;
}

//check user can edit thread / reply or not
function user_can_edit($post){
	global $user_ID,$current_user;

	$now =  strtotime(current_time( 'mysql' ));
	$time_to_edit = apply_filters( 'fe_time_to_edit', 300 );

	if( current_user_can( "manage_threads" ) || ($user_ID == $post->post_author && ($now - strtotime($post->post_date) <= $time_to_edit))) return true;
	else return false;
}
/**
 * Edit WP_NAV_MENUs HTML list of nav menu items.
 *
 * @since 1.0
 * @uses Walker
 */
class AE_Custom_Walker_Nav_Menu extends Walker_Nav_Menu {
	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 3.0.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item   Menu item data object.
	 * @param int    $depth  Depth of menu item. Used for padding.
	 * @param array  $args   An array of arguments. @see wp_nav_menu()
	 * @param int    $id     Current item ID.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$custom_class = isset($item->classes[0]) ? $item->classes[0] : '';
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		/**
		 * Filter the CSS class(es) applied to a menu item's <li>.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array  $classes The CSS classes that are applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filter the ID applied to a menu item's <li>.
		 *
		 * @since 3.0.1
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $menu_id The ID that is applied to the menu item's <li>.
		 * @param object $item    The current menu item.
		 * @param array  $args    An array of wp_nav_menu() arguments.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names .'>';

		$atts = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target )     ? $item->target     : '';
		$atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
		$atts['href']   = ! empty( $item->url )        ? $item->url        : '';

		/**
		 * Filter the HTML attributes applied to a menu item's <a>.
		 *
		 * @since 3.6.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's <a>, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param object $item The current menu item.
		 * @param array  $args An array of wp_nav_menu() arguments.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		$item_output = $args->before;
		// $custom_icon = $custom_class ? '<i class="fa '.$custom_class.'"></i>' : '';
		$item_output .= '<a'. $attributes .'><span class="icon" data-icon="'.$custom_class.'"></span>';
		/** This filter is documented in wp-includes/post-template.php */
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		/**
		 * Filter a menu item's starting output.
		 *
		 * The menu item's starting output only includes $args->before, the opening <a>,
		 * the menu item's title, the closing </a>, and $args->after. Currently, there is
		 * no filter for modifying the opening and closing <li> for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @see wp_nav_menu()
		 *
		 * @param string $item_output The menu item's starting HTML output.
		 * @param object $item        Menu item data object.
		 * @param int    $depth       Depth of menu item. Used for padding.
		 * @param array  $args        An array of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
/**
 * Add custom menu to header
 */
add_action('fe_navigations', 'add_custom_menu');
function  add_custom_menu(){
	if(has_nav_menu('et_header_menu')){
		wp_nav_menu(array(
				'theme_location' => 'et_header_menu',
				'items_wrap' => '%3$s',
				'container' => '',
				'walker' =>  new AE_Custom_Walker_Nav_Menu()
			));
	}
}
/*
* Shorten long numbers to K / M / B
*
*/
function custom_number_format($n, $precision = 1) {
    // first strip any formatting;
    $n = (0+str_replace(",","",$n));

    // is this a number?
    if(!is_numeric($n)) return false;

    // now filter it;
    if($n >= 1000000000000) return round(($n/1000000000000),1).'T';
    else if($n >= 1000000000) return round(($n/1000000000),1).'B';
    else if($n >= 1000000) return round(($n/1000000),1).'M';
    else if($n >= 1000) return round(($n/1000),1).'K';

    return number_format($n);
}
?>
