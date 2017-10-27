<?php

/**
 * Contains some quick method for post type
 */
class ET_PostType extends ET_Base{

	static $instance;
	public $name;
	public $args;
	public $taxonomy_args;
	public $meta_data;

	function __construct($name, $args, $taxonomy_args, $meta_data){
		$this->name 			= $name;
		$this->args 			= $args;
		$this->taxonomy_args 	= $taxonomy_args;
		$this->meta_data 		= $meta_data;
	}

	/**
	 * Init post type by registering post type and taxonomy
	 */
	static public function _init($name, $args, $taxonomy_args){
		// register post type
		register_post_type(
			$name,
			$args
		);
		// register taxonomies
		if (!empty($taxonomy_args)){
			foreach ($taxonomy_args as $tax_name => $args) {
				register_taxonomy( $tax_name, array($name), $args );
			}
		}
	}

	protected function trip_meta($data){
        // trip meta datas
        $args = $data;
		$meta = array();
		foreach ($args as $key => $value) {
			if ( in_array($key, $this->meta_data) ){
				$meta[$key] = $value;
				unset($args[$key]);
			}
		}

		return array(
			'args' 	=> $args,
			'meta' 	=> $meta
		);
	}

	/**
	 * Insert post type data into database
	 */
	protected function _insert($args){
		global $current_user;

		$args = wp_parse_args( $args, array(
            'post_type'     => $this->name,
            'post_status'   => 'pending',
        ) );

        if(isset($args['author']) && !empty($args['author'])) $args['post_author'] = $args['author'];

		if ( empty($args['post_author']) ) return new WP_Error('missing_author', __('Missing Author', ET_DOMAIN));

        // filter args
        $args = apply_filters( 'et_pre_insert_' . $this->name, $args );

        $data = $this->trip_meta($args);

        $result = wp_insert_post( $data['args'], true );

		if ( !($result instanceof WP_Error) ){
			if (isset($args['tax_input']['thread_category']) && term_exists($args['tax_input']['thread_category'],'thread_category')){
				$terms = wp_set_object_terms($result, $args['tax_input']['thread_category'], 'thread_category');
			}
		}

        if ($result != false || !is_wp_error( $result )){
        	foreach ($data['meta'] as $key => $value) {
        		update_post_meta( $result, $key, $value );
        	}

        	// do action here
        	do_action('et_insert_' . $this->name, $result);
        }
        return $result;
	}

	/**
	 * Update post type data in database
	 */
	protected function _update($args){
		global $current_user;

		$args = wp_parse_args( $args );

		// filter args
        $args = apply_filters( 'et_pre_update_' . $this->name, $args );

		// if missing ID, return errors
        if (empty($args['ID'])) return new WP_Error('et_missing_ID', __('Thread not found!', ET_DOMAIN));

        // separate default data and meta data
        $data = $this->trip_meta($args);

    	// insert into database
        $result = wp_update_post( $data['args'], true );

		if ( !($result instanceof WP_Error) ){
			if (isset($args['tax_input']['thread_category']) && term_exists($args['tax_input']['thread_category'],'thread_category')){
				$terms = wp_set_object_terms($result, $args['tax_input']['thread_category'], 'thread_category');
			}
		}

        // insert meta data
        if ($result != false || !is_wp_error( $result )){
        	foreach ($data['meta'] as $key => $value) {
        		update_post_meta( $result, $key, $value );
        	}

        	// make an action so develop can modify it
        	do_action('et_update_' . $this->name, $result);
        }

        return $result;
	}

	protected function _delete($ID, $force_delete = false){

		if ( $force_delete ){
			$result = wp_delete_post( $ID, true );
		} else {
			$result = wp_trash_post( $ID );
		}
		if ( $result )
			do_action('et_delete_' . $this->name, $ID);

		return $result;
	}

	protected function _update_field($id, $field_name, $value){
		update_post_meta( $id, $field_name, $value );
	}

	protected function _get_field($id, $field_name){
		return get_post_meta( $id, $field_name, true );
	}

	/**
	 * Get post type data by ID
	 */
	public function _get($id, $raw = false){
		$post = get_post($id);
		if ( $raw )
			return $raw;
		else
			return $this->convert($post);
	}

	public function _convert($post, $taxonomy = true, $meta = true){
		$result = (array)$post;

		// generate taxonomy
		if ( $taxonomy ){
			foreach ($this->taxonomy_args as $name => $args) {
				$result[$name]	 = wp_get_object_terms( $result['ID'], $name );
			}
		}

		$post->post_content = $this->_filter_content($post->post_content);

		// generate meta data
		if ( $meta ){
			foreach ($this->meta_data as $key) {
				$result[$key] 	= get_post_meta( $result['ID'], $key, true );
			}
		}

		return (object)$result;
	}
	public function _filter_content ($content) {
		$pattern = "/<[^\/>]*>(&nbsp;)*([\s]?)*<\/[^>]*>/";  //use this pattern to remove any empty tag '<a target="_blank" rel="nofollow" href="$1">$3</a>'

		$content	=	 preg_replace($pattern, '', $content);


		$link_pattern = "/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/";
		$content	=	str_replace('<a', '<a target="_blank" rel="nofollow"', $content);
		$content	  =	 strip_tags( $content, '<p><a><ul><ol><li><h6><span><b><em><strong><br>');

		return $content;

	}
}

/**
 * Class FE_Threads
 */
class FE_Threads extends ET_PostType{
	CONST POST_TYPE = 'thread';

	static $instance = null;

