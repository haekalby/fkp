<?php
 
// Your php code goes here
 
function fs_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'fs_excerpt_length', 999 );

function fs_excerpt_more( $more ) {
    return ' ... ';
}
add_filter( 'excerpt_more', 'fs_excerpt_more' );

add_filter('template_include', 'restict_by_category');

function check_user() {
  $user = wp_get_current_user();
  if ( ! $user->ID || in_array('subscriber', $user->roles) ) {
    // user is not logged or is a subscriber
    return false;
  }
  return true;
}

function restict_by_category( $template ) {
  if ( ! is_main_query() ) return $template; // only affect main query.
  $allow = true;
  $private_categories = array('literasi-perbendaharaan', 'hasil-tahapan-knowledge-capturing'); // categories subscribers cannot see
  if ( is_single() ) {
    $cats = wp_get_object_terms( get_queried_object()->ID, 'thread_category', array('fields' => 'slugs') ); // get the categories associated to the required post
    if ( array_intersect( $private_categories, $cats ) ) {
      // post has a reserved category, let's check user
      $allow = check_user();
    }
  } elseif ( is_tax('thread_category', $private_categories) ) {
    // the archive for one of private categories is required, let's check user
    $allow = check_user();
  }
  // if allowed include the required template, otherwise include the 'not-allowed' one
  return $allow ? $template : get_template_directory() . '/kk.php';
}
?>