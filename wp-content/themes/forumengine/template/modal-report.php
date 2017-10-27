<?php
$terms = get_terms( 'report-taxonomy', 'orderby=count&hide_empty=0' );
 ?>
<div class="upload-img modal-insert-link">
	<div class="modal fade" id="reportFormModal" style="display:none;" aria-hidden="true">
		<form id="report_form" class="main-form">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
						<span class="icon" data-icon="D"></span>
					</button>
					<h4 class="modal-title"><?php printf(__( 'Report ', ET_DOMAIN )) ?></h4>
				</div>
                <div class="modal-body">
	                <div class="message">
	                    <p><?php _e( 'Your message', ET_DOMAIN ) ?></p>
	                    <textarea id="txt_report" placeholder="<?php _e( 'Got something to say? Type your message here.', ET_DOMAIN ) ?>" name= "message"></textarea>
	                </div>
	                <button type="submit" data-loading-text="<?php _e("Loading...", ET_DOMAIN); ?>" class="btn"><?php _e( 'Send', ET_DOMAIN ) ?></button>
                </div>
            </div>
        </div>
    	</form>
    </div>
</div>