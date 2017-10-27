(function($){

$(document).ready(function(){
	new Backend.Views.Member();
});

Backend.Views.MemberItem = Backbone.View.extend({
	//template: $('#member_template'),
	tagName: 'li',
	className: 'et-member',

	events: {
		'change select[name=role]' 	: 'updateRole',
		'click .et-act-ban' 		: 'renderBanForm',
		'click .et-act-unban' 		: 'unbanUser',
		'click .et-act-confirm' 	: 'confirmUser'
	},

	initialize: function(options){
		this.options = options;
		this.model = new ForumEngine.Models.Member(this.options.model);
	},
	confirmUser: function(e){
		e.preventDefault();
		//console.log(this.model.get('id'));
		this.model.confirm({
			beforeSend: function(){
				//console.log('before update user role');
				$(e.currentTarget).css('opacity', '0.5');
				$(e.currentTarget).attr('disabled', 'disabled');
			},
			success: function(resp){
				//console.log('update success');
				//console.log(resp);
				if(resp.success)
					$(e.currentTarget).remove();
			},
			complete: function(resp){
				//console.log('update complete');
				$(e.currentTarget).css('opacity', '1');
				$(e.currentTarget).removeAttr('disabled');
			}
		});		
	},

	updateRole: function(e){
		//console.log('update role');
		var newRole = $(e.currentTarget).val();
		var element = $(e.currentTarget).parent();
		this.model.updateRole(newRole, {
			beforeSend: function(){
				//console.log('before update user role');
				$(element).loader('load');
				$(e.currentTarget).attr('disabled', 'disabled');
			},
			success: function(resp){
				//console.log('update success');
				//console.log(resp);
			},
			complete: function(resp){
				//console.log('update complete');
				$(element).loader('unload');
				$(e.currentTarget).removeAttr('disabled');
			}
		});
	},

	unbanUser: function(e){
		e.preventDefault();
		var element = $(e.currentTarget).closest('.et-member');
		var view 	= this;
		this.model.unban({
			beforeSend: function(){
				$(element).loader('load');
			},
			success: function(resp){
				if ( resp.success ){
					// reset model
					view.model.set( resp.data.user );
					// re-render
					view.render();
				}
			},
			complete: function(resp){
				$(element).loader('unload');
			}
		});
	},

	render: function(){
		var template = _.template( $('#member_template').html() );

		// generate html
		this.$el.html(template( this.model.attributes )).attr('data-id', this.model.attributes.ID);

		// style select
		this.$('.selector').styleSelect();

		return this;
	},

	renderBanForm: function(e){
		var form = $('#form_ban_user');

		$('#ban_modal .modal-header .display-name').text(this.model.get('display_name'));
		$('#form_ban_user input[name=id]').val( this.model.get('id') );
	}
});

Backend.Views.Member = Backbone.View.extend({
	el: '#engine_setting_content',
	queryVars: {
		offset: 0,
		number: parseInt(fe_globals.posts_per_page),
		search: '',
		role: ''
	},
	events: {
		'click #load-more' : 'loadMore',
		'change .et-search-role select[name=role]' 		: 'filterRole',
		'keyup .et-search-input input[name=keyword]' 	: 'filterText',
		'submit #form_ban_user' 						: 'banUser',
		'submit .et-member-search form'					: 'submit'
		//'change .et-search-role select[name=role]' : 'filterRole'
	},

	initialize: function(){
		var view = this;

		// generate view
		view.memberViews = [];
		// $('#members_list li').each(function(){
		// 	var userID 	= $(this).attr('data-id');
		// 	var name 	= $(this).find('.et-mem-top span.name').text();
		// 	view.memberViews.push(new Backend.Views.MemberItem({ el: $(this), model: {id: userID, ID: userID, 'display_name': name} }));
		// })

		//override underscore template
		_.templateSettings = {
			evaluate: /\<\#(.+?)\#\>/g,
			interpolate: /\{\{=(.+?)\}\}/g,
			escape: /\{\{-(.+?)\}\}/g
		};

		this.searchAction = _.debounce(function(){
			var element = view.$('.et-search-input input[name=keyword]');
			var s 		= element.val();

			if ( view.queryVars.search == s )
				return false;

			this.updateQueryVars({search: s, offset: 0});
			this.filter(element, true);
		}, 1000);

		this.renderMembers();
	},
	submit: function(event) {
		event.preventDefault();
		this.searchAction();
	},
	renderMembers: function(){
		var view = this;
		if ( typeof (members) != 'undefined' ){
			_.each( members, function(e){
				var memberView 	= new Backend.Views.MemberItem({ model: e });
				view.memberViews.push(memberView);

				$('#members_list').append( memberView.render().$el );
				//console.log('load member id:' + e.id);
			} );
		}
	},

	updateQueryVars: function(newValues){
		this.queryVars = _.extend(this.queryVars, newValues);
	},

	updateMemberList: function(newMembers, clear){
		var clear 	= clear ? true : false;
		var view 	= this;

		if (clear){
			view.memberViews = [];
			$('#members_list').html('');
		}
		
		$.each(newMembers, function(){
			var data 		= this;
			var memberView 	= new Backend.Views.MemberItem({ model: data });
			view.memberViews.push(memberView);

			$('#members_list').append( memberView.render().$el );
		});
	},

	filterRole: function(e){
		var element = $(e.currentTarget);
		var role 	= element.val();

		if ( !role ) role = '';

		this.updateQueryVars({role: role, offset: 0});
		this.filter(element, true);
	},

	filterText: function(e){		
		this.searchAction();
	},

	loadMore: function(e){
		this.queryVars.offset = this.queryVars.offset + this.queryVars.number;
		this.filter($(e.currentTarget), false);
	},

	filter: function(element, clearList){
		var view = this;
		var params = {
			url: fe_globals.ajaxURL,
			type: 'post',
			data: {
				action: 'et_user_sync',
				method: 'get_members',
				content: {
					query_vars: this.queryVars
				}
			},
			beforeSend:function(){
				element.loader('load');
			},
			success: function(resp){
				if ( resp.success ){
					view.updateMemberList( resp.data.users, clearList );

					//check
					//console.log(resp.data.total);
					//console.log(resp.data.offset + resp.data.number);
					if ( resp.data.total <= (resp.data.offset + resp.data.number) ){
						$('#load-more').hide();
					} else {
						$('#load-more').show();
					}
				}
			},
			complete: function(){
				//console.log('load more complete');
				element.loader('unload');
			}
		}
		$.ajax(params);
	},

	banUser: function(event){
		event.preventDefault();
		var form 	= $(event.currentTarget);
		var id 		= form.find('input[name=id]').val();
		var view 	= this;
		var params 	= {
			url: fe_globals.ajaxURL,
			type: 'post',
			data: {
				action: 'et_user_sync',
				method: 'ban',
				content: form.serialize()
			},
			beforeSend: function(){

			},
			success: function(resp){
				if ( resp.success ){
					// re-render
					_.each( view.memberViews, function(e){
						if ( e.model.get('id') == id ){
							e.model.set( resp.data.user );

							// perform render
							e.render();

							//$('#members_list').append( memberView.render().$el );
						}
					});

					// close modal
					$('#ban_modal').modal('hide');
				}
			},
			complete: function(){

			}
		}

		$.ajax(params);
	}
});

})(jQuery);