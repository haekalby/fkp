<?php
function fe_navigations(){
	global $wp_query, $wp_rewrite, $post, $current_user , $user_ID, $wpdb;

	$data = et_get_unread_follow();
	$taxonomy = is_tax( 'thread_category' ) ? 'thread_category' : 'fe_tag';
	$term = get_term_by( 'slug' , get_query_var( "term" ), $taxonomy);
	?>
		<ul class="nav-link">

			<li <?php if(is_front_page()){ ?> class="active" <?php }?>>
				<a href="<?php echo home_url() ?>">
					<span class="icon" data-icon="W"></span>
					<span class="text"><?php _e('ALL POSTS',ET_DOMAIN) ?></span>
					<?php
						if(!empty($data) && isset($data['unread']['data']) && count($data['unread']['data']) > 0){
					?>
					<span class="number"><?php echo count($data['unread']['data']) ?></span>
					<?php } ?>
				</a>
			</li>

			<li <?php echo is_page_template( 'page-following.php' ) ? 'class="active"' : ''; ?>>
				<a
			<?php
				if($user_ID){
					echo 'href="'.et_get_page_link("following").'"';
				} else {
					echo 'id="open_login" data-toggle="modal" href="#modal_login"';
				}
			?>>
				<span class="icon" data-icon="&"></span>
				<span class="text"><?php _e('Following',ET_DOMAIN) ?></span>
				<?php if($user_ID && count($data['follow']) > 0){ ?>
				<span class="number"><?php echo count($data['follow']) ;?></span>
				<?php } ?>
				</a>
			</li>
			<?php if ( et_get_option("pending_thread") && (et_get_counter('pending') > 0) &&(current_user_can("manage_threads") || current_user_can( 'trash_threads' )) ) {?>
			<li <?php echo is_page_template( 'page-pending.php' ) ? 'class="active"' : ''; ?>>
				<a href="<?php echo et_get_page_link("pending");?>">
					<span class="icon" data-icon="N"></span>
					<span class="text"><?php _e('PENDING POSTS',ET_DOMAIN) ?></span>
				</a>
			</li>
			<?php } ?>
			<?php do_action( 'fe_navigations' ); ?>
		</ul>
		<div class="clear-fix"></div>
	<?php
}
/**
 *
 */
function et_the_time( $from ){
	//
	if ( time() - $from > (7 * 24 * 60 * 60) ){
		//return sprintf( __('on %s at %s', ET_DOMAIN), date( get_option('date_format'), $from ), date( get_option( 'time_format' ), $from ) );
		return sprintf( __('on %s', ET_DOMAIN), date_i18n( get_option('date_format'), $from, true ) );
	} else {
		return et_human_time_diff( $from ) .' '.__('ago',ET_DOMAIN);
	}
}

function et_number_based($zero, $single, $plural, $num){
	if ( (int)$num <= 0 ){
		return $zero;
	} else if ( (int)$num == 1 ){
		return $single;
	} else if ( (int)$num > 1 ){
		return $plural;
	}
}

function et_selected( $selected, $current, $echo = true){
	if ( $selected == $current ){
		$return = 'selected="selected"';
	} else {
		$return = '';
	}

	if ( $echo ) echo $return;

	return $return;
}

/**
 * Determines the difference between two timestamps.
 *
 * The difference is returned in a human readable format such as "1 hour",
 * "5 mins", "2 days".
 *
 * @since 1.5.0
 *
 * @param int $from Unix timestamp from which the difference begins.
 * @param int $to Optional. Unix timestamp to end the time difference. Default becomes time() if not set.
 * @return string Human readable time difference.
 */
