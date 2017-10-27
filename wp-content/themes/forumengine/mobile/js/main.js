(function($){

$(document).on("pageinit",function(){
	ForumMobile.app = new ForumMobile.Views.App();
});

ForumMobile = {} || ForumMobile;
ForumMobile.Views = {} || ForumMobile.View;

/**
 * Website script here
 **/
ForumMobile.Views.App = Backbone.View.extend({
	el: 'div.ui-page',
	events: {
		'click .fe-cat-list ul > li > a > .arrow' 	: 'expandCategories',
		'focus .fe-topic-input input[type=text]' 	: 'openTopicForm',
		'tap .fe-btn-cancel' 						: 'closeTopicForm',
		'click .fe-post .fe-post-edit' 				: 'openEditPanel',
		'tap .ui-page-active .fe-header .header-part a.fe-search' 	: 'toggleHeader',
		'tap .toggle-menu' 							: 'toggleMenu'
	},
	initialize: function(){
		//
		$('.fe-thread-info').each(function(){
			var container 	= $(this);
			var controls 	= container.find('.fe-actions-container');
			var infoArea 	= container.find('.fe-info-container');
			var trigger 	= container.find('.fe-btn-ctrl');

			if(fe_globals.isEditable){
				trigger.on('tap', applySlide);
				container.on('swiperight', applySlide);
				container.on('swipeleft', applySlide);
			}

			function applySlide(event){
				event.preventDefault();
				if ( container.hasClass('editing') ){
					container
						.animate({
							left: 0
						}, 'fast', function(){
						})
						.removeClass('editing');
				} else {
					container
						.animate({
							left: infoArea.width() + 10
						}, 'fast')
						.addClass('editing');
				}
			}
		});
	},

	notice: function(status, msg){
		var html = '<div class="fe-notice">'+
		'<span class="fe-icon"></span>' +
		'<span class="fe-notice-text">' + msg + '</span>' +
		'</div>';

		var notice = $(html);
		notice.addClass('fe-notice-success');

		if ( $('body').scrollTop() > 44 ) {
			notice
				.css({
					'position' 	: 'fixed',
					'top' 		: 0
				});
		}
		$(window).scroll( function(){
			if ( $('body').scrollTop() > 44 ){
				notice
					.css({
					'position' 	: 'fixed',
					'top' 		: 0
				});
			} else {
				notice.css({
					'position' 	: 'absolute',
					'top' 		: '44px'
				});
			}
		});

		if ( status != 'hide' ){
			if ( status == 'success' || status == 'show' ){
				notice.addClass('fe-notice-success');
			} else if ( status == 'error'){
				notice.addClass('fe-notice-error');
			} else if ( status == 'warning'){
				notice.addClass('fe-notice-warning');
			}

			$('.ui-page-active').prepend(notice);
			notice.delay(2000).fadeOut('normal', function(){
				$(this).remove();
				$(window).unbind('scroll');
			});
		} else {
			$('.fe-notice').fadeOut('normal', function(){
				$(this).remove();
				$(window).unbind('scroll');
			});
		}
		return notice;
	},

	toggleMenu: function(event){
		event.preventDefault();
		var container = $(event.currentTarget).closest('.fe-content');
		container.toggleClass('open-profile-menu');
	},

	initDataToggle: function(){
		// tabs

	},

	toggleHeader: function(event){
		event.preventDefault();
		event.stopPropagation();
		var element 	= $(event.currentTarget);
		//var target 		= $('.ui-page-active ' + element.attr('href'));
		var container 	= element.closest('.header-part');
		var target  	= $(container).siblings();

		//event.preventDefault();
		container.removeClass('active');
		target.addClass('active');

		if ( target.hasClass('header-search') ){
			target.find('input[type=text]').focus();
		}
	},
	expandCategories: function(event){
		event.preventDefault();
		var element = $(event.currentTarget);
		var parent 	= element.closest('li');
		var childList = parent.children('ul');

		if (parent.hasClass('fe-opened')){
			childList.css('max-height', 0);
			parent.removeClass('fe-opened');
		} else {
			var count = childList.find('li').length;
			childList.css('max-height', count*44);
			parent.addClass('fe-opened');
		}
	},
	closeTopicForm: function(event){
		var container 		= $('div.ui-page-active').find('.fe-topic-form');
		var textarea 	= $('div.ui-page-active').find('.fe-topic-form .fe-topic-content');
		$('div.ui-page-active').find('.fe-topic-dropbox').hide();
		if (textarea.hasClass('fe-expanded')){
			textarea.slideUp();
			textarea.removeClass('fe-expanded');
			container.removeClass('fe-expanded');
			$('body').animate({scrollTop: 0});
		}
	},
	openTopicForm: function(event){
		var element 	= event.currentTarget;
		if(!$('.fe-topic-content.fe-expanded').is(':visible')){
			$("#reCaptcha").hide();
		}
		var container 	=  $('div.ui-page-active').find('.fe-topic-form');
		var textarea 	= $('div.ui-page-active').find('.fe-topic-form .fe-topic-content');

		$('div.ui-page-active').find('.fe-topic-dropbox').show();

		if (!textarea.hasClass('fe-expanded')){
			textarea.slideDown();
			textarea.addClass('fe-expanded');
			container.addClass('fe-expanded')
		}

		var select = $('#thread_category').val();
		$( "#thread_category" ).change(function(e) {
			if( currentUser.ID != 0 ){
				if(currentUser.captcha && currentUser.captcha_cat.length != 0){
					if( currentUser.captcha_cat.toString().indexOf($(this).val()) != -1 ){
						Recaptcha.reload();
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
		
		// else {
		// 	textarea.slideDown();
		// 	textarea.addClass('.fe-expanded')
		// }
	},
	openEditPanel: function(event){
		var element 	= $(event.currentTarget);
		var container 	= element.closest('.fe-post');
		if ( container.hasClass('fe-editing') ){
			container.removeClass('fe-editing');
		} else {
			container.addClass('fe-editing');
		}
	}
});

})(jQuery);