	public function __construct(){
		$this->name = self::POST_TYPE;
		$this->args = array(
			'labels' => array(
			    'name' => __( 'Threads', ET_DOMAIN ),
			    'singular_name' => __('Thread', ET_DOMAIN ),
			    'add_new' => __('Add New', ET_DOMAIN ),
			    'add_new_item' => __('Add New Thread', ET_DOMAIN ),
			    'edit_item' => __('Edit Thread', ET_DOMAIN ),
			    'new_item' => __('New Thread', ET_DOMAIN ),
			    'all_items' => __('All Threads', ET_DOMAIN ),
			    'view_item' => __('View Thread', ET_DOMAIN ),
			    'search_items' => __('Search Threads', ET_DOMAIN ),
			    'not_found' =>  __('No threads found', ET_DOMAIN ),
			    'not_found_in_trash' => __('No threads found in Trash', ET_DOMAIN ),
			    'parent_item_colon' => '',
			    'menu_name' => __('Threads', ET_DOMAIN )
			),
		    'public' => true,
		    'publicly_queryable' => true,
		    'show_ui' => true,
		    'show_in_menu' => true,
		    'query_var' => true,
		    'rewrite' => array(
		    	'slug' => apply_filters( 'fe_thread_slug' , et_get_option('et_thread_slug', 'thread') )
		    	),
		    'capability_type' => 'post',
		    'capabilities' => array(
		    	'publish_posts' => 'publish_threads',
			    'edit_posts' => 'edit_threads',
			    'edit_others_posts' => 'edit_others_threads',
			    'delete_posts' => 'delete_threads',
			    'delete_others_posts' => 'delete_others_threads',
			    'read_private_posts' => 'read_private_threads',
			    'edit_post' => 'edit_thread',
			    'delete_post' => 'delete_thread',
			    'read_post' => 'read_threads'
		    	),
		    'has_archive' => 'threads',
		    'hierarchical' => false,
		    'menu_position' => null,
		    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' )
		);
		$this->taxonomies =  array(
			'thread_category' => array(
				'hierarchical'      => true,
				'labels'            => array(
					'name'              => __( 'Thread Categories', ET_DOMAIN ),
					'singular_name'     => __( 'Category', ET_DOMAIN ),
					'search_items'      => __( 'Search Categories', ET_DOMAIN ),
					'all_items'         => __( 'All Categories', ET_DOMAIN ),
					'parent_item'       => __( 'Parent Category', ET_DOMAIN ),
					'parent_item_colon' => __( 'Parent Category:', ET_DOMAIN ),
					'edit_item'         => __( 'Edit Category' , ET_DOMAIN),
					'update_item'       => __( 'Update Category', ET_DOMAIN ),
					'add_new_item'      => __( 'Add New Category' , ET_DOMAIN),
					'new_item_name'     => __( 'New Category Name', ET_DOMAIN ),
					'menu_name'         => __( 'Category' , ET_DOMAIN),
				),
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array(
					'slug' => apply_filters( 'fe_thread_cat_slug' , et_get_option('et_category_slug', 'thread-category') )
					),
			),
			'fe_tag'  => array(
				'hierarchical'          => false,
				'labels'                => array(
					'name'                       => _x( 'Tags', 'taxonomy general name' ),
					'singular_name'              => _x( 'Tag', 'taxonomy singular name' ),
					'search_items'               => __( 'Search Tags',ET_DOMAIN ),
					'popular_items'              => __( 'Popular Tags',ET_DOMAIN ),
					'all_items'                  => __( 'All Tags',ET_DOMAIN ),
					'parent_item'                => null,
					'parent_item_colon'          => null,
					'edit_item'                  => __( 'Edit Tag',ET_DOMAIN ),
					'update_item'                => __( 'Update Tag',ET_DOMAIN ),
					'add_new_item'               => __( 'Add New Tag',ET_DOMAIN ),
					'new_item_name'              => __( 'New Tag Name',ET_DOMAIN ),
					'separate_items_with_commas' => __( 'Separate tags with commas',ET_DOMAIN ),
					'add_or_remove_items'        => __( 'Add or remove tags',ET_DOMAIN ),
					'choose_from_most_used'      => __( 'Choose from the most used tags',ET_DOMAIN ),
					'not_found'                  => __( 'No tags found.',ET_DOMAIN ),
					'menu_name'                  => __( 'Tags',ET_DOMAIN ),
				),
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'hashtag' ),
			)
		);

