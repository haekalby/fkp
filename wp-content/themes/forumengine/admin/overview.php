<?php 

class ET_AdminOverview extends ET_AdminMenuItem{

	private $options;

	function __construct(){
		parent::__construct('et-overview',  array(
			'menu_title'	=> __('Overview', ET_DOMAIN),
			'page_title' 	=> __('OVERVIEW', ET_DOMAIN),
			'callback'   	=> array($this, 'menu_view'),
			'slug'			=> 'et-overview',
			'icon_class'	=> 'icon-menu-overview',
			'page_subtitle'	=> __('ForumEngine Overview', ET_DOMAIN),
			'pos' 			=> 5
		));

		$this->add_ajax('et-filter-stat', 'filter_stat');
	}

	public function on_add_scripts(){
		$this->add_existed_script( 'jquery' );
		$this->add_existed_script( 'underscore' );
		$this->add_existed_script( 'backbone' );
		$this->add_existed_script( 'jquery-ui-datepicker' );
		?>
		<!--[if lt IE 9]> <?php $this->add_script( 'excanvas', TEMPLATEURL . '/js/libs/excanvas.min.js' ); ?> <![endif]-->
		<?php 
		$this->add_script( 'jqplot', TEMPLATEURL . '/js/libs/jquery.jqplot.min.js', array('jquery') );
		$this->add_script( 'jqplot-plugins', TEMPLATEURL . '/js/libs/jqplot.plugins.js', array('jquery', 'jqplot') );
		$this->add_script('fe-function',  		TEMPLATEURL . '/js/functions.js', array('jquery', 'backbone', 'underscore' ));
		$this->add_script('backend-script',  	TEMPLATEURL . '/admin/js/admin.js', array('jquery', 'backbone', 'underscore' ));
		$this->add_script( 'overview', TEMPLATEURL . '/admin/js/overview.js', array('jquery', 'jqplot') );
		do_action('fe_overview_script');
	}

	public function on_add_styles(){
		$this->add_style( 'jqplot_style', TEMPLATEURL . '/css/libs/jquery.jqplot.min.css', array(), false, 'all' );
		$this->add_style( 'admin_styles', TEMPLATEURL . '/admin/css/admin.css', array(), false, 'all' ); 
		$this->add_style( 'admin_forum_styles', TEMPLATEURL . '/admin/css/admin-forum.css', array(), false, 'all' );
		do_action('fe_overview_style');
	}

	public function menu_view($args){
		$threads_data 	= $this->get_thread_statistic(strtotime('-2 months'));
		$replies 		= $this->get_replies_statistic( strtotime('-2 months') );
		$percentage 	= $this->get_threads_percent();
		$users  		= $this->get_registration_stat();
		?>
		<style>
		.et-main-main {
		margin-left: 0 !important;
		}
		</style>
		<script>
		var threads 		= <?php echo json_encode($threads_data); ?>;
		var replies 		= <?php echo json_encode($replies); ?>;
		var percentage  	= <?php echo json_encode($percentage); ?>;
		var registration 	= <?php echo json_encode($users); ?>;
		</script>
		<div class="et-main-header">
			<div class="title font-quicksand"><?php echo $args->menu_title ?></div>
			<div class="desc"><?php echo $args->page_subtitle ?>:  
				<form id="stat_filter" action="">
					<select class="" name="time">
						<option value=""><?php _e('Select one', ET_DOMAIN) ?></option>
						<option value="7"><?php _e('last 7 days', ET_DOMAIN) ?></option>
						<option value="30"><?php _e('last 30 days ', ET_DOMAIN) ?></option>
						<option value="90"><?php _e('last 90 days', ET_DOMAIN) ?></option>
						<option value="365"><?php _e('last 365 days', ET_DOMAIN) ?></option>
					</select>
				</form>
			</div>
		</div>
		<div class="et-main-content" id="overview">
			<div class="overview-content">
				<?php  /* ?><div class="container">
					<form id="stat_filter" action="">
						<select class="selector" name="time">
							<option value=""><?php _e('Select one', ET_DOMAIN) ?></option>
							<option value="7"><?php _e('last 7 days', ET_DOMAIN) ?></option>
							<option value="30"><?php _e('last 30 days ', ET_DOMAIN) ?></option>
							<option value="90"><?php _e('last 90 days', ET_DOMAIN) ?></option>
							<option value="365"><?php _e('last 365 days', ET_DOMAIN) ?></option>
						</select>
					</form>
				</div> */ ?>
				<div class="stat-container">
					<div id="users_statistic" class="col-6 stat-6"></div>
					<div id="threads_pie" class="col-6 stat-6"></div>
				</div>
				<div class="stat-container">
					<div id="threads_statistic" class="col-12 stat-12"></div>
				</div>
			</div>
		</div>
		<?php
		do_action('fe_overview_view');
	}

