(function($){
	$(document).ready(function(){
		new BackendLanguage();
	});

	var BackendLanguage = Backbone.View.extend({
		el : '#setting-language',
		events: {
			'click .list-language a.set-lang' 	: 'onSetCurrentLang',
			'submit .form-new-lang ' 			: 'onAddNewLang',
			'click .add-lang' 					: 'onOpenLangForm',
			'change #language_to_edit' 			: 'onChooseLanguage',
			'click #save-language' 			 	: 'onSaveLanguage',
			'change .form-item textarea'		: 'onAddClass',
		},

		initialize: function(){

		},

		ajaxParams : function(params){
			return _.extend( {
				url 	: fe_globals.ajaxURL,
				type 	: 'post',
			}, params );
		},

		toggleLangForm: function(type){
			var button 	= $('button.add-lang');
			var input 	= button.siblings('.lang-field-wrap').find('input');
			if (type == 'show'){
				button.fadeOut(400, function(){
					input.fadeIn(400);
				});
			}
			else {
				input.fadeOut(400, function(){
					button.fadeIn(400);
				})
			}
		},

		onOpenLangForm: function(event){
			event.preventDefault();
			this.toggleLangForm('show');
		},

		onSetCurrentLang: function(event){
			event.preventDefault();
			var element = $(event.currentTarget);
			var data 	= $(element).attr('data');
			var params 	= this.ajaxParams({
				data: {
					'action' : 'et-set-current-language',
					'content' : {
						'lang' : data
					}
				},
				beforeSend: function(){
					$(element).loader('load');
				}, 
				success: function(resp){
					//console.log(resp);
					if (resp.success){
						$('.list-language a.set-lang').removeClass('active');
						$(element).addClass('active');
						location.reload();						
					} else {
						alert(resp.msg);
					}
				},
				complete: function(){
					$(element).loader('unload');
				}
			});

			$.ajax(params);
		},

		onAddNewLang: function(event){
			event.preventDefault()
			var form = $(event.currentTarget);
			var data = $(event.currentTarget).serialize();
			var view = this;

			var params = this.ajaxParams({
				data: {
					action : 'et-add-lang',
					content: data
				},
				beforeSend: function(){},
				success: function(resp){
					if (resp.success){
						html = $('<li><a class="set-lang" title="' + resp.data.label + '" href="#et-change-language" data="' + resp.data.name + '">' + resp.data.label + '</a></li>');
						$('.list-language .new-language').before(html);
						form.find('input[type=text]').val('');
					}
				},
				complete: function(){
					view.toggleLangForm();
				}
			});

			$.ajax(params);
		},

		onChooseLanguage: function(event){
			var val 	= $(event.currentTarget).val();
			var wrapper = $(event.currentTarget).parent();
			var view 	= this;
			var params 	= this.ajaxParams({
				data: {
					action : 'et-get-translations',
					content :  {
						lang : val
					}
				}, 
				beforeSend: function(){
					view.loading = true;
					$(wrapper).loader('load');
				},
				success: function(resp){
					//console.log(resp);
					if ( resp.success ){
						$('#form_translate').html(resp.data.html);
						// autosize
						$('#form_translate .autosize').attr('row', 1).autosize();
					} else {
						alert(resp.msg);
					}
				},
				complete: function(){
					view.loading = false;
					$(wrapper).loader('unload');
				}
			});

			$.ajax(params);
		},

		onSaveLanguage: function(event){
			event.preventDefault();
			var form = $('#form_translate');
			var element = $(event.currentTarget);

			// return if event is loading
			if ( element.hasClass('disabled') ) return false;

			var data = [];
			form.find('.form-item textarea.changed').each(function(){
				// if ( $.trim($(this).val()) != '' ){
					var tran = $.trim($(this).val());
					var sing = $(this).siblings('input[type=hidden]').val();
					data.push( {singular : sing, translations : tran } );
				// }
			});
			var lang = $('#language_to_edit').val();

			var params = this.ajaxParams({
				data : {
					action : 'et-save-translation',
					content: {
						lang: lang,
						trans: data
					}
				},
				beforeSend: function(){
					element.loader('load');
				}, 
				success: function(resp){
					form.find('.form-item .changed').removeClass('changed');
					if(element.hasClass('changed') || element.prev('input[type=hidden]').hasClass('changed')){
						element.removeClass('changed');
					}
					if (resp.success){
						alert(resp.msg);
					} else {
						alert(resp.msg);
					}
				},
				complete: function(){
					element.loader('unload');
				}
			});
			$.ajax(params)
		},
		onAddClass: function(event){
			$(event.currentTarget).prev('input[type=hidden]').addClass('changed');
			$(event.currentTarget).addClass('changed');
		},


	})
})(jQuery)