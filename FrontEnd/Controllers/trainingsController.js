gildaApp.controller("trainingsCtrl", function($scope, $http, baseUrl) {

	$scope.training = {}

	//Edzés típusok
	function getTrainings() {
		$http.get(baseUrl + '/trainings')
			.success(function(data) {
				$scope.trainings = data.trainings;
			})
			.error(function(data) {
				alert(data.message);
			});
	}

	getTrainings();

	// Edzés törlése
	$scope.deleteTraining = function(trainingId) {
		$http.delete(baseUrl + '/training/' + trainingId)
			.success(function(data) {
				getTrainings();

				alert(data.message);
			})
			.error(function(data) {
				alert(data.message);
			});
	}

	// Edzés mentése
	$scope.saveTraining = function() {
		$http.post(baseUrl + '/training', $scope.training)
			.success(function(data) {
				$scope.training.name = null;
				getTrainings();

				alert(data.message);
			})
			.error(function(data) {
				alert(data.message);
			});
	}
});