		$this->meta_data = apply_filters( 'thread_meta_fields', array(
			'et_like_count',
			'et_updated_date',
			'et_likes',
			'et_replies_count',
			'et_last_author',
			'et_users_follow',
			'et_reply_authors',
			'et_users_report',
		));
		parent::__construct( self::POST_TYPE , $this->args, $this->taxonomies, $this->meta_data );
	}

	/**
	 *
	 */
	static public function init(){
		$instance = self::get_instance();

		// register post type
		ET_PostType::_init( self::POST_TYPE , $instance->args, $instance->taxonomies);

		// add meta boxes
		//add_action('add_meta_boxes'   ,  array( 'FE_Threads', 'add_meta_boxes'));
		add_action('et_insert_thread' ,  array( 'FE_Threads', 'save_following_threads') );
		add_action('et_insert_reply'  ,  array( 'FE_Threads', 'save_following_threads') );

		if(get_option('fe_send_following_mail' ))
			add_action('et_insert_reply'  ,  array( 'FE_Threads', 'fe_threads_new_reply') , 20 );

		add_action('untrashed_post'     ,	 array( 'FE_Threads', 'restore_thread_likes') );

	}

	static public function get_instance(){
		if ( self::$instance == null){
			self::$instance = new FE_Threads();
		}
		return self::$instance;
	}

	public static function insert($data){
		// required login
		global $user_ID;
		if ( !is_user_logged_in() )
			return new WP_Error('user_logged_required', __('Required Log In', ET_DOMAIN));

		// update tag link
		if(isset($data['post_content'])){
			$data['post_content'] = et_add_tag_links( $data['post_content'] );
		}

		//$data['post_title'] = substr($data['post_title'],0,90);
		$data['post_author'] = $user_ID;
		//print_r($data);die();
		$instance = self::get_instance();
		$return = $instance->_insert($data);

		// update counter for user
		if ( !is_wp_error( $return ) && $return && isset($data['post_author']) ){
			FE_Member::update_counter( $data['post_author'], 'thread' );
		}

		return $return;
	}

	public static function update($data){
		// update tag link

		if(isset($data['post_content'])){
			$data['post_content'] = et_add_tag_links( $data['post_content'] );
		}

		//if(isset($data['post_title'])) $data['post_title'] = substr($data['post_title'],0,90);

		// update thread category
		if (isset($data['thread_category'])){

			// change the input data
			$data['tax_input'] = array(
				'thread_category' => $data['thread_category']
			);
			unset($data['thread_category']);
		}

		// update old status
		$old_status = false;
		if ( $data['ID'] && !empty($data['post_status']) ) {
			$post 		= get_post($data['ID']);
			$old_status = $post->post_status;
		}

		$instance = self::get_instance();
		$return = $instance->_update($data);

		// update counter for user
		if ( !is_wp_error( $return ) && $return ){
			$post = get_post($return);
			FE_Member::update_counter( $post->post_author, 'thread' );

			// update old status
			if ( !empty($data['post_status']) ){
				self::update_field($post->ID, '_et_old_status', $old_status);
			}
		}

		return $return;
	}

	/**
	 * Delete a thread + reply of this thread
	 */
	public static function delete($id, $force_delete = false){
		global $user_ID;
		$post = get_post($id);

		if(!current_user_can( 'edit_others_threads' ) && $user_ID != $post->post_author) {
			return new WP_Error('permission_denied', __("Permission Denied!", ET_DOMAIN));
		}

		/* also delete thread's reply likes */
		$likes = (int) get_post_meta( $id, 'et_like_count', true );
		$replies = get_posts(array('post_parent' => $id,'post_type' => 'reply'));

		if($likes > 0)
			fe_update_user_likes($id, "delete", $likes);
		/* also delete thread's reply likes */

		if (is_array($replies) && count($replies) > 0) {

		    foreach($replies as $reply){
		    	/* also delete thread's reply likes */
		    	$likes = (int) get_post_meta( $reply->ID, 'et_like_count', true );
				if($likes > 0)
					fe_update_user_likes($reply->ID, "delete", $likes);
				/* also delete thread's reply likes */
		    	if($force_delete){
		        	wp_delete_post($reply->ID, $force_delete);
		    	} else {
		    		wp_trash_post( $reply->ID );
		    	}
		    	FE_Member::update_counter( $reply->post_author, 'reply');
		    }
		}

		if($force_delete){
			if ( wp_delete_post( $id, $force_delete ) != false)
				do_action('et_delete_' . self::POST_TYPE, $id);
		} else {
			if ( wp_trash_post( $id ) != false)
				do_action('et_delete_' . self::POST_TYPE, $id);
		}
		FE_Member::update_counter( $post->post_author, 'thread');
		FE_Member::update_unread($id);
	}

	public static function get($id){
		return	self::get_instance()->_get($id);
	}

	public static function convert($post){
		global $current_user;
		$result = self::get_instance()->_convert($post);
		$badges = get_option( 'fe_user_badges' );
		$user_role = get_user_role($post->post_author);

		$result->fe_tag 			= wp_get_object_terms( $post->ID, 'fe_tag' );
		$result->et_likes 			= is_array($result->et_likes) ? $result->et_likes : array();
		$result->et_likes_count 	= count($result->et_likes);
		$result->et_reply_authors 	= is_array($result->et_reply_authors) ? $result->et_reply_authors : array();
		$result->et_replies_count 	= $result->et_replies_count ? $result->et_replies_count : 0;
		$result->liked 				= in_array($current_user->ID, $result->et_likes);
		$result->replied 			= in_array($current_user->ID, (array)$result->et_reply_authors);
		$result->reported  			= in_array($current_user->ID,(array)get_post_meta($post->ID, 'et_users_report', true ));
		$result->has_category 		= !empty($result->thread_category);
		$result->user_badge   		= get_option( 'user_badges' ) && isset($badges[$user_role]) && $user_role ? '<span class="user-badge">'.$badges[$user_role].'</span>' : '';
		$result->avatar   			= et_get_avatar($result->post_author);
		$result->et_updated_date_string 	= sprintf( __( 'Updated %s in', ET_DOMAIN ), et_the_time(strtotime($result->et_updated_date)) );
		$result->et_get_last_page   = et_get_last_page($result->ID);

		$result->author_badge 		= isset($badges[get_user_role($result->post_author)]) && get_user_role($result->post_author) ? $badges[get_user_role($result->post_author)] : '';

		foreach ( $result->thread_category as $category ) {
			$category->color = FE_ThreadCategory::get_category_color($category->term_id); //$color[$category->term_id];
			$category->link  = get_term_link( $category, 'thread_category' );
		}

		if ( !empty($result->et_last_author) ){
			$et_user = new FE_Member();
			$user_data = get_userdata( $result->et_last_author );
			$user_data = $et_user->convert($user_data);
			$result->et_last_author = $user_data;
		}
		else
			$result->et_last_author = false;

		if(isset($result->post_password)){
			unset($result->post_password);
		}
		return apply_filters('fe_thread_response', $result);
	}

	public static function get_last_author($post_id){

	}

	/**
	 * Refresh thread's meta
	 */
	public static function update_meta($id){
		// refresh last update
		$last_replies = get_posts(array(
			'post_type' 	=> 'reply',
			'post_parent' 	=> $id,
			'numberposts' 	=> 1
		));

		if ( isset($last_replies[0]) ){
			$last_reply = $last_replies[0];

			// update last reply author
			update_post_meta( $id, 'et_last_author', $last_reply->post_author );
		} else {
			delete_post_meta( $id, 'et_last_author' );
		}
	}

	/**
	 * Additional methods in theme
	 */
	public static function change_status($id, $new_status){
		$available_statuses = array('pending', 'publish', 'trash');

		if (in_array($new_status, $available_statuses))
			return self::update(array(
				'ID' => $id,
				'post_status' => $new_status
			));
		else
			return false;
	}

	// add new thread
	public static function insert_thread($title, $content, $category, $status = "publish" , $author = 0){
		global $current_user;
		// if ( !$author && $current_user->ID == 0 )
		// 	return false;



		// check post_title is contain HTML
		if( $title != strip_tags($title) ){
			return new WP_Error('htm_detect', __('You can not insert HTML tag to Thread title.', ET_DOMAIN));
		}

		/*$content = preg_replace('/\[code\].*(<br\s*\/?>\s*).*\[\/code\]/', '\n', $content);
		*/
		if ( empty($category) ) return new WP_Error(__('Category must not empty', ET_DOMAIN));
		if ( !term_exists($category, 'thread_category') ) return new WP_Error(__('Category doest not exist', ET_DOMAIN));
		if ( empty($title) ) return new WP_Error(__('Title must not empty', ET_DOMAIN));

		$data = array(
			'post_title' 		=> $title,
			'post_content' 		=> et_add_tag_links( $content ),
			'post_type' 		=> self::POST_TYPE,
			'post_author' 		=> !$author ? $current_user->ID : $author,
			'post_status' 		=> $status,//'publish', // auto set publish for posts,
			'tax_input'			=> array(
				'fe_tag' 		=> et_generate_tag($content),
				'thread_category' 	=> $category
			),
			'et_updated_date' 		=> current_time( 'mysql' ),

		);
		//print_r($data);die();
		return self::insert($data);
	}

	// add like into database
	public static function toggle_like($thread_id, $author = false){
		global $current_user;
		// required logged in
		if ( !$current_user->ID ) return false;

		// auto author
		if ( !$author ) $author = $current_user->ID;

		// get current likes list
		$likes = get_post_meta( $thread_id, 'et_likes', true );

		// clear array
		if (!is_array($likes)) $likes = array();

		// add new author id
		$index = array_search($author, $likes);

		if ( $index === false){
			//$likes[] = $author;
			array_unshift($likes, $author);
			fe_update_user_likes($thread_id);
		} else {
			foreach ($likes as $i => $id) {
				if ( $id == $author )
					unset($likes[$i]);
			}
			fe_update_user_likes($thread_id,'unlike');
		}

		// update to database
		update_post_meta( $thread_id, 'et_likes', $likes);
		update_post_meta( $thread_id, 'et_like_count', count($likes));

		return $likes;
	}

	public static function report($id, $report_data){
		global $current_user;
		// required logged in
		if ( !$current_user->ID || !$id  || !$report_data) return false;

		$post_type = get_post_type($id);
		$post      = get_post($id);
		$report_tx = '';

		switch ($post_type) {
			case 'thread':
				$title     = $post->post_title;
				$link      = $post->guid;
				$report_tx = 'report-thread';
				break;
			case 'reply':
				$thread    = get_post($post->post_parent);
				$title     = $thread->post_title;
				$link      = $thread->guid;
				$report_tx = 'report-reply';
				break;
			default:
				$report_tx = 'untaxonomy';
				break;
		}

		$content = '<p>Post Content:</p>
					<p>'.$post->post_content.'</p>
					<p>|----------------------------------------------|</p>
					<p>Message:</p>
					<p>'.$report_data.'</p>';

		$my_post = array(
			  'post_title'    => 'REPORT#',
			  'post_content'  => $content,
			  'post_status'   => 'publish',
			  'post_author'   => $current_user->ID,
			  'post_type'     => 'report',
			);

		// Insert the post into the database
		$users_report = (array)get_post_meta( $id, 'et_users_report',true);

		if(!in_array($current_user->ID,$users_report)){
			$result = wp_insert_post( $my_post );
			$m_post = array(
					'ID'         => $result,
					'post_title' => '[REPORT#'.$result.']'.$title
				 );
			wp_update_post($m_post);
			wp_set_object_terms( $result, $report_tx, 'report-taxonomy' );
			if($result){
				array_push($users_report, $current_user->ID);
				update_post_meta( $id, 'et_users_report', $users_report );
				update_post_meta( $result, '_link_report', $link );
			}
			do_action('fe_after_reported', $id, $report_data);
		} else {
			$result = false;
		}

		if($result)
			return true;
		else
			return false;
	}
	public static function close($thread_id){
		global $current_user;

		if ( !current_user_can( 'close_threads' ) ) return new WP_Error('permission_denied', __('Permission denied', ET_DOMAIN));

		//
		$result = FE_Threads::update( array(
			'ID' 			=> $thread_id,
			'post_status' 	=> 'closed'
		) );

		return $result;
	}

	/**
	 * Retrieve comment number of a thread and save to database
	 */
	public static function count_comments($thread_id){
		global $wpdb;

		$sql 	= "SELECT count(*) FROM {$wpdb->posts} WHERE post_parent = $thread_id AND post_type = 'reply' AND post_status = 'publish'";
		$count 	= $wpdb->get_var($sql);

		// save
		update_post_meta($thread_id, 'et_replies_count', (int) $count);

		return $count;
	}

	public static function update_field($id, $key, $value){
		$instance = self::get_instance();

		$instance->_update_field($id, $key, $value);
	}

	public static function get_field($id, $key){
		$instance = self::get_instance();

		return $instance->_get_field($id, $key);
	}

	// search
	public static function search($data){
		$data = wp_parse_args( $data, array(
			'post_type' 	=> array(self::POST_TYPE),
			'post_status' 	=> apply_filters('fe_thread_status', array('publish','closed'))
		) );

		if ($data['s']){
			global $et_query;
			$et_query['s'] = explode(' ', $data['s']);
			//unset($data['s']);
		}

		add_filter('posts_distinct', array('FE_Threads', 'query_distinct'));
		// add_filter('posts_join', array('FE_Threads', 'query_reply_join'));
		// add_filter('posts_where', array('FE_Threads', 'query_reply_where'));
		// add_filter('posts_orderby', array('FE_Threads', 'query_reply_orderby'));
		$query = new WP_Query($data);
		remove_filter('posts_distinct', array('FE_Threads', 'query_distinct'));
		// remove_filter('posts_join', array('FE_Threads', 'query_reply_join'));
		// remove_filter('posts_join', array('FE_Threads', 'query_reply_where'));
		// remove_filter('posts_orderby', array('FE_Threads', 'query_reply_orderby'));
		return $query;
	}

	/**
	 * Add a thread category and colors
	 * @param $name category name
	 * @param $color category color, a hex code
	 * @param $parent parent category id, this is optional
	 * @return return array of term id and taxonomy
	 */
	public static function add_category($name, $color, $parent = 0){
		if ( $parent )
			$result = wp_insert_term( $name, 'thread_category', array('parent' => $parent));
		else
			$result = wp_insert_term( $name, 'thread_category');

		if ( !is_wp_error( $result ) ){
			$colors 					= get_option('et_category_colors', array());
			$colors[$result['term_id']] = (int)$color;

			update_option('et_category_colors', $colors);
		}

		return $result;
	}

	/**
	 * Edit a thread category
	 * @param int $id term id
	 * @param array $args argument contain new values (name and color)
	 */
	public static function update_category($id, array $args){
		if (!empty($args)){
			// update normal params
			if ( !empty($args['name']) ){
				wp_update_term( $id, 'thread_category', array('name' => $args['name']) );
			}

			// update color
			if ( !empty($args['color']) ){
				$colors 					= get_option('et_category_colors', array());
				$colors[$result['term_id']] = $color;

				update_option('et_category_colors', $colors);
			}
		}
	}

	/**
	 * Delete category
	 * @param int $term_id
	 * @param int $alternative term id
	 */
	public static function delete_category($term_id, $alternative){

		wp_delete_term( $term_id, 'thread_category', array('default' => $alternative) );

	}

	/**
	 * get categories with colors
	 * @param array $args
	 */
	public static function get_categories(array $args = array()){
		$terms = get_terms( 'thread_category', $args );

		if ( !is_wp_error( $terms ) ){
			$colors = get_option('et_category_colors', array());

			foreach ((array)$terms as $key => $term) {
				if ( isset($colors[$term->term_id]) )
					$terms[$key]->color = $colors[$term->term_id];
				else
					$terms[$key]->color = 0;
			}
		}

		return $terms;
	}

	public static function query_distinct($distinct){
		return "DISTINCT";
	}

	public static function query_reply_join($join){
		global $wpdb;
		$join .= " LEFT JOIN {$wpdb->posts} AS et_reply ON et_reply.post_parent = {$wpdb->posts}.ID AND et_reply.post_type = '" . FE_Replies::POST_TYPE . "' ";
		return $join;
	}

	public static function query_reply_where($where){
		global $wpdb, $et_query;

		if (!empty($et_query['s'])){
			$q = array();
			foreach ($et_query['s'] as $value) {
				$q[] = " ({$wpdb->posts}.post_title LIKE '%{$value}%') OR ({$wpdb->posts}.post_content LIKE '%{$value}%') OR (et_reply.post_content LIKE '%{$value}%') ";
			}

			$where .= " AND (" . implode(' OR ', $q) .") ";
		}
		return $where;
	}

	public static function query_reply_orderby($order){
		global $wpdb, $et_query;

		if (!empty($et_query['s'])){
			$q = array();
			foreach ($et_query['s'] as $value) {
				$q[] = " ( CASE WHEN ({$wpdb->posts}.post_title LIKE '%{$value}%') OR ({$wpdb->posts}.post_content LIKE '%{$value}%') OR (et_reply.post_content LIKE '%{$value}%') THEN 1 ELSE 0 END ) ";
			}
			$order = '(' . implode(' + ', $q) . ') DESC, ' . $wpdb->posts .'.post_date DESC';
		}
		return $order;
	}

	/**
	 * All about meta boxes in backend
	 */
	static public function add_meta_boxes(){
		add_meta_box( 'thread_info',
			__('Thread Information', ET_DOMAIN),
			array('FE_Threads', 'meta_view'),
			self::POST_TYPE,
			'normal',
			'high' );
	}
	/**
	 * Restore all Likes when undo trash thread
	 */
	static public function restore_thread_likes($id){
		if(get_post_type( $id ) == "thread"){
			$post = get_post($id);
			$likes = (int) get_post_meta( $id, 'et_like_count', true );

			if($likes > 0)
				fe_update_user_likes($id, "undo", $likes);

			$replies = get_posts(array('post_parent' => $id,'post_type' => 'reply'));

			if (is_array($replies) && count($replies) > 0) {

			    foreach($replies as $reply){
			    	$likes = (int) get_post_meta( $reply->ID, 'et_like_count', true );
					if($likes > 0)
						fe_update_user_likes($reply->ID, "undo", $likes);
			    	FE_Member::update_counter( $reply->post_author, 'reply');
			    }
			}
			FE_Member::update_counter( $post->post_author, 'thread');
		}
	}

	static public function fe_threads_new_reply($id){
		$token = md5($id . time());
		update_post_meta( $id, 'notify_follower_token', $token );
		$url = add_query_arg(array('reply_id' => $id, 'token' => $token), home_url());

		wp_remote_post($url, array('blocking' => false , 'timeout' => 1));
	}

	public static function new_reply_email_follower() {
		if( isset( $_REQUEST['reply_id']) && isset($_REQUEST['token']) ) {

			global $current_user;
			$reply_id = $_REQUEST['reply_id'];

			$reply 		  = get_post( $reply_id );

			// make sure reply id is exist and current user is reply owner
			if( is_wp_error( $reply ) ) wp_die('Invalid reply');
			// check the token
			$token = get_post_meta( $reply_id, 'notify_follower_token', true );
			if($_REQUEST['token'] != $token) wp_die('Invalid Token');


			$id 		  = $reply->post_parent;

			$send_following_mail = get_option( 'fe_send_following_mail' );
			$last_author  = get_post_meta( $id, 'et_last_author', true );
			$users_follow = explode(',',get_post_meta($id,'et_users_follow',true));

			$from_email = apply_filters( 'fe_new_reply_from_email', get_option('admin_email') );
			$headers    = 'MIME-Version: 1.0' . "\r\n";
			$headers    .= 'Content-type: text/html; charset=utf-8' . "\r\n";
			$headers    .= "From: ".get_option('blogname')." < ".$from_email."> \r\n";

			$subject = sprintf(__('[New Reply] Your thread  %s has a new reply.',ET_DOMAIN), get_the_title($id) );
			$subject = apply_filters('fe_send_following_mail_title', $subject, $id );

			$FE_MailTemplate = new FE_MailTemplate();
			$message         = $FE_MailTemplate->get_following_thread_mail();
			$message         = apply_filters('fe_send_following_mail_template', $message, $id );

			/* ============ filter placeholder ============ */
			$message = str_ireplace('[display_name]', __("there", ET_DOMAIN), $message);
			$message = str_ireplace('[thread_title]', get_the_title($id), $message);
			$message = str_ireplace('[thread_link]', get_permalink($id), $message);
			$message = str_ireplace('[reply_content]', $reply->post_content, $message);
			$message = str_ireplace('[blogname]', get_option('blogname'), $message);


			$follower_list = array();
			foreach ($users_follow as $key => $value) {
				$user = get_user_by('id',$value);
				/* ============ filter placeholder ============ */
				if($user->user_email && $value == $reply->post_author) continue;
				array_push($follower_list, $user->user_email);
			}

			$bcc = implode($follower_list , ',');
			$headers .= 'BCC: ' . $bcc;

			$a = wp_mail( '' , $subject , $message, $headers);
			// remove the token
			delete_post_meta( $reply_id, 'notify_follower_token', $token ) ;
			wp_die('Finish');
		}


	}
	/**
	 * Save thread id to following to usermeta
	 */
	static public function save_following_threads($id){
		global $user_ID;

		if(get_post_type($id) != "reply" && get_post_type($id) != "thread") {return '';}

			if(get_post_type($id) == "reply"){
				$reply = get_post( $id );
				$id = $reply->post_parent;
			}

			$users_follow = explode(',',get_post_meta($id,'et_users_follow',true));

			if(!in_array($user_ID, $users_follow)){
				$users_follow[] = $user_ID;
			}
			$users_follow = array_unique(array_filter($users_follow));
			$users_follow = implode(',', $users_follow);
			FE_Threads::update_field($id, 'et_users_follow', $users_follow);
	}

	static public function meta_view(){

	}

	static public function save_meta_fields(){

	}

	public static function get_threads($args = array()){
		// modify query
		add_action('posts_join', array('ET_ForumFront', '_thread_join'));
		add_action('posts_orderby', array('ET_ForumFront', '_thread_orderby'));

		if ( isset($args['orderby']) ){
			global $et_meta_order;

			$et_meta_order = array( 'meta_key' => $args['meta_key'], 'order' => 'ASC' );
			unset( $args['meta_key'] , $args['orderby']);

			add_action('posts_join', array('FE_Threads', 'meta_order_join'), 20);
			add_action('posts_orderby', array('FE_Threads', 'meta_order_orderby'), 20);
		}
		$args = wp_parse_args(  $args, array(
			'post_type'   => 'thread'
		) );
		$query = new WP_Query($args);
		// remove modified query
		remove_action('posts_join', array('ET_ForumFront', '_thread_join'));
		remove_action('posts_orderby', array('ET_ForumFront', '_thread_orderby'));

		remove_action('posts_join', array('FE_Threads', 'meta_order_join'));
		remove_action('posts_orderby', array('FE_Threads', 'meta_order_orderby'));
		return $query;
	}

	/**
	 *
	 */
	public static function meta_order_join($join){
		global $et_meta_order, $wpdb;
		return $join . " LEFT JOIN {$wpdb->postmeta} as meta_order ON meta_order.post_id = {$wpdb->posts}.ID AND meta_order.meta_key = '{$et_meta_order['meta_key']}' ";
	}

	/**
	 *
	 */
	public static function meta_order_orderby($orderby){
		global $et_meta_order, $wpdb;
		return " meta_order.meta_value DESC, " . $orderby;
	}
}

