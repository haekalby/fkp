<?php
// need languages extend
require_once TEMPLATEPATH . '/includes/languages.php';

class ET_AdminSetting extends ET_AdminMenuItem{

	private $options;

	function __construct(){
		parent::__construct('et-settings',  array(
			'menu_title'	=> __('Settings', ET_DOMAIN),
			'page_title' 	=> __('SETTINGS', ET_DOMAIN),
			'callback'   	=> array($this, 'menu_view'),
			'slug'			=> 'et-settings',
			'page_subtitle'	=> __('Manage all settings for ForumEngine', ET_DOMAIN),
			'pos' 			=> 10
		));

		// only admin can do these actions
		if(current_user_can( 'manage_options' )) {
			$this->add_ajax('et-change-branding', 'change_branding', true, false );
			$this->add_ajax('et-update-general-setting', 'update_general_settings', true, false );
			$this->add_ajax('et-update-mail-template', 'update_mail_template', true, false );
			$this->add_ajax('et-set-default-mail-template', 'reset_mail_template', true, false );
			$this->add_ajax('et_set_current_language', 'set_current_language', true, false);
			$this->add_ajax('et-set-current-language', 'ajax_set_current_language', true, false);
			$this->add_ajax('et-add-lang', 'ajax_add_language', true, false);
			$this->add_ajax('et-get-translations', 'ajax_get_translations', true, false);
			$this->add_ajax('et-save-translation', 'ajax_save_translations', true, false);
			$this->add_ajax('fe_update_content', 'fe_update_content', true, false);
			$this->add_ajax('et-toggle-option', 'toggle_option', true, false);	
		}
		
	}

	public function on_add_scripts(){
		$this->add_existed_script( 'jquery' );
		$this->add_existed_script( 'underscore' );
		$this->add_existed_script( 'backbone' );
		$this->add_existed_script( 'jquery-ui-sortable' );
		$this->add_script( 'lib-nested-sortable', TEMPLATEURL . '/js/libs/jquery.nestedSortable.js', array('jquery', 'jquery-ui-sortable') );
		$this->add_script('lib-autosize',  		TEMPLATEURL . '/js/libs/jquery.autosize.min.js', array('jquery'));
		$this->add_script('fe-function',  		TEMPLATEURL . '/js/functions.js');
		$this->add_script('backend-script',  	TEMPLATEURL . '/admin/js/admin.js');
		$this->add_script('backend-setting',  	TEMPLATEURL . '/admin/js/settings.js', array('jquery', 'underscore', 'backbone', 'backend-script'));
		$this->add_script('fe-upload-images', 	TEMPLATEURL . '/js/upload-images.js');
		wp_enqueue_script( 'plupload-all' );
		$this->add_script('backend-setting-lang',  TEMPLATEURL . '/admin/js/lang.js', array('jquery', 'underscore', 'backbone'));

		wp_localize_script( 'backend-setting', 'fe_setting_msgs', array(
			'limit_category_level' => __("Categories' level is limited to 3", ET_DOMAIN)
		) );
	}

	public function on_add_styles(){
		$this->add_style('backend-style', TEMPLATEURL . '/admin/css/admin.css');
		$this->add_style( 'job-label', TEMPLATEURL . '/admin/css/job-label.min.css' );
	}

