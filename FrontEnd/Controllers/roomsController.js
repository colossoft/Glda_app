gildaApp.controller("roomsCtrl", function($scope, $http, baseUrl, locationService) {

	$scope.room = {}

	// Termek
	$http.get(baseUrl + '/rooms/' + locationService.getLocationId())
		.success(function(data) {
			$scope.rooms = data.rooms;
		})
		.error(function(data) {
			alert(data.message);
		});
	
	// Terem törlése
	$scope.deleteRoom = function(index) {
		$scope.rooms.splice(index, 1);
	}

	// Edzés mentése
	$scope.saveRoom = function() {
		$scope.rooms.push({id: 1, name: $scope.room.roomName});
		alert("Terem hozzáadása sikeres!");
		$scope.room.roomName = null;
	}
});