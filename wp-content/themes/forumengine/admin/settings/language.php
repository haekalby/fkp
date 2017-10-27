<div id="setting-language" class="inner-content et-main-main clearfix hide">
<?php 
$langs = et_get_option('et_languages');
$current_tran = et_get_option('et_current_language');
?>
	<div class="title font-quicksand"><?php _e('Website Language', ET_DOMAIN) ?></div>
    <div class="desc">
   		Select the language you want to use for your website. 
   		<!-- <a class="find-out font-quicksand" href="#">Find out more <span class="icon" data-icon="i"></span></a> -->
    	<ul class="list-language">
    		<?php foreach ($langs as $name => $label) { ?>
    			<li>
	        		<a class="set-lang <?php if ($current_tran == $name) echo 'active' ?>" title="<?php echo $label ?>" href="#et-change-language" data="<?php echo $name ?>"><?php echo $label ?></a>
	        	</li>
    		<?php } ?>
            <li class="new-language">
        		<button class="add-lang"><?php _e('Add a new language', ET_DOMAIN) ?><span class="icon" data-icon="+"></span></button>
        		<div class="lang-field-wrap">
        			<form class="form-new-lang">
        				<input id="new-language-name" type="text" placeholder="Enter language name" name="lang_name" class="input-new-lang">
        			</form>
        		</div>
        	</li>
        </ul>
        <div class="no-padding">
			<div class="show-new-language">
				<div class="item form no-background no-padding no-margin">
					<div class="form-item form-item-short">
						<!-- <div class="label">Language name:</div> -->
						<input id="new-language-name" class="bg-grey-input" type="text" placeholder="<?php _e("Enter the language's name", ET_DOMAIN) ?>">
						<button id="add-new-language"><?php _e('Add language', ET_DOMAIN) ?><span class="icon" data-icon="+"></span></button>
						<a class="cancel" id="cancel-add-lang"><?php _e('Cancel', ET_DOMAIN) ?></a>
					</div>
				</div>
			</div>
		</div>		
	</div>
	<div class="desc">
		<?php 
		// $handle = new FE_Language();
		// $translation = $handle->get_translation_from_file('tieng-viet'); 
		// echo '<pre>';
		// print_r($translation);
		// echo '</pre>';
		?>
	</div>
	<div class="desc">   
		<div class="title font-quicksand"><?php _e('Translator', ET_DOMAIN) ?></div>
        	<div class="item">
        		<div class="form no-background no-margin padding10">
        			<div class="form-item language-translate-bar">
		        		<div class="label"><?php _e('Translate a language', ET_DOMAIN) ?></div>
		        		<div class="f-left-all width100p clearfix">
	        				<select class="selector" id="language_to_edit" style="z-index: 10; opacity: 0;">
	        					<option class="" value=""><?php _e('Choose a Language', ET_DOMAIN) ?></option>
	        					<?php foreach ($langs as $name => $label) {
	        						echo '<option value="' . $name . '">' . $label . '</option>';
	        					} ?>
	        				</select>
		        			<div class="btn-language">
        						<button id="save-language"><?php _e('Save', ET_DOMAIN) ?> <span class="icon" data-icon="~"></span></button>
        					</div>
		        		</div>
	        		</div>
	        		<form id="form_translate">
	        		</form>
				</div>
				
   			</div>
	</div>
</div>