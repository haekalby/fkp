<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<!--[if lt IE 7]> <html class="ie ie6 oldie" lang="en"> <![endif]-->
	<!--[if IE 7]>    <html class="ie ie7 oldie" lang="en"> <![endif]-->
	<!--[if IE 8]>    <html class="ie ie8 oldie" lang="en"> <![endif]-->
	<!--[if gt IE 8]> <html class="ie ie9 newest" lang="en"> <![endif]-->
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=Edge">
		<meta charset="utf-8">
	    <title>
            <?php wp_title( '|', true, 'right' ); ?>
	    </title>
		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
		<!-- <meta name="thumbnail" content="<?php echo TEMPLATEURL . '/screenshot.png' ?>" /> -->
		<meta name="description" content="<?php echo et_get_option("blogdescription"); ?>" />
		<!-- Facebook Metas -->
		<meta property="og:image" content="<?php echo TEMPLATEURL . '/screenshot.png' ?>" />
		<!-- Facebook Metas / END -->
		<?php
			$favicon = et_get_option("et_mobile_icon") ? et_get_option("et_mobile_icon") : (TEMPLATEURL . '/img/fe-favicon.png');
		?>
		<link rel="shortcut icon" href="<?php echo $favicon ?>"/>
		<?php wp_head() ?>
		<link href="<?php echo TEMPLATEURL ?>/css/custom-ie.css" rel="stylesheet" media="screen">
		<!--[if lt IE 9]>
	      <script src="<?php echo TEMPLATEURL . '/js/libs/respond.min.js' ?>"></script>
	    <![endif]-->
	</head>
	<body <?php echo body_class() ?>>
	<div class="site-container">
		<div class="cnt-container">
			<div class="header-top bg-header">
				<div class="main-center container">
					<div class="row header-info">
						<div class="col-md-2 logo-header">
							<a href="<?php echo home_url( ) ?>" class="logo">
								<img src="<?php echo fe_get_logo() ?>"/>
							</a>
						</div>
						<?php global $current_user,$et_query; ?>
						<div class="col-md-10">
							<div class="row">
								<div class="login col-md-2 col-sm-2 <?php if($current_user->ID){ echo 'collapse';} ?>">
									<a id="open_login" data-toggle="modal" href="#modal_login">
										<span class="icon" data-icon="U"></span>
										<?php _e('Login or Join', ET_DOMAIN) ?>
									</a>
								</div>
								<div class="profile-account col-md-2 col-sm-2 <?php if(!$current_user->ID){ echo 'collapse';} ?>">
									<span class="name"><a href="javascript:void(0);"><?php echo $current_user->display_name; ?></a></span><span class="arrow"></span>
									<span class="img"><?php echo  et_get_avatar($current_user->ID) ?></span>
									<!-- <span class="number">8</span>   -->
									<div class="clearfix"></div>
								</div>
								<div class="search-header col-md-8  col-sm-6">
									<form id="fe_search_form" action="" method="post">
										<?php if(is_tax( 'thread_category' ) || isset($_REQUEST['tax_cat'])){ ?>
										<input type="hidden" id="thread_category" name="thread_category" value='<?php echo isset($_REQUEST['tax_cat']) ? $_REQUEST['tax_cat'] : get_query_var( 'term' ) ?>'>
										<?php } ?>
										<button class="btn" type="submit"><span class="icon-s"></span></button>
										<div class="search-text">
											<input type="text" autocomplete="off" id="search_field" name="s" value="<?php if(!empty($et_query)) echo implode(' ', $et_query['s']); ?>" placeholder="<?php _e('Search...',ET_DOMAIN); ?>">
										</div>
										<!-- <span class="icon clear-field collapse" data-icon="D"></span> -->
										<div class="btn-mobile"></div>
									</form>
									<div id="search_preview" class="search-preview empty">
									</div>
								</div>
							</div>
							<div class="dropdown-profile">
								<span class="arrow-up"></span>
								<div class="content-profile">
									<div class="head"><span class="text">@<?php echo $current_user->user_login; ?></span></div>
									<ul class="list-profile">
										<li>
											<a href="<?php echo get_author_posts_url($current_user->ID); ?>">
												<span class="icon" data-icon="U"></span>
												<br />
												<span class="text"><?php _e('Profile',ET_DOMAIN); ?></span>
											</a>
										</li>
										<!-- <li>
											<a href="#">
												<span class="icon" data-icon="M"></span>
												<br />
												<span class="text">Inbox (8)</span>
											</a>
										</li> -->
										<li>
											<a href="<?php echo wp_logout_url( $_SERVER['REQUEST_URI'] ); ?>">
												<span class="icon" data-icon="Q"></span>
												<br />
												<span class="text"><?php _e('Logout',ET_DOMAIN); ?></span>
											</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php if ( (!is_page_template( 'page-following.php' ) && !is_page_template( 'page-pending.php' ) && !is_search() && !is_front_page() && !is_tax('thread_category') && !is_tax('fe_tag') && !is_page_template( 'page-threads.php' ) ))  { ?>

			<div class="header-bottom">
				<div class="main-center">
					<?php
					$breadcrumbs = get_the_breadcrumb(array(
						'class' 		=> 'breadcrumbs',
						'item_class' 	=> 'icon'
						));
					echo $breadcrumbs;
					?>
				</div>
			</div>

			<?php } ?>