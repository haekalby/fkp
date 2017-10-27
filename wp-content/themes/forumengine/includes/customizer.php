<?php
define('CUSTOMIZE_DIR' , THEME_CONTENT_DIR . '/css');
/**
 * Trigger the customization mode here
 * When administrator decide to customize something,
 * he trigger a link that activate "customization mode".
 *
 * When he finish customizing, he click on the close button
 * on customizer panel to close the "customization mode".
 */
function et_customizer_init(){
	//
	$current_url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	if ( isset($_REQUEST['activate']) && $_REQUEST['activate'] == 'customizer' ){
		setcookie('et-customizer', '1', time() + 3600, '/');
		wp_redirect(esc_url(remove_query_arg('activate')));
		exit;
	} else if (isset($_REQUEST['deactivate']) && $_REQUEST['deactivate'] == 'customizer') {
		setcookie('et-customizer', '', time() - 3600, '/');
		wp_redirect(esc_url(remove_query_arg('deactivate')));
		exit;
	}
	if ( (isset($_COOKIE['et-customizer']) && $_COOKIE['et-customizer'] == true) && current_user_can( 'manage_options' ) ){
		add_action('wp_print_styles', 'et_customizer_print_styles');
		add_action('wp_print_scripts', 'et_customizer_print_scripts');
		add_action('wp_ajax_save-customization', 'et_customizer_save');
		add_action('wp_ajax_delete-customization-mobile', 'et_customizer_delete');
		add_action('wp_footer','et_customizer_panel');
	}else {
		// print customization
		add_action('fe_after_print_styles', 'et_customization_styles');
		add_action('wp_footer','et_customizer_trigger');
		add_action('body_class', 'et_layout_classes');
	}

	// check if customization is create or not
	if(!is_multisite() || (is_multisite() && get_current_blog_id() == 1) )
		if ( !file_exists( TEMPLATEPATH . '/css/customization.css' ) ){
			// save customization value into database
			$option 		= FE_Options::get_instance();
			$customization 	= $option->get('et_customization');
			// $general_opt	=	new ET_GeneralOptions();
			// $customization  = $general_opt->get_customization();
			$customization['pattern'] = "'" . $customization['pattern'] . "'";

			et_apply_customization($customization);
		}
	else {
		$site_id	=	get_current_blog_id();
		if ( !file_exists( TEMPLATEPATH . '/css/customization_{$site_id}.css' ) ){
			$option 			= FE_Options::get_instance();
			$customization 		= $option->get('et_customization');
			$customization['pattern'] = "'" . $customization['pattern'] . "'";

			et_apply_customization($customization);
		}
	}
}
add_action('init', 'et_customizer_init');


/**
 * Adds theme layout classes to the array of body classes.
 */
function et_layout_classes( $existing_classes ) {
	$current_layout 	= et_get_layout();

	if ( in_array( $current_layout, array( 'content-sidebar', 'sidebar-content' ) ) )
		$classes = array( 'two-column' );
	else
		$classes = array( 'one-column' );

	if('' == $current_layout ) $current_layout = 'content-sidebar';

	if ( 'content-sidebar' == $current_layout )
		$classes[] = 'right-sidebar';
	elseif ( 'sidebar-content' == $current_layout )
		$classes[] = 'left-sidebar';
	else
		$classes[] = $current_layout;

	$classes = apply_filters( 'et_layout_classes', $classes, $current_layout );

	return array_merge( $existing_classes, $classes );
}
add_filter( 'body_class', 'et_layout_classes' );

function et_get_customize_css_path () {
	/**
	 * add multisite check for customize style
	*/
	if(is_multisite() && get_current_blog_id() != 1 ) {
		$blog_id	=	get_current_blog_id();
		$customize_css 	=	TEMPLATEURL . "/css/customization_$blog_id.css";
	} else {
		$customize_css 	=	TEMPLATEURL . "/css/customization.css";
	}
	return $customize_css;
}

/**
 * Get customization option
 */
function et_get_customization_option(){
	$option = FE_Options::get_instance();
	$style 	= $option->get('et_customization');
	return $style;
}

/**
 * Set customization option
 */