function et_human_time_diff( $from, $to = '' ) {
	if ( empty( $to ) )
		$to = current_time('timestamp');

	$diff = (int) abs( $to - $from );

	if ( $diff < HOUR_IN_SECONDS ) {
		$mins = round( $diff / MINUTE_IN_SECONDS );
		if ( $mins <= 1 )
			$mins = 1;
		/* translators: min=minute */
		$since = sprintf( et_number_based( __('%s min', ET_DOMAIN), __('%s min', ET_DOMAIN) , __('%s mins', ET_DOMAIN), $mins ), $mins );
	} elseif ( $diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS ) {
		$hours = round( $diff / HOUR_IN_SECONDS );
		if ( $hours <= 1 )
			$hours = 1;
		$since = sprintf( et_number_based( __('%s hour', ET_DOMAIN), __('%s hour', ET_DOMAIN), __('%s hours', ET_DOMAIN), $hours ), $hours );
	} elseif ( $diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS ) {
		$hours = round( $diff / HOUR_IN_SECONDS );
		$days = round( $diff / DAY_IN_SECONDS );
		if ( $days <= 1 )
			$days = 1;
		$since = sprintf( et_number_based( __('%s day', ET_DOMAIN), __('%s day', ET_DOMAIN), __('%s days', ET_DOMAIN), $days ), $days );
	} elseif ( $diff < 30 * DAY_IN_SECONDS && $diff >= WEEK_IN_SECONDS ) {
		$hours = round( $diff / HOUR_IN_SECONDS );
		$weeks = round( $diff / WEEK_IN_SECONDS );
		if ( $weeks <= 1 )
			$weeks = 1;
		$since = sprintf( et_number_based( __('%s week', ET_DOMAIN), __('%s week', ET_DOMAIN), __('%s weeks', ET_DOMAIN), $weeks ), $weeks );
	} elseif ( $diff < YEAR_IN_SECONDS && $diff >= 30 * DAY_IN_SECONDS ) {
		$hours = round( $diff / HOUR_IN_SECONDS );
		$months = round( $diff / ( 30 * DAY_IN_SECONDS ) );
		if ( $months <= 1 )
			$months = 1;
		$since = sprintf( et_number_based( __('%s month', ET_DOMAIN), __('%s month', ET_DOMAIN), __('%s months', ET_DOMAIN), $months ), $months );
	} elseif ( $diff >= YEAR_IN_SECONDS ) {
		$hours = round( $diff / HOUR_IN_SECONDS );
		$years = round( $diff / YEAR_IN_SECONDS );
		if ( $years <= 1 )
			$years = 1;
		$since = sprintf( et_number_based( __('%s year', ET_DOMAIN), __('%s year', ET_DOMAIN), __('%s years', ET_DOMAIN), $years ), $years );
	}

	return $since;
}

/**
 * Get elapsed time string
 * @param int $timestamp
 *
 */
function time_elapsed_string($ptime){
	$etime = time() - $ptime;

	if ($etime < 1){
		return '0 seconds';
	}

	$a = array( 12 * 30 * 24 * 60 * 60  =>  __('year', ET_DOMAIN),
				30 * 24 * 60 * 60       =>  __('month', ET_DOMAIN),
				24 * 60 * 60            =>  __('day', ET_DOMAIN),
				60 * 60                 =>  __('hour', ET_DOMAIN),
				60                      =>  __('minute', ET_DOMAIN),
				1                       =>  __('second', ET_DOMAIN)
				);

	if ( $etime > (7 * 24 * 60 * 60) ){
		return sprintf(' on %s at %s', date( get_option('date_format'), $ptime ), date( get_option( 'time_format' ) ) );
	}

	foreach ($a as $secs => $str)
	{
		$d = $etime / $secs;
		if ($d >= 1)
		{
			$r = round($d);
			return $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
		}
	}
}


/**
 * Print the breadcrumb
 */
function the_breadcrumb($args) {
	$args = wp_parse_args( $args, array(
		'id' 			=> 'crumbs',
		'class' 		=> '',
		'item_class' 	=> '',
		'item_before' 	=> '',
		'item_after' 	=> '',
		'link_before' 	=> '',
		'link_after' 	=> '',
	) );
	extract($args);
	if ( $class != '' ) $class = 'class="' . $class . '"';
	if ( $id != '' ) $id = 'id="' . $id . '"';
	if ( $item_class != '') $item_class = 'class="' . $item_class . '"';

	echo '<ul ' . $id . ' ' . $class . '>';
	if (!is_home()) {
		echo '<li ' . $item_class .'><a href="';
		echo home_url();
		echo '">';
		echo 'Home';
		echo "</a></li>";

		if (is_author()) {
			$author = get_user_by( 'id', get_query_var( 'author' ) );
			echo '<li ' . $item_class .'>';
			echo $author->display_name;
			echo __("'s Profile",ET_DOMAIN).'</li>';
		} elseif (is_page_template( 'page-change-pass.php' ) || is_page_template( 'page-edit-profile.php' )  ){
			global $user_ID;
			$userid = (isset($_GET['uid'])) ? $_GET['uid'] : $user_ID;
			$author = get_user_by( 'id', $userid );

			echo '<li ' . $item_class .'><a href="'.get_author_posts_url($userid).'">';
			echo $author->display_name;
			echo __("'s Profile",ET_DOMAIN).'</a></li>';

			echo '<li ' . $item_class .'>';
			echo the_title();
			echo '</li>';

		} elseif (is_category() || is_singular('post')) {
			echo '<li ' . $item_class .'>';
			the_category(' </li><li ' . $item_class .'> ');
			if (is_single()) {
				echo "</li><li ' . $item_class .'>";
				the_title();
				echo '</li>';
			}
		} elseif (is_page()) {
			echo '<li ' . $item_class .'>';
			echo the_title();
			echo '</li>';
		} elseif ( !is_singular( 'post' ) ) {
			do_action( 'et_breabcrumb_post_type' , $args);
		}
	}
	elseif (is_tag()) { single_tag_title(); }
	elseif (is_day()) { echo"<li ' . $item_class .'>Archive for "; the_time('F jS, Y'); echo'</li>';}
	elseif (is_month()) {echo"<li ' . $item_class .'>Archive for "; the_time('F, Y'); echo'</li>';}
	elseif (is_year()) {echo"<li ' . $item_class .'>Archive for "; the_time('Y'); echo'</li>';}
	elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li ' . $item_class .'>Blog Archives"; echo'</li>';}
	elseif (is_search()) {echo"<li ' . $item_class .'>Search Results"; echo'</li>';}

	do_action('et_breadcrumb', $args);
	echo '</ul>';
}

