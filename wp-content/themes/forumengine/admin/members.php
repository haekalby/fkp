<?php

class ET_AdminMember extends ET_AdminMenuItem{

	private $options;

	function __construct(){
		parent::__construct('et-members',  array(
			'menu_title'	=> __('Members', ET_DOMAIN),
			'page_title' 	=> __('MEMBERS', ET_DOMAIN),
			'callback'   	=> array($this, 'menu_view'),
			'slug'			=> 'et-members',
			'icon_class'	=> 'icon-menu-members',
			'page_subtitle'	=> __('ForumEngine members', ET_DOMAIN),
			'pos' 			=> 15
		));
		
		if(current_user_can( 'manage_options' )) {
			$this->add_ajax('et-filter-stat', 'filter_stat');	
		}		
	}

	public function on_add_scripts(){
		$this->add_existed_script( 'jquery' );
		$this->add_existed_script( 'underscore' );
		$this->add_existed_script( 'backbone' );
		//$this->add_existed_script( 'jquery-ui-datepicker' );
		?>
		<!--[if lt IE 9]> <?php $this->add_script( 'excanvas', TEMPLATEURL . '/js/libs/excanvas.min.js' ); ?> <![endif]-->
		<?php 
		$this->add_script('bootstrap',  		TEMPLATEURL . '/js/bootstrap.min.js', array());
		$this->add_script('fe-function',  		TEMPLATEURL . '/js/functions.js', array('jquery', 'backbone', 'underscore' ));
		$this->add_script('backend-script',  	TEMPLATEURL . '/admin/js/admin.js', array('jquery', 'backbone', 'underscore' ));
		$this->add_script('backend-member',  	TEMPLATEURL . '/admin/js/members.js', array('jquery', 'backbone', 'underscore', 'backend-script' ));
	}

	public function on_add_styles(){
		$this->add_style( 'bootstrap', TEMPLATEURL . '/css/bootstrap.css', array(), false, 'all' ); 
		$this->add_style( 'admin_styles', TEMPLATEURL . '/admin/css/admin.css', array(), false, 'all' ); 
		$this->add_style( 'admin_forum_styles', TEMPLATEURL . '/admin/css/admin-forum.css', array(), false, 'all' );
	}

