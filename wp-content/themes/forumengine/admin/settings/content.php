<div id="setting-content" class="inner-content et-main-main clearfix hide">
	<div class="title font-quicksand"><?php _e("Thread Categories",ET_DOMAIN);?></div>
	<div class="desc">
		<?php _e("Users can filter their thread searches by Thread Categories",ET_DOMAIN);?>
		<div class="cat-list-container" id="thread-categories">
			<?php
				$thread_category	=	new FE_BackendCategory ();
				$thread_category->print_backend_terms();
			?>
		</div>
	</div>

	<div class="title font-quicksand"><?php _e("Pending threads",ET_DOMAIN);?></div>
	<div class="desc">
	 	<?php _e("Enabling this will make every new thread pending until you review and approve it manually.",ET_DOMAIN);?>
		<div class="inner no-border btn-left">
			<div class="payment">
				<?php et_toggle_button('pending_thread', __("Pending thread",ET_DOMAIN), get_option('pending_thread', false) ); ?>
			</div>
		</div>
	</div>

	<div class="title font-quicksand"><?php _e("Upload Images",ET_DOMAIN);?></div>
	<div class="desc">
	 	<?php _e("Enabling this will allow users to add images to the thread by direct upload.",ET_DOMAIN);?>
		<div class="inner no-border btn-left">
			<div class="payment">
				<?php et_toggle_button('upload_images', __("Upload Images",ET_DOMAIN), get_option('upload_images', false) ); ?>
			</div>
		</div>
	</div>

	<div class="title font-quicksand"><?php _e("Infinite Scroll",ET_DOMAIN);?></div>
	<div class="desc">
	 	<?php _e("Enabling this will activate the Infinite Scrolling.",ET_DOMAIN);?>
		<div class="inner no-border btn-left">
			<div class="payment">
				<?php et_toggle_button('et_infinite_scroll', __("Infinite Scroll",ET_DOMAIN), get_option('et_infinite_scroll', false) ); ?>
			</div>
		</div>
	</div>

	<div class="title font-quicksand"><?php _e("Auto Expand Replies",ET_DOMAIN);?></div>
	<div class="desc">
	 	<?php _e("Enabling this will automatically expand the sub-level replies.",ET_DOMAIN);?>
		<div class="inner no-border btn-left">
			<div class="payment">
				<?php et_toggle_button('et_auto_expand_replies', __("Auto Expand Replies",ET_DOMAIN), get_option('et_auto_expand_replies', true) ); ?>
			</div>
		</div>
	</div>

	<div class="title font-quicksand"><?php _e("Send Mail To User's Following Threads",ET_DOMAIN);?></div>
	<div class="desc">
	 	<?php _e("Enabling this will automatically send email to users who following threads.",ET_DOMAIN);?>
		<div class="inner no-border btn-left">
			<div class="payment">
				<?php et_toggle_button('fe_send_following_mail', __("Send Mail To User's Following Threads",ET_DOMAIN), get_option('fe_send_following_mail', false) ); ?>
			</div>
		</div>
	</div>

	<div class="title font-quicksand"><?php _e("Live Notifications",ET_DOMAIN);?></div>
	<div class="desc">
	 	<?php _e("Turn on / off live notifications feature.",ET_DOMAIN);?>
		<div class="inner no-border btn-left">
			<div class="payment">
				<?php
					et_toggle_button(
						'fe_live_notifications',
						__("Live Notifications",ET_DOMAIN),
						get_option('fe_live_notifications', false)
					);
				?>
			</div>
		</div>
	</div>

	<script id="cat_item_template" type="text/template">
		<div class="container">
			<div class="sort-handle"></div>
			<div class="controls controls-2">
				<a class="button act-open-form" rel="{{= term_id }}"  title="<?php _e('Add sub tax for this tax', ET_DOMAIN) ?>">
					<span class="icon" data-icon="+"></span>
				</a>
				<a class="button act-del" rel="{{= term_id }}">
					<span class="icon" data-icon="*"></span>
				</a>
			</div>
			<div class="input-form input-form-1">
				<div class="cursor"><span class="flag"></span></div>
				<input class="bg-grey-input tax-name" name="name" rel="{{= term_id }}" type="text" value="{{= name }}">
			</div>
		</div>
		<ul>
		</ul>
	</script>
	<script id="cat_item_form" type="text/template">
		<div class="container">
			<div class="controls controls-2">
				<!-- <div class="button">
					<span class="icon" data-icon="+"></span>
				</div> -->
				<button class="button" type="submit"><span class="icon" data-icon="+"></span></button>
			</div>
			<div class="input-form input-form-1 color-default">
				<div class="cursor color-0" data="0"><span class="flag"></span></div>
				<input class="bg-grey-input tax-name" name="name" placeholder="<?php _e('Add a category', ET_DOMAIN) ?>" type="text" />
			</div>
		</div>
	</script>
</div>