/**
 * Replies class
 */
class FE_Replies extends ET_PostType{
	CONST POST_TYPE = 'reply';

	static $instance = null;

	public function __construct(){
		$this->name = self::POST_TYPE;
		$this->args = array(
			'labels' => array(
			    'name' => __('Replies', ET_DOMAIN),
			    'singular_name' => __('Reply', ET_DOMAIN),
			    'add_new' => __('Add New', ET_DOMAIN),
			    'add_new_item' => __('Add New Reply', ET_DOMAIN),
			    'edit_item' => __('Edit Reply', ET_DOMAIN),
			    'new_item' => __('New Reply', ET_DOMAIN),
			    'all_items' => __('All Replies', ET_DOMAIN),
			    'view_item' => __('View Reply', ET_DOMAIN),
			    'search_items' => __('Search Replies', ET_DOMAIN),
			    'not_found' =>  __('No replies found', ET_DOMAIN),
			    'not_found_in_trash' => __('No replies found in Trash', ET_DOMAIN),
			    'parent_item_colon' => '',
			    'menu_name' => __('Replies', ET_DOMAIN)
			),
		    'public' => true,
		    'publicly_queryable' => true,
		    'show_ui' => true,
		    'show_in_menu' => true,
		    'query_var' => true,
		    'rewrite' => array( 'slug' => apply_filters( 'fe_reply_slug' , 'reply' )),
		    'capability_type' => 'post',
		    'capabilities' => array(
		    	'publish_posts' => 'publish_replies',
			    'edit_posts' => 'edit_replies',
			    'edit_others_posts' => 'edit_others_replies',
			    'delete_posts' => 'delete_replies',
			    'delete_others_posts' => 'delete_others_replies',
			    'read_private_posts' => 'read_private_replies',
			    'edit_post' => 'edit_replies',
			    'delete_post' => 'delete_replies',
			    'read_post' => 'read_replies'
		    	),
		    'has_archive' => 'replies',
		    'hierarchical' => false,
		    'menu_position' => null,
		    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' )
		);
		$this->taxonomies = array();
		$this->meta_data = apply_filters( 'reply_meta_fields', array('et_like_count', 'et_updated_date', 'et_likes', 'et_reply_parent', 'et_replies_count', 'et_reply_authors'));

		parent::__construct( self::POST_TYPE , $this->args, $this->taxonomies, $this->meta_data );
	}

