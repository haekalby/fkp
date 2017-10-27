<div id="setting-general" class="inner-content et-main-main clearfix">
<?php
	$site_title       =	et_get_option("blogname");
	$site_desc        =	et_get_option("blogdescription");
	$copyright        =	et_get_option("et_copyright");
	$twitter          =	et_get_option("et_twitter_account");
	$facebook         =	et_get_option("et_facebook_link");
	$admin_email      =	et_get_option("et_admin_email");
	$google           =	et_get_option("et_google_plus");
	$google_analytics =	et_get_option("et_google_analytics");
	$thread_slug      =	et_get_option("et_thread_slug");
	$category_slug       =	et_get_option("et_category_slug");
	$validator        =	new ET_Validator();
?>
	<!-- BRANDING -->
	<div class="title font-quicksand"><?php _e('Upload Logo', ET_DOMAIN );?></div>
	<div class="desc">
		<?php _e('Your logo should be in PNG, GIF or JPG format, within <strong>120x70px</strong>  and less than <strong>1500Kb</strong>.', ET_DOMAIN);?>
		<div class="customization-info">
			<?php $uploaderID = 'website_logo';?>
			<div class="input-file upload-logo" id="<?php echo $uploaderID;?>_container">
			<?php
				$website_logo = et_get_option("et_website_logo");
			?>
					<div class="left clearfix">
						<div class="image" id="<?php echo $uploaderID;?>_thumbnail">
							<img src="<?php echo fe_get_logo();?>"/>
						</div>
					</div>

				<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( $uploaderID . '_et_uploader' ); ?>"></span>
				<span class="bg-grey-button button btn-button" id="<?php echo $uploaderID;?>_browse_button">
					<?php _e('Browse', ET_DOMAIN);?>
					<span class="icon" data-icon="o"></span>
				</span>

			</div>
		</div>
		<div style="clear:left"></div>
	</div>

	<div class="title font-quicksand margin-top30"><?php _e('Upload Mobile Icon', ET_DOMAIN);?></div>
	<div class="desc">
		<?php _e('This icon will be used as a launcher icon for iPhone and Android smartphones and also as the website favicon. The image dimensions should be <strong>57x57px</strong>.', ET_DOMAIN);?>
		<div class="customization-info">
			<?php $uploaderID = 'mobile_icon';?>
			<div class="input-file  mobile-logo" id="<?php echo $uploaderID;?>_container">
				<?php
				$mobile_icon = et_get_option("et_mobile_icon");

					?>
					<div class="left clearfix">
						<div class="image" id="<?php echo $uploaderID;?>_thumbnail">
						<?php if ($mobile_icon){ ?>
							<img src="<?php echo $mobile_icon;?>"/>
						<?php } else { ?>
							<img src="<?php echo TEMPLATEURL . '/img/fe-favicon.png' ?>"/>
						<?php } ?>
						</div>
					</div>

				<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( $uploaderID . '_et_uploader' ); ?>"></span>
				<span class="bg-grey-button button btn-button" id="<?php echo $uploaderID;?>_browse_button">
					<?php _e('Browse', ET_DOMAIN);?>
					<span class="icon" data-icon="o"></span>
				</span>
			</div>
		</div>
		<div style="clear:left"></div>
	</div>
	<!-- BRANDING -->
	<!-- GENERAL -->
	<div class="title font-quicksand"><?php _e("Website Title",ET_DOMAIN);?></div>
	<div class="desc">
		<?php _e("Enter your website title ",ET_DOMAIN);?>
		<div class="form no-margin no-padding no-background">
			<div class="form-item">
				<input class="bg-grey-input <?php if($site_title == '') echo 'color-error' ?>" type="text" value="<?php echo $site_title?>" id="site_title" name="blogname" />
				<span class="icon  <?php if($site_title == '') echo 'color-error' ?>" data-icon="<?php data_icon($site_title) ?>"></span>
			</div>
		</div>
	</div>
	<div class="title font-quicksand"><?php _e("Website Description",ET_DOMAIN);?></div>
	<div class="desc">
		<?php _e("This description will appear next to your website logo in the header.",ET_DOMAIN);?>
		<div class="form no-margin no-padding no-background">
			<div class="form-item">
				<input class="bg-grey-input <?php if($site_desc == '') echo 'color-error' ?>" type="text" value="<?php echo $site_desc?>" id="site_desc" name="blogdescription" />
				<span class="icon  <?php if($site_desc == '') echo 'color-error' ?>" data-icon="<?php data_icon($site_desc) ?>"></span>
			</div>
		</div>
	</div>
    <div class="title font-quicksand"><?php _e("Copyright Information",ET_DOMAIN);?></div>
	<div class="desc">
		<?php _e("This copyright information will appear in the footer.",ET_DOMAIN);?>
		<div class="form no-margin no-padding no-background">
			<div class="form-item">
				<input class="bg-grey-input <?php if($copyright == '') echo 'color-error' ?>" type="text" value="<?php echo htmlentities($copyright) ?>" id="copyright" name="et_copyright" />
				<span class="icon  <?php if($copyright == '') echo 'color-error' ?>" data-icon="<?php data_icon($copyright) ?>"></span>
			</div>
		</div>
	</div>
    <div class="title font-quicksand"><?php _e("Social Links",ET_DOMAIN);?></div>
	<div class="desc">
	    <?php _e("Social links are displayed in the footer and in your blog sidebar.",ET_DOMAIN);?>
    	<div class="form no-margin no-background">
    		<div class="form-item">
        		<div class="label"><?php _e("Twitter URL",ET_DOMAIN);?></div>
        		<input class="url bg-grey-input <?php if(!$validator->validate('link', $twitter)) echo 'color-error' ?>" type="text" value="<?php echo htmlentities($twitter) ?>" id="twitter_account" name="et_twitter_account"/>
        		<span class="icon <?php if(!$validator->validate('link', $twitter) ) echo 'color-error' ?>" data-icon="<?php data_icon($twitter ,'link') ?>"></span>
        	</div>
        	<div class="form-item">
        		<div class="label"><?php _e("Facebook URL",ET_DOMAIN);?></div>
        		<input class="url bg-grey-input <?php if (!$validator->validate('link', $facebook)) echo 'color-error' ?>" type="text" value="<?php echo htmlentities($facebook) ?>" id="facebook_link" name="et_facebook_link"/>
        		<span class="icon <?php if( !$validator->validate('link', $facebook) ) echo 'color-error' ?>" data-icon="<?php data_icon($facebook, 'link') ?>"></span>
        	</div>
        	<div class="form-item">
        		<div class="label"><?php _e("Google Plus URL",ET_DOMAIN);?></div>
        		<input class="url bg-grey-input <?php if (!$validator->validate('link', $google)) echo 'color-error' ?>" type="text" value="<?php echo htmlentities($google) ?>" id="google_plus" name="et_google_plus"/>
        		<span class="icon <?php if (!$validator->validate('link', $google)) echo 'color-error' ?>" data-icon="<?php data_icon($google, 'link') ?>"></span>
        	</div>
        	<div class="form-item">
        		<div class="label"><?php _e("Contact email",ET_DOMAIN);?></div>
        		<input class="bg-grey-input " type="text" value="<?php echo htmlentities($admin_email) ?>" id="admin_email" name="et_admin_email"/>
        		<span class="icon <?php if( !$validator->validate('email', $admin_email) ) echo 'color-error' ?>" data-icon="<?php data_icon($admin_email, 'email') ?>"></span>
        	</div>
    	</div>
	</div>

	<div class="title font-quicksand"><?php _e("Google Analytics",ET_DOMAIN);?></div>
	<div class="desc">
		<?php _e("Google analytics is a service offered by Google that generates detailed statistics about the visits to a website.",ET_DOMAIN);?>
		<div class="form no-margin no-padding no-background">
    		<div class="form-item">
        		<textarea class="autosize" row="4" style="height: auto;overflow: visible;" id="google_analytics" name="et_google_analytics" ><?php echo $google_analytics ?></textarea>
        		<span class="icon <?php if ($google_analytics == '') echo 'color-error' ?>" data-icon="<?php data_icon($google_analytics, 'text') ?>"></span>
        	</div>
        </div>
	<!-- GENERAL -->
	</div>

    <div class="title font-quicksand"><?php _e("Change Custom Slugs",ET_DOMAIN);?></div>
	<div class="desc">
	    <?php _e("You can redefine a new slug for thread & reply.",ET_DOMAIN);?>
    	<div class="form no-margin no-background">
        	<div class="form-item">
        		<div class="label"><?php _e("Thread Slug",ET_DOMAIN);?></div>
        		<input class="bg-grey-input <?php if($thread_slug == "") echo 'color-error' ?>" type="text" value="<?php echo htmlentities($thread_slug) ?>" id="thread_slug" name="et_thread_slug"/>
        		<span class="icon <?php if($thread_slug == "") echo 'color-error' ?>" data-icon="<?php data_icon($thread_slug) ?>"></span>
        	</div>
        	<div class="form-item">
        		<div class="label"><?php _e("Category Slug",ET_DOMAIN);?></div>
        		<input class="bg-grey-input <?php if( $category_slug == "") echo 'color-error' ?>" type="text" value="<?php echo htmlentities($category_slug) ?>" id="category_slug" name="et_category_slug"/>
        		<span class="icon <?php if( $category_slug == "") echo 'color-error' ?>" data-icon="<?php data_icon($category_slug) ?>"></span>
        	</div>
    	</div>
	</div>

	<div class="title font-quicksand"><?php _e("Email Confirmation",ET_DOMAIN);?></div>
	<div class="desc">
	 	<?php _e("Enabling this will require users to confirm their email addresses after registration.",ET_DOMAIN);?>
		<div class="inner no-border btn-left">
			<div class="payment">
				<?php et_toggle_button('user_confirm', __("User Confirmaton",ET_DOMAIN), get_option('user_confirm', false) ); ?>
			</div>
		</div>
	</div>
	<?php
		$google_captcha	=	ET_GoogleCaptcha::get_api();
		function fe_captcha_multi_categories_selector($option_name, $parent = null){
			$categories = get_terms('thread_category', array(
				'hide_empty' 	=> 	false,
				'parent'		=>	$parent,
			) );
			echo '<select multiple="multiple" size="5" style="min-width: 180px" name="'.$option_name.'" >';
			if($option_name=="google_captcha_cat")
				fe_captcha_categories_options($categories, $option_name,'','',true);
			else
				fe_captcha_categories_options($categories, $option_name);
			echo '</select>';
		}
		function fe_captcha_categories_options($categories,$option, $parent = false, $level = 0, $subCat = false){
			$next_lvl = $level + 1;
			$google_captcha_cat=get_option( $option , '' );
			foreach ($categories as $category) {				
				$selected = '';
				if($google_captcha_cat && in_array($category->term_id, $google_captcha_cat)){
					$selected = 'selected="selected"';
				}
				if($subCat){
					echo '<option value = "' . $category->term_id . '" ' . $selected . '>' . $category->name . '</option>';					
					$subCategories = get_terms('thread_category', array(
						'hide_empty' 	=> 	false,
						'parent'		=>	$category->term_id,
					));
					if($subCategories){
						foreach ($subCategories as $subCategory) {
							$subSelected = '';
							if($google_captcha_cat && in_array($subCategory->term_id, $google_captcha_cat)){
								$subSelected = 'selected="selected"';
							}
							echo '<option value = "' . $subCategory->term_id . '" ' . $subSelected . '> - ' . $subCategory->name . '</option>';
						} // end foreach
					} // end if					
				}
				else
				{
					if( ($parent == false && empty($category->parent) ) || ($parent == $category->parent) )
					{
						?>
						<option value="<?php echo $category->term_id ?>" <?php echo $selected ?>>
							<?php
								for ($i=0; $i < $level; $i++) {
									echo '-';
								}
								echo $category->name;
							?>
						</option>
						<?php
						fe_captcha_categories_options( $categories, $category->term_id, $next_lvl, $option);
					} // end if					
				} // end else			
			} // end foreach
		} // end function
		function fe_captcha_multi_user_roles_selector(){
			$roles = get_editable_roles();
			$roles_option =	get_option('google_captcha_user_role', '');
			echo '<select multiple="multiple" size="5" style="min-width: 180px" name="google_captcha_user_role" >';
			foreach( $roles as $role_name => $role_info){
				$selected='';
				if($roles_option && in_array($role_name, $roles_option)){
					$selected = 'selected="selected"';
				}
				echo "<option value='$role_name' $selected>$role_name</option>";
			}
			echo '</select>';
		}
	?>
	<div class="title font-quicksand"><?php _e("Google captcha",ET_DOMAIN);?></div>
	<div class="desc">
		 	<?php
		 		_e("Enabling this will require users to enter captcha when create topic.",ET_DOMAIN);
				echo "<br>";
				_e('You can find the API key', ET_DOMAIN);
		 	?>
		 	<a target="_blank" href="https://www.google.com/recaptcha/admin#list"><?php _e('here', ET_DOMAIN); ?></a>
		<div class="inner no-border btn-left">
			<div class="payment">
				<?php et_toggle_button('google_captcha', __("Google captcha",ET_DOMAIN), get_option('google_captcha', false) ); ?>
			</div>
		</div>
	</div>
	<div class="desc">
    	<div class="form no-margin no-padding no-background">
        	<div class="form-item">
        		<input class="option-item bg-grey-input google-captcha " type="text" value="<?php echo $google_captcha['public_key'] ?>" id="public_key" name="public_key" placeholder="<?php _e("Site key", ET_DOMAIN) ?>" />
        		<span class="icon <?php if ($google_captcha['public_key'] == '') echo 'color-error' ?>" data-icon="<?php data_icon($google_captcha['public_key'], 'text') ?>"></span>
        	</div>
    		<div class="form-item">
        		<input class="option-item bg-grey-input google-captcha" type="text" value="<?php echo $google_captcha['private_key'] ?>" id="private_key" name="private_key" placeholder="<?php _e("Secret key", ET_DOMAIN) ?>" />
        		<span class="icon <?php if ($google_captcha['private_key'] == '') echo 'color-error' ?>" data-icon="<?php data_icon($google_captcha['private_key'], 'text') ?>"></span>
        	</div>
    	</div>
	</div>
	<div class="desc">
			<?php
				_e("Choose the category that you want to enable captcha",ET_DOMAIN);
			?>
			<br/><br/>
		<?php fe_captcha_multi_categories_selector('google_captcha_cat',0);?>
	</div>
	<div class="desc">
			<?php _e("Choose user roles that you want to enable captcha",ET_DOMAIN);?>
			<br/><br/>
		<?php fe_captcha_multi_user_roles_selector();?>
	</div>
	<div class="title font-quicksand"><?php _e("Allow to view",ET_DOMAIN);?></div>
	<div class="desc">
	<?php _e("Enabling this will require users log into their account to view thread in category wasn't selected below.",ET_DOMAIN);?>
		<br/><br/>
		<div class="inner no-border btn-left">
			<div class="payment">
				<?php et_toggle_button('user_view', __("User view",ET_DOMAIN), get_option('user_view', false) ); ?>
			</div>
		</div>
		<?php
			_e("Choose categories that user can view without login",ET_DOMAIN);
		?>
		<br/><br/>
		<?php fe_captcha_multi_categories_selector('authorize_to_view');?>
	</div>
</div> <!-- END #setting-general -->