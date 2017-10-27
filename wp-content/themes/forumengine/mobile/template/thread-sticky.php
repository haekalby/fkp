<?php 
global $post, $wp_query, $et_sticky_pagename;

if ( is_front_page() || is_tax('thread_category') ) {
	if ( is_front_page() )
		$et_sticky_pagename = 'home';
	else 
		$et_sticky_pagename = 'thread_category';

	$sticky_threads = et_get_sticky_threads();

	$args = array(
		'post__in' 			=> is_front_page() ? $sticky_threads[0] : $sticky_threads[1],
		'post_type' 		=> 'thread',
		'posts_per_page' 	=> -1
	);

	if ( is_tax('thread_category') ){
		$term_id 					= $wp_query->queried_object->slug;
		$args['thread_category'] 	= $term_id;
	}

	// get sticky thread
	query_posts($args);

	if ( have_posts() ){
		while( have_posts()){
			the_post();

			// get template file to render
			//$template_file = get_template_directory() . '/mobile/mobile-template-thread.php';
			//if ( file_exists( get_stylesheet_directory() . '/mobile/mobile-template-thread.php' ) )
			//	$template_file = get_stylesheet_directory() . '/mobile/mobile-template-thread.php';

			//$template_file = apply_filters( 'forumengine_thread_list_item', $template_file );

			//load_template( $template_file, false );
			get_template_part( 'mobile/template/thread', 'loop' );
		}
	}

}
?>