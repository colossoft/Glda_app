gildaApp.factory("locationService", function(sessionService) {
	return {
		setLocationId: function(id) {
			sessionService.set("locationId", id);
		}, 
		setLocationName: function(name) {
			sessionService.set("locationName", name);
		}, 
		getLocationId: function() {
			return sessionService.get('locationId');
		}, 
		getLocationName: function() {
			return sessionService.get('locationName');
		}
	}
});