	/**
	 * Handle
	 */
	public function filter_stat(){
		$this->ajax_header();

		// parse params
		wp_parse_str( $_POST['content'], $data );

		// calc date
		$from 				= strtotime( ' -'. $data['time'] . ' days' );

		// get new stat
		$thread_statistic 	= $this->get_thread_statistic($from);
		$users_statistic 	= $this->get_registration_stat($from);

		echo json_encode( array(
			'success' => true,
			'data' 	=> array(
				'threads' 			=> $thread_statistic,
				'replies' 			=> $this->get_replies_statistic($from),
				'users' 			=> $users_statistic
			)
		) );
		exit;
	}

	/**
	 * Retrieve threads statistic through time
	 */
	protected function get_thread_statistic($from = false, $to = false){
		global $wpdb;

		if ( $from == false ) $from = strtotime('-1 month');
		if ( $to == false ) $to = time();

		$to += 24*60*60; // add one day

		$from_date 	= date('Y-m-d 00:00:00', $from);
		$to_date 	= date('Y-m-d 00:00:00', $to);
		$status = apply_filters('fe_thread_status', array('publish','pending','closed'));
		$status = "'" . implode("','", $status) . "'";
		$sql = "SELECT DATE(post_date) AS `date`, COUNT(ID) as `count` FROM {$wpdb->posts} 
				WHERE post_type = 'thread' AND 
				post_status IN ({$status}) AND
				STRCMP(post_date, '$from_date') >= 0 AND 
				STRCMP(post_date, '$to_date' ) <=0
				GROUP BY DATE(post_date)";
		
		$result = $wpdb->get_results( $sql, ARRAY_A  );
		$statistic = array();

		foreach ($result as $index => $row) {
			$statistic[] = array( date( 'd-M-y', strtotime($row['date']) ), $row['count']);
		}

		return $statistic;
	}

	/**
	 * Retrieve replies statistic through time
	 */
	protected function get_replies_statistic($from = false, $to = false){
		global $wpdb;

		if ( $from == false ) $from = strtotime('-1 month');
		if ( $to == false ) $to = time();

		$to += 24*60*60; // add one day

		$from_date 	= date('Y-m-d 00:00:00', $from);
		$to_date 	= date('Y-m-d 00:00:00', $to);
		$status = apply_filters('fe_thread_status', array('publish','pending','closed'));
		$status = "'" . implode("','", $status) . "'";
		$sql = "SELECT DATE(post_date) AS `date`, COUNT(ID) as `count` FROM {$wpdb->posts} 
				WHERE post_type = 'reply' AND 
				post_status IN ({$status}) AND
				STRCMP(post_date, '$from_date') >= 0 AND 
				STRCMP(post_date, '$to_date' ) <=0
				GROUP BY DATE(post_date)";
		
		$result = $wpdb->get_results( $sql, ARRAY_A  );
		$statistic = array();

		foreach ($result as $index => $row) {
			$statistic[] = array( date( 'd-M-y', strtotime($row['date']) ), $row['count']);
		}

		return $statistic;
	}

	protected function get_threads_percent(){
		global $wpdb;
		$status = apply_filters('fe_thread_status', array('publish','pending','closed'));
		$status = "'" . implode("','", $status) . "'";
		$sql 	= "SELECT post_status as status, COUNT(ID) as count FROM {$wpdb->posts} 
					WHERE post_type = 'thread' 
					AND post_status IN ({$status})
					GROUP BY post_status";

		$result = $wpdb->get_results( $sql, ARRAY_A );
		$stat  	= array();

		foreach ($result as $index => $row) {
			$stat[] = array( ucfirst($row['status']), (int)$row['count'] );
		}

		return $stat;
	}

	protected function get_registration_stat($from = false, $to = false){
		global $wpdb;

		$key = $wpdb->prefix . 'capabilities';

		if ( $from == false ) $from = strtotime('-1 month');
		if ( $to == false ) $to = time();

		$to += 24*60*60; // add one day

		$from_date 	= date('Y-m-d 00:00:00', $from);
		$to_date 	= date('Y-m-d 00:00:00', $to);

		$sql = "SELECT date({$wpdb->users}.user_registered) as date, count({$wpdb->users}.ID) as count FROM {$wpdb->users} 
				INNER JOIN {$wpdb->usermeta} ON {$wpdb->usermeta}.user_id = {$wpdb->users}.ID AND {$wpdb->usermeta}.meta_key = '$key' 
				WHERE 
					STRCMP(user_registered, '$from_date') >= 0 AND 
					STRCMP(user_registered, '$to_date' ) <=0
				GROUP BY date({$wpdb->users}.user_registered)";
		
		$result = $wpdb->get_results( $sql, ARRAY_A  );
		$statistic = array();

		foreach ($result as $index => $row) {
			$statistic[] = array( date( 'd-M-y', strtotime($row['date']) ), $row['count']);
		}

		return $statistic;
	}
}
?>