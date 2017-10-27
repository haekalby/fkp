<?php
class FE_Hashtags_Widget extends WP_Widget
{

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'A list of your Forum\'s hashtags.', ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('forum_hashtags_widget', __('FE Forum Hashtags', ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => 'Forum Hashtags','number' => 12) );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of tags:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
	<?php
	}

	function widget( $args, $instance ) {
	?>

	<div class="widget widget-hashtags">
		<?php if($instance['title']){ ?>
		<h2><?php echo esc_attr($instance['title']) ?></h2>
		<?php } ?>
		<ul class="hashtags-items">
			<?php
				$tags = get_terms( 'fe_tag', array('hide_empty'=>false, 'orderby'=>'count', 'number'=>$instance['number']) );
				foreach ($tags as $tag) {
			?>
			<li class="item">
				<a href="<?php echo get_term_link( $tag, 'fe_tag' );?>" rel="tag"><?php echo $tag->name; ?></a>
			</li>
			<?php } ?>
		</ul>
	</div>

	<?php
	}
}
class FE_Thread_Category_Widget extends WP_Widget
{

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'A list of your Forum\'s categories.', ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('forum_categories_widget', __('FE Forum Categories', ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
	<?php
	}

	function widget( $args, $instance ) {
	?>

	<div class="widget widget-categories">
		<?php if($instance['title']){ ?>
		<h2><?php echo esc_attr($instance['title']) ?></h2>
		<?php } ?>
		<ul class="category-items">
			<?php
				$current_cat = get_query_var( 'term' );
				if ( $current_cat ){
					$parents = et_get_cat_parents($current_cat);
					et_thread_categories_list(0, 1, false, $parents);
				} else {
					et_thread_categories_list();
				}
			?>
		</ul>
	</div>

	<?php
	}
}
class FE_Thread_Discuss_Widget extends WP_Widget
{

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to Single Thread sidebar to display the list of people taking part in the thread.',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('thread_discuss_widget', __('FE People under Discussion',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('PEOPLE IN THIS DISCUSSION',ET_DOMAIN)) );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
	<?php
	}

