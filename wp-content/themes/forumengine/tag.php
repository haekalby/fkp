<?php
get_header();
?>
<!--end header Bottom-->
<div class="container main-center">
	<div class="row">        
		<div class="col-md-9 col-sm-12 marginTop30">
			<?php 
			if (  have_posts() ){ ?>
				<?php 
				/**
				 * Display regular threads
				 */
				while (have_posts()){
					the_post();
					get_template_part( 'content' );
				} // end while 
				?>
				<?php 
			} else { ?>
				<div class="notice-noresult">
					<span class="icon" data-icon="!"></span><?php _e('No post has been created yet.', ET_DOMAIN) ?> <a href="#" id="create_first"><?php _e('Create the first one', ET_DOMAIN) ?></a>.
				</div>
				<?php 
			} // end if
			?>
			<div class="pagination pagination-centered" id="main_pagination">
				<?php 
				//wp_reset_query();
				echo paginate_links( array(
					'base' 		=> str_replace('99999', '%#%', esc_url(get_pagenum_link( 99999 ))),
					'format' 	=> $wp_rewrite->using_permalinks() ? 'page/%#%' : '?paged=%#%',
					'current' 	=> max(1, get_query_var('paged')),
					'total' 	=> $wp_query->max_num_pages,
					'prev_text' => '<',
					'next_text' => '>',
					'type' 		=> 'list'
				) ); ?>
			</div>
		</div>
		<div class="col-md-3 hidden-sm hidden-xs sidebar">
			<?php get_sidebar( ) ?>
		</div>
	</div>
</div>
 
<?php get_footer(); ?>

