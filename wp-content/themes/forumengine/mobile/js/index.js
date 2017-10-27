(function($){

$(document).ready(function(){
	$.mobile.ajaxEnabled = false;
	new ForumMobile.Views.Index();
});

/**
 * Website script here
 **/
ForumMobile.Views.Index = Backbone.View.extend({
	el: 'body',
	events: {
		'tap a#more_thread' 			: 'loadMoreThreads',
		'tap a#more_blog' 				: 'loadMorePosts',
		'tap a#create_thread' 			: 'insertThread',
	},
	initialize: function(){
		// initialize thread view
		var view = this;
		this.threadItems = [];
		this.$('article').each(function(){
			var element = $(this);
			var model 	= { 'ID' : element.attr('data-id'), 'id' : element.attr('data-id') };
			view.threadItems.push( new ForumMobile.Views.ThreadItem({model : model, el : this }) );
		});
		this.uploadImage(
				'#images_upload_browse_button',			// Upload button
				'#images_upload_container',		// Upload container
				'images_upload',			// Upload ID
				'.fe-topic-content.fe-expanded textarea'		// Textarea to receive shortcode
			);
	},

	insertThread: function(event){

		if(currentUser.id == 0){
			$.mobile.changePage(loginUrl, {
					        allowSamePageTransition: true,
					        transition: 'none',
					        showLoadMsg : false,
					        reloadPage: true
					    });
			return false;
		}

		var active 	 = $('div.ui-page-active'),
			title    = active.find("input#thread_title").val(),
			content  = active.find("textarea#thread_content").val(),
			category = active.find("select#thread_category").val(),
			data = {post_title : title,post_content : content , thread_category : category};

		if(content == "" || title == "") {
			ForumMobile.app.notice('error', translation_text.fill_out);
			return false;
		}
		if(currentUser.captcha && currentUser.captcha_cat.length != 0){
			var captcha_challend = active.find('input#recaptcha_challenge_field').val(),
				catpcha_response = active.find('input#recaptcha_response_field').val();
			data = {
				post_title : title,
				post_content : content ,
				thread_category : category,
				recaptcha_challenge_field: captcha_challend,
				recaptcha_response_field : catpcha_response
			};
		}
		$.ajax({
			url : fe_globals.ajaxURL,
			type : 'post',
			data : {
				action		: 'et_post_sync',
				method		: 'create',
				content 	: data
			},
			beforeSend : function(){
				$.mobile.showPageLoadingMsg();
			},
			error : function(request){
				$.mobile.hidePageLoadingMsg();
			},
			success : function(response){
				$.mobile.hidePageLoadingMsg();

				if( response.success ){
					if ( response.link )
						window.location.href = response.link;
						// $.mobile.navigate(response.link);
					ForumMobile.app.notice('success', response.msg);
				} else {
					ForumMobile.app.notice('error', response.msg);
				}
			}
		});
	},

	loadMorePosts:function(event){
		event.preventDefault();
		var paged 		= $('div.ui-page-active').find('a#more_blog').attr('data-page'),
			category  	= $('div.ui-page-active').find('a#more_blog').attr('data-cat'),
			query_default = {
				action		: 'fe_get_posts',
				content : {
					paged			: paged,
					category 		: category,
					}
			},
			that = this;

		$.ajax({
			url : fe_globals.ajaxURL,
			type : 'post',
			data : query_default,
			beforeSend : function(){
				$.mobile.showPageLoadingMsg();
			},
			error : function(request){
				$.mobile.hidePageLoadingMsg();
			},
			success : function(response){
				$.mobile.hidePageLoadingMsg();
				current_page = response.data.paged;
				max_page_query = response.data.total_pages;

				if(response.success){

					$('div.ui-page-active').find('a#more_blog').attr('data-page', current_page);
					$('div.ui-page-active').find('a#more_blog').hide();

					var container = $('div.ui-page-active').find('#posts_container');
					for (key in response.data.posts){
						container.append(response.data.posts[key]);
					}

					if( current_page < max_page_query && max_page_query != 0){
						$('div.ui-page-active').find('a#more_blog').show();
					}
				}
				else alert('Query error');
			}
		});
	},

	loadMoreThreads:function(event){
		event.preventDefault();
		var paged 		= $('div.ui-page-active').find('a#more_thread').attr('data-page'),
			status  	= $('div.ui-page-active').find('a#more_thread').attr('data-status'),
			category  	= $('div.ui-page-active').find('a#more_thread').attr('data-term'),
			s  			= $('div.ui-page-active').find('a#more_thread').attr('data-s'),
			query_default = {
				action		: 'et_post_sync',
				method		: 'get',
				content : {
					paged			: paged,
					status 			: status,
					thread_category : category,
					s				: s,
					exclude 		: typeof threads_exclude == 'undefined' ? [] : threads_exclude
					}
			},
			that = this;

		$.ajax({
			url : fe_globals.ajaxURL,
			type : 'post',
			data : query_default,
			beforeSend : function(){
				$.mobile.showPageLoadingMsg();
			},
			error : function(request){
				$.mobile.hidePageLoadingMsg();
			},
			success : function(response){
				$.mobile.hidePageLoadingMsg();
				current_page = response.data.paged;
				max_page_query = response.data.total_pages;

				if(response.success){

					$('div.ui-page-active').find('a#more_thread').attr('data-page', current_page);
					$('div.ui-page-active').find('a#more_thread').hide();

					that.renderLoadMore(response.data.threads);

					if( current_page < max_page_query && max_page_query != 0){
						$('div.ui-page-active').find('a#more_thread').show();
					}
				}
				else alert('Query error');
				$(".login_to_view").click(function(){
			    	ForumMobile.app.notice('error', fe_front.login_2_view);
			    });
			}
		});
	},

	renderLoadMore:function(threads){
		var container = $('div.ui-page-active').find('#posts_container');
		for (key in threads){
			container.append(threads[key]);
		}
	},

	uploadImage: function(upload_button, upload_container, upload_id, upload_textarea) {
		var $images_upload = $(upload_container);
		var $upload_image_button = $(upload_button);
		var view = this;
		this.uploader = new ImagesUpload({
			el: $images_upload,
			uploaderID: upload_id,
			multi_selection: false,
			unique_names: false,
			upload_later: false,
			filters: [{
				title: "Image Files",
				extensions: 'gif,jpg,jpeg,png'
			}, ],
			multipart_params: {
				_ajax_nonce: $images_upload.find('.et_ajaxnonce').attr('id'),
				action: 'et_upload_images'
			},
			cbAdded: function(up, files) {
				if (up.files.length > 1) {
					while (up.files.length > 1) {
						up.removeFile(up.files[0]);
					}
				}
			},
			cbUploaded: function(up, file, res) {
				if (res.success == true) {
					var textarea = $(upload_textarea);
					var post_content = textarea.val();
					post_content = post_content + '[img]'+ res.data +'[/img]';
					textarea.val(post_content);
				} else {
					alert(res.msg);
				}
			},
			beforeSend: function() {
			},
			success: function() {
			}
		});
	},
});

ForumMobile.Views.ThreadItem = Backbone.View.extend({
	tagName : 'article',
	events: {
		'tap a.fe-act'					: 'doAction',
		'tap a.act-undo' 				: 'onUndo'
	},
	initialize: function(){
		this.model = new ForumEngine.Models.Post(this.options.model);
		//console.log(this.model);
	},

	onUndo: function(event){
		event.preventDefault();
		var view = this;

		this.model.undoStatus({
			beforeSend: function(){
				$.mobile.showPageLoadingMsg();
			},
			success: function(resp){
				view.$el.removeClass('undo-active');
			},
			complete: function(){
				$.mobile.hidePageLoadingMsg();
			}
		})
	},

	doAction: function(event){
		var element 	= $(event.currentTarget),
			method 		= element.attr('data-act');
		var view 		= this;

		view.$el.addClass('undo-active')
					.removeClass('fe-editing');

		$.ajax({
			url : fe_globals.ajaxURL,
			type : 'post',
			data : {
				action		: 'et_post_sync',
				method		: method,
				content : {
					id: element.attr('data-id')
				}
			},
			beforeSend : function(){
				$.mobile.showPageLoadingMsg();
			},
			error : function(request){
				$.mobile.hidePageLoadingMsg();
			},
			success : function(response){
				$.mobile.hidePageLoadingMsg();

				if(response.success){
					// notification
					ForumMobile.app.notice('success', response.msg);
					//if(response.link){
						// var redirect;
						// if(method == "delete") redirect = response.link;
						// else redirect = window.location.href;
						// $.mobile.changePage(redirect, {
					 //        allowSamePageTransition: true,
					 //        transition: 'none',
					 //        showLoadMsg : false,
					 //        reloadPage: true
					 //    });
					//} else {
						view.$el.addClass('undo-active')
							.removeClass('fe-editing');
						//element.closest('article').fadeOut('slow');
					//}
				}
				else {
					// notification
					ForumMobile.app.notice('error', response.msg);
				}
			}
		});
	},
});

ImagesUpload	= Backbone.View.extend({

		initialize	: function(options){
			_.bindAll( this , 'onFileUploaded', 'onFileAdded' , 'onFilesBeforeSend' , 'onUploadComplete');
			this.options 	= options;
			this.uploaderID	= (this.options.uploaderID) ? this.options.uploaderID : 'et_uploader';
			this.config	= {
				runtimes			: 'gears,html5,flash,silverlight,browserplus,html4',
				multiple_queues		: true,
				multipart			: true,
				urlstream_upload	: true,
				multi_selection		: false,
				upload_later		: false,
				container			: this.uploaderID + '_container',
				browse_button		: this.uploaderID + '_browse_button',
				thumbnail			: this.uploaderID + '_thumbnail',
				thumbsize			: 'thumbnail',
				file_data_name		: this.uploaderID,
				max_file_size 		: '1mb',
				//chunk_size 			: '1mb',
				// this filters is an array so if we declare it when init Uploader View, this filters will be replaced instead of extend
				filters				: [
					{ title : 'Image Files', extensions : 'jpg,jpeg,gif,png' }
				],
				multipart_params	: {
					fileID		: this.uploaderID
				}
			};

			jQuery.extend( true, this.config, fe_globals.plupload_config, this.options );

			this.controller	= new plupload.Uploader( this.config );
			this.controller.init();

			this.controller.bind( 'FileUploaded', this.onFileUploaded );
			this.controller.bind( 'FilesAdded', this.onFileAdded );
			this.controller.bind( 'BeforeUpload', this.onFilesBeforeSend );
			this.bind( 'UploadSuccessfully', this.onUploadComplete );

			if( typeof this.controller.settings.onProgress === 'function' ){
				this.controller.bind( 'UploadProgress', this.controller.settings.onProgress );
			}
			if( typeof this.controller.settings.onError === 'function' ){
				this.controller.bind( 'Error', this.controller.settings.onError );
			}
			if( typeof this.controller.settings.cbRemoved === 'function' ){
				this.controller.bind( 'FilesRemoved', this.controller.settings.cbRemoved );
			}

		},

		onFileAdded	: function(up, files){
			if( typeof this.controller.settings.cbAdded === 'function' ){
				this.controller.settings.cbAdded(up,files);
			}
			if(!this.controller.settings.upload_later){
				up.refresh();
				up.start();
				//console.log('start');
			}
		},

		onFileUploaded	: function(up, file, res){
			res	= $.parseJSON(res.response);
			if( typeof this.controller.settings.cbUploaded === 'function' ){
				this.controller.settings.cbUploaded(up,file,res);
			}
			if (res.success){
				this.updateThumbnail(res.data);
				this.trigger('UploadSuccessfully', res);
			}
		},

		updateThumbnail	: function(res){
			var that		= this,
				$thumb_div	= this.$('#' + this.controller.settings['thumbnail']),
				$existing_imgs, thumbsize;

			if ($thumb_div.length>0){

				$existing_imgs	= $thumb_div.find('img'),
				thumbsize	= this.controller.settings['thumbsize'];

				if ($existing_imgs.length > 0){
					$existing_imgs.fadeOut(100, function(){
						$existing_imgs.remove();
						if( _.isArray(res[thumbsize]) ){
							that.insertThumb( res[thumbsize][0], $thumb_div );
						}
					});
				}
				else if( _.isArray(res[thumbsize]) ){
					this.insertThumb( res[thumbsize][0], $thumb_div );
				}
			}
		},

		insertThumb	: function(src,target){
			jQuery('<img>').attr({
					'id'	: this.uploaderID + '_thumb',
					'class' : 'avatar',
					'src'	: src
				})
				// .hide()
				.appendTo(target)
				.fadeIn(300);
		},

		updateConfig	: function(options){
			if ('updateThumbnail' in options && 'data' in options ){
				this.updateThumbnail(options.data);
			}
			$.extend( true, this.controller.settings, options );
			this.controller.refresh();
		},

		onFilesBeforeSend : function(){
			if('beforeSend' in this.options && typeof this.options.beforeSend === 'function'){
				this.options.beforeSend(this.$el);
			}
		},
		onUploadComplete : function(res){
			if('success' in this.options && typeof this.options.success === 'function'){
				this.options.success(res);
			}
		}

	});
/*Login to view thread*/
$(document).ready(function() {
    $(".login_to_view").click(function(){
    	ForumMobile.app.notice('error', fe_front.login_2_view);
    });
    var select = $('#thread_category').val();
	if( currentUser.ID != 0 ){
		if(currentUser.captcha && currentUser.captcha_cat.length != 0){
			if( currentUser.captcha_cat.toString().indexOf(select) != -1 ){
				$("#reCaptcha").show();
			} else {
				$("#reCaptcha").hide();
			}
		} else {
			$("#reCaptcha").hide();
		}
	} else {
		$("reCaptcha").show();
	}
});
})(jQuery);