function et_set_customization_option($value){
	$option = FE_Options::get_instance();
	$style 	= $option->set('et_customization', $value);
}

/**
 *
 */
function et_enqueue_gfont(){
	// enqueue google web font
	$fonts = array(
		'quicksand' => array(
			'fontface' 	=> 'Quicksand, sans-serif',
			'link' 		=> 'Quicksand'
		),
		'ebgaramond' => array(
			'fontface' 	=> 'EB Garamond, serif',
			'link' 		=> 'EB+Garamond'
		),
		'imprima' => array(
			'fontface' 	=> 'Imprima, sans-serif',
			'link' 		=> 'Imprima'
		),
		'ubuntu' => array(
			'fontface' 	=> 'Ubuntu, sans-serif',
			'link' 		=> 'Ubuntu'
		),
		'adventpro' => array(
			'fontface' 	=> 'Advent Pro, sans-serif',
			'link' 		=> 'Advent+Pro'
		),
		'mavenpro' => array(
			'fontface' 	=> 'Maven Pro, sans-serif',
			'link' 		=> 'Maven+Pro'
		),
	);
	$home_url	=	home_url();
	$http		=	substr($home_url, 0,5);
	if($http != 'https') {
		$http	=	'http';
	}
	foreach ($fonts as $key => $font) {
		echo "<link href='".$http."://fonts.googleapis.com/css?family=" . $font['link'] . "' rel='stylesheet' type='text/css'>";
	}
}

/**
 * Enqueue Customization css file
 */
function et_customization_styles(){
	global $wp_rewrite, $wpdb;

	// enqueue font style

	// enqueue customization file
	if ( is_multisite() && $wpdb->blogid !=1 )
		$file = THEME_CONTENT_DIR . '/css/customization_' . $wpdb->blogid . '.css';
	else
		$file = THEME_CONTENT_DIR . '/css/customization.css';

	if ( file_exists( $file ) ){
		$url = THEME_CONTENT_URL . '/css' . '/' . basename($file);
		wp_enqueue_style( 'fe-customization', $url, array('fe-mainstyle'));
		//echo '<link rel="stylesheet" type="text/css" href="' . $url . '"/>';
	}
}

function et_customizer_print_styles(){
	if ( current_user_can('manage_options') && !is_admin()){

		wp_register_style( 'et_colorpicker', FRAMEWORK_URL. '/js/lib/css/colorpicker.css' );
		wp_enqueue_style('et_colorpicker');

		// include fonts
		et_enqueue_gfont();

		// include less
		?>
		<script type="text/javascript">
			var customizer = {};
			<?php
				$style 	= et_get_customization();
				$layout = et_get_layout();
				foreach ($style as $key => $value) {
					$variable = $key;
					//$variable = str_replace('-', '_', $key);
					if ( preg_match('/^rgb/', $value) ){
						preg_match('/rgb\(([0-9]+), ([0-9]+), ([0-9]+)\)/', $value, $matches);
						$val = rgb2html($matches[1],$matches[2],$matches[3]);
						echo "customizer['{$variable}'] = '{$val}';\n";
					} else {
						echo "customizer['{$variable}'] = '" . stripslashes($value) . "';\n";
					}
				}
				echo "customizer['layout'] = '{$layout}';";
			?>
			var test = <?php echo json_encode(et_get_customization()); ?>
		</script>
		<?php
	}
}