	function widget( $args, $instance ) {
		global $post;
		$thread 	= FE_Threads::convert($post);
		$thread_id = $post->ID;
		if($thread->post_status != "pending" && is_singular('thread')){
	?>
	<div class="widget hide-discuss-tablet who-in-discuss">
		<h2><?php echo esc_attr($instance['title']) ?></h2>
		<ul class="user-discuss">
			<?php
				$ava_reply_authors = get_post_meta( $thread_id, 'et_reply_authors', true );
				$ava_author = et_get_avatar($thread->post_author,30,array('class'=> 'img-circle','title'=> '','alt'=> ''));
				if($ava_reply_authors){
					foreach ($ava_reply_authors as $key => $value) {
						if($value != $thread->post_author){
			?>
			<li><a href="<?php echo get_author_posts_url( $value )?>"><?php echo et_get_avatar($value,30,array('class'=> 'img-circle','title'=> '','alt'=> ''));?></a></li>
			<?php
						}
					}
				}
			?>
            <li><a href="<?php echo get_author_posts_url( $thread->post_author )?>"><?php echo $ava_author; ?></a></li>
        </ul>
    </div>
	<?php
		}
	}
}
class FE_Statistic_Widget extends WP_Widget
{

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to sidebar to display the statistic of forum.',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('statistic_widget', __('FE Forum Statistics',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('STATISTICS WIDGET',ET_DOMAIN)) );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
	<?php
	}

	function widget( $args, $instance ) {
		$users = count_users();
		$threads = wp_count_posts('thread');
		$replies = wp_count_posts('reply');
	?>
	<div class="widget statistics-wg">
		<h2 class="widgettitle"><?php echo esc_attr($instance['title']) ?></h2>
	    <div class="statistics">
	    	<ul>
	        	<li><span class="number-stt"><?php echo $users['total_users'] ?> </span><span class="name-stt"> <?php _e('Members',ET_DOMAIN) ?> </span></li>
	            <li><span class="number-stt"><?php echo $threads->publish + $threads->closed  ?> </span><span class="name-stt"> <?php _e('Threads',ET_DOMAIN) ?> </span></li>
	            <li><span class="number-stt"><?php echo $replies->publish ?> </span><span class="name-stt"> <?php _e('Replies',ET_DOMAIN) ?> </span></li>
	        </ul>
	    </div>
	</div>
	<?php
	}
}
class FE_Thread_Hot_Widget extends WP_Widget
{

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to any sidebars to display your site\'s total number of members, threads, and replies.',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('thread_hot_widget', __('FE Latest Threads / Hot Topics',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		// print_r($new_instance);
		// print_r($old_instance);die();
		if($new_instance['normal_thread'] != $old_instance['normal_thread'] || $new_instance['number'] != $old_instance['number']){
			delete_transient( 'hot_topics_query' );
			delete_transient( 'latest_topics_query' );
		}
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('HOT TOPICS',ET_DOMAIN) , 'number' => '8', 'date' => '', 'normal_thread' => 0) );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of topics to display:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('normal_thread'); ?>"><?php _e('Latest Threads (sort by date)', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('normal_thread'); ?>" name="<?php echo $this->get_field_name('normal_thread'); ?>" value="1" type="checkbox" <?php checked( $instance['normal_thread'], 1 ); ?> value="<?php echo esc_attr( $instance['normal_thread'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Date range:', ET_DOMAIN) ?></label>
			<select id="<?php echo $this->get_field_id('date'); ?>" name="<?php echo $this->get_field_name('date'); ?>">
				<option <?php selected( $instance['date'], "all" ); ?> value="all"><?php _e('All days', ET_DOMAIN) ?></option>
				<option <?php selected( $instance['date'], "last7days" ); ?> value="last7days"><?php _e('Last 7 days', ET_DOMAIN) ?></option>
				<option <?php selected( $instance['date'], "last30days" ); ?> value="last30days"><?php _e('Last 30 days', ET_DOMAIN) ?></option>
			</select>
		</p>
	<?php
	}

	function widget( $args, $instance ) {

		global $wpdb;
		if(!isset($instance['normal_thread'])){

			$thread_status = apply_filters('fe_thread_status', array('publish','closed', 'pending' ));
			$status_query = array();
			foreach($thread_status as $status){
				$status_query[]='post_status = \'' . $status. '\'';
			}
			$status_query_string = implode(' OR ', $status_query);

			if(get_transient( 'hot_topics_query' ) === false){
				$hour = 12;
				$today = strtotime("$hour:00:00");
				$last7days = strtotime('-7 day', $today);
				$last30days = strtotime('-30 day', $today);

				if($instance['date'] == "last7days"){
					$custom = "AND post_date >= '".date("Y-m-d H:i:s", $last7days)."' AND post_date <= '".date("Y-m-d H:i:s", $today)."' ";
				} elseif ($instance['date'] == "last30days") {
					$custom = "AND post_date >= '".date("Y-m-d H:i:s", $last30days)."' AND post_date <= '".date("Y-m-d H:i:s", $today)."' ";
				} else {
					$custom = "";
				}
				$query ="
					SELECT * FROM $wpdb->posts as post
					LEFT JOIN $wpdb->postmeta as postmeta
					ON post.ID = postmeta.post_id
					WHERE ({$status_query_string})
						AND post_type = 'thread'
						AND ( postmeta.meta_key = 'et_replies_count' )  ";
				$query .= $custom;
				$query .="	GROUP BY post.ID
					ORDER BY CAST(postmeta.meta_value AS SIGNED) DESC,post_date DESC
					LIMIT ".$instance['number']."
					";
				$threads = $wpdb->get_results($query);
				set_transient( 'hot_topics_query', $threads, apply_filters( 'fe_time_expired_transient', 2*24*60*60 ));
			} else {
				$threads = get_transient( 'hot_topics_query' );
			}

		} else {

			if(get_transient( 'latest_topics_query' ) === false){
				$query ="
					SELECT * FROM $wpdb->posts as post
					WHERE post_type = 'thread'
					";
				$query .="
					ORDER BY post_date DESC
					LIMIT ".$instance['number']."
					";

			$threads = $wpdb->get_results($query);
			set_transient( 'latest_topics_query', $threads, apply_filters( 'fe_time_expired_transient', 7*24*60*60 ) );

			} else {
				$threads = get_transient( 'latest_topics_query' );
			}
		}
		// delete_transient( 'latest_topics_query' );
		// delete_transient( 'hot_topics_query' );
	?>
	<div class="widget user-wg">
		<h2 class="widgettitle"><?php echo esc_attr($instance['title']) ?></h2>
	    <div class="hot-user-topic">
	    	<ul>
            <?php
            	$i = 1;
            	foreach ($threads as $thread) {
            		$thread = FE_Threads::convert($thread);
            ?>
	        	<li>
	            	<p>
	                    <span class="number"><?php echo $i ?></span>
	                    <span class="text"><a href="<?php echo get_permalink( $thread->ID ); ?>"><?php echo $thread->post_title ?></a></span>
	                 </p>
	            </li>
            <?php $i++;} ?>
	        </ul>
	    </div>
	</div>
	<?php
	}
}
class FE_Top_Users_Widget extends WP_Widget
{

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to sidebar to display the list of top users.',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('top_users_widget', __('FE Top Users',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		if($new_instance['number'] != $old_instance['number'] )
			delete_transient( 'top_users_query' );
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('TOP USERS',ET_DOMAIN) , 'number' => '8') );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of users to display:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
	<?php
	}

	function widget( $args, $instance ) {
		global $wpdb;
		if(get_transient( 'top_users_query' ) === false){
			$query =
				"
				SELECT  user.ID as uid,
						display_name,
						(IFNULL(reply.meta_value, 0)  + IFNULL(thread.meta_value, 0)) as count,
						like_count.meta_value as likes
				FROM $wpdb->users as user
				LEFT JOIN $wpdb->usermeta as reply  ON user.ID = reply.user_id  AND reply.meta_key  = 'et_reply_count'
				LEFT JOIN $wpdb->usermeta as thread ON user.ID = thread.user_id AND thread.meta_key = 'et_thread_count'
				LEFT JOIN $wpdb->usermeta as like_count   ON user.ID = like_count.user_id   AND like_count.meta_key   = 'et_like_count'
				WHERE (IFNULL(reply.meta_value, 0)  + IFNULL(thread.meta_value, 0)) > 0
				GROUP BY user.ID
				ORDER BY (IFNULL(reply.meta_value, 0)  + IFNULL(thread.meta_value, 0)) DESC, CAST(likes AS SIGNED) DESC
				LIMIT ".$instance['number'];
			$users = $wpdb->get_results($query);
			set_transient( 'top_users_query', $users, apply_filters( 'fe_time_expired_transient', 7*24*60*60 ) );
		} else {
			$users = get_transient( 'top_users_query' );
		}
		//delete_transient( 'top_users_query' );
	?>
	<div class="widget user-wg">
		<h2 class="widgettitle"><?php echo esc_attr($instance['title']) ?></h2>
	    <div class="hot-user-topic">
	    	<ul>
            <?php
            	$i = 1;
            	foreach ($users as $user) {
            ?>
	        	<li>
	            	<p>
	                    <span class="number">
	                    	<?php echo $i ?>
	                    </span>
	                    <span class="username">
	                    	<a href="<?php echo get_author_posts_url($user->uid); ?>" title="<?php echo $user->display_name ?>">
	                    		<?php echo $user->display_name ?>
	                    	</a>
	                    </span>
	                    <span class="icon" data-icon="w"></span><?php echo $user->count > 0 ? custom_number_format($user->count) : 0 ?>
	                    <span class="icon" data-icon="k"></span><?php echo $user->likes > 0 ? custom_number_format($user->likes) : 0 ?>
	                 </p>
	            </li>
	        <?php $i++;} ?>
	        </ul>
	    </div>
	</div>
	<?php
	}
}

