(function($){

$(document).on("pageinit",function(){
	new ForumMobile.Views.editProfile();
});

ForumMobile.Views.editProfile = Backbone.View.extend({
	el: 'div.page.page-template-page-edit-profile-php',
	events: {
		'submit form.form-edit-profile' 			: 'updateProfile',
		'tap form.form-edit-profile .submit-modal' 	: 'submitUpdate',
		'tap form#form_password .submit-modal' 		: 'submitUpdate',
		'submit form#form_password' 				: 'changePassword',
		'change #hide_profile' 						: 'updateHideInfo'
	},
	initialize: function(){
		$('[data-toggle="modal-edit"]').on('tap', function(event){
			event.preventDefault();
			var element = $(event.currentTarget);
			var target 	= $(element.attr('href'));

			target.addClass('active');

			if ( target.find('input[type=text]').length > 0){
				target.find('input').focus();
			} else if ( target.find('textarea').length > 0 ){
				target.find('textarea').focus().trigger('keyup');
			}

		});
		$('.modal-edit .cancel-modal').on('tap', function(event){
			event.preventDefault();
			var element = $(event.currentTarget);
			var container = element.closest('.modal-edit');
			container.removeClass('active');

			document.activeElement.blur();
		});
	},

	submitUpdate: function(event){
		event.preventDefault();
		$(event.currentTarget).closest('form').trigger('submit');
	},

	changePassword: function(event){
		event.preventDefault();
		var form = $(event.currentTarget);
		var params = {
			url: 	fe_globals.ajaxURL,
			type: 	'post',
			data: {
				action: 'et_user_sync',
				method: 'change_pass',
				content: form.serializeObject()
			},
			beforeSend: function(){
				$.mobile.loading('show');
			},
			success: function(resp){
				if ( resp.success ){
					console.log('finish');
					form.closest('.modal-edit').find('.cancel-modal').trigger('tap');
					ForumMobile.app.notice('success', resp.msg);
				} else {
					ForumMobile.app.notice('error', resp.msg);
				}
			},
			complete: function(){
				$.mobile.loading('hide');
			}
		};

		$.ajax(params);
		return false;
	},

	updateProfile: function(event){
		event.preventDefault();
		var form = $(event.currentTarget);
		var target = form.attr('data-target');
		var params = {
			url: 	fe_globals.ajaxURL,
			type: 	'post',
			data: 	{
				action: 'et_member_sync',
				method: 'update',
				content: form.serialize()
			},
			beforeSend: function(){
				$.mobile.loading('show');
			},
			success: function(resp){
				if ( resp.success ){
					_.each(resp.data, function(value, key){
						var target = $('#content_' + key);
						target.html(value);
					});
					form.closest('.modal-edit').find('.cancel-modal').trigger('tap');
					//ForumMobile.app.notice('success', resp.msg);
				} else {
					//ForumMobile.app.notice('error', resp.msg);
				}
			},
			complete: function(){
				$.mobile.loading('hide');
			}
		}
		$.ajax(params);
		return false;
	},

	updateHideInfo: function(event){
		var element = $(event.currentTarget);
		var value 	= element.is(':checked') ? 1 : 0;
		var params = {
			url: 	fe_globals.ajaxURL,
			type: 	'post',
			data: 	{
				action: 'et_member_sync',
				method: 'update',
				content: element.attr('name') + '=' + value
			},
			beforeSend: function(){
				$.mobile.loading('show');
				element.attr('disabled', 'disabled');
			},
			success: function(resp){
				if ( resp.success ){
					_.each(resp.data, function(value, key){
						var target = $('#content_' + key);
						target.html(value);
					});
					//form.closest('.modal-edit').find('.cancel-modal').trigger('tap');
				}
			},
			complete: function(){
				$.mobile.loading('hide');
				element.removeAttr('disabled');
			}
		}
		$.ajax(params);
	}
});

})(jQuery);