function et_customizer_print_scripts(){
	if ( current_user_can('manage_options')&& !is_admin()){
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-slider');

		// color picker
		wp_register_script( 'et-colorpicker', FRAMEWORK_URL . '/js/lib/colorpicker.js', array('jquery', 'underscore', 'backbone') );
		wp_enqueue_script('et-colorpicker', false, array('jquery', 'underscore', 'backbone'));

		// scrollbar
		wp_register_script( 'et-tinyscrollbar', TEMPLATEURL . '/js/libs/jquery.tinyscrollbar.min.js' );
		wp_enqueue_script('et-tinyscrollbar', false, array('jquery', 'underscore', 'backbone'));

		// customizer script
		wp_register_script('et_customizer', TEMPLATEURL . '/js/customizer.js', array('jquery','underscore','backbone', 'et-colorpicker'), false, true);
		wp_enqueue_script('et_customizer');
		?>
		<link rel="stylesheet/less" type="txt/less" href="<?php echo TEMPLATEURL . '/css/define.less'?>">
		<?php

		wp_register_script( 'less-js', TEMPLATEURL . '/js/libs/less-1.4.1.min.js');
		wp_enqueue_script('less-js');
	}
}
function et_customizer_delete(){
	if( is_multisite() ) {

		$site_id	=	get_current_blog_id();
		if($site_id == 1){
			$css_mobile = ET_CSS_PATH . '/customization_mobile.css';
		}
		else {
			$css_mobile = ET_CSS_PATH . "/customization_mobile_$site_id.css";
		}
	} else {
		$css_mobile = ET_CSS_PATH . '/customization_mobile.css';
	}
	$return = unlink($css_mobile);
	wp_send_json($return);
}
function et_customizer_save(){
	if ( !current_user_can('manage_options') ) return;

	try {
		$customization = $_REQUEST['content']['customization'];

		// create css style from less file
		$clone = $customization;
		$clone['pattern'] = "'" . $clone['pattern'] . "'";
		et_apply_customization($clone);

		// set new layout
		et_set_layout( empty($customization['layout']) ? 'content-sidebar' : $customization['layout'] );

		// save customization value into database

		$option = FE_Options::get_instance();
		$style 	= $option->set('et_customization', $customization);

		$resp = array(
			'success' 	=> true,
			'code' 		=> 200,
			'msg' 		=> __("Changes are saved successfully.", ET_DOMAIN),
			'data' 		=> $option->get('et_customization')
		);
	} catch (Exception $e) {
		$resp = array(
			'success' 	=> false,
			'code' 		=> true,
			'msg' 		=> sprintf(__("Something went wrong! System cause following error <br/> %s", ET_DOMAIN) , $e->getMessage() )
		);
	}

	header( 'HTTP/1.0 200 OK' );
	header( 'Content-type: application/json' );
	echo json_encode($resp);
	exit;
}


/**
 * Apple customization from user and create css file
 * @since 1.0
 * @param options
 */
function et_apply_customization($options = array(), $preview = false){
	$default = array(
		'background' 	=> '#ffffff',
		'header' 		=> '#4B4B4B',
		'heading' 		=> '#4B4B4B',
		'text' 			=> '#555555',
		'footer' 		=> '#E0E0E0',
		'action_1' 		=> '#E87863',
		'action_2' 		=> '#E87863',
		'pattern' 		=> "'" . TEMPLATEURL . "/img/patterns/pattern.png'",
		'font-text' 	=> 'Arial, san-serif',
		'font-text-size' 	=> '14px',
		'font-action' 		=> 'Arial, san-serif',
		'font-action-size' 	=> '14px',
		'font-heading' 		=> 'Arial, san-serif',
		'font-heading-size' 	=> '12px',
	);
	$options 	= wp_parse_args($options, $default);
	$keys 		= array_keys($default);

	foreach ($options as $key => $value) {
		if (!in_array($key,$keys)){
			unset($options[$key]);
		}
	}

	// generate folder
	if ( !file_exists( ET_CONTENT_DIR ) )
		mkdir( ET_CONTENT_DIR );

	if ( !file_exists(THEME_CONTENT_DIR) )
		mkdir( THEME_CONTENT_DIR );

	if ( !file_exists(ET_CSS_PATH) )
		mkdir( ET_CSS_PATH );

	// convert file format from less to css
	$less = TEMPLATEPATH . '/css/customization.less';
	$less_mobile = TEMPLATEPATH . '/css/customization_mobile.less';
	// $less = TEMPLATEPATH . '/css/custom-et.less';
	// if ( $preview )
	// 	$css = TEMPLATEPATH . '/css/customization-preview.css';
	// else
	if( is_multisite() ) {

		$site_id	=	get_current_blog_id();
		if($site_id == 1){
			$css = ET_CSS_PATH . '/customization.css';
			$css_mobile = ET_CSS_PATH . '/customization_mobile.css';
		}
		else {
			$css = ET_CSS_PATH . "/customization_$site_id.css";
			$css_mobile = ET_CSS_PATH . "/customization_mobile_$site_id.css";
		}
	} else {
		$css = ET_CSS_PATH . '/customization.css';
		$css_mobile = ET_CSS_PATH . '/customization_mobile.css';
	}

	et_less2css( $less, $css, $options );
	et_less2css( $less_mobile, $css_mobile, $options );
}