class FE_Related_Threads_Widget extends WP_Widget
{

	function __construct() {
		$widget_ops = array('classname' => 'widget', 'description' => __( 'Drag this widget to single thread sidebars to display the ...',ET_DOMAIN) );
		$control_ops = array('width' => 250, 'height' => 100);
		parent::__construct('related_threads_widget', __('FE Related Threads',ET_DOMAIN) , $widget_ops ,$control_ops );
	}

	function update ( $new_instance, $old_instance ) {
		if($new_instance['number'] != $old_instance['number']){
			delete_transient( 'related_topics_query' );
		}
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __('RELATED TOPICS',ET_DOMAIN) , 'number' => '8') );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of topics to display:', ET_DOMAIN) ?> </label>
			<input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo esc_attr( $instance['number'] ); ?>" />
		</p>
	<?php
	}

	function widget( $args, $instance ) {
		if(is_singular( 'thread' )):
			global $post;

			if(get_transient( 'related_topics_query' ) === false){

				$tags_id = array();
				$tags = get_the_terms($post->ID,'fe_tag');
				$category = array_pop(get_the_terms($post->ID,'thread_category'));

				if(!empty($tags)):
					foreach ($tags as $tag) {
						$tags_id[]  = $tag->term_id;
					}
					$status = apply_filters('fe_thread_status', array( 'publish' ));
					$args = array(
						'post_type' => 'thread',
						'post_status' => $status,
						'posts_per_page' => $instance['number'],
						'post__not_in' => array($post->ID),
						'tax_query' => array(
								array(
								'taxonomy' => 'fe_tag',
								'field' => 'id',
								'terms' => $tags_id,
								'operator' => 'IN'
								)
							)
						);
					$threads = get_posts($args);
				else:
					$threads = get_posts( array(
						'post_type' => 'thread',
						'post__not_in' => array($post->ID),
						'posts_per_page' => $instance['number'],
						'thread_category' => $category->slug
					));
				endif;

				set_transient( 'related_topics_query', $threads, apply_filters( 'fe_time_expired_transient', 7*24*60*60 ));
			} else {
				$threads = get_transient( 'related_topics_query' );
			}
	?>
	<div class="widget user-wg">
		<h2 class="widgettitle"><?php echo esc_attr($instance['title']) ?></h2>
	    <div class="hot-user-topic">
	    	<ul>
            <?php
            	$i = 1;
            	foreach ($threads as $thread) {
            		$thread = FE_Threads::convert($thread);
            ?>
	        	<li>
	            	<p>
	                    <span class="number"><?php echo $i ?></span>
	                    <span class="text"><a href="<?php echo get_permalink( $thread->ID ); ?>"><?php echo $thread->post_title ?></a></span>
	                 </p>
	            </li>
            <?php $i++;} ?>
	        </ul>
	    </div>
	</div>
	<?php
		else:
	?>
	<div class="widget user-wg">
		<h2 class="widgettitle"><?php echo esc_attr($instance['title']) ?></h2>
	    <div class="hot-user-topic">
	    	<?php _e('This widget should be placed in Single Thread Widget only.',ET_DOMAIN) ?>
	    </div>
	</div>
	<?php
		endif;
	}
}