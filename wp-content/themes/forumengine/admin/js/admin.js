(function($){

$(document).ready(function(){
	$('.selector').styleSelect();	
});

Backend 			= {};
Backend.Views 		= {};
Backend.Models 		= {};


/**
 * View for option type on/off
 */
Backend.Views.OptionToggle = Backbone.View.extend({
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

/**
 * Category list view
 */
Backend.Views.CategoryList = Backbone.View.extend({
	el: '#thread-categories',
	events: {
		//'submit #form_new_tax' : 'onCreateCategory'
	},

	initialize: function(){
		var view 		= this;
		view.categories = new ForumEngine.Collections.Categories();

		// 
		view.categoriesView = [];
		$('#thread_cats li ').each(function(){
			var element 	= $(this);
			var id 			= element.attr('data-id');
			var matches 	= /color-(\d)+/.exec(element.attr('class'));
			var color 		= typeof matches[1] != 'undefined' ? matches[1] : 0;
			var name 		= element.find('input[name=name]').val();
			var model  		= new ForumEngine.Models.Category({id: id, ID: id, color: color, name : name});
			var itemView 	= new Backend.Views.CategoryListItem({
				el: element,
				model: model
			});

			// add model to collection

			// view.categories.push(model);

			// // add view to list view
			// view.categoriesView.push(itemView);
		});

		this.initSortable();

		// setup create category view
		var newView = new Backend.Views.NewCategoryItem({
			el: '#cat_create li', 
			model: new ForumEngine.Models.Category({ color: 2}),
			disableEscape: true
		});

		newView.bind('onCreateSuccess', function(model, abc){
			// create new view
			var itemView = new Backend.Views.CategoryListItem({model: model});

			// append view to list
			view.$el.children('ul#thread_cats').append( $(itemView.render().$el).hide().fadeIn() );
		});
	},

	initSortable: function(){
		$('#thread-categories > ul').nestedSortable({
			handle: '.sort-handle',
			listType: 'ul',
			items: 'li',
			maxLevels: 3,
			forcePlaceholderSize: true,
			placeholder: "placeholder",
			update: this.updateCategoriesOrder
		})
	},

	updateCategoriesOrder: function(event, ui){
		var element = $('#thread-categories > ul');
		var order 	= element.nestedSortable('serialize');

		var params = {
			url: fe_globals.ajaxURL,
			type: 'post',
			data: {
				action: 'et_term_sync',
				method: 'sort',
				content: {
					order: order
				}
			},
			beforeSend: function(){},
			success: function(resp, model){
			},
			complete: function(){}
		}

		$.ajax(params);
	},

	onCreateSuccess: function( model, view ){
	},

	onCreateCategory: function(event){
		event.preventDefault();

		var form 	= $(event.currentTarget);
		var name 	= form.find('input[type=text]').val();
		var color   = form.find('.cursor').attr('data');
		var view 	= this;
		var model 	= new ForumEngine.Models.Category({
			name: name,
			color: color
		});

		model.save(model.attributes, {
			beforeSend: function(){
				$(form).find('input[type=text]').val('');
			},
			success: function(model, resp){
				var el 		= $('#cat_item_template')
				var newCat 	= new Backend.Views.CategoryListItem({
					model : model
				});
				view.$el.children('ul#thread_cats').append( $(newCat.render().$el).hide().fadeIn() );
			}
		})
	}

});

Backend.Views.NewCategoryItem = Backbone.View.extend({
	tagName: 'li',
	className: 'tax-item',

	defaults: {
		disableEscape: false
	},

	template : null,

	events: {
		'click .cursor' 			: 'onOpenColorPanel',
		'change input.tax-name'		: 'onChangeName',
		'click button[type=submit]' : 'onCreate',
		'keyup input.tax-name' 		: 'onKeyupInput'
	},

	initialize: function(){
		this.bind('onChangeColor', this.onChangeColor);

		if ( this.template == null ){
			this.template = _.template( $('#cat_item_form').html() );
		}
	},

	onOpenColorPanel : function(event){
		event.stopPropagation();
		// create panel
		var view 	= this;
		var panel 	= $('<div class="color-panel">');

		if ( !view.$el.hasClass('colored') ){
			for (var i = 0; i < 40; i++) {
				var element = $('<div class="color-item" data="' + i + '">').append('<span class="flags color-' + i + '"></span>');

				// set event
				element.bind('click', function(event){
					var val = $(event.currentTarget).attr('data');

					panel.fadeOut('normal', function(){ $(this).remove() });
					view.$el.removeClass('colored');

					var classes = view.$el.attr('class');
					classes = classes.replace(/color-(\d)+/, "");
					classes += ' color-' + val;
					view.$el.attr('class', classes);

					view.trigger('onChangeColor', val, panel);
				});
				panel.append(element);
			};
			view.$el.addClass('colored').append(panel);
		} else {
			view.$el.removeClass('colored').find('.color-panel').remove();
		}
	},

	onChangeColor: function(val, panel){
		this.model.set('color', val);
	},

	onChangeName: function(event){
		event.stopPropagation();

		this.model.set('name', $(event.currentTarget).val());
	},

	onKeyupInput: function(event){
		if ( event.which == 27 && !this.options.disableEscape ){
			this.$el.fadeOut('normal', function(){ $(this).remove() });
		} if ( event.which == 13){
			this.$('button[type=submit]').trigger('click');
		}
	},

	onCreate: function(event){
		event.stopPropagation();
		var view = this;
		
		this.model.set('name', view.$('input[type=text][name=name]').val() );

		this.model.save(this.model.attributes, {
			beforeSend: function(){
				view.clearForm();
			},
			success: function(model, resp){ 
				if ( resp.success ){
					view.trigger('onCreateSuccess', view.model, view);
					view.model = new ForumEngine.Models.Category({ color: 0});
				}
				else {
					view.trigger('onCreateFailed', view.model, view);
				}
			}
		})
	},

	clearForm: function(){
		this.$('input[type=text]').val('');
	},

	render: function(){
		this.$el.html( this.template() );
		if ( this.model.get('color') != 0 ){
			this.$el.addClass('color-' + this.model.get('color'));
		}
		return this;
	}

});

/**
 * Category list item view
 */
Backend.Views.CategoryListItem = Backbone.View.extend({
	tagName: 'li',
	className: 'tax-item',
	events: {
		'change input.tax-name'	: 'onChangeTermName',
		'click .act-open-form' 	: 'onOpenSubForm',
		'click .cursor' 		: 'onOpenColorPanel',
		'click .act-del' 		: 'onDelete'
	},
	template: null,
	initialize: function(){
		this.bind('onChangeColor', this.onChangeColor);

		if ( this.template == null ){
			this.template = _.template( $( '#cat_item_template' ).html() );
		}
	},

	onChangeTermName: function(event){
		event.stopPropagation();
		var element = $(event.currentTarget);
		var view 	= this;
		this.model.save({name: element.val()}, {
			beforeSend: function(){
				view.$el.loader('load');
			}, 
			success: function(){},
			complete: function(){
				view.$el.loader('unload');
			}
		});
	},

	onOpenSubForm: function(event){
		event.stopPropagation();
		event.preventDefault();

		// check the level
		var level 		= this.$el.parents('ul').length;
		var limit 		= 3
		if ( level >= limit ){
			alert(fe_setting_msgs.limit_category_level);
			return false;
		}

		// create 
		var view 		= this;
		var newModel 	= new ForumEngine.Models.Category({parent: this.model.get('id'), color: this.model.get('color')});
		var newView  	= new Backend.Views.NewCategoryItem( {model : newModel} );
		var list  		= null;
		var html 		= $('#cat_item_form').html();

		if ( view.$el.children('ul').length > 0 ){
			list = view.$el.children('ul');
		} else {
			list = $('ul').appendTo(view.$el);
		}

		// create small view
		list.append( newView.render().$el );

		// handle of term has been created
		newView.bind('onCreateSuccess', function(model){
			var itemView = new Backend.Views.CategoryListItem({model: model});
			newView.remove();
			list.append( itemView.render().$el.hide().fadeIn() );
		})
	},

	onOpenColorPanel: function(event){
		event.stopPropagation();
		// create panel
		var view 	= this;
		var panel 	= $('<div class="color-panel">');

		if ( !view.$el.hasClass('colored') ){
			for (var i = 0; i < 40; i++) {
				var element = $('<div class="color-item" data="' + i + '">').append('<span class="flags color-' + i + '"></span>');

				// set event
				element.bind('click', function(event){
					var val = $(event.currentTarget).attr('data');

					panel.fadeOut('normal', function(){ $(this).remove() });
					view.$el.removeClass('colored');

					var classes = view.$el.attr('class');
					classes = classes.replace(/color-(\d)+/, "");
					classes += ' color-' + val;
					view.$el.attr('class', classes);

					view.trigger('onChangeColor', val, panel);
				});
				panel.append(element);
			};
			view.$el.addClass('colored').append(panel);
		} else {
			view.$el.removeClass('colored').find('.color-panel').remove();
		}
		
	},

	onChangeColor: function(val, panel){
		// set color for model
		this.model.set('color', val);

		//
		if ( typeof this.model.attributes.id == 'undefined' ) return false;

		var tempModel  	= new ForumEngine.Models.Category({id: this.model.get('id'), ID: this.model.get('id'), color: val});
		var view 		= this;
		this.model.set('color', val);
		tempModel.save({color: val}, {
			beforeSend: function(){
				view.$el.loader('load');
			}, 
			success : function(model, resp){
				if ( resp.success ){
				}
			},
			complete: function(){
				view.$el.loader('unload');
			}
		})
	},

	onDelete: function(event){
		event.stopPropagation();
		event.preventDefault();
		var element = $(event.currentTarget);
		var view 	= this;

		this.model.deleteItem({
			beforeSend: function(){
				view.$el.loader('load');
			}, 
			success: function(resp, model){
				if (resp.success){
					view.$el.fadeOut('normal', function(){ $(this).remove(); })
				} else {
					alert(resp.msg);
				}
			},
			complete: function(){
				view.$el.loader('unload');
			}
		});
	},

	render: function(){
		var html  		= this.template(this.model.attributes);
		var colorClass  = 'color-' + this.model.get('color');
		this.$el.html(html)
			.addClass(this.className)
			.addClass(colorClass)
			.attr('data-id', this.model.get('term_id'))
			.attr('id', 'tax_' + this.model.get('term_id'));
		return this;
	}
});

ForumEngine.Collections.Categories = Backbone.Collection.extend({
	model: ForumEngine.Models.Category
})

ForumEngine.Models.Category = Backbone.Model.extend({
	initialize: function(){

	},
	parse		: function(resp){
		if ( resp.data.term ){
			var result 	= resp.data.term;
			result.id 	= result.term_id;
			return result;
		}
		else 
			return {};
	},

	deleteItem: function(options){
		this.sync('delete', this, options);
	},

	setColor: function(newColor, options){
		this.sync('changeColor', this, options);
	},

	sync: function(method, model, options){
		// build all params
		var params = {
			url: fe_globals.ajaxURL,
			type: 'post',
			data: {
				action: 'et_term_sync',
				method: method
			},
			beforeSend: function(){},
			success: function(resp, model){},
			complete: function(){}
		}

		// build data
		var data = model.attributes;
		if ( options.fields ){
			var data = {};
			_.each( options.fields, function(field){
				data[field] = model.get(fields);
			});
		}
		params.data.content = data;

		// build callback
		var beforeSend 	= options.beforeSend || function(){};
		var success 	= options.success || function(resp, model){};
		var complete 	= options.complete || function(){};

		params.beforeSend = beforeSend;
		params.success = function(resp){
			success(resp, model);
		}
		params.complete = complete;

		return $.ajax(params);
	},
})

})(jQuery);