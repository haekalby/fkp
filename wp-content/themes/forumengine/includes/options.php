<?php

class FE_Options{

	static $instance = null;

	protected $defaults = array(
		'et_languages'        => array(),
		'et_current_language' => 'en',
		'et_sticky_threads'   => array()
	);

	/**
	 *
	 */
	public function __construct(){}

	public function set($name, $value){
		if (is_string($value)){
			$value	=	stripcslashes($value);
			$pattern = "/<[^\/>]*>(&nbsp;)*([\s]?)*<\/[^>]*>/";
			$value	=	preg_replace($pattern, '', $value);
			$value	= 	trim($value);
		}

		switch ($name) {
			case 'et_current_language':
				$this->set_current_language($value);
				break;
			case 'et_twitter_account':
			case 'et_facebook_link':
			case 'et_google_plus':
				return $this->set_social_links($name,$value);
				break;
			default:
				return update_option($name, $value);
				break;
		}
		return empty($value);
	}

	public function get($name, $default = false){
		$result = get_option($name, $default);
		if ( $result == false && isset($this->defaults[$name])){
			switch ($name) {
				case 'et_languages':
					return $this->default_languages();
					break;

				default:
					return $this->defaults[$name];
					break;
			}
		}
		else {
			return $result;
		}
	}

	/**
	 *
	 */
	public static function get_instance(){
		if ( self::$instance == null ){
			self::$instance = new FE_Options();
		}
		return self::$instance;
	}

	/**
	 * Get and add default language files
	 */
	protected function default_languages(){
		// default value
		$defaults = array('english' => 'English');
		et_update_option('et_languages', $defaults);

		// add new lang file
		$handle = new FE_Language();
		$handle->add_lang_file('english');

		//return
		return $defaults;
	}

	protected function set_social_links($name,$new_value){
		$validator	=	new ET_Validator();
		if( $validator->validate('url', $new_value) || $new_value == '' )
			return update_option($name, $new_value);
		return false;
	}

	protected function set_current_language($new_lang){
		$langs 			= (array)$this->get('et_languages');
		if ( !array_key_exists($new_lang, $langs) ){
			$new_lang = $this->defaults['et_current_language'];
		}

		update_option('et_current_language', $new_lang);
	}

}
/**
 * Update & Reset Mail Template
 * Class FE_MailTemplate
 *
 */
class FE_MailTemplate {
	public function __construct() {}
	/**
	 * update mail template settings
	 * @param string $mail : mail type
	 * @param string $value : new mail value
	 */
	public function update_mail_template ( $mail, $value ) {
		$value		=	stripcslashes($value);
		$key		=	'et_' . $mail;
		return update_option($key, $value);
	}

	public function reset_mail_template ( $mail) {
		$new_value	=	'';
		switch ($mail) {
			case 'et_register_mail':
				return $this->set_register_mail ( $new_value, true );

			case 'et_confirm_mail':
				return $this->set_confirm_mail ( $new_value, true );

			case 'et_confirmed_mail':
				return $this->set_confirmed_mail ( $new_value, true );

			case 'et_forgot_pass_mail':
				return $this->set_forgot_pass_mail ( $new_value, true );

			case 'et_reset_pass_mail':
				return $this->set_reset_pass_mail ( $new_value, true );

			case 'et_following_thread_mail':
				return $this->set_following_thread_mail ( $new_value, true );

			case 'et_pending_thread_mail':
				return $this->set_pending_thread_mail ( $new_value, true );

			case 'et_twitter_mail':
				return $this->set_twitter_mail ( $new_value, true );

			case 'et_facebook_mail':
				return $this->set_facebook_mail ( $new_value, true );

			case 'et_facebook_mail':
				return $this->set_facebook_mail ( $new_value, true );

			case 'et_ban_member_mail':
				return $this->set_ban_member_mail ( $new_value, true );

			case 'et_report_mail':
				return $this->set_report_mail ( $new_value, true );
			default:
				return false;
		}
	}