	static public function init(){
		$instance = self::get_instance();

		// register post type
		parent::_init( self::POST_TYPE , $instance->args, $instance->taxonomies);

		// add meta boxes
		//add_action('add_meta_boxes', array( 'FE_Replies', 'add_meta_boxes'));

		//
		$instance->add_action( 'after_delete_post', 'action_delete_post' );
		$instance->add_action( 'save_post', 'action_update_counter' , 10, 2);
	}

	/**
	 * Action trigger after delete post
	 */
	public function action_delete_post($post_id){
		$post = get_post($post_id);
		if ( !empty($post) && $post->post_status == self::POST_TYPE )
			$this->action_update_counter($post_id, $post);
	}

	/**
	 * action trigger after update post
	 */
	public function action_update_counter($post_id, $post){
		if ( $post->post_type != self::POST_TYPE ) return;

		// get reply parent
		$reply_parent 	= get_post_meta( $post_id, 'et_reply_parent', true );

		if ( $reply_parent ){
			FE_Replies::count_comments($reply_parent);
		} else {
			FE_Threads::count_comments($post->post_parent);
		}
	}

	static public function get_instance(){
		if ( self::$instance == null){
			self::$instance = new FE_Replies();
		}
		return self::$instance;
	}

	public static function insert($data){
		// update tag link
		global $user_ID;
		$data['post_content'] 	= et_add_tag_links( $data['post_content'] );
		$tags 					= et_generate_tag( $data['post_content'] );
		$data['post_author'] 	= $user_ID;
		// perform action
		$instance 	= self::get_instance();
		$result 	=  $instance->_insert($data);

		// update tag for parent thread
		if ( !is_wp_error( $result ) && $result && !empty( $tags ) ){
			$reply 		= get_post($result)	;
			$thread 	= get_post($reply->post_parent);
			wp_set_object_terms( $thread->ID, $tags, 'fe_tag', true );
		}

		return $result;
	}

