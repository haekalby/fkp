<div <?php echo body_class( '' ) ?> data-role="page" id="<?php echo apply_filters( 'mobile_page_id', 'main' ) ?>">
	<!-- <div class="fe-notice fe-notice-success">
		<span class="fe-icon"></span>
		<span class="fe-notice-text">this is notification</span>
	</div> -->
	<div data-role="panel" id="fe_category">
		<div class="fe-cat-list">
			<ul>
				<li class="<?php if (is_home() || is_front_page()) echo 'fe-current' ?>">
					<a href="<?php echo home_url( ) ?>">
						<span class="arrow"><span class="fe-sprite"></span></span>
						<span class="name"><?php _e('All', ET_DOMAIN) ?></span>
						<span class="flags"></span>
					</a>
				</li>
				<?php et_mobile_categories(); ?>
			</ul>
		</div>
	</div>
	<div data-role="header" class="fe-header">
		<div id="header_standard" class="header-part header-standard active">
			<h1 data-role="heading" class="ui-title"><a target="_self" href="<?php echo home_url( ) ?>"><img src="<?php echo fe_get_logo() ?>" alt="<?php bloginfo( 'name' ) ?>"/></a></h1>
			<a class="fe-search ui-btn-right ui-link" href="#header_search">
				<span class="fe-btn-search fe-sprite"></span>
			</a>
		</div>
		<div id="header_search" class="header-part header-search">
			<div class="header-input">
				<form>
					<input type="text" name="s" data-role="none">
				</form>
			</div>
			<a class="fe-search ui-btn-right ui-link" href="#header_standard">
				<span class="fe-btn-search-cancel fe-sprite"></span>
			</a>
		</div>
	</div>
	<?php if(apply_filters( 'fe_expand_category' , true )){ ?>
	<style type="text/css">
		.fe-cat-list ul li.fe-has-child > ul{
			max-height: initial;
		}
	</style>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.fe-cat-list ul li.fe-has-child').addClass('fe-opened');
		});
	</script>
	<?php } ?>