	public function get_register_mail() {
		$default	=	"<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Thank you and welcome to [blogname].</p>";

		if(get_option('et_register_mail', $default)){
			return stripcslashes(get_option('et_register_mail', $default));
		} else {
			return $default;
		}

	}

	public function set_register_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Thank you and welcome to [blogname].</p>";
		}
		update_option('et_register_mail', $new_value);
		return $new_value;
	}

	public function get_confirm_mail() {
		$default	=	"<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Please click the link below to confirm your email address.</p><p><a href='[confirm_link]'>[confirm_link]</a></p><p>Thank you and welcome to [blogname].</p>";

		if(get_option('et_confirm_mail', $default)){
			return stripcslashes(get_option('et_confirm_mail', $default));
		} else {
			return $default;
		}
	}

	public function set_confirm_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Please click the link below to confirm your email address.</p><p><a href='[confirm_link]'>[confirm_link]</a></p><p>Thank you and welcome to [blogname].</p>";
		}
		update_option('et_confirm_mail', $new_value);
		return $new_value;
	}

	public function get_confirmed_mail() {
		$default	=	"<p>Hi [display_name],</p><p>Your email address has been successfully confirmed.</p><p>Thank you and welcome to [blogname].</p>";
		if(get_option('et_confirmed_mail', $default)){
			return stripcslashes(get_option('et_confirmed_mail', $default));
		} else {
			return $default;
		}
	}

	public function set_confirmed_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hi [display_name],</p><p>Your email address has been successfully confirmed.</p><p>Thank you and welcome to [blogname].</p>";
		}
		update_option('et_confirmed_mail', $new_value);
		return $new_value;
	}

	public function get_forgot_pass_mail() {
		$default	=	"<p>Hello [display_name],</p><p>You have just sent a request to recover the password associated with your account in [blogname]. If you did not make this request, please ignore this email; otherwise, click the link below to create your new password:</p><p>[activate_url]</p><p>Regards,<br />[blogname]</p>";

		if(get_option('et_forgot_pass_mail', $default)){
			return stripcslashes(get_option('et_forgot_pass_mail', $default));
		} else {
			return $default;
		}
	}

	public function set_forgot_pass_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hello [display_name],</p><p>You have just sent a request to recover the password associated with your account in [blogname]. If you did not make this request, please ignore this email; otherwise, click the link below to create your new password:</p><p>[activate_url]</p><p>Regards,<br />[blogname]</p>";
		}
		update_option('et_forgot_pass_mail', $new_value);
		return $new_value;
	}

	public function get_reset_pass_mail() {
		$default	=	"<p>Hello [display_name],</p><p>You have successfully changed your password. Click this link &nbsp;[site_url] to login to your [blogname]'s account.</p><p>Sincerely,<br />[blogname]</p>";
		if(get_option('et_reset_pass_mail', $default)){
			return stripcslashes(get_option('et_reset_pass_mail', $default));
		} else {
			return $default;
		}
	}

	public function set_reset_pass_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hello [display_name],</p><p>You have successfully changed your password. Click this link &nbsp;[site_url] to login to your [blogname]'s account.</p><p>Sincerely,<br />[blogname]</p>";
		}
		update_option('et_reset_pass_mail', $new_value);
		return $new_value;
	}

	public function get_following_thread_mail() {
		$default	=	"<p>Hi there,</p><p>The thread <strong>'[thread_title]'</strong> you are following has a new reply. </p><p>Click <a href='[thread_link]'>here</a> to view the thread.</p><p>Sincerely,<br />[blogname]</p>";
		if(get_option('et_following_thread_mail', $default)){
			return stripcslashes(get_option('et_following_thread_mail', $default));
		} else {
			return $default;
		}
	}
	public function get_pending_thread_mail() {
		$default	=	"<p>Hello [display_name],</p><p>The thread <strong>'[thread_title]'</strong> has just been created and it’s waiting for your confirmation.</p><p>Please click <a href='[thread_link]'>here</a> to view the thread.</p><p>Sincerely,<br />[blogname]</p>";
		if(get_option('et_following_thread_mail', $default)){
			return stripcslashes(get_option('et_pending_thread_mail', $default));
		} else {
			return $default;
		}
	}

	public function get_ban_member_mail() {
		$default	=	"<p>Hello [display_name],</p><p>You have been banned from [blogname] for reason:</p><p>[reason]</p><p>Your ban will be expired on [ban_expired]</p><p>Please contact our staff for more information</p><p>Sincerely,<br />[blogname]</p>";
		if(get_option('et_ban_member_mail', $default)){
			return stripcslashes(get_option('et_ban_member_mail', $default));
		} else {
			return $default;
		}
	}

	public function get_report_mail() {
		$default	=	"<p>Hello [display_name],</p><p>You have a new  report from:  [blogname]</p><p>Thread content :</p><p> [thread_content]</p><p>Report message:</p><p> [report_message]</p><p>Thread link:</p><p> [thread_link]</p><p>Sincerely,<br />[blogname]</p>";
		if(get_option('et_report_mail', $default)){
			return stripcslashes(get_option('et_report_mail', $default));
		} else {
			return $default;
		}
	}

	public function set_following_thread_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hello [display_name],</p><p>The thread <strong>'[thread_title]'</strong> you are following has a new reply. </p><p>Click <a href='[thread_link]'>here</a> to view the thread.</p><p>Sincerely,<br />[blogname]</p>";
		}
		update_option('et_following_thread_mail', $new_value);
		return $new_value;
	}
	public function set_pending_thread_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hello [display_name],</p><p>The thread <strong>'[thread_title]'</strong> has just been created and it’s waiting for your confirmation.</p><p>Please click <a href='[thread_link]'>here</a> to view the thread.</p><p>Sincerely,<br />[blogname]</p>";
		}
		update_option('et_pending_thread_mail', $new_value);
		return $new_value;
	}

	public function set_ban_member_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hello [display_name],</p><p>You have been banned from [blogname] for reason:</p><p>[reason]</p><p>Your ban will be expired on [ban_expired]</p><p>Please contact our staff for more information</p><p>Sincerely,<br />[blogname]</p>";
		}
		update_option('et_ban_member_mail', $new_value);
		return $new_value;
	}
	public function set_report_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hello [display_name],</p><p>You have a new  report from:  [blogname]</p><p>Thread content :</p><p> [thread_content]</p><p>Report message:</p><p> [report_message]</p><p>Thread link:</p><p> [thread_link]</p><p>Sincerely,<br />[blogname]</p>";
		}
		update_option('et_report_mail', $new_value);
		return $new_value;
	}

	public function get_twitter_mail() {
		$default	=	"<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Thank you and welcome to [blogname].</p>";

		if(get_option('et_twitter_mail', $default)){
			return stripcslashes(get_option('et_twitter_mail', $default));
		} else {
			return $default;
		}
	}

	public function set_twitter_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hello [display_name],</p><p>You have successfully registered an account with&nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Thank you and welcome to [blogname].</p>";
		}
		update_option('et_twitter_mail', $new_value);
		return $new_value;
	}

	public function get_facebook_mail() {
		$default	=	"<p>Hello [display_name],</p><p>You have successfully registered an account with&nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Thank you and welcome to [blogname].</p>";

		if(get_option('et_facebook_mail', $default)){
			return stripcslashes(get_option('et_facebook_mail', $default));
		} else {
			return $default;
		}
	}

	public function set_facebook_mail ( $new_value, $default ) {
		if($default) {
			$new_value	=	"<p>Hello [display_name],</p><p>You have successfully registered an account with&nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Thank you and welcome to [blogname].</p>";
		}
		update_option('et_facebook_mail', $new_value);
		return $new_value;
	}

}