function get_the_breadcrumb($args){
	$args = wp_parse_args( $args, array(
		'id' 			=> 'crumbs',
		'class' 		=> '',
		'item_class' 	=> '',
		'item_before' 	=> '',
		'item_after' 	=> '',
		'link_before' 	=> '',
		'link_after' 	=> '',
	) );
	extract($args);
	if ( $class != '' ) $class = 'class="' . $class . '"';
	if ( $id != '' ) $id = 'id="' . $id . '"';
	if ( $item_class != '') $item_class = 'class="' . $item_class . '"';

	$breadcrumbs = '';

	$breadcrumbs = '<ul ' . $id . ' ' . $class . '>';
	if (!is_home()) {
		$breadcrumbs .= '<li ' . $item_class .'><a href="';
		$breadcrumbs .= home_url();
		$breadcrumbs .= '">';
		$breadcrumbs .= __('Home',ET_DOMAIN);
		$breadcrumbs .= "</a></li>";

		if (is_author()) {
			$author = get_user_by( 'id', get_query_var( 'author' ) );
			$breadcrumbs .= '<li ' . $item_class .'>';
			$breadcrumbs .= $author->display_name;
			$breadcrumbs .= __("'s Profile",ET_DOMAIN).'</li>';
		} elseif (is_page_template( 'page-change-pass.php' ) || is_page_template( 'page-edit-profile.php' )  ){
			global $user_ID;
			$userid = (isset($_GET['uid'])) ? $_GET['uid'] : $user_ID;
			$author = get_user_by( 'id', $userid );

			$breadcrumbs .= '<li ' . $item_class .'><a href="'.get_author_posts_url($userid).'">';
			$breadcrumbs .= $author->display_name;
			$breadcrumbs .= __("'s Profile",ET_DOMAIN).'</a></li>';

			$breadcrumbs .= '<li ' . $item_class .'>';
			$breadcrumbs .= get_the_title();
			$breadcrumbs .= '</li>';

		} elseif ( is_category() || is_singular('post')) {
			global $post;
			$categories = get_the_category();
			$category 	= '';
			foreach ($categories as $cat) {
				$category = $cat;
				break;
			}
			if ( get_option('page_for_posts') ){
				$breadcrumbs .= "<li $item_class><a href=" .get_permalink( get_option('page_for_posts') ) . ">" . __('Blog', ET_DOMAIN) . "</a></li>";
			}
			$breadcrumbs .= '<li ' . $item_class .'><a href="'. get_category_link( $category->term_id ) .'">' . $category->name . '</a></li>';
			if (is_single()) {
				$breadcrumbs .= "<li ' . $item_class .'>";
				$breadcrumbs .= get_the_title();
				$breadcrumbs .= '</li>';
			}
		} elseif (is_page()) {
			$breadcrumbs .= '<li ' . $item_class .'>';
			$breadcrumbs .= get_the_title();
			$breadcrumbs .= '</li>';
		} elseif (is_tag()) {
			//$breadcrumbs .= '<li ' . $item_class .'>';
			$tag = get_query_var('tag');
			$breadcrumbs .= '<li ' . $item_class .'>'. $tag .'</li>';
			//$posttags = get_the_tags();
			// foreach ($posttags as $tag) {
			// 	$breadcrumbs .= '<li ' . $item_class .'><a href="'. get_tag_link( $tag->term_id ) .'">' . $tag->name . '</a></li>';
			// }
			//single_tag_title();
		} elseif ( !is_singular( 'post' ) ) {
			do_action( 'et_breabcrumb_post_type' , $args);
		}
	}
	elseif (is_day()) {
		$breadcrumbs .= "<li ' . $item_class .'>Archive for ";
		$breadcrumbs .= get_the_time('F jS, Y');
		$breadcrumbs .= '</li>';}
	elseif (is_month()) {
		$breadcrumbs .= "<li ' . $item_class .'>Archive for ";
		$breadcrumbs .= get_the_time('F, Y');
		$breadcrumbs .= '</li>';
	}
	elseif (is_year()) {
		$breadcrumbs .="<li ' . $item_class .'>Archive for ";
		$breadcrumbs .= get_the_time('Y');
		$breadcrumbs .='</li>';
	}
	elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {
		$breadcrumbs .="<li ' . $item_class .'>Blog Archives";
		$breadcrumbs .='</li>';
	}
	elseif (is_search()) {
		$breadcrumbs .= "<li ' . $item_class .'>Search Results";
		$breadcrumbs .='</li>';
	}

	$breadcrumbs .= '</ul>';
	$breadcrumbs = apply_filters( 'et_get_breadcrumb', $breadcrumbs , $args);
	return $breadcrumbs;
}

