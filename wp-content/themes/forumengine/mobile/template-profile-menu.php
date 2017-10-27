<div class="fe-profile-menu clearfix">
	<ul>
		<li>
			<a href="<?php echo get_author_posts_url($user_ID) ?>">
				<span class="fe-icon-b fe-icon-b-profile"></span>
				<span class="fe-grid-menu-text"><?php _e('Profile', ET_DOMAIN) ?></span>
			</a>
		</li>				
		<!-- <li>
			<a href="#">
				<span class="fe-icon-b fe-icon-b-mail-2"></span>
				<span class="fe-grid-menu-text">Mail</span>
			</a>
		</li> -->
		<li>
			<a href="<?php echo wp_logout_url( home_url( ) ) ?>" id="logout_link">
				<span class="fe-icon-b fe-icon-b-logout"></span>
				<span class="fe-grid-menu-text"><?php _e('Logout', ET_DOMAIN) ?></span>
			</a>
		</li>
	</ul>
</div>