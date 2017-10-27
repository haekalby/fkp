<?php

do_action( 'fe_before_sidebar' );

wp_reset_query();

if (is_active_sidebar( 'fe-homepage-sidebar' )){
	dynamic_sidebar( 'fe-homepage-sidebar' );
}

do_action( 'fe_after_sidebar' );
