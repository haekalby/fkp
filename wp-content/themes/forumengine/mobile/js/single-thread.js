(function($){

$(document).ready(function(){
	$.mobile.ajaxEnabled = false;
	new ForumMobile.Views.Single();
});

/**
 * Website script here
 **/
ForumMobile.Views.Single = Backbone.View.extend({
	el: 'body',
	events: {
		'tap a.like'						: 'onLike',
		'tap a.show-comment-child'			: 'showReplyChild',
		'tap a.btn-more-reply'				: 'loadMoreRepliesChild',
		'tap a#more_reply'					: 'loadMoreReplies',
		'tap a#reply_thread'				: 'replyThread',
		'tap a.reply-child'					: 'replyChild',
		'tap a.tog-follow'					: 'toggleFollow',
		'tap a.fe-reply'					: 'openReplyForm',
		'tap .child-reply-box .fe-btn-cancel' : 'closeReplyForm',
		'tap a.fe-icon-edit'				: 'openEditForm',
		'tap .fe-topic-form .fe-form-actions a.fe-btn-cancel'	: 'cancelEditForm',
		'tap a#update_thread'				: 'saveEditForm',
		'tap a.update-reply'				: 'saveEditForm',
		'tap a.scroll_to_reply'				: 'scrollBottom',
		'tap a.fe-icon-quote'				: 'onQuote',
		'tap #main_reply a.fe-btn-cancel'  	: 'onCancelReply',
		'tap #main_reply .fe-reply-overlay' : 'openReplyInput',
		'tap a.fe-act'						: 'doAction',
		'tap a#more_comment' 				: 'loadMoreComments',
		'tap a.fe-comment-reply' 			: 'replyPost',
		//'tap #submit' 					: 'insertComment',
		'submit #commentform' 				: 'submitComment',
		'tap #cancel-comment-reply-link' 	: 'cancelReply'
	},

	initialize: function(){
		this.replyPages = [];
		if( $("#mobile_reply_template").length > 0 )
			this.replyTemplate = _.template( $("#mobile_reply_template").html() );
	},

	cancelReply: function(event){
		event.preventDefault();
		var element = $(event.currentTarget);
		var container 	= element.closest('.fe-reply-form');

		container.find('input[name=comment_parent]').val('');
		$('#cancel-comment-reply-link').hide();

		$('.fe-comment-form').append(container);
		$('body').animate({scrollTop : $('#respond').offset().top });
	},
	redirectLoginPage: function(url){
		$.mobile.changePage(url, {
				        allowSamePageTransition: true,
				        transition: 'none',
				        showLoadMsg : false,
				        reloadPage: true
				    });
	},
	replyPost:function(event){
		event.preventDefault();

		var reply 			= $(event.currentTarget),
			comment_form 	= $("#comment_form_wrap"),
			parent_id 		= reply.attr('data-id'),
			comment_parent  = comment_form.find('input#comment_parent'),
			textarea 	 	= comment_form.find('textarea#comment'),
			container 		= reply.closest('li.fe-comment');

		$('#cancel-comment-reply-link').show();

		comment_parent.val(parent_id);
		container.append(comment_form);
		$('body').animate({scrollTop : $('#respond').offset().top });
	},

	submitComment: function(event){

		if(currentUser.id == 0){
			this.redirectLoginPage(loginUrl);
		}

		var data = $(event.currentTarget).serializeObject();

		$.ajax({
			url : fe_globals.ajaxURL,
			type : 'POST',
			data : {
				action: 'fe_insert_comment',
				content: data
			},
			beforeSend : function(){
				$.mobile.showPageLoadingMsg();
			},
			success : function(response){
				$.mobile.hidePageLoadingMsg();
				if(response.success){
					$.mobile.changePage(
					    window.location.href,
					    {
					      allowSamePageTransition : true,
					      transition              : 'none',
					      showLoadMsg             : true,
					      reloadPage              : true
					    }
					  );
				}
			}
		});
		return false;
	},

	insertComment:function(event){
		event.preventDefault();

		var comment_form 	= $("form#comment_form"),
			comment_post_ID  = comment_form.find('input#comment_post_ID'),
			parent  = comment_form.find('input#comment_parent'),
			author  = comment_form.find('input#author'),
			email  	= comment_form.find('input#email'),
			url  	= comment_form.find('input#url'),
			content = comment_form.find('textarea#comment'),
			query_default = {
				action		: 'fe_insert_comment',
				content : {
					comment_post_ID 		: comment_post_ID.val(),
					comment_parent			: parent.val(),
					comment_author			: author.val(),
					comment_author_email	: email.val(),
					comment_author_url		: url.val(),
					comment_content			: content.val(),
				}
			};

		if(author == "" || email == "" || content.val() == "" || $.trim(content.val()) == "") {
			return false;
		}

		$.ajax({
			url : fe_globals.ajaxURL,
			type : 'POST',
			data : query_default,
			beforeSend : function(){
				$.mobile.showPageLoadingMsg();
			},
			success : function(response){
				$.mobile.hidePageLoadingMsg();
				if(response.success){
					$.mobile.changePage(
					    window.location.href,
					    {
					      allowSamePageTransition : true,
					      transition              : 'none',
					      showLoadMsg             : true,
					      reloadPage              : true
					    }
					  );
				}
			}
		});
	},

	loadMoreComments:function(event){
		event.preventDefault();
		var paged 		  = $('div.ui-page-active').find('a#more_comment').attr('data-page'),
			id  		  = $('div.ui-page-active').find('a#more_comment').attr('data-id'),
			query_default = {
				action		: 'fe_get_comments',
				content : {
					paged		: paged,
					id 			: id,
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
				max_page_query = $('div.ui-page-active').find('a#more_comment').attr('data-max-page');//response.data.total_pages;

				if(response.success){

					$('div.ui-page-active').find('a#more_comment').attr('data-page', current_page);
					$('div.ui-page-active').find('a#more_comment').hide();

					var container = $('div.ui-page-active').find('#comments_list');
					//for (key in response.data.comments){
						container.append(response.data.comments);
					//}

					if( current_page < max_page_query ){
						$('div.ui-page-active').find('a#more_comment').show();
					}
				}
				else alert('Query error');
			}
		});
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
				if(response != null){
					if(response.success){
						// notification
						ForumMobile.app.notice('success', response.msg);
						if(response.link){
							var redirect;
							if(method == "delete") redirect = response.link;
							else redirect = window.location.href;
							window.location.href = redirect;
							// $.mobile.changePage(redirect, {
						 //        allowSamePageTransition: true,
						 //        transition: 'none',
						 //        showLoadMsg : false,
						 //        reloadPage: true
						 //    });
						} else {
							// view.$el.addClass('undo-active')
							// 	.removeClass('fe-editing');
							//element.closest('article').fadeOut('slow');
						}
					}
					else {
						// notification
						ForumMobile.app.notice('error', response.msg);
					}
				}
			}
		});
	},
	onQuote:function(event){
		var element  = $(event.currentTarget),
			target   = $(element.attr('href')),
			active   = $('div.ui-page-active'),
			quote    = target.find('.fe-th-content').first().html(),
			author   = target.find('span.title').first().text(),
			quotetrim = quote.replace(/^\s\s*/, '').replace(/\s\s*$/, '');

			if(quotetrim.indexOf("</blockquote>") > 0) {
				quotetrim = quotetrim.split("</blockquote>");
				newContent = quotetrim[1].replace(/(<br ?\/?>)*/g,"").replace(/[&]nbsp[;]/gi,"").replace(/<p>\s*<\/p>/,"");
				//console.log(newContent);
			} else {
				newContent = quotetrim;
			}

			$('.child-reply-box').hide();

			if(element.attr('data-id')){
				active.find('.fe-reply-box').addClass('expand');
				textarea = active.find("textarea#reply_content");
				textarea.focus();
				textarea.val('[quote author="'+author+'"]'+newContent+'[/quote]'+"\n");
				textarea.css('height', '150px');
			} else {
				target.find('.child-reply-box').show();
				target.find('.fe-reply-box').addClass('expand');
				textarea = target.find("textarea.reply_child_content");
				textarea.focus();
				textarea.val('[quote author="'+author+'"]'+newContent+'[/quote]'+"\n");
				textarea.css('height', '150px');
			}

		//console.log(target.find('.fe-th-content').html());
	},
	scrollBottom:function(event){
		$('body').animate({scrollTop : $('#main_reply').offset().top });
		$('#main_reply .fe-reply-overlay').trigger('tap');
	},
	openEditForm:function(event){
		event.preventDefault();

		var element 	= $(event.currentTarget),
			target  	= $(element.attr('href')),
			container 	= element.closest('.fe-th-post'),
			childReplies = target.find('.fe-th-replies');

		childReplies.hide();
		container.addClass('open-edit').siblings().removeClass('open-edit');
		$('body').animate({'scrollTop': container.offset().top});


		var id = $(container).find('.insert-image-wrap').data('id'),
			upload_container = $(container).find('.fe-topic-content #'+id+'_images_upload_container'),
			upload_button = $(container).find('.fe-topic-content #'+id+'_images_upload_browse_button'),
			textarea = $(container).find('.fe-topic-content textarea');

		this.uploadImage(
			upload_button,
			upload_container,
			id+'_images_upload',			// Upload ID
			textarea
		);

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
	cancelEditForm: function(event){
		event.preventDefault();
		var element 	= $(event.currentTarget);
		var container 	= element.closest('.fe-th-post');

		container.removeClass('open-edit');
		$('body').animate({'scrollTop': container.offset().top});
	},
	saveEditForm:function(event){
		var element = $(event.currentTarget),
			target  = $(element.attr('href')),
			id 		= element.attr('data-id'),
			fe_nonce = target.find("input#fe_nonce").val(),
			title    = target.find("input#thread_title").val(),
			content  = target.find("textarea#thread_content").val(),
			category = target.find("select#thread_category").val();

		if(content == "" || title == "" || category == "") return false;

		$.ajax({
			url : fe_globals.ajaxURL,
			type : 'post',
			data : {
				action		: 'et_post_sync',
				method		: 'update',
				content 	: {
					ID				: id,
					fe_nonce		: fe_nonce,
					post_title		: title,
					post_content 	: content,
					thread_category : category,
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
					$.mobile.changePage(window.location.href, {
				        allowSamePageTransition: true,
				        transition: 'none',
				        showLoadMsg : false,
				        reloadPage: true
				    });
				}
				else {
					ForumMobile.app.notice('error', response.msg);
				}
			}
		});
	},
	toggleFollow:function(event){
		event.preventDefault();
		var element = $(event.currentTarget),
			ul 		= element.parent().parent(),
			params 		= {
				url 	: fe_globals.ajaxURL,
				type 	: 'post',
				data 	:   {
					action : 'et_user_sync',
					method : 'follow',
					content : {
						post_id : element.attr('data-id'),
					}
				},
				success: function(resp){
					if (resp.success){
						ForumMobile.app.notice('success', resp.msg);

						if (resp.data.isFollow){
							ul.find('li.follow').hide();
							ul.find('li.unfollow').show();
						}
						else {
							ul.find('li.unfollow').hide();
							ul.find('li.follow').show();
						}
					} else {
						ForumMobile.app.notice('error', resp.msg);
						//console.log('fetch fail');
					}
				}
			};
		$.ajax(params);
	},

	onLike:function(event){
		var	element = $(event.currentTarget),
			params 		= {
				url 	: fe_globals.ajaxURL,
				type 	: 'post',
				data 	:   {
					action : 'et_post_sync',
					method : 'like',
					content : {
						id 			: element.attr('data-id'),
					}
				},
				success: function(resp){
					if (resp.success){
						if (resp.data.isLike){
							// update like count
							element.find('.like').addClass('active');
							element.find('.count').text(resp.data.count);
						}
						else {
							element.find('.like').removeClass('active');
							element.find('.count').text(resp.data.count);
						}
					} else {
						ForumMobile.app.notice('error', resp.msg);
						//console.log('fetch fail');
					}
				}
			};
		$.ajax(params);
	},

	openReplyForm:function(event){
		event.preventDefault();
		var	element 	= $(event.currentTarget),
			target 		= $(element.attr("href")),
			reply_box 	= target.find(".child-reply-box"),
			container 	= element.closest('.fe-th-post'),
			control		= target.find(".fe-th-ctrl");

		$(".child-reply-box").slideUp();
		$(".fe-th-ctrl").fadeIn();
		control.hide();
		$('#main_reply').removeClass('expand');

		reply_box.slideDown('normal', function() {
			reply_box.find('textarea').focus();
		});
		// if ( !container.hasClass('fe-th-thread') ){
		// 	container.addClass('open-reply').siblings().removeClass('open-reply');
		// 	$('#main_reply').removeClass('expand');

		// 	reply_box.find('textarea').focus();
		// }
	},

	closeReplyForm: function(event){
		event.preventDefault();
		// var container = $(event.currentTarget).closest('.fe-th-post');
		// //container.removeClass('open-reply');
		// container.find('.child-reply-box').hide();
		// document.activeElement.blur();
		$('.child-reply-box').slideUp('normal', function(){
			$('.fe-th-ctrl').fadeIn();
		});
		document.activeElement.blur();
	},

	replyChild:function(event){
		event.preventDefault();

		if(currentUser.id == 0){
			this.redirectLoginPage(loginUrl);
		}

		var element	 	= $(event.currentTarget),
			parentId 	= element.attr('data-id'),
			textarea 	= $("#reply_"+parentId).find("textarea.reply_child_content"),
			container 	= $("#reply_"+parentId).find('.fe-th-replies'),
			view 		= this,
			params 		= {
				url 	: fe_globals.ajaxURL,
				type 	: 'post',
				data 	:   {
					action : 'et_post_sync',
					method : 'reply',
					content : {
						et_reply_parent 	: parentId,
						post_content 		: textarea.val(),
						post_type 			: 'reply'
					}
				},
				beforeSend: function (){
					element.prop('disabled', true);
					$.mobile.loading('show');
				},
				success: function(resp){
					element.prop('disabled', false);
					if (resp.success){
						container.append(resp.data.reply.mobile_child_html);
						$("#reply_"+parentId).find('.c-count').text(resp.data.found_posts)
						textarea.val("");
						textarea.focusout();
					} else {
						ForumMobile.app.notice('error', resp.msg);
						//console.log('fetch fail');
					}
				},
				complete: function(){
					$.mobile.loading('hide');
				}
			};
		if(textarea.val() == ""){
			textarea.focus();
			return false;
		}
		$.ajax(params);
	},

	replyThread:function(event){

		if(currentUser.id == 0){
			this.redirectLoginPage(loginUrl);
		}

		var element	 	= $(event.currentTarget),
			parentId 	= element.attr('data-id'),
			textarea 	= $('div.ui-page-active').find("textarea#reply_content"),
			container 	= $('div.ui-page-active').find('.fe-th-posts'),
			view 		= this,
			params 		= {
				url 	: fe_globals.ajaxURL,
				type 	: 'post',
				data 	:   {
					action : 'et_post_sync',
					method : 'reply',
					dataType: 'json',
					content : {
						ID 			: parentId,
						post_content 	: textarea.val(),
					}
				},
				beforeSend: function (){
					$.mobile.showPageLoadingMsg();
					element.prop('disabled', true);
				},
				success: function(resp){
					$.mobile.hidePageLoadingMsg();
					element.prop('disabled', false);
					if (resp.success){
						pubsub.trigger('fe:newReply', resp.data.reply);
						// add content
						//container.append(resp.data.reply.mobile_html);
						container.append( view.replyTemplate(resp.data.reply) );
						textarea.val("");
					} else {
						ForumMobile.app.notice('error', resp.msg);
					}
				}
			};
			//console.log(parentId);
		if(textarea.val() == ""){
			textarea.focus();
			return false;
		}
		$.ajax(params);

	},
	showReplyChild: function(event){

		var element	 	= $(event.currentTarget),
			parentId 	= element.attr('data-id'),
			target 		= $('#reply_'+parentId),
			container 	= target.find(".fe-th-replies"),
			paged 		=  this.replyPages[parentId] ? this.replyPages[parentId] : 1,
			buttonMore 	= target.find('.btn-more-reply'),
			view 		= this;

		view.fetchReplies(parentId, paged, {
			beforeSend: function (){
				container.html('Loading...');
				buttonMore.hide();
			},
			success: function(resp){
				if (resp.success){
					container.html('');

					// add content
					_.each( resp.data.replies, function(element){
						container.append(element.mobile_child_html);
					} );

					//verify pagination
					if ( resp.data.current_page < resp.data.total_pages ){
						view.replyPages[parentId] = paged;
						buttonMore.show();
					} else {
						buttonMore.hide();
					}
				} else {
					ForumMobile.app.notice('error', resp.msg);
					//console.log('fetch fail');
				}
			}
		});
	},
	loadMoreRepliesChild:function(event){

		var element  	= $(event.currentTarget),
			parentId  	= element.attr('data-id'),
			button 		= $('#reply_'+parentId).find('.btn-more-reply'),
			container 	= $('#reply_'+parentId).find('.fe-th-replies'),
			paged 		= this.replyPages[parentId] ? this.replyPages[parentId] : 1,
			view 		= this;

		paged++;

		view.fetchReplies(parentId, paged, {
			beforeSend : function(){
				button.hide();
			},
			error : function(request){
			},
			success : function(resp){
				if(resp.success){

					_.each( resp.data.replies, function(element){
						container.append(element.mobile_child_html).listview();
					} );

					if ( resp.data.current_page < resp.data.total_pages ){
						view.replyPages[parentId] = paged;
						button.show();
					} else {
						button.hide();
					}

				}
				else
					ForumMobile.app.notice('error', resp.msg);
			}
		});
	},
	loadMoreReplies:function(event){

		var button 		= $('div.ui-page-active').find('a#more_reply'),
			parentId  	= button.attr('data-id'),
			paged 		= this.replyPages[parentId] ? this.replyPages[parentId] : 1,
			container 	= $('div.ui-page-active').find('.fe-th-posts'),
			view 		= this;

		paged++;

		view.fetchReplies(parentId, paged, {
			data 	:   {
				action : 'et_fetch_replies',
				content : {
					post_parent 	: parentId,
					order			: 'ASC',
					paged 			: paged ? paged : 1
				}
			},
			beforeSend : function(){
				$.mobile.showPageLoadingMsg();
				button.hide();
			},
			error : function(request){
				$.mobile.hidePageLoadingMsg();
			},
			success : function(resp){
				$.mobile.hidePageLoadingMsg();

				if(resp.success){

					_.each( resp.data.replies, function(element){
						container.append(element.mobile_html);
						//container.append( view.replyTemplate(element) );
					} );

					if ( resp.data.current_page < resp.data.total_pages ){
						view.replyPages[parentId] = paged;
						button.show();
					} else {
						button.hide();
					}

				}
				else alert('Query error');
			}
		});
	},
	fetchReplies:function(parentId, page,params){
		var def = {
			url 	: fe_globals.ajaxURL,
			type 	: 'post',
			data 	:   {
				action : 'et_fetch_replies',
				content : {
					reply_parent 	: parentId,
					paged 			: page ? parseInt(page) : 1
				}
			},
		};
		params = $.extend( def, params );
		$.ajax(params);
	},
	onCancelReply: function(event){
		event.preventDefault();

		var element 	= $(event.currentTarget);
		var container 	= $(element.closest('.fe-reply-box'));
		$(".fe-th-ctrl").show();
		container.removeClass('expand');
		container.find('.insert-image-wrap').css('display','none');
		document.activeElement.blur();
	},
	openReplyInput: function(event){
		event.preventDefault();
		var element 	= $(event.currentTarget);
		var container 	= element.closest('.fe-reply-box');

		container
			.addClass('expand')
			.find('textarea').focus();
		container.find('.insert-image-wrap').css('display','block');
		$('.fe-th-post').removeClass('open-reply');

		var id = $(container).find('.fe-reply-box .insert-image-wrap').data('id'),
			upload_container = $(container).find('.fe-topic-content #'+id+'_images_upload_container'),
			upload_button = $(container).find('.fe-topic-content #'+id+'_images_upload_browse_button'),
			textarea = $(container).find('.fe-reply-box textarea');

		this.uploadImage(
			upload_button,
			upload_container,
			id+'_images_upload',			// Upload ID
			textarea
		);
	}
});
})(jQuery);