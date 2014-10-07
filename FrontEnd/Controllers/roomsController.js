gildaApp.controller("roomsCtrl", function($scope, $http, baseUrl, locationService) {

	$scope.room = {}

	// Termek
	function getRooms() {
		$http.get(baseUrl + '/rooms/' + locationService.getLocationId())
			.success(function(data) {
				$scope.rooms = data.rooms;
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					getRooms();
				} else {
					alert(data.message);	
				}
			});
	}

	getRooms();
	
	// Terem törlése
	$scope.deleteRoom = function(roomId) {
		function delRoom(roomId) {
			$http.delete(baseUrl + '/rooms/' + roomId)
				.success(function(data) {
					getRooms();

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						delRoom(roomId);
					} else {
						alert(data.message);	
					}
				});
		}
		
		delRoom(roomId);
	}

	// Edzés mentése
	$scope.saveRoom = function() {
		$scope.room.locationId = locationService.getLocationId();

		function sRoom() {
			$http.post(baseUrl + '/rooms', $scope.room)
				.success(function(data) {
					$scope.room.name = null;
					getRooms();

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						sRoom();
					} else {
						alert(data.message);	
					}
				});
		}

		sRoom();
	}
});