<?php
/**
 * Template Name: Terms & Conditions Page
 */ 
	get_header();
?>
<?php the_post();?>

<div class="container main-center">
	<div class="row">        
		<div class="col-md-9 marginTop30">
			<h1><?php the_title() ?></h1>
			<?php the_content() ;?>
		</div>
		<div class="col-md-3 hidden-sm hidden-xs sidebar">
			<?php get_sidebar() ?>
			<!-- end widget -->
		</div>
	</div>
</div>	

<?php get_footer() ?>