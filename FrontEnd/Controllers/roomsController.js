gildaApp.controller("roomsCtrl", function($scope) {

	$scope.room = {}

	// Termek
	$scope.rooms = [
		{
			Id: 1, 
			Name: "Nagyterem"
		}, 
		{
			Id: 2, 
			Name: "Kisterem"
		}, 
		{
			Id: 3, 
			Name: "Boxterem"
		}
	];
	
	// Terem törlése
	$scope.deleteRoom = function(index) {
		$scope.rooms.splice(index, 1);
	}

	// Edzés mentése
	$scope.saveRoom = function() {
		$scope.rooms.push({Id: 1, Name: $scope.room.roomName});
		alert("Terem hozzáadása sikeres!");
		$scope.room.roomName = null;
	}
});