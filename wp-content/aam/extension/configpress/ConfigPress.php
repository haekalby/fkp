<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

/**
 * AAM ConfigPress extension
 *
 * @package AAM
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class AAM_ConfigPress extends AAM_Backend_Feature_Abstract {

    /**
     * Instance of itself
     * 
     * @var AAM_ConfigPress 
     * 
     * @access private
     */
    protected static $instance = null;
    
    /**
     *
     * @var type 
     */
    protected $config = null;
    
    /**
     *
     * @var type 
     */
    protected $rawConfig = null;
    
    /**
     * Initialize the extension
     * 
     * @return void
     * 
     * @access protected
     */
    public function __construct() {
        if (is_admin()) {
            add_action('aam-feature-registration', array($this, 'registerUI'));
            //print required JS & CSS
            add_action('admin_print_scripts', array($this, 'printJavascript'));
            //add custom ajax handler
            add_filter('aam-ajax-filter', array($this, 'ajax'), 10, 3);
            //export hook
            add_action('aam-export', array($this, 'export'), 10, 3);
        }
    }
    
    /**
     * 
     * @param type $bucket
     * @param type $feature
     * @param type $exporter
     */
    public function export($bucket, $feature, $exporter) {
        if ($bucket == 'system' && $feature == 'configpress') {
            $exporter->add('aam-configpress', $this->rawConfig);
        }
    }
    
    /**
     * Get configuration option/setting
     * 
     * If $option is defined, return it, otherwise return the $default value
     * 
     * @param string $option
     * @param mixed  $default
     * 
     * @return mixed
     * 
     * @access public
     */
    public static function get($option = null, $default = null) {
        //init config only when requested and only one time
        self::$instance->initialize();
        
        if (is_null($option)) {
            $value = self::$instance->config;
        } else {
            $chunks = explode('.', $option);
            $value = self::$instance->config;
            foreach ($chunks as $chunk) {
                if (isset($value[$chunk])) {
                    $value = $value[$chunk];
                } else {
                    $value = $default;
                    break;
                }
            }
        }
        
        return $value;
    }
    
    /**
     * Initialize the ConfigPress
     * 
     * @param boolean $force
     * 
     * @return void
     * 
     * @access protected
     */
    protected function initialize() {
        if (is_null($this->config)) {
            $this->rawConfig = $this->getConfig();
            
            try {
                $reader = new AAM_ConfigPress_Reader;
                $this->config = $reader->parseString($this->rawConfig);
            } catch (Exception $e) {
                AAM_Core_Console::add($e->getMessage());
                $this->config = array();
            }
        }
    }
    
    /**
     * Print javascript libraries
     *
     * @return void
     *
     * @access public
     */
    public function printJavascript() {
        if (AAM::isAAM()) {
            $baseurl = $this->getBaseurl('/js');
            wp_enqueue_script('aam-cp', $baseurl . '/aam-cp.js', array('aam-main'));
        }
    }
    
    /**
     * Get extension base URL
     * 
     * @param string $path
     * 
     * @return string
     * 
     * @access protected
     */
    protected function getBaseurl($path = '') {
        $contentDir = str_replace('\\', '/', WP_CONTENT_DIR);
        $baseDir    = str_replace('\\', '/', dirname(__FILE__));
        
        $relative = str_replace($contentDir, '', $baseDir);
        
        return content_url() . $relative . $path;
    }

    /**
     * Custom ajax handler
     * 
     * @param mixed            $response
     * @param AAM_Core_Subject $subject
     * @param string           $action
     * 
     * @return string
     * 
     * @access public
     */
    public function ajax($response, $subject, $action) {
        $cap   = AAM_Core_ConfigPress::get(self::getAccessOption(), 'administrator');
        $can   = AAM::getUser()->hasCapability($cap);
        
        if ($action == 'ConfigPress.save' && $can) {
            $response = $this->save();
        }
        
        return $response;
    }
    
    /**
     * Save config
     * 
     * @return boolean
     * 
     * @access protected
     */
    protected function save() {
        $blog   = (defined('BLOG_ID_CURRENT_SITE') ? BLOG_ID_CURRENT_SITE : 1);
        $config = filter_input(INPUT_POST, 'config');
        
        //normalize
        $data = str_replace(array('“', '”'), '"', $config);
        
        return AAM_Core_API::updateOption('aam-configpress', $data, $blog);
    }
    
     /**
     * @inheritdoc
     */
    public static function getAccessOption() {
        return 'aam.feature.utility.configpress';
    }
    
    /**
     * Get HTML content
     * 
     * @return string
     * 
     * @access public
     */
    public function getContent() {
        ob_start();
        require_once(dirname(__FILE__) . '/phtml/configpress.phtml');
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
    
    /**
     * 
     * @return type
     */
    public function getConfig() {
        $blog   = (defined('BLOG_ID_CURRENT_SITE') ? BLOG_ID_CURRENT_SITE : 1);
        $config = AAM_Core_API::getOption('aam-configpress', 'null', $blog);
        
        if (($config === 'null') && class_exists('ConfigPress')) {
            //transfer all configurations first time
            $config = ConfigPress::getInstance()->readOption();
            AAM_Core_API::updateOption('aam-configpress', $config, $blog);
        }
        
        return ($config === 'null' ? '' : $config);
    }
    
    /**
     * Register Contact/Hire feature
     * 
     * @return void
     * 
     * @access public
     */
    public function registerUI() {
        $cap = AAM_Core_Config::get(self::getAccessOption(), 'administrator');
        
        AAM_Backend_Feature::registerFeature((object) array(
            'uid'        => 'configpress',
            'position'   => 90,
            'title'      => __('ConfigPress', AAM_KEY),
            'capability' => $cap,
            'subjects'   => array(
                'AAM_Core_Subject_Role', 
                'AAM_Core_Subject_Default'
            ),
            'view'       => __CLASS__
        ));
    }
    
    /**
     * Bootstrap the extension
     * 
     * @return AAM_ConfigPress
     * 
     * @access public
     */
    public static function bootstrap() {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}