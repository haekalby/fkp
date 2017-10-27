<?php 
	$current_author = get_query_var( 'author' ) ? get_query_var( 'author' ) : get_query_var( 'member' );
	$author = get_user_by( 'id', $current_author ) ? get_user_by( 'id', $current_author ) : get_user_by( 'slug', $current_author ) ;
?>
<div class="upload-img modal-insert-link">
	<div class="modal fade" id="contactFormModal" style="display:none;" aria-hidden="true">
		<form id="contact_form" class="main-form">
			<input type="hidden" name="author_id" id="author_id" value="<?php echo $author->ID ?>" />
			<input type="hidden" name="author_email" id="author_email" value="<?php echo $author->email ?>" />
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						<span class="icon" data-icon="D"></span>
					</button>
					<h4 class="modal-title"><?php printf(__( 'Contact %s', ET_DOMAIN ),$author->display_name) ?></h4>
				</div>
                <div class="modal-body">		                  	
	                <div class="message">			                    
	                    <p><?php _e( 'Your message', ET_DOMAIN ) ?></p> 
	                    <textarea id="txt_contact" placeholder="<?php _e( 'Got something to say? Type your message here.', ET_DOMAIN ) ?>"></textarea>        	  
	                </div> 
	                <button type="submit" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" class="btn"><?php _e( 'Send', ET_DOMAIN ) ?></button>               
                </div>                  
            </div>
        </div>
    	</form>
    </div> 
</div>