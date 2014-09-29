gildaApp.controller("roomsCtrl", function($scope, $http, baseUrl, locationService) {

	$scope.room = {}

	// Termek
	function getRooms() {
		$http.get(baseUrl + '/rooms/' + locationService.getLocationId())
			.success(function(data) {
				$scope.rooms = data.rooms;
			})
			.error(function(data) {
				alert(data.message);
			});
	}

	getRooms();
	
	// Terem törlése
	$scope.deleteRoom = function(roomId) {
		$http.delete(baseUrl + '/rooms/' + roomId)
			.success(function(data) {
				getRooms();

				alert(data.message);
			})
			.error(function(data) {
				alert(data.message);
			});
	}

	// Edzés mentése
	$scope.saveRoom = function() {
		$scope.room.locationId = locationService.getLocationId();

		$http.post(baseUrl + '/rooms', $scope.room)
			.success(function(data) {
				$scope.room.name = null;
				getRooms();

				alert(data.message);
			})
			.error(function(data) {
				alert(data.message);
			});
	}
});