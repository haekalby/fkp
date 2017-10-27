(function($){

	$(document).ready(function(){
		BESetting = new BackendSetting();
	});

	var BackendSetting = Backbone.View.extend({
		el: '#forum_settings',
		events: {
			'change #setting-general input'			:	'updateGeneralSetting',
			'change #setting-social input'			:	'updateGeneralSetting',
			'change #setting-general textarea'		:	'updateGeneralSetting',
			'change #setting-general select'		:	'updateGeneralSetting',

			'focusout .mail-template'				: 'updateMailTemplate',
			'click .trigger-editor'					: 'triggerEditor',
			'click .reset-default'					: 'resetDefaultMailTemplate',
			'click .et-main-main .desc .payment'	: 'togglePaymentSetting',

			'keyup #license_key' : 'keyupLicenseKey',
			'change #license_key' : 'changeLicenseKey',

			'click button#fe_update_content' 		: 'feUpdateContent',

			// 'click .payment a.deactive' 		: 'deactiveOption',
			// 'click .payment a.active'   		: 'activeOption',
		},

		initialize: function(){
			var view = this;
			//generate upload images
			//this.uploaderIDs	= ['website_logo','mobile_icon','default_logo'];
			this.uploaderIDs	= ['website_logo','mobile_icon'];
			//this.uploaderThumbs	= ['large','thumbnail','company-logo'];
			this.uploaderThumbs	= ['large','thumbnail'];
			this.uploaders		= [];

			//override underscore template
			_.templateSettings = {
				evaluate: /\<\#(.+?)\#\>/g,
				interpolate: /\{\{=(.+?)\}\}/g,
				escape: /\{\{-(.+?)\}\}/g
			};

			var cbBeforeSend = function(ele){
					var image = $(ele).find('div.image > img');
					image.css("opacity","0.3");
				},
				cbSuccess = function(){
					$('div.image > img').css("opacity","1.0");
				};

			// loop through the array to init uploaders
			for( i=0; i<this.uploaderIDs.length; i++ ){
				// get the container of the target
				$container	= this.$('#' + this.uploaderIDs[i] + '_container');

				this.uploaders[this.uploaderIDs[i]]	= new ImagesUpload({
					el					: $container,
					uploaderID			: this.uploaderIDs[i],
					thumbsize			: this.uploaderThumbs[i],
					multipart_params	: {
						_ajax_nonce	: $container.find('.et_ajaxnonce').attr('id'),
						action		: 'et-change-branding',
						imgType		: this.uploaderIDs[i]
					},
					cbUploaded	: function(up,file,res){
						if(res.success){
							$('#'+this.container).parents('.desc').find('.error').remove();
						} else {
							$('#'+this.container).parents('.desc').append('<div class="error">'+res.msg+'</div>');
						}
					},
					beforeSend	: cbBeforeSend,
					success		: cbSuccess
				});
			}

			jQuery.event.add(window, "scroll", function() {
				//console.log('scroll');
				var p 	 	= $(window).scrollTop(),
					lang_bar 	= jQuery('.language-translate-bar');
					// location_url= $(location).attr("href")

				var	sheight	= 0, swidth = 0;
				// var n = location_url.search("language");
				var dlenght = jQuery("#form_translate").height();

				if (lang_bar.length) {
					sheight  = lang_bar.offset().top;
					swidth   = lang_bar.width();
				}

				if ( (p>sheight-38) && (dlenght>0) ) {

					if ( !jQuery('#language-bar').length ) {
						lang_bar.append(
							'<div id="language-bar">'
							+ lang_bar.html()
							+'</div>'
						);

						lang_bar.find('#language-bar').css({ "width" : swidth+1 });
					}

				} else  {
					lang_bar.find('#language-bar').remove();
				}

			} );

			// show template help content
			$('.btn-template-help').click(function(){
				$('.cont-template-help').slideToggle(300);
				return false;
			});

			new Backend.Views.CategoryList();

			// set up toggle buttons
			$('.button-enable').each(function(){
				var element = $(this);
				var view = new Backend.Views.OptionToggle({el: element});
			});

			// autosize
			$('.autosize').attr('row', 1).autosize();

			// start router
			new MultiPageRouter();
			Backbone.history.start();
		},
		feUpdateContent: function(event){
			event.preventDefault();
			var target = $(event.currentTarget);
			$.ajax ({
				url  : fe_globals.ajaxURL,
				type : 'post',
				data : {
					action 	   	: 'fe_update_content',
				},
				beforeSend : function(){
					target.prop('disabled', true);
					target.css('opacity', '0.5');
				},
				success : function( response ){
					if(response.success){
						alert(response.msg);
						$("#update_content_wrap").fadeOut();
					} else {
						alert(response.msg);
					}

				}
			});
		},
		updateGeneralSetting : function ( event ) {
			var $new_value	=	$(event.currentTarget);

			if($new_value.attr('type') == "file") return false;

			if($new_value.hasClass('url')) {
				//console.log('aaa');
				var val		=	$new_value.val();
				//if (val.length == 0) { return true; }

			    // if user has not entered http:// https:// or ftp:// assume they mean http://
			    if(!/^(https?|ftp):\/\//i.test(val)) {
			        val = 'http://'+val; // set both the value
			        $new_value.val(val); // also update the form element
			        //$(event.currentTarget).focus();
			    }
			}

			var $container	=	$new_value.parent('div');
			var $icon	=	$container.find('.icon');
			$.ajax ({
				url  : fe_globals.ajaxURL,
				type : 'post',
				data : {
					action 	   	: 'et-update-general-setting',
					content 	: {
						new_value  : $new_value.val(),
						option_name: $new_value.attr('name')
					}
				},
				beforeSend : function(){
					$icon.attr('data-icon', '');
					$icon.html('<img src="'+fe_globals.imgURL+'/loading.gif" />');
					if( $new_value.attr('name') == "google_captcha_user_role" || $new_value.attr('name') == "google_captcha_cat"  || $new_value.attr('name') == "authorize_to_view" )	{
						$new_value.css('opacity', "0.5");
					}
				},
				success : function( response ){
					if( $new_value.attr('name') == "google_captcha_user_role" || $new_value.attr('name') == "google_captcha_cat" || $new_value.attr('name') == "authorize_to_view" )	{
						$new_value.css('opacity', "1");
					}
					if(response.success) {
						$icon.html('');
						$new_value.removeClass('color-error');
						$icon.removeClass('color-error');
						$icon.attr('data-icon', '3');

					} else {
						$icon.html('');
						$new_value.addClass('color-error');
						$icon.addClass('color-error');
						$icon.attr('data-icon', '!');
					}
					if ($new_value.val() == '' ){
						$icon.html('');
						$new_value.addClass('color-error');
						$icon.addClass('color-error');
						$icon.attr('data-icon', '!');
					}
				}
			});
			return false;
		},

		updateMailTemplate : function (event,editor) {
				if(editor){
					var $textarea	=	$("textarea#"+editor.id),
						content 	=	editor.getContent(),
						mail_type	=	$textarea.attr('name');
				} else {
					var $target 	=	$(event.currentTarget),
						$textarea	=	$target.find('textarea'),
						content 	=	$textarea.val(),
						mail_type	=	$textarea.attr ('name');
				}
				var	action 		=	'et-update-mail-template',
					$container	=	$textarea.closest("div.form-item"),
					$icon	=	$container.find('.icon');

				$.ajax ({
					url : fe_globals.ajaxURL,
					type : 'post',
					data : {
						content : {
							type : mail_type,
							data : content,
						},
						action : action
					},
					beforeSend : function () {
						$icon.attr('data-icon', '');
						$icon.html('<img src="'+fe_globals.imgURL+'/loading.gif" />');
					},
					success : function ( response) {
						if(response.success) {
							$icon.html('');
							$textarea.removeClass('color-error');
							$icon.removeClass('color-error');
							$icon.attr('data-icon', '3');

						} else {
							$icon.html('');
							$textarea.addClass('color-error');
							$icon.addClass('color-error');
							$icon.attr('data-icon', '!');
						}
					}
				});
		},

		triggerEditor : function (event) {
			event.preventDefault ();
			var $target 	=	$(event.currentTarget),
				editor_id 	=	$target.attr('rel');

			if(!$("a#"+editor_id+"-html").hasClass('active')) {
				$("a#"+editor_id+"-tmce").removeClass('active');
				$("a#"+editor_id+"-html").addClass('active').trigger("click");
			} else {
				$("a#"+editor_id+"-html").removeClass('active');
				$("a#"+editor_id+"-tmce").addClass('active').trigger("click");
			}
		},
		resetDefaultMailTemplate : function (event) {
			event.preventDefault ();
			var $target 	=	$(event.currentTarget),
				$textarea	=	$target.parents('.mail-template').find('textarea'),
				mail_type	=	$textarea.attr ('name'),
				action 		=	'et-set-default-mail-template';

			$.ajax ({
				url : fe_globals.ajaxURL,
				type : 'post',
				data : {
						content : {
							type : mail_type
						},
					action : action
				},
				beforeSend : function () {

				},
				success : function ( response) {
					$textarea.val (response.msg);
					var ed 			=	tinyMCE.get($textarea.attr('id'));
					ed.setContent (response.msg);
				}
			});
		},

		togglePaymentSetting : function (event) {
			event.preventDefault();
			var $target	=	$(event.currentTarget);
			$target.parents('.item').find('.payment-setting').slideToggle();
		},

		deactiveOption	: function (event) {
			event.preventDefault ();
			var payment	=	$(event.currentTarget),
				icon	=	payment.parents('.payment').find('a.icon'),
				view 	= this,
				//loadingView = new JobEngine.Views.LoadingEffect(),
				//blockUI = new JobEngine.Views.BlockUi(),
				container 	= $(event.currentTarget).parent(),
				enableBtn = container.children('a.active');

			if (container.hasClass('disabled')) return false;

			$.ajax ( {
				url  : fe_globals.ajaxURL,
				type : 'post',
				data : {
					action 	 : 'et-disable-option',
					content :{
						gateway :  payment.attr('rel')
					}
				},
				beforeSend : function(){
					//blockUI.block(payment);
					container.addClass('disabled');
					payment.addClass('selected');
					enableBtn.removeClass('selected');
				},
				success : function(response){
					//blockUI.unblock();
					container.removeClass('disabled');
					if( response.success == true) {
						//change display
					} else {
						enableBtn.addClass('selected');
						payment.removeClass('selected');
					}
				}
			});
			return false;
		},

		activeOption 	: function  (event) {
			event.preventDefault();
			var payment	=	$(event.currentTarget),
				icon_container	=	payment.parents('.payment'),
				icon 			= 	icon_container.find('a.icon'),
				view 	= this,
				//loadingView = new JobEngine.Views.LoadingEffect(),
				container 	= $(event.currentTarget).parent(),
				//blockUI = new JobEngine.Views.BlockUi(),
				disableBtn = container.children('a.deactive');

			if (container.hasClass('disabled')) return false;

			$.ajax ( {
				url  : fe_globals.ajaxURL,
				type : 'post',
				data : {
					action 	: 'et-enable-option',
					content :{
						gateway :  payment.attr('rel'),
						label	:  payment.attr ('title')
					}

				},
				beforeSend : function(){
					//blockUI.block(payment);
					container.addClass('disabled');
					payment.addClass('selected');
					disableBtn.removeClass('selected');
				},
				success : function(response){
					//blockUI.unblock();
					container.removeClass('disabled');
					if( response.success == true) {
						icon_container.find('.message').hide ();
					} else {
						disableBtn.addClass('selected');
						payment.removeClass('selected');
						icon_container.find('.message').html (response.msg);
					}
				}
			});
			return false;
		},
		// update menu
		keyupLicenseKey: function(e){
			var view = this,
				input = $(e.currentTarget),
				value = input.val();
			if (this.previousValue != value){
				this.previousValue = value;
				if (this.timing) clearTimeout(this.timing);
				this.timing = setTimeout(function(){ view.update_license(value) }, 3000);
			}
		},
		changeLicenseKey : function(e){
			var view = this,
				input = $(e.currentTarget),
				value = input.val();
			if (this.timing) clearTimeout(this.timing);
			this.update_license(value);
		},
		update_license : function(value){
			var loading 		= $('.license-field'),
				loading_url 	= fe_globals.imgURL + '/loading.gif',
				icon			= $('<span class="icon"></span>').append( $('<img src="' + loading_url + '">') );
			$.ajax({
					url: fe_globals.ajaxURL,
				type: 'POST',
				data: {
					action 	: 'et-update-license-key',
					key 	: value
				},
				beforeSend: function(){
					loading.append(icon);
					// show the loading image
				},
				success: function(resp){
					// receive response from server
					icon.find('img').remove();
					icon.attr('data-icon', '3');
					setTimeout(function(){ $(icon).fadeOut('normal', function(){ $(this).remove(); }) }, 2000);
				}
			})
		}
	})

	var MultiPageRouter = Backbone.Router.extend({
		routes: {
			'section/:page' : 'section'
		},
		section: function(page){
			//console.log('router');
			//
			$('.inner-menu li a').removeClass('active');
			$('.inner-menu li a[href="#section/' + page + '"]').addClass('active');

			$('.inner-content').hide();
			$('#' + page).show();
		}
	});

})(jQuery);