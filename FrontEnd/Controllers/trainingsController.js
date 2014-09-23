gildaApp.controller("trainingsCtrl", function($scope) {

	$scope.training = {}

	//Edzés típusok
	$scope.trainings = [
		{
			Id: 1, 
			Name: "Aerobic"
		}, 
		{
			Id: 1, 
			Name: "Pilates"
		},
		{
			Id: 1, 
			Name: "Crossfit"
		},
		{
			Id: 1, 
			Name: "Spinning"
		}
	];

	// Edzés törlése
	$scope.deleteTraining = function(index) {
		$scope.trainings.splice(index, 1);
	}

	// Edzés mentése
	$scope.saveTraining = function() {
		$scope.trainings.push({Id: 1, Name: $scope.training.trainingName});
		alert("Edzéstípus hozzáadása sikeres!");
		$scope.training.trainingName = null;
	}
});