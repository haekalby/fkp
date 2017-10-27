<?php
/**
* Author: Dakachi
* Date created: 10-03-2014
* Description: Google recaptcha class
* GOOGLE CAPTCHA TUTORIAL
* To use google captcha, you need to have 2 variables public key and private key
* Register at https://www.google.com/recaptcha/admin/create
* //======================================================
* To generate Google recaptcha box:
* //========================================
* $GCaptcha = DCGoogleCaptcha::getInstance();
* $publicKey = '';
* $GCaptcha->generateCaptchaBox($publicKey)
* //======================================================
* To check words:
* //========================================
* $GCaptcha = DCGoogleCaptcha::getInstance();
* $privateKey = '';
* if ($GCaptcha->checkCaptcha($privateKey)) {
*   //Correct result
* }
* else {
*   //Incorrect result
* }
* //======================================================
*/

class ET_GoogleCaptcha
{
    private static $_instance;
    public function __construct(){ }
    private function __clone() {}

    public static function getInstance(){
        if ( ! self::$_instance instanceof ET_GoogleCaptcha )
            self::$_instance = new ET_GoogleCaptcha();
        return self::$_instance;
    }

    /**
    * generate captcha box to check security
    *
    * @param mixed $publicKey
    */
    public function generateCaptchaBox()
    {
        $key        =   $this->get_api();
        $publicKey =   $key['public_key'];

        echo "<div style='width: 100%'>" . recaptcha_get_html( $publicKey, null) . '</div>';
    }

    /**
    * check words typed correctly
    *
    * @param mixed $privateKey
    */
    public function checkCaptcha($challenge , $response )
    {
        $key        =   $this->get_api();
        $privateKey =   $key['private_key'];

        $bResult = false;

        if ( $response ) {
            $response = recaptcha_check_answer($privateKey, $_SERVER['REMOTE_ADDR'], $challenge, $response);

            if ($response->is_valid) {
                $bResult = true;
            }
        }

        return $bResult;
    }

    /**
     * check captcha
     * edited by Dakachi
     */
    public function et_checkCaptcha($content){
        global $user_ID;
        // not use captcha return true
        $useCaptcha =   et_get_option('google_captcha') ;
        if(!$useCaptcha) return 1;

        $google_captcha_user_role = get_option( 'google_captcha_user_role' );
        $role                     = get_user_role( $user_ID );

        if ( !in_array( $role, $google_captcha_user_role ) ) {
            return 1;
        }

        $myCategory = get_term_by('slug', $content['thread_category'] , 'thread_category');
        $google_captcha_cat=get_option( 'google_captcha_cat' );
        $response=1;
        if(!empty($google_captcha_cat)){
            if(in_array( $myCategory->term_id,$google_captcha_cat)){
                if( !$this->checkCaptcha( $content['recaptcha_challenge_field'] , $content['recaptcha_response_field']  ) ) {
                    $response = 0;
                }
            }
        }
        return $response;
    }
    public static function get_api () {
        return get_option('et_google_api_key', array (
                                    'private_key' =>  /*'6LdmzO8SAAAAALkfFCb7Twppu4axyXtjm4maJ82Y'*/ '' ,
                                    'public_key' =>  '' /*'6LdmzO8SAAAAAOQgKCsol68zZ4ob8W4AFxss8USn'*/ )
                );
    }

    public static function set_api ( $api ) {
        update_option( 'et_google_api_key' , $api );
    }

}

function check_google_captcha(){

    $data               = $_POST['content'];
    $myCategory         = get_term_by('slug', $data , 'thread_category');
    $google_captcha_cat = get_option( 'google_captcha_cat' );
    $useCaptcha         = et_get_option('google_captcha') ;

    $resp = array(
        'success' => false,
        'msg'     => 'Fail'
    );

    if($useCaptcha && $google_captcha_cat){
        if(in_array( $myCategory->term_id,$google_captcha_cat)){
            $resp = array(
                'success' => true,
                'msg'     => 'Success'
            );
        }
    }
    wp_send_json($resp);
}
add_action( 'fe_custom_fields_form' , 'render_captcha' );
add_action( 'wp_ajax_fe_check_google_captcha', 'check_google_captcha' );

