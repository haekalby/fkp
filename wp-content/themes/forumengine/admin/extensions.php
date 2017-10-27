<?php
class ET_AdminExtensions extends ET_AdminMenuItem{

	function __construct(){
		parent::__construct('et-extensions',  array(
			'menu_title'	=> __('Extensions', ET_DOMAIN),
			'page_title' 	=> __('EXTENSIONS', ET_DOMAIN),
			'callback'   	=> array($this, 'menu_view'),
			'slug'			=> 'et-extensions',
			'icon_class'	=> 'icon-menu-extensions',
			'page_subtitle'	=> __('Check out these extensions for your forums from EngineThemes', ET_DOMAIN),
			'pos' 			=> 99
		));
	}

	public function on_add_scripts(){
		//$this->add_existed_script( 'jquery' );
		//$this->add_existed_script( 'backbone' );
		//wp_enqueue_script( 'fe-user-badge', TEMPLATEURL.'/admin/js/user-badge.js', array('jquery','backbone'));
	}

	public function on_add_styles(){
		$this->add_style('backend-style', TEMPLATEURL . '/admin/css/admin.css');
	}
	public function return_7200(){
		return 10;
	}
	public function menu_view($args){
			//add_filter( 'wp_feed_cache_transient_lifetime' , array($this,'return_7200') );
			$feed_extensions = fetch_feed( '//www.enginethemes.com/?act=request_extension&theme=forumengine' );
			$feed_themes 	 = fetch_feed( '//www.enginethemes.com/?act=request_theme&theme=forumengine' );
			//remove_filter( 'wp_feed_cache_transient_lifetime' , array($this,'return_7200') );
			//var_dump($feed_extensions);
			if ( ! is_wp_error( $feed_extensions ) ) :
			    $maxitems = $feed_extensions->get_item_quantity( 10 );
			    $feed_extensions_items = $feed_extensions->get_items( 0, $maxitems );
			endif;
			//var_dump($feed_themes);
			if ( ! is_wp_error( $feed_themes ) ) :
			    $maxitems = $feed_themes->get_item_quantity( 10 );
			    $feed_themes_items = $feed_themes->get_items( 0, $maxitems );
			endif;
		?>
		<div class="et-main-header">
            <div class="title font-quicksand"><?php echo $args->menu_title ?></div>
            <div class="desc"><?php echo $args->page_subtitle ?>.</div>
            <a href="//www.enginethemes.com/extensions/" class="et-btn-extension-goto"><span class="icon" data-icon="|"></span>&nbsp;&nbsp;Go to extension page</a>
        </div>

        <div class="et-main-content et-main-main" id="anonymous">
        	<?php
        	if(!empty($feed_extensions_items)){
        		foreach ( $feed_extensions_items as $item ) {
        			$release 		= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'release');
        			$release 		= $release[0]['data'];

        			$thumbnail_url 	= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'thumbnail');
        			$thumbnail_url  = $thumbnail_url[0]['data'];

        			$version 		= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'version');
        			$version 		= $version[0]['data'];

        			$compatible 	= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'compatible');
        			$compatible 	= $compatible[0]['data'];

        			$updated 		= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'updated');
        			$updated 		= $updated[0]['data'];

        			$price 			= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'price');
        			$price 			= $price[0]['data'];

        			$purchase 		= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'purchase');
        			$purchase 		= $purchase[0]['data'];

        			$isNew 			= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'isNew');
        			$isNew 			= $isNew[0]['data'];

        	?>
        	<div class="et-pakage-extension-wrapper">
                <div class="et-img-extension">
                    <img src="<?php echo $thumbnail_url ?>">
					<?php if($isNew == "true") { ?>
                    <img class="new-extension" src="//www.enginethemes.com/wp-content/themes/et_home/img/ex-new.png">
					<?php } ?>
                </div>

                <div class="et-extension-stt">
                	<p>Version<span><?php echo $version ?></span></p>
					<p class="bottom">Last updated<span><?php echo $updated ?></span></p>
                </div>
                <div class="et-extension-stt">
                	<p>Release Date<span><?php echo $release ?></span></p>
					<p class="bottom">Compatible up to<span><?php echo $compatible ?></span></p>
                </div>
                <div class="et-extension-content">
                	<div class="et-extension-text">
                    	<h2 class="et-extension-title"><?php echo $item->get_title() ?></h2>
                        <?php echo $item->get_description() ?>
                    </div>
                    <div class="button-exension">
                        <a target="_blank" href="<?php echo $purchase ?>"><span class="b"><small>$</small><?php echo $price ?> </span><span class="buy-now">GET <br> THIS NOW</span></a>
                    </div>
                </div>
                <div class="et-extension-line" style="margin:30px 0 40px;"></div>
			</div>
			<?php }} ?>

            <div class="title font-quicksand"><?php _e("Other themes from EngineThemes",ET_DOMAIN);?><a rel="nofollow" href="http://www.enginethemes.com/all-themes/" class="exten-link-page">Go to themes page&nbsp;&nbsp;<span class="icon" data-icon="]"></span></a></div>
            <div class="desc">
			 	<?php _e("Check out the other products from our team in 2014",ET_DOMAIN);?>
			</div>
			<?php
			if(!empty($feed_themes_items)){
				//$i = 1;
        		foreach ( $feed_themes_items as $item ) {
        			$price 			= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'price');
        			$price 			= $price[0]['data'];

        			$thumbnail_url 	= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'thumbnail');
        			$thumbnail_url  = $thumbnail_url[0]['data'];

        			$link 			= $item->get_item_tags(SIMPLEPIE_NAMESPACE_RSS_20, 'link');
        			$link  			= $link[0]['data'];
        	?>
            <div class="exten-theme-wrapper">
                <a target="_blank" href="<?php echo $link ?>" class="img-theme">
                   <img src="<?php echo $thumbnail_url ?>">
                </a>
                <div class="exten-theme-info">
                    <h1 class="exten-title"><a target="_blank" href="<?php echo $link ?>"><?php echo $item->get_title() ?></a> <span class="price">$<?php echo $price ?></span></h1>
                </div>
            </div>
            <?php //if( $i % 2 == 0 ){ echo '<div style="clear:both;"></div>';} ?>

			<?php
				//$i++;
				}
			}
			?>
        </div>
		<?php
	}
}