(function($){

$(document).ready(function(){
	new Backend.Views.Overview();
});

Backend.Views.Overview = Backbone.View.extend({
	el: '#engine_setting_content',
	events: {
		'change #stat_filter select[name=time]': 'changeTime',
		'submit #stat_filter' : 'filterStat'
	},
	initialize : function(){
		this.initGraphs();
		//console.log(this.threadPlot.series);
	},

	changeTime: function(){
		this.$('#stat_filter').submit();
	},

	colors: ['#1abc9c', '#c0392b', '#3498db', '#9b59b6', '#34495e', '#f1c40f', '#e67e22', '#8dbdd8'],

	filterStat: function(event){
		event.preventDefault();
		var view 	= this;
		var data 	= $(event.currentTarget).serialize();
		var selector = $(event.currentTarget).find('select').parent();
		var params 	= {
			url: fe_globals.ajaxURL,
			type: 'post',
			data: {
				action: 'et-filter-stat',
				content: data
			},
			beforeSend: function(){
				selector.loader('load');
			},
			success: function(resp){
				//console.log(resp);
				if ( resp.success ){
					//console.log(view.threadPlot.series);
					var data = resp.data;

					// replot data
					view.threadPlot.replot({data: [data.threads, data.replies]});

					view.userPlot.replot({data: [data.users]});
				}
			},
			complete: function(){
				selector.loader('unload');
			}
		};

		$.ajax(params);
	},

	getLinerPLotOption: function(data){
		var maxThreads 	= 0;
		var maxReplies 	= 0;
		var ticks 	= data.length;
		var threads = data[0];
		var replies = data[1];

		_.each(threads, function(element){
			maxThreads = Math.max(maxThreads, element[1]);
		});

		_.each(replies, function(element){
			maxReplies = Math.max(maxReplies, element[1]);
		});

		return {
			seriesColors:  this.colors,
			title: 'Threads statistic',
			grid:{
				shadow:false
			},
			axes: {
				xaxis: {
					renderer: $.jqplot.DateAxisRenderer,
					tickOptions: {
						formatString: '%b&nbsp;%#d'
					},
					//numberTicks: ticks,
					//tickInterval : '1 day'
				},
				yaxis: {
					//label: 'Threads',
					max: maxThreads + 1,
					min: 0,
					tickOptions: {
						formatString: '%d'
					}
				},
				y2axis: {
					max: maxReplies + 1,
					min: 0,
					//label: 'Replies',
					tickOptions:{showGridline:false, formatString: '%d'}
				}
			},
			seriesDefaults: {
				shadow: false,
				markerOptions: {
					shadow: false
				}
			},
			series: [{
				label: 'Threads'
			}, {
				label: 'Replies', 
				yaxis: 'y2axis'
			}],
			legend: { show: true , location: 'nw'},			
			highlighter: {
		        show: true,
				sizeAdjust: 7.5,
				tooltipContentEditor: function(str, seriesIndex, pointIndex, plot){				
					var dateStr = plot.data[seriesIndex][pointIndex][0];
					var d = dateStr.split("-");
					//console.log(d);
					var post_type 	= seriesIndex == 0 ? 'Threads' : 'Replies';
					var text 		= '<div class="stat-tooltip">' + d[1] + ' ' + d[0] + ' : ' + plot.data[seriesIndex][pointIndex][1] + ' ' + post_type + '</div>';
					return  text;
				}
		    },
		    cursor: {
		    	show: false
		    }
		}
	},

	initGraphs: function(){
		// thread & replies statistic
		this.threadPlot = $.jqplot('threads_statistic', [threads, replies], this.getLinerPLotOption([threads, replies]));
		
		// thread pie chart
		this.threadPiePlot = $.jqplot('threads_pie', [percentage], {
			seriesColors:  this.colors,
			title: 'Threads',
			grid:{
				shadow:false
			},			
			seriesDefaults: {
				renderer: $.jqplot.PieRenderer,
				rendererOptions: {
					showDataLabels: true,
					shadow: false,
					color: '#fff'
				}
			},
			legend: {show: true, location: 'e'},
			highlighter: {
			    show: true,
			    formatString:'%s: %d', 
			    tooltipLocation:'sw', 
			    useAxesFormatters:false
				// tooltipContentEditor: function(str, seriesIndex, pointIndex, plot){
				// 	var post_type 	= 'Threads'
				// 	var text 		= plot.data[seriesIndex][pointIndex][0] + ': '  + plot.data[seriesIndex][pointIndex][1] + ' ' + post_type ;
				// 	return  text;
				// }
		    },
		    cursor: {
		    	show: false
		    }
		});


		// registration statistic
		this.userPlot = $.jqplot('users_statistic', [registration], {
			seriesColors: this.colors,
			title: 'Registration statistic',
			grid:{
				shadow:false
			},			
			axes: {
				xaxis: {
					renderer: $.jqplot.DateAxisRenderer,
					tickOptions: {
						formatString: '%b&nbsp;%#d'
					},
					//numberTicks: ticks,
					//tickInterval : '1 day'
				},
				yaxis: {
					//label: 'Threads',
					tickOptions: {
						formatString: '%d'
					}
				},
			},
			highlighter: {
			    show: true,
			    formatString:'%s : %d new user(s)', 			    
		    },			
		});
	}

})

})(jQuery);