/**
 * Category manupilations
 */
class FE_ThreadCategory extends ET_Term {
	public $color_option = '';

	public function __construct () {
		parent::__construct('thread_category', __('Thread category', ET_DOMAIN));
		$this->color_option = 'et_color_thread_category';
	}

	public function getAll($args = array()){
		$ordered    = array();
		$result 	= array();
        $args   	= wp_parse_args( $args,  array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
        $categories = get_terms($this->taxonomy, $args);

        $order      = get_option($this->order_option);

        if ($order) {
            foreach ($order as $id => $parent) {
                foreach ($categories as $key => $cat) {
                    if ($cat->term_id == $id ){
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
		$result = parent::create($term, $args);

		// add color
		if ( !is_wp_error( $result ) && isset($args['color']) )
			$this->set_term_color( $result['term_id'], $args['color'] );

		return $result;
	}

	public function update($id, $args = array()){
		$result = parent::update($id, $args);

		// add color
		if ( !is_wp_error( $result ) && isset($args['color']) )
			$this->set_term_color( $result['term_id'], $args['color'] );

		return $result;
	}

	public function sort( $data ,$args = array()){

        $args  = wp_parse_args( $args,  array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
		$terms = get_terms( $this->taxonomy, $args );

		foreach ($terms as $key => $term) {
			foreach ($data as $id => $parent) {
				if ( $term->term_id == $id && !empty($parent) )
					wp_update_term( $term->term_id, $this->taxonomy, array('parent' => $parent) );
			}
		}

        update_option($this->order_option, $data);
	}

	protected function set_term_color($term_id, $color){
		$colors = et_get_option( $this->color_option );

		if ( !is_array( $colors ) ) $colors = array();

		$colors[$term_id] = $color;
		et_update_option( $this->color_option, $colors );
	}

	public function get_term_color($term_id){
		$colors = et_get_option( $this->color_option );
		return isset($colors[$term_id]) ? $colors[$term_id] : 1;
	}

	function get_term_link ($term) {
		return get_term_link( $term,$this->taxonomy );
	}

	function set_color ($colors) {
		//this function should be override if tax have color
		update_option( $this->color_option, $colors);
	}

	function get_color () {
		// this function should be override if tax have color
		return (array) get_option( $this->color_option , array());
	}

	function change_color(){
		$resp = array();
		if ( !empty($_REQUEST['content']['term_id']) && !empty($_REQUEST['content']['color']) ){
			$this->update_term_color($_REQUEST['content']['term_id'], $_REQUEST['content']['color']);
			$resp = array(
				'success'   => true,
				'msg'       => sprintf(__('%s color has been updated', ET_DOMAIN), $this->_tax_label )
				);
		}
		else {
			$resp = array(
				'success'   => false,
				'msg'       => __("An error has occurred!", ET_DOMAIN)
				);
		}
		return $resp;
	}

	function update_term_color ($term_id, $color ) {
		$colors = $this->get_color();

		$colors[$term_id] = $color;
		$this->set_color($colors);
	}

	static public function get_category_color($term_id){
		$colors = et_get_option('et_color_thread_category');
		return !empty($colors[$term_id]) ? $colors[$term_id] : 0;
	}

	static public function get_categories($args=array()){
		$handle = new FE_ThreadCategory();
		return $handle->getAll($args);
	}
}

/**
 * Handle Ajax request about thread category
 */
class FE_ThreadCategoryAjax extends FE_ThreadCategory{
	public function __construct(){
		parent::__construct('thread_category', __('Thread category', ET_DOMAIN));
		add_action('wp_ajax_et_term_sync', array(&$this, 'sync_term'));
	}

	function sync_term () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		// ajax class should overide this function
		try {
			// return false if request method is empty
			if ( empty($_REQUEST['method']) ) throw new Exception(__('There is an error occurred', ET_DOMAIN), 400);

			$method = empty( $_REQUEST['method'] ) ? '' : $_REQUEST['method'] ;
			$data   = $_REQUEST['content'];

			switch ($method) {
				case 'delete':
					$term  		= $_REQUEST['content']['id'];
					$default 	= isset($_REQUEST['content']['default']) ? $_REQUEST['content']['default'] : false;

					// check if category has children or not
					$children = get_terms('thread_category', array('parent' => $term, 'hide_empty' => false));

					if ( !empty($children) )
						throw new Exception(__('You cannot delete a parent category. You need to delete its sub-categories first.', ET_DOMAIN));

					// delete
					$result   	= $this->delete ( $term, $default );

					if ( is_wp_error( $result ) ){
						throw new Exception($result->get_error_message());
					}
					else if ( $result ) {
						$resp = array(
							'success' => true
						);
					} else {
						throw new Exception(__("Can't delete category", ET_DOMAIN));
					}
					break;

				case 'create' :
					$term  		= $_POST['content']['name'];
					$args 		= array('color' => 0);

					if ( empty($term) ) throw new Exception( __('Category name is required', ET_DOMAIN) );

					if ( isset($_REQUEST['content']['color']) ) $args['color'] = $_REQUEST['content']['color'];
					if ( isset($_REQUEST['content']['parent']) ) $args['parent'] = $_REQUEST['content']['parent'];

					$result   	= $this->create($term, $args);

					if ( is_wp_error( $result ) ){
						throw new Exception($result->get_error_message());
					}
					else {
						$data 			= get_term($result['term_id'], $this->taxonomy);
						$data->color 	= $this->get_term_color($result['term_id']);
						$resp = array(
							'success' 	=> true,
							'data' 		=> array(
								'term' 		=> $data,
							)
						);
					}
					break;

				case 'update' :
					$term  	= $_REQUEST['content']['id'];
					$args 	= array();

					if ( empty($term) ) throw new Exception( __("Cannot find category", ET_DOMAIN) );

					if ( isset($_REQUEST['content']['name']) ) $args['name'] 		= $_REQUEST['content']['name'];
					if ( isset($_REQUEST['content']['color']) ) $args['color'] 		= $_REQUEST['content']['color'];
					if ( isset($_REQUEST['content']['parent']) ) $args['parent'] 	= $_REQUEST['content']['parent'];
					//$args 	= $_REQUEST['content']['args'] ? $_REQUEST['content']['args'] : array();

					$result   = $this->update($term, $args);
					if ( is_wp_error( $result ) ){
						throw new Exception($result->get_error_message());
					}
					else {
						$data = get_term($result['term_id'], $this->taxonomy);
						$data->color 	= $this->get_term_color($result['term_id']);
						$resp = array(
							'success' 	=> true,
							'data' 		=> array(
								'term' 		=> $data
							)
						);
					}
					break;

				case 'sort':
					wp_parse_str( $_POST['content']['order'], $order );

					$order = $order['tax'];
					$handle = new FE_ThreadCategory ();
					$handle->sort($order);

					$resp = array(
						'success' 	=> true,
						'data' 		=> array(
							'order' => $order
						)
					);
					break;

				case 'changeColor' :
					$resp   =   $this->change_color($data);
					break;

				default:
					throw new Exception(__('There is an error occurred', ET_DOMAIN), 400);
					break;
			}
			// refresh sorted job categories
		} catch (Exception $e) {
			$resp = build_error_ajax_response(array(), $e->getMessage() );
		}
		echo json_encode( $resp );
		exit;
	}
}

class FE_BackendCategory extends FE_ThreadCategory{

	function print_backend_terms ($parent = 0, $positions = false) {
		?>
		<ul class="list-job-input list-tax category list-job-categories cat-sortable tax-sortable" id="thread_cats" data-tax="<?php echo $this->taxonomy ?>">
		<?php
			$this->print_backend_terms_li ($parent,$positions) ;
		?>
		</ul>
		<ul id="cat_create" class="list-job-input category add-category ">
			<li class="tax-item color-2">
				<div class="container">
					<!-- <form id ="form_new_tax" class="new_tax" action="" data-tax='<?php echo $this->taxonomy ?>'> -->
						<div class="controls controls-2">
							<!-- <div class="button">
								<span class="icon" data-icon="+"></span>
							</div> -->
							<button class="button" type="submit"><span class="icon" data-icon="+"></span></button>
						</div>
						<div class="input-form input-form-1 color-default">
							<div class="cursor color-2" data="2"><span class="flag"></span></div>
							<input class="bg-grey-input tax-name" name="name" placeholder="<?php _e('Add a category', ET_DOMAIN) ?>" type="text" />
						</div>
					<!-- </form> -->
				</div>
			</li>
		</ul>
		<?php
	}

	function print_backend_terms_li($parent = 0, $positions = false) {
		$colors = $this->get_color();
		if ( !$positions )
			$positions = $this->getAll();
		foreach ($positions as $job_pos) {
			if ( $job_pos->parent == $parent ){
			?>
			<li class="tax-item <?php echo isset($colors[$job_pos->term_id]) ? 'color-' . (int)$colors[$job_pos->term_id] : 'color-0' ?>" data-id="<?php echo $job_pos->term_id ?>" id="tax_<?php echo $job_pos->term_id ?>">
				<div class="container">
					<div class="sort-handle"></div>
					<div class="controls controls-2">
						<a class="button act-open-form" rel="<?php echo $job_pos->term_id ?>"  title="<?php _e('Add sub tax for this tax', ET_DOMAIN) ?>">
							<span class="icon" data-icon="+"></span>
						</a>
						<a class="button act-del" rel="<?php echo $job_pos->term_id ?>">
							<span class="icon" data-icon="*"></span>
						</a>
					</div>
					<div class="input-form input-form-1" data-action="et_update_<?php echo $this->taxonomy ?>_color">
						<div class="cursor"><span class="flag"></span></div>
						<input class="bg-grey-input tax-name" name="name" rel="<?php echo $job_pos->term_id ?>" type="text" value="<?php echo $job_pos->name ?>">
					</div>
				</div>
				<ul>
					<?php $this->print_backend_terms_li($job_pos->term_id, $positions); ?>
				</ul>
			</li>
			<?php
			} // end if
		} // end foreach
	}

	function print_confirm_list () {
		if(!is_array($this->_term_in_order) ) $this->getAll ();
	?>
		<script type="text/template" id="temp_<?php echo $this->taxonomy ?>_delete_confirm">
			<div class="moved-tax">
				<span><?php _e('Move jobs to', ET_DOMAIN) ?></span>
				<div class="select-style et-button-select">
					<select name="move_<?php echo $this->taxonomy ?>" id="move_<?php  echo $this->taxonomy ?>">

					<?php foreach ($this->_term_in_order as $term ) {  ?>
							<option value="<?php echo $term->term_id ?>"><?php echo $term->name ?></option>
					<?php } ?>

					</select>
				</div>
				<button class="backend-button accept-btn"><?php _e("Accept", ET_DOMAIN); ?></button>
				<a class="icon cancel-del" data-icon="*"></a>
			</div>
		</script>
	<?php
	}
}

function et_get_option($name, $default = false){
	$instance = FE_Options::get_instance();
	return $instance->get($name, $default);
}

function et_update_option($name, $value){
	$instance = FE_Options::get_instance();
	return $instance->set($name, $value);
}

/**
 * Build general success response for ajax request
 * @param $data returned data
 * @param $msg returned message
 * @param $code returned code
 * @since 1.0
 */
function build_success_ajax_response($data, $msg = '', $code = 200){
	return array(
		'success' 	=> true,
		'code' 		=> $code,
		'msg' 		=> $msg,
		'data' 		=> $data
		);
}

/**
 * Build general error response for ajax request
 * @param $data returned data
 * @param $msg returned message
 * @param $code returned code
 * @since 1.0
 */
function build_error_ajax_response($data, $msg = '',$code = 400){
	return array(
		'success' 	=> false,
		'code' 		=> $code,
		'msg' 		=> $msg,
		'data' 		=> $data
		);
}

?>