/**
 *
 */
function et_thread_categories_list ( $parent =	0 , $level = 1 , $categories = false, $parents = array() ) {
	$current_cat = get_query_var( 'term' );
	if ( !$categories )
		$cats = FE_ThreadCategory::get_categories();
	else
		$cats = $categories;
	$expand = apply_filters( 'fe_expand_category', false );
	if ( !empty($cats) ){
		foreach ($cats as $cat) {
			if ( $cat->parent != $parent ) continue;

			$cat_link = get_term_link( $cat, 'thread_category' );

			$has_child = false;
			foreach ($cats as $child) {
				if ( $child->parent == $cat->term_id ){
					$has_child = true;
					break;
				}
			}

			$opened = in_array( $cat->term_id, $parents);

			$color = FE_ThreadCategory::get_category_color($cat->term_id);
			?>
			<li id="nav_cat_<?php echo $cat->term_id ?>" class="<?php echo $current_cat == $cat->slug ? "all" : '' ?>">
				<a class="cat-item <?php echo $current_cat == $cat->slug ? "current" : '' ?>" href="<?php echo $cat_link ?> ">
					<?php echo $cat->name ?>
					<span class="flags color-<?php echo $color ?>"></span>
				</a>
				<?php if ( $has_child ) { ?>
					<a class="arrow  <?php if($expand){} else {echo $opened ? "" : 'collapsed' ;}?>" data-toggle="collapse" href="#nav_cat_children_<?php echo $cat->term_id ?>">
						<span></span>
					</a>
				<?php } ?>
				<?php if ($has_child){ ?>
					<ul id="nav_cat_children_<?php echo $cat->term_id ?>" class="child <?php if(!$expand){echo 'collapse ';} echo $opened ? "in" : '' ?>">
						<?php et_thread_categories_list ( $cat->term_id , $level + 1, $cats, $parents); ?>
					</ul>
				<?php } ?>
			</li> <?php
		}
	}
}

function et_get_cat_parents($current, $cats = array()){
	if ( empty($cats) )
		$cats = FE_ThreadCategory::get_categories();

	$c = $current;
	if ( empty($current) ) return array();

	$t = null;
	$return = array();

	while ( true ){
		$break = true;
		foreach ($cats as $cat) {
			if ( !empty($cat->parent) && ($c == $cat->term_id || $c == $cat->slug) ) {
				$return[] = $cat->term_id;

				$c = $cat->parent;
				$break = false;
				break;
			} else if ( empty($cat->parent) && ($c == $cat->term_id || $c == $cat->slug) ) {
				$return[] = $cat->term_id;
				$break = true;
				break;
			}
		}
		if ( $break ) {
			return $return;
		}
	}
}