function render_captcha(){
    $useCaptcha = et_get_option('google_captcha') ;
    if(!$useCaptcha){ return false; }
    if( !is_singular( 'thread' ) ){
        $captcha = ET_GoogleCaptcha::getInstance();
        $api     = $captcha->get_api();
        if( et_get_option( 'google_captcha') && $api['public_key'] && et_get_option( 'google_captcha_cat') && et_get_option( 'google_captcha_user_role') ){
            echo  "<div class='form-item' id='reCaptcha'>";
            $captcha->generateCaptchaBox();
            echo "</div>";
        } else {
            et_update_option('google_captcha', false);
        }
    }
}

function fe_check_captcha($content) {
    $captcha =  ET_GoogleCaptcha::getInstance();
    if(!$captcha->et_checkCaptcha($content)){
        wp_die( __("You have entered an invalid captcha.", ET_DOMAIN), __("Invalid captcha.", ET_DOMAIN)  );
    }
}
add_action('fe_before_submit_thread', 'fe_check_captcha');
/*
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          http://recaptcha.net/plugins/php/
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
 * AUTHORS:
 *   Mike Crawford
 *   Ben Maurer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * The reCAPTCHA server URL's
 */
define("RECAPTCHA_API_SERVER", "http://www.google.com/recaptcha/api");
define("RECAPTCHA_API_SECURE_SERVER", "https://www.google.com/recaptcha/api");
define("RECAPTCHA_VERIFY_SERVER", "www.google.com");

/**
 * Encodes the given data into a query string format
 * @param $data - array of string elements to be encoded
 * @return string - encoded request
 */
function _recaptcha_qsencode ($data) {
        $req = "";
        foreach ( $data as $key => $value )
                $req .= $key . '=' . urlencode( stripslashes($value) ) . '&';

        // Cut the last '&'
        $req=substr($req,0,strlen($req)-1);
        return $req;
}



/**
 * Submits an HTTP POST to a reCAPTCHA server
 * @param string $host
 * @param string $path
 * @param array $data
 * @param int port
 * @return array response
 */
function _recaptcha_http_post($host, $path, $data, $port = 80) {

        $req = _recaptcha_qsencode ($data);

        $http_request  = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
        $http_request .= "Content-Length: " . strlen($req) . "\r\n";
        $http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
        $http_request .= "\r\n";
        $http_request .= $req;

        $response = '';
        if( false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) ) {
                die ('Could not open socket');
        }

        fwrite($fs, $http_request);

        while ( !feof($fs) )
                $response .= fgets($fs, 1160); // One TCP-IP packet
        fclose($fs);
        $response = explode("\r\n\r\n", $response, 2);

        return $response;
}



/**
 * Gets the challenge HTML (javascript and non-javascript version).
 * This is called from the browser, and the resulting reCAPTCHA HTML widget
 * is embedded within the HTML form it was called from.
 * @param string $pubkey A public key for reCAPTCHA
 * @param string $error The error given by reCAPTCHA (optional, default is null)
 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)

 * @return string - The HTML to be embedded in the user's form.
 */
