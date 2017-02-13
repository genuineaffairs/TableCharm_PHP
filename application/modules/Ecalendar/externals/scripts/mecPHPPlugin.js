var mecPHPPlugin = new Class({
	
	Implements: [Options, Events],
	options: {
		cEvents:[],
		url:''
	},
	initialize: function(options){
		this.setOptions(options);
	},
	getEvents: function(that,eventRangeStart,eventRangeEnd){
		
		var thisObj = this;

		new Request.JSON({
			method: 'get',
			url: this.options.url,
			onComplete: function(cevents){
				that.options.cEvents = cevents;
				that.gotEvents = true;
				$('loading').fade('out');
				that.loadCalEvents();
			}
		}).send('startDate=' + eventRangeStart.ymd() + '&endDate=' + eventRangeEnd.ymd());

		
	}
});