	public static function update($data){
		// update tag link
		$data['post_content'] = et_add_tag_links( $data['post_content'] );
		$tags 				= et_generate_tag( $data['post_content'] );

		$instance = self::get_instance();
		$result =  $instance->_update($data);

		// update tag for parent thread
		if ( !is_wp_error( $result ) && $result && !empty( $tags ) ){
			$reply 		= get_post($result)	;
			$thread 	= get_post($reply->post_parent);
			wp_set_object_terms( $thread->ID, $tags, 'fe_tag', true );
		}

		return $result;
	}

	/**
	 * Delete a reply and child replies
	 * @param int $id
	 * @param bool $force_delete
	 * @return bool $success
	 */
	public static function delete($id, $force_delete = false){
		global $user_ID;
		$instance = self::get_instance();

		$parent_reply = get_post($id);
		if(!current_user_can( 'edit_others_threads' ) && $user_ID != $parent_reply->post_author) {
			return new WP_Error('permission_denied', __("Permission Denied!", ET_DOMAIN));
		}

		$parent_reply = FE_Replies::convert($parent_reply);

		/* also delete thread likes */
		$likes = (int) get_post_meta( $id, 'et_like_count', true );
		if($likes > 0)
			fe_update_user_likes($id, "delete", $likes);
		/* also delete thread likes */
		$replies = get_posts(array('post_type' => 'reply','meta_key' => 'et_reply_parent' , 'meta_value' => $id));

		if (is_array($replies) && count($replies) > 0) {

		    foreach($replies as $reply){

		    	/* also delete thread's reply likes */
		    	$likes = (int) get_post_meta( $reply->ID, 'et_like_count', true );

				/* also delete thread's reply likes */
				if($likes > 0)
					fe_update_user_likes($reply->ID, "delete", $likes);

				update_post_meta($p_reply->ID,'et_reply_authors',(array)$p_reply->et_reply_authors);

		    	if($force_delete){
		        	wp_delete_post($reply->ID, $force_delete);
		    	} else {
		    		wp_trash_post( $reply->ID );
		    	}
				FE_Member::update_counter( $reply->post_author, 'reply');
		    }
		}

		$success = $instance->_delete($id, $force_delete);

		// refresh thread's data
		if(get_post_type( $parent_reply->et_reply_parent ) == "reply"){
			$post = get_post($parent_reply->et_reply_parent);
			$thread = FE_Replies::convert($post);
		} else {
			$post = get_post($parent_reply->post_parent);
			$thread = FE_Threads::convert($post);
		}

		fe_update_status_replied($thread,$parent_reply);

		FE_Member::update_counter( $parent_reply->post_author, 'reply');
		FE_Threads::update_meta($parent_reply->post_parent);

		return $success;
	}

	public static function get($id){
		return	self::get_instance()->_get($id);
	}

	public static function convert($post){
		global $current_user;
		$result = self::get_instance()->_convert($post);
		$badges = get_option( 'fe_user_badges' );
		$user_role = get_user_role($post->post_author);

		$result->fe_tag 			= wp_get_object_terms( $post->ID, 'fe_tag' );
		$result->et_likes 			= !is_array($result->et_likes) ? array() : (array)$result->et_likes;
		$result->et_likes_count 	= count($result->et_likes);
		$result->et_replies_count 	= $result->et_replies_count ? $result->et_replies_count : 0;
		$result->et_reply_authors 	= is_array($result->et_reply_authors) ? $result->et_reply_authors : array();
		$result->liked 				= in_array($current_user->ID, $result->et_likes);
		$result->replied 			= in_array($current_user->ID, (array)$result->et_reply_authors);
		$result->reported  			= in_array($current_user->ID,(array)get_post_meta($post->ID, 'et_users_report', true ));
		$result->user_badge   		= get_option( 'user_badges' ) && isset($badges[$user_role]) && $user_role ? '<span class="user-badge">'.$badges[$user_role].'</span>' : '';
		$result->avatar   			= et_get_avatar($result->post_author);

		$result->author_badge 		= isset($badges[get_user_role($result->post_author)]) && get_user_role($result->post_author) ? $badges[get_user_role($result->post_author)] : '';
		$result->author_url 		= get_author_posts_url( $result->post_author );
		$result->reply_author 		= apply_filters( 'fe_author', get_the_author_meta( 'display_name', $result->post_author ), $reply->post_author );
		$result->human_date 		= et_the_time( strtotime( $result->post_date ) );
		$result->content_filter		= apply_filters('et_the_content', $result->post_content );
		$result->ajax_insert_reply_nonce = wp_create_nonce( 'ajax_insert_reply' ) ;
		$result->edit_reply_nonce	= wp_create_nonce( 'edit_reply' );
		$result->content_edit		= nl2br($result->post_content);

		if(isset($result->post_password)){
			unset($result->post_password);
		}
		return apply_filters( 'fe_reply_response', $result);
	}

	/**
	 * Additional methods in theme
	 */

	public static function insert_reply($thread_id, $content, $author = false, $reply_id = 0){
		$instance = self::get_instance();

		global $current_user;
		if (!$current_user->ID) return new WP_Error('logged_in_required', __('Login Required',ET_DOMAIN));

		if ($author == false) $author = $current_user->ID;

		$thread = get_post($thread_id);

		/*$content = preg_replace('/^(?:<br\s*\/?>\s*)+/', '', $content);
		$content = preg_replace('/(?:<br\s*\/?>\s*)+$/', '', $content); */
		$content = preg_replace('/\[quote\].*(<br\s*\/?>\s*).*\[\/quote\]/', '', $content);
		/*$content = preg_replace('/\[code\].*(<br\s*\/?>\s*).*\[\/code\]/', '\n', $content);*/

		$data = array(
			'post_title' 	=> 'RE: ' . $thread->post_title,
			'post_content' 	=> et_add_tag_links($content),
			'post_parent' 	=> $thread_id,
			'author' 		=> $author,
			'post_type' 	=> 'reply',
			'post_status' 	=> 'publish',
			// 'tax_input'			=> array(
			// 	'fe_tag' 		=> et_generate_tag($content)
			// ),
			'et_reply_parent' 	=> $reply_id
		);

		$result = $instance->_insert($data);

		// if item is inserted successfully, update statistic
		if ($result){

			// update thread's update date
			update_post_meta( $thread_id , 'et_updated_date', current_time( 'mysql' ));

			// update last update author
			update_post_meta( $thread_id , 'et_last_author', $author);

			// update reply_authors
			$reply_authors = get_post_meta( $thread_id , 'et_reply_authors', true );
			$reply_authors = is_array($reply_authors) ? $reply_authors : array();
			if ( !in_array($author, $reply_authors) ){
				$reply_authors[] = $author;
				update_post_meta( $thread_id, 'et_reply_authors', $reply_authors );
			}
			// update reply author for reply
			if ( $reply_id ){
				$reply_authors = get_post_meta( $reply_id , 'et_reply_authors', true );
				$reply_authors = is_array($reply_authors) ? $reply_authors : array();
				if ( !in_array($author, $reply_authors) ){
					$reply_authors[] = $author;
					update_post_meta( $reply_id, 'et_reply_authors', $reply_authors );
				}
			}

			//
			if ( $reply_id == 0 ){
				FE_Threads::count_comments($thread->ID);
			} else {
				FE_Replies::count_comments($reply_id);
			}

			// update tag
			$tags		= et_generate_tag( $data['post_content'] );
			if ( !empty($tags) ){
				$reply 		= get_post($result);
				$thread 	= get_post($reply->post_parent);
				wp_set_object_terms( $thread->ID, $tags, 'fe_tag', true );
			}

			// update counter for user
			//if ( isset($data['post_author']) ){
				$test = FE_Member::update_counter( $author, 'reply' );
				FE_Member::update_unread($thread_id);
			//}
		}
		return $result;

		//return $instance->_insert($data);
	}

