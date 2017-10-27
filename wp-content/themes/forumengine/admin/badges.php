<?php 
class ET_AdminBadges extends ET_AdminMenuItem{

	private $options;

	function __construct(){
		parent::__construct('et-user-badges',  array(
			'menu_title'	=> __('User Badges', ET_DOMAIN),
			'page_title' 	=> __('USER BADGES', ET_DOMAIN),
			'callback'   	=> array($this, 'menu_view'),
			'slug'			=> 'et-user-badges',
			'icon_class'	=> 'icon-menu-badges',
			'page_subtitle'	=> __('Manage user badges for ForumEngine', ET_DOMAIN),
			'pos' 			=> 10
		));
		// only admin can do these actions
		if(current_user_can( 'manage_options' )) {
			$this->add_ajax('fe_save_user_badge', 'fe_save_user_badge', true, false );
			$this->add_ajax('et-toggle-option', 'toggle_option', true, false);	
		}		
	}

	public function on_add_scripts(){
		$this->add_existed_script( 'jquery' );
		$this->add_existed_script( 'backbone' );
		wp_enqueue_script( 'fe-user-badge', TEMPLATEURL.'/admin/js/user-badge.js', array('jquery','backbone')); 
	}

	public function on_add_styles(){
		$this->add_style('backend-style', TEMPLATEURL . '/admin/css/admin.css');
	}

	public function menu_view($args){		
		?>
		<div class="et-main-header">
            <div class="title font-quicksand"><?php echo $args->menu_title ?></div>
            <div class="desc"><?php echo $args->page_subtitle ?>. 
            </div>
        </div>     
        <div class="et-main-content et-main-main" id="anonymous">

			<div class="title font-quicksand"><?php _e("Enable/Disable User Badges",ET_DOMAIN);?></div>
			<div class="desc">
			 	<?php _e("Enabling this will allow admins to add badges or titles for different member roles.",ET_DOMAIN);?>			
				<div class="inner no-border btn-left">
					<div class="payment">
						<?php et_toggle_button('user_badges', __("User Badges",ET_DOMAIN), get_option('user_badges', false) ); ?>
					</div>
				</div>	        				
			</div>
			<div class="title font-quicksand"><?php _e("Manage User Badges",ET_DOMAIN);?></div>
			<div class="desc">
				<p class="title-badge"><?php _e("Enter the badge for each role.Leave it blank if not needed.",ET_DOMAIN);?></p>
	        	<form id="form_user_badge">
	            <?php 
	                $badges = get_option( 'fe_user_badges');
	                foreach (get_editable_roles() as $role_name => $role_info){
	                	?>
	                	<label class="role" for="<?php echo $role_name ?>"><?php echo $role_name ?></label>
	                	<input class="user-badge bg-grey-input" type="text" id="<?php echo $role_name ?>" name="<?php echo $role_name ?>" value="<?php echo $badges[$role_name] ?>" /><br><br>
	                	<?php
	                }
	            ?>
				<button id="save" class="btn-button engine-submit-btn" <?php echo get_option( 'user_badges' ) ? '' : 'disabled="disabled"';?>>
					<span>Save</span> <!-- &nbsp; <span class="icon" data-icon="+"> --></span>
				</button>            
	            </form>
        	</div>
        </div>
        <style type="text/css">
		#anonymous {
			margin-left: 0;
		}
		p.title-badge {
			margin-top: 0;
			margin-bottom: 25px;
			font-size: 1em;
		}
        </style>
		<?php
	}
	//handle update content 
	function fe_enable_user_badge(){
		try {
			$name 	= $_REQUEST['content']['name'];
			$value 	= empty( $_REQUEST['content']['value'] ) ? 0 : 1;

			et_update_option( $name, $value );

			$resp = array(
				'success'	=> 	true,
			);
		} catch (Exception $e) {
			$resp = array(
				'success'	=> 	false,
				'msg' 		=> $e->getMessage
			);
		}
		wp_send_json( $resp );	
	}
    function fe_save_user_badge(){
    	$data = wp_parse_args($_POST['content']);
    	$data = array_filter(array_map('trim', $data));
    	if(update_option( 'fe_user_badges', array_unique($data) ))
	        $response    = array(
	            'success'   => true,
	            'msg'       => __('Option save successfully!', ET_DOMAIN),
	        );
	    else 
	        $response    = array(
	            'success'   => false,
	            'msg'       => __('Option save failed!', ET_DOMAIN),
	        );
	    wp_send_json( $response );    	
	}
}