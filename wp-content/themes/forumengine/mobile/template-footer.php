<div data-role="footer" class="fe-footer fe-container">
		<div class="fe-social">
			<ul>
				<?php $links = array(
					'fb'   => et_get_option("et_facebook_link"),
					'tw'   => et_get_option("et_twitter_account"),
					'rss'  => get_feed_link( 'rss2' ),
					'mail' => et_get_option("et_admin_email")
				) ?>
				<?php if ( !empty($links['fb'] )) { ?>
					<li class="fe-social-fb"><a target="_blank" href="<?php echo $links['fb']; ?>"><span class="fe-sprite"></span></a></li>
				<?php }
				if ( !empty($links['tw']) ){ ?>
					<li class="fe-social-tw"><a target="_blank" href="<?php echo $links['tw']; ?>"><span class="fe-sprite"></span></a></li>
				<?php } ?>
				<?php if(!empty($links['rss'])){?>
				<li class="fe-social-feed"><a target="_blank" href="<?php echo $links['rss'] ?>"><span class="fe-sprite"></span></a></li>
				<?php } ?>
				<?php if(!empty($links['mail'])){?>
				<li class="fe-social-mail"><a href="mailto:<?php echo $links['mail'] ?>"><span class="fe-sprite"></span></a></li>
				<?php } ?>
			</ul>
		</div>
		<div class="fe-nav">
			<ul>
				<li><a href="<?php echo home_url() ?>"><?php _e( 'All Posts' , ET_DOMAIN ); ?></a></li>
				<li><a href="<?php echo et_get_page_link("following") ?>"><?php _e( 'Following Posts' , ET_DOMAIN ); ?></a></li>
				<li><a href="<?php echo home_url('/blog') ?>"><?php _e( 'Blog' , ET_DOMAIN ); ?></a></li>
			</ul>
		</div>
		<div class="fe-credit"><?php echo et_get_option("et_copyright") ?></div>
	</div>
</div>