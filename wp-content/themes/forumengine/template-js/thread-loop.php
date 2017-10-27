<script type="text/template" id="thread_loop_template">

		<?php if(!is_author() && !is_page_template( 'page-member.php' )) {?>

		<a href="{{= guid }}">
			<span class="thumb avatar">
				{{= avatar }}
				{{= user_badge }}
			</span>
		</a>

		<?php } ?>

		<div class="f-floatright">
			<h2 class="title">
				<a href="{{= guid }}">
					{{= post_title }}
					<# if ( post_status == 'closed' ) { #> <span class="icon" data-icon="("></span> <# } #>
				</a>
			</h2>
			<div class="post-information">
				<span class="times-create">
					{{= et_updated_date_string }}
				</span>
				<span class="type-category">
					<# if(thread_category[0]) { #>
					<a href="{{= thread_category[0].link }}">
						<span class="flags color-{{= thread_category[0].color }}"></span>
						{{= thread_category[0].name }}
					</a>.
					<# } else { #>
						<?php _e( 'No category.', ET_DOMAIN ); ?>
					<# } #>
				</span>
				<span class="author">
					<# 
						if ( et_last_author == false ){

							<?php _e( 'No reply yet', ET_DOMAIN ); ?>

						} else {
					#>
						<span class="last-reply">
							<a href="{{= et_get_last_page }}"><?php _e('Last reply',ET_DOMAIN);?></a>
						</span> 
						<?php _e('by',ET_DOMAIN); ?>
						<span class="semibold">
							<a href="">{{= et_last_author.display_name }}</a>
						</span>.
					<#
						} 
					#>
				</span>
				<span class="user-action">
					<span class="comment <# if(replied) { #> active <# } #>"><span class="icon" data-icon="w"></span>{{= et_replies_count }}</span>
					<span class="like <# if(liked) { #> active <# } #>"><span class="icon" data-icon="k"></span>{{= et_likes_count }}</span>
				</span>
				<span class="undo-action hide">
					<?php printf( __('Want to %s ?',ET_DOMAIN) , '<a href="#" class="act-undo">' . __('undo', ET_DOMAIN) . '</a>' ); ?>
				</span>
			</div>

			<?php if(current_user_can("manage_threads")) {?>

			<div class="control-thread-group">

				<# if ( post_status == 'pending' ){ #>

					<a href="#" data="{{= ID }}" class="approve-thread" data-toggle="tooltip" title="<?php _e('Approve', ET_DOMAIN) ?>"><span class="icon" data-icon="3"></span></a>
					<a href="#" data="{{= ID }}" class="delete-thread" data-toggle="tooltip" title="<?php _e('Delete', ET_DOMAIN) ?>"><span class="icon" data-icon="#"></span></a>		

				<# } else {  #>

					<a href="#" data-toggle="tooltip" title="<?php _e('Sticky', ET_DOMAIN) ?>" class="sticky-thread">
						<span class="icon" data-icon="S"></span>
					</a>
					<a href="#" data-toggle="tooltip" title="<?php _e('Sticky Home', ET_DOMAIN) ?>" class="sticky-thread-home collapse">
						<span class="icon" data-icon="G"></span>
					</a>
					<a href="#" class="close-thread <# if ( post_status == 'closed' ) { #> collapse <# } #>" data-toggle="tooltip" title="<?php _e('Close', ET_DOMAIN) ?>">
						<span class="icon" data-icon="("></span>
					</a>
					<a href="#" class="unclose-thread <# if ( post_status != 'closed' ) { #> collapse <# } #>" data-toggle="tooltip" title="<?php _e('Unclose', ET_DOMAIN) ?>">
						<span class="icon" data-icon=")"></span>
					</a>
					<a href="#" class="delete-thread" data-toggle="tooltip" title="<?php _e('Delete', ET_DOMAIN) ?>">
						<span class="icon" data-icon="#"></span>
					</a>

				<# } #>
			</div>

			<?php } ?>
		</div>
</script>