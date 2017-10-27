<?php
	global $user_ID;
?>
<div class="upload-img">
	<div class="modal fade" id="uploadImgModal" data-keyboard="false" style="display:none;" aria-hidden="true">
		<form id="modal">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						<span class="icon" data-icon="D"></span>
					</button>
					<h4 class="modal-title"><?php _e( 'Insert an Image', ET_DOMAIN ) ?></h4>
				</div>
                <div class="modal-body">

                	<!-- Form Upload Images -->
                	<?php if(!$user_ID){ ?>
                	<p class="text-danger"><?php _e('You need to log in to upload images from your computer.', ET_DOMAIN ) ?></p>
                	<?php } ?>
                	<?php if(!get_option('upload_images')){ ?>
                	<p class="text-danger"><?php _e('Admin has disabled this function.', ET_DOMAIN ) ?></p>
                	<?php } ?>
                	
                  	<div  <?php if(!$user_ID || !get_option('upload_images')){ echo 'style="opacity:0.4;"';} ?> class="upload-location <?php if(!$user_ID || !get_option('upload_images')){ echo 'disabled';} ?>" id="images_upload_container">
	                    <span><?php _e( 'Upload an Image', ET_DOMAIN ) ?></span>
	                    <div class="input-file">                      
	                      	<input type="button" <?php if(!$user_ID || !get_option('upload_images')){ echo 'disabled="disabled"';} ?> value="<?php _e("Browse",ET_DOMAIN);?>" class="bg-button-file button" id="images_upload_browse_button">                        
	                      	<span class="filename"><?php _e("No file chosen",ET_DOMAIN);?></span>
	                      	<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'et_upload_images' ); ?>"></span> 
	                    </div>
                  	</div>
                  	<!-- Form Upload Images -->

                  	<!-- Form Insert Link Images -->
	                <div class="upload-url">
	                    <span><?php _e( 'Add an Image by URL', ET_DOMAIN ) ?></span>
	                    <div class="input-url">
	                      	<input type="text" placeholder="https://www.images.jpg" id="external_link" class="form-control">
		                    <div class="button-event">
		                  		<button type="button" id="insert" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" class="btn"><?php _e( 'Insert', ET_DOMAIN ) ?></button>
		                    	<span class="btn-cancel" data-dismiss="modal"><span data-icon="D" class="icon"></span><?php _e( 'Cancel', ET_DOMAIN ) ?></span>
		                    </div>
	                    </div>                  
	                </div>    
	                <!-- Form Insert Link Images -->            
                </div>                  
            </div>
        </div>
    	</form>
    </div> 
</div>