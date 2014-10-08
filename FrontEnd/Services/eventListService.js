gildaApp.service("eventListService", function() {

	var startDate = new Date();
	var endDate = new Date();
	var room = null;
	var events = null;
	var eventListFilter = null;

	return {
		getStartDate: function() {
			return startDate;
		}, 
		setStartDate: function(value) {
			startDate = value;
		}, 
		getEndDate: function() {
			return endDate;
		}, 
		setEndDate: function(value) {
			endDate = value;
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
		getEventListFilter: function() {
			return eventListFilter;
		}, 
		setEventListFilter: function(value) {
			eventListFilter = value;
		}, 
		reset: function() {
			startDate = new Date();
			endDate = new Date();
			room = null;
			events = null;
			eventListFilter = null;
		}
	}

});