	/**
	 * Retrieve comment number of a thread and save to database
	 */
	public static function count_comments($parent){
		global $wpdb;

		$sql 	= "SELECT count(*) FROM {$wpdb->posts}
					INNER JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id AND {$wpdb->postmeta}.meta_key = 'et_reply_parent'
					WHERE {$wpdb->postmeta}.meta_value = $parent AND {$wpdb->posts}.post_type = 'reply' AND {$wpdb->posts}.post_status = 'publish' ";

		$count 	= $wpdb->get_var($sql);

		// save
		update_post_meta($parent, 'et_replies_count', (int) $count);

		return $count;
	}

	public static function update_field($id, $key, $value){
		$instance = self::get_instance();

		$instance->_update_field($id, $key, $value);
	}

	/**
	 * Retrieve replies in thread
	 */
	public static function get_replies($args = array()){
		// modify query
		global $et_query;
		$et_query['reply_parent'] = !empty($args['reply_parent']) ? $args['reply_parent'] : 0;
		add_action('posts_join', array('FE_Replies', 'get_reply_join'));
		add_action('posts_where', array('FE_Replies', 'get_reply_where'));

		$args = wp_parse_args(  $args, array(
			'post_type' 	=> 'reply'
		) );

		$query = new WP_Query($args);

		// remove modified query
		remove_action('posts_join', array('FE_Replies', 'get_reply_join'));
		remove_action('posts_where', array('FE_Replies', 'get_reply_where'));
		return $query;
	}

	public static function get_reply_join($join){
		global $wpdb, $et_query;
		$join .= " LEFT JOIN {$wpdb->postmeta} as reply_meta ON reply_meta.post_id = {$wpdb->posts}.ID AND reply_meta.meta_key = 'et_reply_parent'";
		return $join;
	}

	public static function get_reply_where($where){
		global $wpdb, $et_query;
		if ( empty($et_query['reply_parent']) ){
			$where .= " AND (reply_meta.meta_value is NULL OR reply_meta.meta_value = '' OR reply_meta.meta_value = '0' ) ";
		} else {
			$where .= " AND (reply_meta.meta_value = '" . $et_query['reply_parent'] . "' ) ";
		}
		return $where;
	}

	/**
	 * Retrieve replies of reply
	 */
	// public static function get_replies($reply_id, $args = array()){
	// 	$args = wp_parse_args( $args, array(
	// 		'post_type' 	=> 'reply',
	// 		'meta_key' 		=> 'et_reply_parent',
	// 		'meta_value' 	=> $reply_id
	// 	) );
	// 	$query = get_posts($args);
	// 	return $query;
	// }


	/**
	 * All about meta boxes in backend
	 */
	static public function add_meta_boxes(){
		add_meta_box( 'thread_info',
			__('Thread Information', ET_DOMAIN),
			array('FE_Threads', 'meta_view'),
			self::POST_TYPE,
			'normal',
			'high' );
	}

	static public function meta_view(){

	}

	static public function save_meta_fields(){

	}


}

/**
 * more function
 */
function et_generate_tag($content){
	//$pattern 		= "/#([a-zA-Z0-9\-\_]+)/";
	$pattern 		= "/#([^\s]+)/";
	//$exc_pattern 	= "/<[^<^>]*#([a-zA-Z0-9\-\_]+)[^>^<]*>/";
	$exc_pattern 	= "/<[^<^>]*#([^\s]+)[^>^<]*>/";
	$shortcode_pattern 		= "/\[([^\]]+)]([^\[]+)\[\/([^\]]+)]/i";
	$new_content 	= $content;

	// get tags
	preg_match_all($pattern, $content, $matches);
	preg_match_all($exc_pattern, $content, $excludes);

	// get tag in shortcode
	preg_match_all($shortcode_pattern, $content, $shortcodes);
	$exc_shortcode = array();
	foreach ($shortcodes[0] as $key => $value) {
		preg_match_all($pattern, $value, $temp);
		//$exc_shortcode = $exc_shortcode + $temp[1];
		$exc_shortcode = array_merge($exc_shortcode, $temp[1]);
	}

	$tags 	= array_diff($matches[1], $excludes[1]); //$matches[1];
	$tags 	= array_diff($tags, $exc_shortcode); //$matches[1];

	return apply_filters( 'fe_use_hashtag', 1 ) ? $tags : array();
}

function et_add_tag_links($content){
	//$pattern 		= "/#([a-zA-Z0-9\-\_]+)/";
	$pattern 		= "/\s#([^\s]+)/";
	//$exc_pattern 	= "/<[^<^>]*#([a-zA-Z0-9\-\_]+)[^>^<]*>/";
	$exc_pattern 	= "/<[^<^>]*#([^\s]+)[^>^<]*>/";
	$shortcode_pattern 		= "/\[([^\]]+)]([^\[]+)\[\/([^\]]+)]/i";
	$new_content 	= $content;

	// get tags
	preg_match_all($pattern, $content, $matches);
	preg_match_all($exc_pattern, $content, $excludes);

	// get tag in shortcode
	preg_match_all($shortcode_pattern, $content, $shortcodes);
	$exc_shortcode = array();
	foreach ($shortcodes[0] as $key => $value) {
		preg_match_all($pattern, $value, $temp);
		//$exc_shortcode = $exc_shortcode + $temp[1];
		$exc_shortcode = array_merge($exc_shortcode, $temp[1]);
	}

	$tags 	= array_diff($matches[1], $excludes[1]); //$matches[1];
	$tags 	= array_diff($tags, $exc_shortcode); //$matches[1];

	// replace tags with links
	foreach ($tags as $key => $value) {
		//$links[$value] = get_term_link( $value, 'fe_tag' );
		$replace 	= get_term_link( $value, 'fe_tag' );
		if ( is_wp_error( $replace ) ){
			$new_term 	= wp_insert_term( $value, 'fe_tag' );
			if(!is_wp_error( $new_term )) {
				$replace 	= get_term_link( $new_term['term_id'], 'fe_tag' );
			}
		}
		if(!is_wp_error($new_term ))
			$new_content = str_replace( '#' . $value, '<a href="' . $replace . '">' . '#' . $value . '</a>', $new_content);
	}

	return apply_filters( 'fe_use_hashtag', 1 ) ? $new_content : $content;
}

/**
 * Retrieve thread counter by post status
 */
function et_get_counter($post_status = false, $post_type = 'thread'){
	$counter = wp_count_posts( $post_type );
	if ( $post_status == false ){
		return $counter;
	} else if ( isset( $counter->$post_status ) ){
		return $counter->$post_status;
	}
}
/**
 * Get number of following threads
 */
