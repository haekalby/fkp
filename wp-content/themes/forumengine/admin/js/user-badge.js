(function($){

	userBadgeView = Backbone.View.extend({
		el : '#engine_setting_content',
		events: {
			'click .goto-reply' 				: 'gotoReply',
			'submit form#form_user_badge'		: 'updateUserBadge'
		},

		initialize: function(){
			// set up toggle buttons
			$('.button-enable').each(function(){
				var element = $(this);
				var view = new OptionToggle({el: element});
			});			
		},
		updateUserBadge: function(event){
			event.preventDefault();
			var form = $(event.currentTarget);

			$.ajax({
				url: fe_globals.ajaxURL,

				type: 'POST',

				data: {
					action 		: 'fe_save_user_badge',
					content 	: form.serialize()						
				},
				beforeSend: function(){
					form.find('button#save').prop('disabled', true);
					form.find('button#save').css('opacity', '0.3');
				},
				success: function(resp){
					form.find('button#save').prop('disabled', false);
					form.find('button#save').css('opacity', '1');

					alert(resp.msg);

				},
			});
		}
	});

	$(document).ready(function(){
		new userBadgeView();
	});
	/**
	 * View for option type on/off
	 */
	OptionToggle = Backbone.View.extend({
		events: {
			'click .active' : 'activate',
			'click .deactive' : 'deactivate'
		},
		
		initialize: function(){
			this.option = $(this.$el).attr('data-name');
		},

		activate: function(event){
			event.preventDefault();
			var view = this;
			this.updateOption(1, function(resp){
				if ( resp.success ){
					$("button#save").prop('disabled',false);
					view.$('a.active').addClass('selected');
					view.$('a.deactive').removeClass('selected');
				}
			});
		},

		deactivate: function(event){
			event.preventDefault();
			var view = this;
			this.updateOption(0, function(resp){
				if ( resp.success ){
					$("button#save").prop('disabled',true);
					view.$('a.deactive').addClass('selected');
					view.$('a.active').removeClass('selected');
				}
			});
		},

		updateOption: function(value, success){
			var view = this;
			var container = $(view.$el);
			var params = {
				url: fe_globals.ajaxURL,
				type: 'post',
				data: {
					action: 'et-toggle-option',
					content: {
						name: this.option,
						value: value
					}
				},
				beforeSend: function(){
					container.loader('load');
				},
				success: function(resp){
					success(resp);
				}, 
				complete: function(){
					container.loader('unload');
				}
			}
			$.ajax(params);
		}
	});

	if ( typeof($.fn.loader) == 'undefined' ){
		$.fn.loader = function(style){
			var element = $(this);
			if ( style == 'load' ){
				element.animate({
					'opacity' : 0.5
				}).addClass('et-loading disabled');
			} else if ( style == 'unload'){
				element.animate({
					'opacity' : 1
				}).removeClass('et-loading disabled');	
			}
		}
	}
})(jQuery);