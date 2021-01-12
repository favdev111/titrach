(function($, undefined) {
	$.fn.servicescal = function(params){
		var container = $('#'+params['id']);
		tools.showOverlay(container);
		var options = {
			editable: false,	
			events: params['feed_url'],
			eventDrop: function(event, delta) {
				alert(event.title + ' was moved ' + delta + ' days\n' +
					'(should probably update your database)');
			},
			loading: function(bool) {
				if (bool) tools.showOverlay(container);
				else tools.hideOverlay(container);
			},
			eventRender: function (event, element) {
				element.find('span.fc-event-title').html(element.find('span.fc-event-title').text());           
			}			
		}
		
		$(container).fullCalendar(options);
	}
})(jQuery)