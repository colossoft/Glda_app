gildaApp.controller("trainingsCtrl", function($scope, $http, baseUrl) {

	$scope.training = {}

	//Edzés típusok
	$http.get(baseUrl + '/trainings')
		.success(function(data) {
			$scope.trainings = data.trainings;
		})
		.error(function(data) {
			alert(data.message);
		});

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