function recaptcha_get_html ($pubkey, $error = null, $use_ssl = false)
{
    if ($pubkey == null || $pubkey == '') {
        die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
    }

    if (is_ssl()) {
                $server = RECAPTCHA_API_SECURE_SERVER;
        } else {
                $server = RECAPTCHA_API_SERVER;
        }

        $errorpart = "";
        if ($error) {
           $errorpart = "&amp;error=" . $error;
        }

        ?>
                <script type="text/javascript">
                     var RecaptchaOptions = {
                        theme : 'custom',
                        custom_theme_widget: 'recaptcha_widget'
                     };
                </script>
                <style type="text/css">
                    #recaptcha_widget a {
                        display: inline-block;
                        width: 14px;
                        height: 14px;
                        margin-top: 1px;
                        line-height: 14px;
                        vertical-align: text-top;
                        margin-bottom: 15px;
                        overflow: hidden;
                    }
                    #recaptcha_widget .input-recaptcha {
                        width: 130px;
                        border: 1px solid #dddddd;
                        margin-right: 5px;
                        padding: 5px;
                    }
                    #recaptcha_widget .button {
                        width:28px;
                        height: 28px;
                        display: inline-block;
                        padding: 5px 0px;
                        margin-bottom: 0;
                        font-size: 14px;
                        line-height: 20px;
                        color: #333;
                        text-align: center;
                        text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
                        vertical-align: middle;
                        cursor: pointer;
                        background-color: #F5F5F5;
                        margin-top: -2px;
                        background-repeat: repeat-x;
                        border: 1px solid #BBB;
                        border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
                        border-color: #E6E6E6 #E6E6E6 #BFBFBF;
                        border-bottom-color: #A2A2A2;
                        -webkit-border-radius: 4px;
                        -moz-border-radius: 4px;
                        border-radius: 4px;
                        margin-right: 3px;
                    }
                    #recaptcha_widget label {
                        line-height: 20px;
                    }
                </style>
                <div id="recaptcha_widget" style="display:none;margin-top:10px;">

                    <div class="control-group">
                        <div class="controls">
                            <a id="recaptcha_image" href="#" class="thumbnail"></a>
                            <div class="recaptcha_only_if_incorrect_sol" style="color:red"><?php _e("Incorrect please try again", ET_DOMAIN); ?></div>
                        </div>
                    </div>

                       <div class="control-group">
                           <label class="recaptcha_only_if_image control-label"><?php _e("Enter the words above:", ET_DOMAIN); ?></label>
                            <label class="recaptcha_only_if_audio control-label"><?php _e("Enter the numbers you hear:", ET_DOMAIN); ?></label>

                            <div class="controls">
                                <div class="input-append">
                                    <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" class="input-recaptcha" />
                                    <a class="button" href="javascript:Recaptcha.reload()"><i  data-icon="0" class="icon"></i></a>
                                    <!-- <a class="button recaptcha_only_if_image" href="javascript:Recaptcha.switch_type('audio')"><span title="Get an audio CAPTCHA" data-icon=">" class="icon"></span></a> -->
                                    <!-- <a class="button recaptcha_only_if_audio" href="javascript:Recaptcha.switch_type('image')"><span title="Get an image CAPTCHA" data-icon="1" class="icon"></a> -->
                                    <a class="button" href="javascript:Recaptcha.showhelp()"><i data-icon="?" class="icon"></i></a>
                                </div>
                          </div>
                    </div>

                </div>

                <script type="text/javascript"
                   src="<?php echo $server . '/challenge?k=' . $pubkey . $errorpart; ?>">
                </script>

                <noscript>
                    <iframe src="<?php echo $server . '/challenge?k=' . $pubkey . $errorpart; ?>"
                       height="300" width="500" frameborder="0"></iframe><br>
                    <textarea name="recaptcha_challenge_field" rows="3" cols="40">
                    </textarea>
                    <input type="hidden" name="recaptcha_response_field"
                       value="manual_challenge">
                </noscript>

        <?php
        return '';
        // return '<script type="text/javascript" src="'. $server . '/challenge?k=' . $pubkey . $errorpart . '"></script>

        // <noscript>
        //       <iframe src="'. $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br/>
        //       <textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
        //       <input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
        // </noscript>';
}




/**
 * A ReCaptchaResponse is returned from recaptcha_check_answer()
 */
class ReCaptchaResponse {
        var $is_valid;
        var $error;
}


/**
  * Calls an HTTP POST function to verify if the user's guess was correct
  * @param string $privkey
  * @param string $remoteip
  * @param string $challenge
  * @param string $response
  * @param array $extra_params an array of extra variables to post to the server
  * @return ReCaptchaResponse
  */
