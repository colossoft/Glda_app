gildaApp.service("eventListService", function() {

	var date = new Date();
	var room = null;
	var events = null;

	return {
		getDate: function() {
			return date;
		}, 
		setDate: function(value) {
			date = value;
		}, 
		getRoom: function() {
			return room;
		}, 
		setRoom: function(value) {
			room = value;
		}, 
		getEvents: function() {
			return events;
		}, 
		setEvents: function(value) {
			events = value;
		}, 
		reset: function() {
			date = new Date();
			room = null;
			events = null;
		}
	}

});