gildaApp.controller("trainersCtrl", function($scope) {

	$scope.trainer = {}

	$scope.trainers = [
		{
			Id: 1, 
			FirstName: "Ákos", 
			LastName: "Átyin", 
			Email: "atyins@gmail.com"
		}, 
		{
			Id: 1, 
			FirstName: "Péter", 
			LastName: "Tóth", 
			Email: "peter@gmail.com"
		}, 
		{
			Id: 1, 
			FirstName: "Józsi", 
			LastName: "Kiss", 
			Email: "jozsi@gmail.com"
		}
	];

	// Edző törlése
	$scope.deleteTrainer = function(index) {
		$scope.trainers.splice(index, 1);
	}

	// Edző mentése
	$scope.saveTrainer = function() {
		$scope.trainers.push(
			{
				Id: 1, 
				FirstName: $scope.trainer.firstName, 
				LastName: $scope.trainer.lastName, 
				Email: $scope.trainer.email
			}
		);
		alert("Edző hozzáadása sikeres!");
		$scope.trainer.firstName = null;
		$scope.trainer.lastName = null;
		$scope.trainer.email = null;
	}
	
});