	public function menu_view($args){ 
		global $wp_roles;
		?>
		<div class="et-main-header">
			<div class="title font-quicksand"><?php echo $args->menu_title ?></div>
			<div class="desc"><?php echo $args->page_subtitle ?>. 
			</div>
		</div>
		<div class="et-main-content" id="overview">
			<div class="et-container et-member-search">
				<form action="">
				<?php global $wp_roles;  //var_dump($wp_roles); ?>
					<span class="et-search-role">
						<select name="role" id="" class="et-input">
							<option value=""><?php _e('All members', ET_DOMAIN) ?></option>
							<option value="administrator"><?php _e('Administrators', ET_DOMAIN) ?></option>
							<option value="moderator"><?php _e('Moderators', ET_DOMAIN) ?></option>
						</select>
					</span>
					<span class="et-search-input">
						<input type="text" class="et-input" name="keyword" placeholder="<?php _e('Search members...', ET_DOMAIN) ?>">
						<span class="et-search-ico"></span>
					</span>
				</form>
			</div>
			<div class="et-container et-members-list">
				<h3 class="title font-quicksand"><?php _e('Members', ET_DOMAIN) ?></h3>
				<?php 
				$posts_per_page = get_option( 'posts_per_page');
				$query = new WP_User_Query(array(
					'number'	=> $posts_per_page
				));
				$users = $query->results;

				if ( !empty($users) ) { ?>
					<ul id="members_list">
						<?php /*
						<?php foreach ($users as $user) { 
							$info = array(
								'thread_count' => get_user_meta($user->ID, 'et_thread_count',true),
								'reply_count' => get_user_meta($user->ID, 'et_reply_count', true),
								'user_location' => get_user_meta($user->ID, 'user_location', true),
							);

							$info['thread_count'] 	= !empty($info['thread_count']) ? $info['thread_count'] : 0;
							$info['reply_count'] 	= !empty($info['reply_count']) ? $info['reply_count'] : 0;

							?>
							<li class="et-member" data-id="<?php echo $user->ID ?>">
								<div class="et-mem-container">
									<div class="et-mem-avatar">
										<?php echo et_get_avatar($user->ID);?>
									</div>
									<div class="et-act">
										<select name="role" id="" class="selector et-act-select" <?php if ( $user->ID == 1 ) echo 'disabled="disabled"' ?>>
											<?php foreach ($wp_roles->roles as $role => $data) {
												if ( $user->roles[0] == $role )
													echo '<option value="' . $role . '" selected="selected">' . $data['name'] . '</option>';
												else 
													echo '<option value="' . $role . '">' . $data['name'] . '</option>';
											} ?>
										</select>
										<a class="et-act-ban" href="#"
										data-toggle="modal" data-target="#ban_modal" title="<?php _e( 'Ban this user', ET_DOMAIN ) ?>"><span class="icon" data-icon="("></span></a>
										<a class="et-act-confirm" href="#" title="<?php _e( 'Confirm this user', ET_DOMAIN ) ?>"><span class="icon" data-icon="3"></span></a>
									</div>
									<div class="et-mem-detail">
										<div class="et-mem-top">
											<span class="name"><?php echo $user->display_name ?></span>
											<span class="thread icon" data-icon="w"><?php echo $info['thread_count'] ?></span>
											<span class="comment icon"  data-icon="q"><?php echo $info['reply_count'] ?></span>
										</div>
										<div class="et-mem-bottom">
											<span class="date"><?php printf( __('Join on %s', ET_DOMAIN), date('jS M, Y', strtotime($user->user_registered)) ) ?></span>
											<span class="loc icon" data-icon="@"><?php echo !empty($info['user_location']) ? $info['user_location'] : 'NA' ?></span>
										</div>
									</div>
								</div>
							</li>
						<?php } ?>
						*/ ?>
					</ul>
				<?php } ?>
				<script type="text/javascript">
					<?php 
					$members = array();
					foreach ((array)$users as $user) {
						$members[] = et_make_member_data($user) ;
					} ?>
					var members = <?php echo json_encode( $members ) ?>;
				</script>
				<button class="et-button btn-button <?php if ( $query->total_users <= $posts_per_page ) echo 'hide' ?>" id="load-more"><?php _e('Load more', ET_DOMAIN) ?></button>

				<div class="modal fade" id="ban_modal">
					<div class="modal-dialog">
						<div class="modal-content">
							<form id="form_ban_user" action="">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
									<h4 class="modal-title"><?php _e('Ban user',ET_DOMAIN) ?> <span class="display-name"></span></h4>
								</div>
								<div class="modal-body">
										<input type="hidden" name="id" value="">
										<div class="dialog-form-item">
											<label for=""><?php _e('Ban for', ET_DOMAIN) ?></label> <br>
											<select name="expired" id="">
												<?php 
												$expired = et_get_ban_expired_period();
												foreach ($expired as $day) {
													echo '<option value="' . $day['value'] . '">' . $day['label'] . '</option>';
												} ?>
											</select>
										</div>
										<div class="dialog-form-item">
											<label for=""><?php _e('Reason', ET_DOMAIN) ?></label> <br>
											<textarea name="reason" id="" cols="30" rows="10"></textarea>
										</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close', ET_DOMAIN) ?></button>
									<button type="submit" class="btn btn-primary"><?php _e('Submit', ET_DOMAIN) ?></button>
								</div>
							</form>
						</div><!-- /.modal-content -->
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->

				<script type="text/template" id="member_template">
					<div class="et-mem-container">
						<div class="et-mem-avatar">
							{{= avatar }}
						</div>
						<div class="et-act">
							<# if ( !banned ) { #>
								<select name="role" id="" class="selector et-act-select" <# if (id == 1) { #> disabled="disabled" <# } #>>
									<?php foreach ($wp_roles->roles as $role => $data) {
										echo "<option value='$role' <# if ('$role' == role) { #>selected='selected'<# }#> >{$data['name']}</option>";
									} ?>
								</select>
							<# } else { #>
								<span class="ban-badge"><?php printf( __('Banned until %s', ET_DOMAIN), '{{= ban_expired }}' )  ?></span>
							<# } #>

							<# if ( role != 'administrator' && banned ) { #>
								<a class="et-act-unban" href="#" title="<?php _e( 'Unban this user', ET_DOMAIN ) ?>"><span class="icon" data-icon=")"></span></a>
							<# } else if ( role != 'administrator' ) { #>
								<a class="et-act-ban" href="#" data-toggle="modal" data-target="#ban_modal" title="<?php _e( 'Ban this user', ET_DOMAIN ) ?>"><span class="icon" data-icon="("></span></a>
							<# } #>
							<# if(register_status == "unconfirm") { #>
							<a class="et-act-confirm" href="#" title="<?php _e( 'Confirm this user', ET_DOMAIN ) ?>"><span class="icon" data-icon="3"></span></a>
							<# } #>
						</div>
						<div class="et-mem-detail">
							<div class="et-mem-top">
								<span class="name">{{= display_name }}</span>
								<span class="thread icon" data-icon="w">{{= thread_count }}</span>
								<span class="comment icon"  data-icon="q">{{= reply_count }}</span>
							</div>
							<div class="et-mem-bottom">
								<span class="date">{{= date_text }}</span>
								<span class="loc icon" data-icon="@">{{= user_location }}</span>
							</div>
						</div>
					</div>
				</script>
			</div>
		</div>
		<?php
	}
}
?>