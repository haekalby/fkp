<?php

do_action( 'fe_before_sidebar' );

wp_reset_query();

if (is_active_sidebar( 'fe-single-post-sidebar' )){
	dynamic_sidebar( 'fe-single-post-sidebar' );
}

do_action( 'fe_after_sidebar' );
