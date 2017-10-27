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
				the_post();
				get_template_part( 'content' );
				?>
				<?php
			} else { ?>
				<div class="notice-noresult">
					<span class="icon" data-icon="!"></span><?php _e('No post has been created yet.', ET_DOMAIN) ?> <a href="#" id="create_first"><?php _e('Create the first one', ET_DOMAIN) ?></a>.
				</div>
				<?php
			} // end if
			?>

			<?php comments_template(); ?>
		</div>
		<div class="col-md-3 hidden-sm hidden-xs sidebar">
			<?php get_sidebar( 'post' ) ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>