function et_the_mobile_cat_list( $parent = 0, $level = 1, $categories = false ){
	$current_cat = get_query_var( 'term' );
	if ( !$categories )
		$cats = FE_ThreadCategory::get_categories();
	else
		$cats = $categories;

	if ( !empty($cats) ){
		foreach ($cats as $cat) {
			if ( $cat->parent != $parent ) continue;

			$cat_link = get_term_link( $cat, 'thread_category' );

			$has_child = false;
			foreach ($cats as $child) {
				if ( $child->parent == $cat->term_id ){
					$has_child = true;
					break;
				}
			}
			$color = FE_ThreadCategory::get_category_color($cat->term_id);
			?>
			<li id="mo_nav_cat_<?php echo $cat->term_id ?>" class="li-cat <?php echo $current_cat == $cat->slug ? "all" : '' ?>">
				<a class="cat-item <?php echo $current_cat == $cat->slug ? "current" : '' ?>" href="<?php echo $cat_link ?> ">
					<?php echo $cat->name ?>
					<span class="flags color-<?php echo $color ?>"></span>
				</a>
				<?php if ( $has_child ) { ?>
					<a class="arrow collapsed" data-toggle="collapse" href="#mo_nav_cat_children_<?php echo $cat->term_id ?>">
						<span></span>
					</a>
				<?php } ?>
				<?php if ($has_child){ ?>
					<ul id="mo_nav_cat_children_<?php echo $cat->term_id ?>" class="child collapse">
						<?php et_the_mobile_cat_list ( $cat->term_id , $level + 1, $cats); ?>
					</ul>
				<?php } ?>
			</li> <?php
		}
	}
}

/**
 * Display
 */
function et_the_cat_select($categories = false, $current = false, $parent = 0, $level = 1){
	if ( !$categories )
		$cats = FE_ThreadCategory::get_categories();
	else
		$cats = $categories;

	if ( !empty($cats) ){
		foreach ($cats as $cat) {
			if ( $cat->parent == $parent ){
				$space = '';
				for ($i = 1; $i < $level; $i++) {
					$space .= "-&nbsp;";
				}

				if ( $current == $cat->term_id ){
					echo '<option selected="selected" value="' . $cat->slug . '" class="option-' . $cat->term_id . '">' . $space . $cat->name . '</option>';
				} else {
					echo '<option value="' . $cat->slug . '" class="option-' . $cat->term_id . '">' . $space . $cat->name . '</option>';
				}

				$has_child = false;
				foreach ($cats as $child) {
					if ( $child->parent == $cat->term_id ){
						$has_child = false;
						break;
					}
				}

				et_the_cat_select($cats, $current, $cat->term_id, $level + 1 );
			}
		}
	}
}

/**
 *
 */
function fe_get_logo(){
	$website_logo = et_get_option("et_website_logo");
	if ($website_logo){
		return $website_logo;
	} else {
		return get_template_directory_uri() . '/img/logo.png';
	}
}

/**
 * Return ForumEngine search link
 * @param string $query
 * @return string $link
 */
function fe_search_link($query){
	global $wp_rewrite;

	if ( $wp_rewrite->using_permalinks() ){
		$search_slug = apply_filters( 'search_thread_slug', 'search-threads' );
		return home_url( '/' . $search_slug . '/' . urlencode( $query ) );
	} else {
		return add_query_arg( array(
			'post_type' => 'thread',
			's' 		=> urlencode( $query )
		), home_url( ) );
	}
}

/**
 * The Admin bars
 */
add_action( 'admin_bar_menu', "fe_admin_bar_menu" , 200);
function fe_admin_bar_menu(){
	global $et_admin_page, $wp_admin_bar;
	//
	//if ( !method_exists($et_admin_page, 'get_menu_items') ) return false;
	if ( !current_user_can('manage_options') || !apply_filters( 'et_backend_admin_bar_menu', true ) ) return false;

	$parent = 'fe_menu';

	$wp_admin_bar->add_menu(array(
		'id' 		=> 'fe_menu',
		'title' 	=> __('Forum Dashboard', ET_DOMAIN),
		'href' 		=> false
	));
	$menu_items = array(
		array(
			'parent' 	=> $parent,
			'id' 		=> 'et-overview',
			'title' 	=> __('Overview', ET_DOMAIN),
			'href' 		=> admin_url( '/admin.php?page=et-overview')
		),
		array(
			'parent' 	=> $parent,
			'id' 		=> 'et-user-badges',
			'title' 	=> __('User Badges', ET_DOMAIN),
			'href' 		=> admin_url( '/admin.php?page=et-user-badges')
		),
		array(
			'parent' 	=> $parent,
			'id' 		=> 'et-settings',
			'title' 	=> __('Settings', ET_DOMAIN),
			'href' 		=> admin_url( '/admin.php?page=et-settings')
		),
		array(
			'parent' 	=> $parent,
			'id' 		=> 'et-members',
			'title' 	=> __('Members', ET_DOMAIN),
			'href' 		=> admin_url( '/admin.php?page=et-members')
		),
	);
	$menu_items = apply_filters('fe_wpbar_menu',$menu_items);
	foreach ($menu_items as $key=>$item) {
		$wp_admin_bar->add_menu( $item);
	}
}
?>