function et_get_user_following_threads(){
	global $wpdb,$user_ID;

	if(!$user_ID) return 0;
	$thread_status = apply_filters('fe_thread_status', array('publish','closed', 'pending' ));
	$status_query = array();
	foreach($thread_status as $status){
		$status_query[]='p.post_status = \'' . $status. '\'';
	}
	$status_query_string = implode(' OR ', $status_query);
	$sql = "SELECT p.ID FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pmt ON p.ID = pmt.post_id
			WHERE p.post_type = 'thread' AND ({$status_query_string}) AND pmt.meta_key = 'et_users_follow' AND FIND_IN_SET({$user_ID},pmt.meta_value) > 0
			GROUP BY p.ID ";

	$results = $wpdb->get_results($sql);
	$return = array();

	foreach ($results as $key => $value) {
		$return[] = $value->ID;
	}
	FE_Member::update(array('ID'=>$user_ID,'et_following_threads'=> $return));
	return $return;
}
/**
 * Get last page of thread
 */
function et_get_last_page($thread_id){
	$replies_query = FE_Replies::get_replies(array('post_parent' => $thread_id));
	$last_page = $replies_query->max_num_pages;

	if(!get_option( 'et_infinite_scroll' ))
		return add_query_arg(array('page'=>$last_page),get_permalink( $thread_id ));
	else
		return get_permalink( $thread_id );
}
/**
 * Update status replied thread/reply of author
 */
function fe_update_status_replied($parent,$child){
	$key = array_search($child->post_author,(array)$parent->et_reply_authors);

	if(get_post_type( $parent->ID ) == "reply"){
		$author_replies = get_posts(array(
			'post_type' 	=> 'reply',
			'meta_key' 		=> 'et_reply_parent' ,
			'meta_value' 	=> $parent->ID,
			'author'		=> $child->post_author
		));
	} else {
		$post_status = apply_filters('fe_thread_status',array('publish'));
		$author_replies = get_posts(array(
			'post_type'		=> 'reply',
			'post_parent'	=> $parent->ID,
			'post_status' 	=> $post_status,
			'author'		=> $child->post_author
		));
	}
	//var_dump($parent->et_reply_authors);die();
	if($key!==false && count($author_replies) == 0){
	    unset($parent->et_reply_authors[$key]);
	}

	update_post_meta($parent->ID,'et_reply_authors',(array)$parent->et_reply_authors);
}
/**
 * Check thread is highlight or not
 */
function et_is_highlight($thread_id){
	global $user_ID;
	if($user_ID){
		$userdata  	 =  (array) get_user_meta($user_ID,'et_unread_threads',true);
		$threads_arr = ($userdata) ? $userdata['data'] : array();
		if(in_array($thread_id, $threads_arr)) {
			return '';
		} else {
			return 'highlights';
		}
	} else {
		$threads = json_decode(stripslashes($_COOKIE['fe_cookie_thread_viewed']));
		$threads_arr = $threads->unread_threads;
		if(is_array($threads_arr) && !in_array($thread_id, $threads_arr)) {
			return '';
		} else {
			return 'highlights';
		}
	}
}

function et_get_sticky_threads( ){
	$sticky_threads = get_option( 'et_sticky_threads', array() );

	if ( count($sticky_threads) != 2 ){
		$sticky_threads = array( array(), array() );
		update_option( 'et_sticky_threads', $sticky_threads );
	}

	return $sticky_threads;
}

/**
 * Sticky thread
 */
function et_update_sticky_thread($id, $homepage = false){
	$sticky_threads = et_get_sticky_threads( );

	if ( $homepage ){
		$sticky_threads[0][] = $id;
		$sticky_threads[0] = array_unique( $sticky_threads[0] );
	}

	$sticky_threads[1][] = $id;
	$sticky_threads[1] = array_unique( $sticky_threads[1] );

	update_option( 'et_sticky_threads', $sticky_threads );

	return $sticky_threads;
}

/**
 * Toggle sticky status for a thread
 */
function et_toggle_sticky_thread($id){
	$sticky_threads = et_get_sticky_threads( );

	if ( in_array( $id, $sticky_threads[1] ) ){
		$key = array_search( $id, $sticky_threads[1] );
		unset( $sticky_threads[1][$key] );

		$key = array_search( $id, $sticky_threads[0] );
		unset( $sticky_threads[0][$key] );
	}
	else {
		$sticky_threads[1][] = $id;
		$sticky_threads[1] = array_unique( $sticky_threads[1] );
	}

	update_option( 'et_sticky_threads', $sticky_threads );

	return $sticky_threads;
}

/**
 * Toggle sticky status for a thread
 */
function et_toggle_sticky_home($id){
	$sticky_threads = et_get_sticky_threads( );

	if ( in_array( $id, $sticky_threads[0] ) ){
		$key = array_search( $id, $sticky_threads[0] );
		unset( $sticky_threads[0][$key] );
	}
	else {
		$sticky_threads[1][] = $id;
		$sticky_threads[1]   = array_unique( $sticky_threads[1] );

		$sticky_threads[0][] = $id;
		$sticky_threads[0]   = array_unique( $sticky_threads[0] );
	}

	update_option( 'et_sticky_threads', $sticky_threads );

	return $sticky_threads;
}

/**
 * Check if thread is sticky
 */
function et_is_sticky_thread( $id, $homepage = false){
	$sticky_threads = et_get_sticky_threads( );

	if ( $homepage && in_array($id, $sticky_threads[0]) ){
		return true;
	}

	if ( !$homepage && in_array($id, $sticky_threads[1]) ){
		return true;
	}

	return false;
}

/**
 * Check if thread is sticky
 */
function et_remove_sticky_thread( $id ){
	$sticky_threads = get_option( 'et_sticky_threads', array() );

	if ( isset( $sticky_threads[0][$id] ) ){
		unset( $sticky_threads[0][$id] );
	}

	if ( isset( $sticky_threads[1][$id] ) ){
		unset( $sticky_threads[0][$id] );
	}
}

/**
 * Terms & Taxonomy
 */
class ET_Term{
	public $transient 		= '';
	public $taxonomy 		= '';
	public $label 			= '';
	public $order_option 	= '';

	public function __construct($taxonomy, $label){
		$this->taxonomy  	= $taxonomy;
		$this->transient 	= $taxonomy;
		$this->label 		= $label;
		$this->order_option = 'et_order_' . $taxonomy;
	}

	public function getAll($args = array()){
		$ordered    = array();
		$result 	= array();
        $args   	= wp_parse_args( $args,  array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
        $categories = get_terms($this->taxonomy, $args);

        $order      = get_option($this->order_option);

        if ($order) {
            foreach ($order as $pos) {
                foreach ($categories as $key => $cat) {
                    if ($cat->term_id == $pos['item_id']){
                        $ordered[] = $cat;
                        unset($categories[$key]);
                    }
                }
            }
            if (count($categories) > 0)
                foreach ($categories as $cat) {
                    $ordered[] = $cat;
                }
            set_transient($this->transient, $ordered);
        }else {
            set_transient($this->transient, $categories);
            $ordered = $categories;
        }

        return $ordered;
	}

	public function create($term, $args = array()){
        $result = wp_insert_term( $term , $this->taxonomy, $args );
		do_action( 'et_insert_term' , $result, $args );
		do_action( 'et_insert_term_' . $this->taxonomy, $result, $args );

        return $result;
	}

	public function update($id, $args = array()){
        $result = wp_update_term( $id , $this->taxonomy, $args );
		do_action( 'et_update_term' , $args );
		do_action( 'et_update_term_' . $this->taxonomy, $args );

        return $result;
	}

	public function sort($data){

        update_option($this->order_option, $data);
	}

	public function delete($id, $default = false){
		if ($default)
			$result = wp_delete_term($id, $this->taxonomy, array( 'default' => $default ));
		else
			$result = wp_delete_term($id, $this->taxonomy );

		do_action( 'et_delete_term' , $result );
		do_action( 'et_delete_term_' . $this->taxonomy, $result );
		return $result;
	}
}