	public function menu_view($args){
		?>
		<div class="et-main-header">
			<div class="title font-quicksand"><?php echo $args->menu_title ?></div>
			<div class="desc"><?php echo $args->page_subtitle ?></div>
		</div>
		<div class="et-main-content" id="forum_settings">
			<div class="et-main-left">
				<ul class="et-menu-content inner-menu">
					<li>
						<a href="#section/setting-general" class="section-link active">
							<span class="icon" data-icon="y"></span><?php _e("General", ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a href="#section/setting-social" class="section-link">
							<span class="icon" data-icon="B"></span><?php _e("Socials", ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a href="#section/setting-content" class="section-link">
							<span class="icon" data-icon="l"></span><?php _e("Content",ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a href="#section/setting-mailing" class="section-link">
							<span class="icon" data-icon="M"></span><?php _e("Email template",ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a href="#section/setting-language" class="section-link">
							<span class="icon" data-icon="G"></span><?php _e("Language",ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a href="#section/setting-update" class="section-link">
							<span class="icon" data-icon="~"></span><?php _e("Update",ET_DOMAIN);?>
						</a>
					</li>
				</ul>
			</div>
			<div class="et-main-right">
				<?php include dirname(__FILE__) . '/settings/general.php'; ?>
				<?php include dirname(__FILE__) . '/settings/social-api.php'; ?>
				<?php include dirname(__FILE__) . '/settings/content.php'; ?>
				<?php include dirname(__FILE__) . '/settings/mailing.php'; ?>
				<?php include dirname(__FILE__) . '/settings/language.php'; ?>
				<?php include dirname(__FILE__) . '/settings/update.php'; ?>
			</div>
		</div>
		<?php
	}
	//handle update content
	public function fe_update_content(){
		try {

			global $wpdb;
			$users = get_users();
			$count = 0;

			foreach ($users as $user) {
				if(!get_user_meta( $user->ID, 'et_like_count', true)){
					$threads = get_posts(array(
							'post_type' => array('thread','reply'),
							'author' => $user->ID,
							'posts_per_page' => -1,
							'meta_query' => array(
									array(
										'key' => 'et_likes',
										'value' => null,
										'compare' => '!='
									)
								)
						));
					foreach ($threads as $thread) {
						$likes = get_post_meta( $thread->ID, 'et_likes', true );
						$count += count($likes);
					}
					update_user_meta( $user->ID, 'et_like_count', $count );
					$count = 0;
				}
			}

			update_option('fe_update_users_likes',true);

			$resp = array(
				'success'	=> true,
				'count'		=> $count,
				'msg'		=> __('Your contents has been updated successfully!',ET_DOMAIN)
			);
		} catch (Exception $e) {
			$resp = array(
				'success'	=> 	false,
				'msg' 		=> $e->getMessage
			);
		}
		wp_send_json( $resp );
	}
	//handle disable option
	public function toggle_option(){
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

	//handle enable option
	public function enable_option(){
		header( 'HTTP/1.0 200 OK' );
		header( "Content-Type: application/json" );
		$response	=	array (
			'success'	=> 	true,
			'msg'		=>	'enable'
		);

		$gateway	=	strtoupper($_POST['content']['gateway']);
		switch ($gateway) {
			case 'PENDING_THREAD' :
				et_update_option('et_pending_thread',1);
			break;

			default :
			break;
		}

		echo json_encode( $response );
		exit;
	}

	//handle update mail template
	public function update_mail_template(){
		$mail 	=	isset($_POST['content']['type']) ? $_POST['content']['type'] : '';
		$value 	=	isset($_POST['content']['data']) ? $_POST['content']['data'] : '';

		$response 	=	array ('success' => false);
		$FE_MailTemplate = new FE_MailTemplate();
		if( $FE_MailTemplate->update_mail_template ($mail, $value) ){
			$response['success'] =	true;
		}
		wp_send_json( $response );
	}

	//handle reset mail template
	public function reset_mail_template(){
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		$mail 	=	isset($_POST['content']['type']) ? $_POST['content']['type'] : '';

		$response 	=	array ('success' => false);
		$FE_MailTemplate = new FE_MailTemplate();
		$return 	=	 $FE_MailTemplate->reset_mail_template ('et_'.$mail);

		if( $return != 1 ){
			$response['success'] =	true;
			$response['msg'] 	=	$return;
		}
		echo json_encode($response);
		exit();
	}

	//handle upload images logo
	public function change_branding(){
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		$res	= array(
			'success'	=> false,
			'msg'		=> __('There is an error occurred', ET_DOMAIN ),
			'code'		=> 400,
		);

		// check fileID
		if(!isset($_POST['fileID']) || empty($_POST['fileID']) || !isset($_POST['imgType']) || empty($_POST['imgType']) ){
			$res['msg']	= __('Missing image ID', ET_DOMAIN );
		}
		else {
			$fileID		= $_POST["fileID"];
			$imgType	= $_POST['imgType'];

			// check ajax nonce
			if ( !check_ajax_referer( $imgType . '_et_uploader', '_ajax_nonce', false ) ){
				$res['msg']	= __('Security error!', ET_DOMAIN );
			}
			elseif(isset($_FILES[$fileID])){

				// handle file upload
				$attach_id	=	et_process_file_upload( $_FILES[$fileID], 0, 0, array(
									'jpg|jpeg|jpe'	=> 'image/jpeg',
									'gif'			=> 'image/gif',
									'png'			=> 'image/png',
									'bmp'			=> 'image/bmp',
									'tif|tiff'		=> 'image/tiff'
									)
								);

				if ( !is_wp_error($attach_id) ){

					try {
						$attach_data	= et_get_attachment_data($attach_id);

						et_update_option('et_'.$imgType,$attach_data['large'][0]);

						$res	= array(
							'success'	=> true,
							'msg'		=> __('Branding image has been uploaded successfully', ET_DOMAIN ),
							'data'		=> $attach_data
						);
					}
					catch (Exception $e) {
						$res['msg']	= __( 'Error when updating settings.', ET_DOMAIN );
					}
				}
				else{
					$res['msg']	= $attach_id->get_error_message();
				}
			}
			else {
				$res['msg']	= __('Uploaded file not found', ET_DOMAIN);
			}
		}
		echo json_encode($res);
		exit;
	}
	//handle setting fields
	public function update_general_settings () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		$option_name	=	$_POST['content']['option_name'];
		$value			=	$_POST['content']['new_value'];
		if( $option_name == 'private_key' || $option_name == 'public_key' ) {
			$google_captcha	=	ET_GoogleCaptcha::get_api();
			$google_captcha[$option_name] = $value;
			ET_GoogleCaptcha::set_api( $google_captcha );
			wp_send_json( array ('success' => true ) );
		}
		$result  = et_update_option($option_name,$value);

		if($result){
			echo json_encode(array (
					'success'	=> true
			));
		} else {
			echo json_encode(array (
					'success'	=> false
			));
		}

		exit;
	}

	public function ajax_set_current_language(){
		try {
			$new_lang = $_POST['content']['lang'];
			et_update_option('et_current_language', $new_lang);
			$resp = array(
				'success' 	=> true,
				'data' 		=> array(
					'current_lang' 	=> $new_lang
				)
			);
		} catch (Exception $e) {
			$resp = array(
				'success' 	=> false,
				'msg'		=> $e->getMessage()
			);
		}
		$this->ajax_header();
		echo json_encode($resp);
		exit;
	}

	public function ajax_add_language(){
		try {
			parse_str($_POST['content'], $data);
			$new_lang 	= $data['lang_name'];
			$langs 		= et_get_option( 'et_languages' );

			$key 		= sanitize_title( $new_lang );

			if ( array_key_exists( $key, $langs) )
				throw new Exception( __('Language is existed', ET_DOMAIN) );

			if ( empty($key) || empty($new_lang) ){
				throw new Exception( __('Language name must not empty', ET_DOMAIN) );
			}

			// add new lang
			$langs[$key] 	= $new_lang;
			et_update_option( 'et_languages', $langs);
			// create new file
			$handle 		= new FE_Language();
			$handle->add_lang_file( $key );

			$resp = array(
				'success' 	=> true,
				'msg' 		=>'',
				'data'		=> array(
					'name' 		=> $key,
					'label' 	=> $new_lang
				)
			);
		} catch (Exception $e) {
			$resp = array(
				'success' 	=> false,
				'msg' 		=> $e->getMessage(),
			);
		}
		$this->ajax_header();
		echo json_encode($resp);
		exit;
	}

	public function ajax_get_translations(){
		try {
			$lang = $_POST['content']['lang'];

			// get language to edit
			$lang_handler = new FE_Language();
			$entries = $lang_handler->get_translation_from_file( $lang );

			// sort entries, the untranslated goes top
			$translated = array();
			if(!empty($entries)){
				foreach ($entries as $key => $entry) {
					if ( !empty($entry->translations[0]) ){
						$translated[$key] = $entries[$key];
						unset($entries[$key]);
					}
				}

				$entries += $translated;
			}
			// build html
			$count = 0;
			$htmlData = '';
			foreach ($entries as $entry) {
				$placeholder = __('Type the translation in your language', ET_DOMAIN);
				$translation = empty($entry->translations[0]) ? '' : $entry->translations[0];
				$html = <<<HTML
				<div class="form-item">
					<div class="label">{$entry->singular}</div>
					<input type="hidden" value="{$entry->singular}" name="singular[{$count}]">
					<textarea type="text" name="translations[{$count}]" class="autosize" row="1" placeholder="">{$translation}</textarea>
				</div>
HTML;
				$htmlData .= $html;
				$count++;
			}

			$resp = array(
				'success' 	=> true,
				'msg' 		=> '',
				'data' 		=> array(
					'html' => $htmlData
				)
			);
		} catch (Exception $e) {
			$resp = array(
				'success' 	=> false,
				'msg' 		=> $e->getMessage()
			);
		}

		$this->ajax_header();
		echo json_encode($resp);
		exit;
	}

	public function ajax_save_translations(){
		try {
			if ( empty($_POST['content']['trans']) )
				throw new Exception(__('Nothing to translate', ET_DOMAIN));
			$data = $_POST['content']['trans'];
			$lang = $_POST['content']['lang'];

			$lang_handle = new FE_Language();
			$lang_handle->save_lang($lang, $data);
			$count = count($data);

			$resp = array(
				'success' 	=> true,
				'msg' 		=> sprintf( __('You have updated %s translation(s) successfully.', ET_DOMAIN), $count),
				'data' 		=> array(
					'count' => $count
				)
			);
		} catch (Exception $e) {
			$resp = array(
				'success' 	=> false,
				'msg' 		=> $e->getMessage()
			);
		}

		$this->ajax_header();
		echo json_encode($resp);
		exit;
	}
}

function data_icon ( $data , $type = 'text' ) {
    if( $data == '' )
        echo '!';
    else {
    	if($type == 'text') echo 3;
    	if($type == 'link') {
    		$validator	=	new ET_Validator();
    		if($validator->validate('link', $data))  echo 3;
    		else echo '!';
    	}
    	if($type == 'email') {
    		$validator	=	new ET_Validator();
    		if($validator->validate('email', $data))  echo 3;
    		else echo '!';
    	}
    }
}
/**
 * Get attachment url by id
 */
function et_get_attachment_data($attach_id){

	// if invalid input, return false
	if (empty($attach_id) || !is_numeric($attach_id)) return false;

	$data		= array(
		'attach_id'	=> $attach_id
		);
	$all_sizes	= get_intermediate_image_sizes();

	foreach ($all_sizes as $size) {
		$data[$size]	= wp_get_attachment_image_src( $attach_id, $size );
	}
	return $data;
}
/**
 * Get backend editor default settings
 * @param array $args overwrite settings
 */
function backend_editor_settings($args = array()){
	return array(
	'media_buttons' => false,
	'teeny'			=> true,
	'tinymce' 		=> array(
		'height' 		=> 150,
		'autoresize_min_height'=> 150,
		'statusbar'		=> false,
		'setup' => 'function(ed) {
		    ed.on("blur",function(e){
				BESetting.updateMailTemplate(e,ed);
		    });
		}'
	));
}
/*
 * display enable/disable button
 */
function et_toggle_button ( $option, $label, $value ) {
	?>
	<div class="button-enable font-quicksand" data-name="<?php echo $option ?>">
		<a href="#" rel="<?php echo $option ?>" title="<?php echo $label ?>" class="deactive <?php echo $value ? '' : 'selected' ?>">
			<span><?php _e("Disable", ET_DOMAIN);?></span>
		</a>
		<a href="#" rel="<?php echo $option ?>" title="<?php echo $label ?>" class="active <?php echo $value ? 'selected' : '' ?>">
			<span><?php _e("Enable", ET_DOMAIN);?></span>
		</a>
	</div>
	<?php
}
?>