function recaptcha_check_answer ($privkey, $remoteip, $challenge, $response, $extra_params = array())
{
    if ($privkey == null || $privkey == '') {
        die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
    }

    if ($remoteip == null || $remoteip == '') {
        die ("For security reasons, you must pass the remote ip to reCAPTCHA");
    }



        //discard spam submissions
        if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
                $recaptcha_response = new ReCaptchaResponse();
                $recaptcha_response->is_valid = false;
                $recaptcha_response->error = 'incorrect-captcha-sol';
                return $recaptcha_response;
        }

        $response = _recaptcha_http_post (RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/verify",
                                          array (
                                                 'privatekey' => $privkey,
                                                 'remoteip' => $remoteip,
                                                 'challenge' => $challenge,
                                                 'response' => $response
                                                 ) + $extra_params
                                          );

        $answers = explode ("\n", $response [1]);
        $recaptcha_response = new ReCaptchaResponse();

        if (trim ($answers [0]) == 'true') {
                $recaptcha_response->is_valid = true;
        }
        else {
                $recaptcha_response->is_valid = false;
                $recaptcha_response->error = $answers [1];
        }
        return $recaptcha_response;

}

/**
 * gets a URL where the user can sign up for reCAPTCHA. If your application
 * has a configuration page where you enter a key, you should provide a link
 * using this function.
 * @param string $domain The domain where the page is hosted
 * @param string $appname The name of your application
 */
function recaptcha_get_signup_url ($domain = null, $appname = null) {
    return "https://www.google.com/recaptcha/admin/create?" .  _recaptcha_qsencode (array ('domains' => $domain, 'app' => $appname));
}

function _recaptcha_aes_pad($val) {
    $block_size = 16;
    $numpad = $block_size - (strlen ($val) % $block_size);
    return str_pad($val, strlen ($val) + $numpad, chr($numpad));
}

/* Mailhide related code */

function _recaptcha_aes_encrypt($val,$ky) {
    if (! function_exists ("mcrypt_encrypt")) {
        die ("To use reCAPTCHA Mailhide, you need to have the mcrypt php module installed.");
    }
    $mode=MCRYPT_MODE_CBC;
    $enc=MCRYPT_RIJNDAEL_128;
    $val=_recaptcha_aes_pad($val);
    return mcrypt_encrypt($enc, $ky, $val, $mode, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
}


function _recaptcha_mailhide_urlbase64 ($x) {
    return strtr(base64_encode ($x), '+/', '-_');
}

/* gets the reCAPTCHA Mailhide url for a given email, public key and private key */
function recaptcha_mailhide_url($pubkey, $privkey, $email) {
    if ($pubkey == '' || $pubkey == null || $privkey == "" || $privkey == null) {
        die ("To use reCAPTCHA Mailhide, you have to sign up for a public and private key, " .
             "you can do so at <a href='http://www.google.com/recaptcha/mailhide/apikey'>http://www.google.com/recaptcha/mailhide/apikey</a>");
    }


    $ky = pack('H*', $privkey);
    $cryptmail = _recaptcha_aes_encrypt ($email, $ky);

    return "http://www.google.com/recaptcha/mailhide/d?k=" . $pubkey . "&c=" . _recaptcha_mailhide_urlbase64 ($cryptmail);
}

/**
 * gets the parts of the email to expose to the user.
 * eg, given johndoe@example,com return ["john", "example.com"].
 * the email is then displayed as john...@example.com
 */
function _recaptcha_mailhide_email_parts ($email) {
    $arr = preg_split("/@/", $email );

    if (strlen ($arr[0]) <= 4) {
        $arr[0] = substr ($arr[0], 0, 1);
    } else if (strlen ($arr[0]) <= 6) {
        $arr[0] = substr ($arr[0], 0, 3);
    } else {
        $arr[0] = substr ($arr[0], 0, 4);
    }
    return $arr;
}

/**
 * Gets html to display an email address given a public an private key.
 * to get a key, go to:
 *
 * http://www.google.com/recaptcha/mailhide/apikey
 */
function recaptcha_mailhide_html($pubkey, $privkey, $email) {
    $emailparts = _recaptcha_mailhide_email_parts ($email);
    $url = recaptcha_mailhide_url ($pubkey, $privkey, $email);

    return htmlentities($emailparts[0]) . "<a href='" . htmlentities ($url) .
        "' onclick=\"window.open('" . htmlentities ($url) . "', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;\" title=\"Reveal this e-mail address\">...</a>@" . htmlentities ($emailparts [1]);

}
?>
