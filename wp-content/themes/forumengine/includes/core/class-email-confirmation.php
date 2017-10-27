<?php
/**
 * @author: nguyenvanduocit
 * @date: 3/25/2015 : 2:07 PM
 * @copyright : 2015 nguyenvanduocit
 */
class ET_Email_Confirmation{
	static $instance;
	public static function get_instance(){
		if(null ==self::$instance){
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Add hooks
	 * @author: nguyenvanduocit
	 */
	public function add_hook(){
		/**
		 * Check wherever user confirmed, then show the notification
		 */
		add_action('ce_after_header', array(self::get_instance(),'show_confirm_notification'));
		add_action('ce_after_insert_seller', array(self::get_instance(),'may_send_confirm_email'), 10, 2);
		add_action('ce_user_update_email', array(self::get_instance(),'may_send_confirm_email'), 10, 2);
	}

	/**
	 * Get confirm page url
	 *
	 * @return bool
	 * @test \Test_EmailConfirmation::test_get_confirm_page_url
	 * @author: nguyenvanduocit
	 */
	public function get_confirm_page_url(){
		$link = et_get_page_link('user-confirm');
		$link = apply_filters("et_get_confirm_page_url", $link);
		return $link;
	}

	/**
	 * Show notify when user is not acitvated.
	 * @unitest \Test_EmailConfirmation::test_show_confirm_notification_for_normail_user
	 * @author: nguyenvanduocit
	 */
	public function show_confirm_notification(){ 
		if( et_is_logged_in() ){
			if($this->is_email_confirm_enabled() && !$this->is_user_activate()) {
				$recent_link = $this->get_confirm_page_url();
                ?>
                <div class="top_static_notification">
                	<div class="main-center container">
                		<?php printf(__("Please activate your account to post ads. Otherwise, you can click into the url <a href='%s?action=resend'>here</a> to resend  activation email.", ET_DOMAIN), $recent_link) ?>
                	</div>
            	</div>
            	<?php
			}
		}
	}

	/**
	 * Send confirm email if need
	 *
	 * @param $user
	 * @param $data
	 *
	 * @author: nguyenvanduocit
	 */
	public function may_send_confirm_email($user , $data){
		if($this->is_email_confirm_enabled()){
			$this->send_validate_email();
		}
	}
	/**
	 * Send validate code to user
	 *
	 * @param null $user_id
	 *
	 * @return bool
	 */
	public function send_validate_email($user_id = null)
	{	
		$send_mail = new CE_Mailing();
		global $wpdb;
		if ( null == $user_id ) {
			global $current_user;
			$user_id = $current_user->ID;
		}
		$user_data = get_userdata($user_id);
		$activation_key = $this->generate_validate_code();
		$result = $wpdb->update($wpdb->users, array('user_activation_key' => $activation_key), array('ID' => $user_id), array('%s'), array('%d'));
		if ($result) {

			$message = "<p>".__('To activate account you have to click on the link below :', ET_DOMAIN) . "\r\n";
			$message .= '<a href="' . $this->get_confirm_page_url() . "?action=confirm&key=" . $activation_key . "&login=" . rawurlencode($user_data->user_login) . "\">\r\n";
			$message .= 'Activate</a>' . "<p>";
			$mes = apply_filters('confirm_email', $message);
			$title = sprintf(__('[%s] Account confirm', ET_DOMAIN), get_option('blogname'));

			$sent = $send_mail->wp_mail($user_data->user_email, $title, $mes,'',array(
            	'user_id' => $user_id
        	));

			if (!$sent) {
				wp_die(__('The email could not be sent.', ET_DOMAIN) . "<br />\n" . __('Possible reason: your host may have disabled the mail() function...', ET_DOMAIN));
			}
			return true;
		}
	}

	/**
	 * Generate validate code
	 *
	 * @return
	 * @internal param null $user_id
	 */
	private function generate_validate_code()
	{
		$activation_key = wp_generate_password(20, false);
		return $activation_key;
	}

	/**
	 * Check if validate code is validate
	 *
	 * @param $validate_code
	 * @param $login
	 *
	 * @return bool|\WP_Error
	 */
	public function check_validate_code($validate_code, $login)
	{
		global $wpdb, $wp_hasher;

		$key = preg_replace('/[^a-z0-9]/i', '', $validate_code);

		if (empty($key) || !is_string($key))
			return new WP_Error('invalid_key', __('Your activation key is incorrect.',ET_DOMAIN));

		if (empty($login) || !is_string($login))
			return new WP_Error('invalid_key', __('Your activation key is incorrect.', ET_DOMAIN));

		$row = $wpdb->get_row($wpdb->prepare("SELECT ID, user_activation_key FROM $wpdb->users WHERE user_login = %s", $login));
		if (!$row)
			return new WP_Error('invalid_key', __('Invalid key'));

		if($row->user_activation_key == ''){
			return new WP_Error('invalid_key', __('Your account has already been activated before.',ET_DOMAIN));
		}

		if (empty($wp_hasher)) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash(8, true);
		}

		if ($key == $row->user_activation_key) {
			return $this->activate_user($login);
		}

		return new WP_Error('expired_key', __('An error has occured but we couldn\'t find out the error code. Please contact the administrator to fix this issue.', ET_DOMAIN));
	}

	/**
	 * Approve user
	 *
	 * @Active : Check hash, delete user_activation_key if approved
	 *
	 * @param null $login
	 *
	 * @return bool
	 */
	public function activate_user($login = null)
	{
		global $wpdb;
		if ( null == $login ) {
			global $current_user;
			$login = $current_user->ID;
		}
		$wpdb->update($wpdb->users, array('user_activation_key' => ""), array('user_login' => $login), array('%s'), array('%s'));
		return true;
	}

	/**
	 * Check if user is activated
	 * @param integer $userid
	 * @return true if user is activate. failse if user is not activate
	 */
	public function is_user_activate($userid = null)
	{
		global $wpdb;
		/** @var int $userid */
		if ( null == $userid ) {
			global $current_user;
			$userid = $current_user->ID;
		}

		if(user_can($userid,  'manage_options' )){
			return true;
		}

		$row = $wpdb->get_var($wpdb->prepare("SELECT user_activation_key FROM $wpdb->users WHERE ID = '%s'", $userid));
		return ($row === null);
	}

	/**
	 * Check if email validate is enable
	 */
	public function is_email_confirm_enabled()
	{
		$is_enable = get_theme_mod('ce_email_confirm', 0);
		return ( 1 == $is_enable );
	}
}
ET_Email_Confirmation::get_instance()->add_hook();