/**
 * Show off the customizer pannel
 */
function et_customizer_panel(){
	if ( current_user_can('manage_options') ){
		$style 		= et_get_customization();
		$layout 	= et_get_layout();
		?>
		<div id="customizer" class="customizer-panel">
			<div class="close-panel"><a href="<?php echo esc_url(add_query_arg('deactivate', 'customizer')); ?>" class=""><span>*</span></a></div>
			<form action="" id="f_customizer">
				<div class="section">
					<div class="custom-head">
						<span class="spacer"></span><h3><?php _e('Color Schemes', ET_DOMAIN) ?></h3><span class="spacer"></span>
					</div>
					<div class="section-content">
						<ul class="blocks-grid">
							<li class="clr-block scheme-item" data="" style="background: #1abc9c"></li>
							<li class="clr-block scheme-item" data="" style="background: #ec9e03"></li>
							<li class="clr-block scheme-item" data="" style="background: #eb5257"></li>
							<li class="clr-block scheme-item" data="" style="background: #1abc9c"></li>
							<li class="clr-block scheme-item" data="" style="background: #CE534D"></li>
							<li class="clr-block scheme-item" data="" style="background: #2c3e50"></li>
							<li class="clr-block scheme-item" data="" style="background: #B5740B"></li>
							<li class="clr-block scheme-item" data="" style="background: #2980b9"></li>
						</ul>
					</div>
				</div>
				<div class="section">
					<div class="custom-head">
						<span class="spacer"></span><h3><?php _e('Page Options', ET_DOMAIN) ?></h3><span class="spacer"></span>
					</div>
					<div class="section-content" style="display: none">
						<h4><?php _e('Layout Style', ET_DOMAIN) ?></h4>
						<ul class="block-layout">
							<li class="<?php if ($layout == 'sidebar-content') echo 'current' ?>">
								<a class="l-sidebar layout-item" rel="two-column left-sidebar" data="sidebar-content" href="" title="<?php _e('Left Sidebar', ET_DOMAIN) ?>"><span></span></a>
							</li>
							<li class="<?php if ($layout == 'content-sidebar') echo 'current' ?>">
								<a class="r-sidebar layout-item" rel="two-column right-sidebar" data="content-sidebar" href="" title="<?php _e('Right Sidebar', ET_DOMAIN) ?>"><span></span></a>
							</li>
							<li class="<?php if ($layout == 'content') echo 'current' ?>">
								<a class="no-sidebar layout-item" rel="one-column" data="content" href="" title="<?php _e('One column', ET_DOMAIN) ?>"><span></span></a>
							</li>
						</ul>
						<h4><?php _e('Background patterns', ET_DOMAIN) ?></h4>
						<ul class="blocks-grid">
							<li class="clr-block pattern-item pattern-0 <?php if ($style['pattern'] == (TEMPLATEURL . '/img/patterns/pattern.png')) echo 'current' ?>" data="<?php echo TEMPLATEURL . '/img/patterns/pattern.png' ?>"></li>
							<li class="clr-block pattern-item pattern-1 <?php if ($style['pattern'] == (TEMPLATEURL . '/img/patterns/pattern1.png')) echo 'current' ?>" data="<?php echo TEMPLATEURL . '/img/patterns/pattern1.png' ?>"></li>
							<li class="clr-block pattern-item pattern-2 <?php if ($style['pattern'] == (TEMPLATEURL . '/img/patterns/pattern2.png')) echo 'current' ?>" data="<?php echo TEMPLATEURL . '/img/patterns/pattern2.png' ?>"></li>
							<li class="clr-block pattern-item pattern-3 <?php if ($style['pattern'] == (TEMPLATEURL . '/img/patterns/pattern3.png')) echo 'current' ?>" data="<?php echo TEMPLATEURL . '/img/patterns/pattern3.png' ?>"></li>
							<li class="clr-block pattern-item pattern-4 <?php if ($style['pattern'] == (TEMPLATEURL . '/img/patterns/pattern4.png')) echo 'current' ?>" data="<?php echo TEMPLATEURL . '/img/patterns/pattern4.png' ?>"></li>
							<li class="clr-block pattern-item pattern-5 <?php if ($style['pattern'] == (TEMPLATEURL . '/img/patterns/pattern5.png')) echo 'current' ?>" data="<?php echo TEMPLATEURL . '/img/patterns/pattern5.png' ?>"></li>
							<li class="clr-block pattern-item pattern-6 <?php if ($style['pattern'] == (TEMPLATEURL . '/img/patterns/pattern6.png')) echo 'current' ?>" data="<?php echo TEMPLATEURL . '/img/patterns/pattern6.png' ?>"></li>
							<li class="clr-block pattern-item pattern-7 <?php if ($style['pattern'] == (TEMPLATEURL . '/img/patterns/pattern7.png')) echo 'current' ?>" data="<?php echo TEMPLATEURL . '/img/patterns/pattern7.png' ?>"></li>
						</ul>
						<h4><?php _e('Colors', ET_DOMAIN) ?></h4>
						<ul class="blocks-list">
							<li>
								<div class="picker-trigger clr-block" data="header" style="background: <?php echo $style['header'] ?>"></div>
								<span class="block-label"><?php _e('Header Background', ET_DOMAIN) ?></span>
							</li>
							<li>
								<div class="picker-trigger clr-block" data="background" style="background: <?php echo $style['background'] ?>"></div>
								<span class="block-label"><?php _e('Page Background', ET_DOMAIN) ?></span>
							</li>
							<li>
								<div class="picker-trigger clr-block" data="footer" style="background: <?php echo $style['footer'] ?>"></div>
								<span class="block-label"><?php _e('Footer Background', ET_DOMAIN) ?></span>
							</li>
							<li>
								<div class="picker-trigger clr-block" data="action_1" style="background: <?php echo $style['action_1'] ?>"></div>
								<span class="block-label"><?php _e('Action 1', ET_DOMAIN) ?></span>
							</li>
							<li>
								<div class="picker-trigger clr-block" data="action_2" style="background: <?php echo $style['action_2'] ?>"></div>
								<span class="block-label"><?php _e('Action 2', ET_DOMAIN) ?></span>
							</li>
						</ul>
					</div>
				</div>
				<div class="section">
					<div class="custom-head">
						<span class="spacer"></span><h3><?php _e('Content Options', ET_DOMAIN) ?></h3><span class="spacer"></span>
					</div>
					<div class="section-content" style="display: none">
						<?php $fonts = apply_filters ('et_customize_fonts_face',array(
							'arial' 		=> array( 'fontface' => 'Arial, san-serif', 'name' => 'Arial' ),
							'helvetica' 	=> array( 'fontface' => 'Helvetica, san-serif', 'name' => 'Helvetica' ),
							'georgia'		=> array( 'fontface' => 'Georgia, serif', 'name' => 'Georgia' ),
							'times' 		=> array( 'fontface' => 'Times New Roman, serif', 'name' => 'Times New Roman' ),
							'quicksand'		=> array( 'fontface' => 'Quicksand, sans-serif', 'name' => 'Quicksand' ),
							'ebgaramond'	=> array( 'fontface' => 'EB Garamond, serif', 'name' => 'EB Garamond' ),
							'imprima' 		=> array( 'fontface' => 'Imprima, sans-serif', 'name' => 'Imprima' ),
							'ubuntu' 		=> array( 'fontface' => 'Ubuntu, sans-serif', 'name' => 'Ubuntu' ),
							'adventpro' 	=> array( 'fontface' => 'Advent Pro, sans-serif', 'name' => 'Advent Pro' ),
							'mavenpro' 		=> array( 'fontface' => 'Maven Pro, sans-serif', 'name' => 'Maven Pro' )
						)); ?>
						<div class="block-select">
							<label for=""><?php _e('Heading', ET_DOMAIN) ?></label>
							<div class="select-wrap">
								<div>
									<select class="fontchoose" name="font-heading">
										<?php foreach ($fonts as $key => $font) { ?>
											<option <?php if ( $style['font-heading'] == $font['fontface'] ) echo 'selected="selected"' ?> value="<?php echo $font['fontface'] ?>"><?php echo $font['name'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="slider-wrap">
							<div class="slider heading-size" data-min="18" data-max="29" data-value="<?php echo str_replace( 'px', '', $style['font-heading-size'] ) ?>">
								<input type="hidden" name="font-heading-size">
							</div>
						</div>
						<div class="block-select">
							<label for=""><?php _e('Content', ET_DOMAIN) ?></label>
							<div class="select-wrap">
								<div>
									<select class="fontchoose" name="font-text" id="">
										<?php foreach ($fonts as $key => $font) {?>
											<option <?php if ( $style['font-text'] == $font['fontface'] ) echo 'selected="selected"' ?> value="<?php echo $font['fontface'] ?>"><?php echo $font['name'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
						<div class="slider-wrap">
							<div class="slider text-size" data-min="12" data-max="14" data-value="<?php echo str_replace( 'px', '', $style['font-text-size'] ) ?>">
								<input type="hidden" name="font-text-size">
							</div>
						</div>
					</div>
				</div>
				<button type="button" class="btn blue-btn" id="save_customizer" title="<?php _e('Save', ET_DOMAIN) ?>"><span><?php _e('Save', ET_DOMAIN) ?></span></button>
				<button type="button" class="btn none-btn" id="reset_customizer" title="<?php _e('Reset', ET_DOMAIN) ?>"><span class="icon" data-icon="D"></span></span><span><?php _e('Reset', ET_DOMAIN) ?></span></button>
			</form>
		</div> <?php
	}
}

/**
 * Displaying the button that trigger the customizer panel
 */
function et_customizer_trigger(){
	if ( current_user_can('administrator') ){ ?>
		<a id="customizer_trigger" title="<?php _e('Activate customization mode', ET_DOMAIN) ?>" href="<?php echo esc_url(add_query_arg('activate','customizer')) ?>"></a>
	<?php
	}
}

/**
 * Functions
 */

function rgb2html($r, $g=-1, $b=-1)
{
	if (is_array($r) && sizeof($r) == 3)
		list($r, $g, $b) = $r;

	$r = intval($r); $g = intval($g);
	$b = intval($b);

	$r = dechex($r<0?0:($r>255?255:$r));
	$g = dechex($g<0?0:($g>255?255:$g));
	$b = dechex($b<0?0:($b>255?255:$b));

	$color = (strlen($r) < 2?'0':'').$r;
	$color .= (strlen($g) < 2?'0':'').$g;
	$color .= (strlen($b) < 2?'0':'').$b;
	return '#'.$color;
}

/**
 * Get and return customization values for
 * @since 1.0
 */
function et_get_customization(){
	// $general_opt	=	new ET_GeneralOptions();
	// $style 			= 	$general_opt->get_customization();
	$option = FE_Options::get_instance();
	$style 	= $option->get('et_customization');
	$style = wp_parse_args($style, array(
		'background' => '#ffffff',
		'header' 	=> '#4F4F4F',
		'heading' 	=> '#333333',
		'footer' 	=> '#F2F2F2',
		'text' 		=> '#446f9f',
		'action_1' 	=> '#e64b21',
		'action_2' 	=> '#e64b21',
		'pattern' 	=> '',
		'font-heading' 			=> 'Open Sans, Arial, Helvetica, sans-serif',
		'font-heading-weight' 	=> 'normal',
		'font-heading-style' 	=> '',
		'font-heading-size' 	=> '14px',
		'font-text' 			=> 'Open Sans, Arial, Helvetica, sans-serif',
		'font-text-weight' 		=> 'normal',
		'font-text-style' 		=> '',
		'font-text-size' 		=> '12px',
		));
	return $style;
}

/**
 * get and return layout
 * @since 1.0
 */
function et_get_layout(){
	$option = FE_Options::get_instance();
	return $option->get('et_layout');
}

/**
 *
 */
function et_set_layout($new_layout){
	$option = FE_Options::get_instance();
	$style 	= $option->set('et_layout', $new_layout);
}

?>