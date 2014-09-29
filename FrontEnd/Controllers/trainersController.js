gildaApp.controller("trainersCtrl", function($scope, $http, baseUrl) {

	$scope.trainer = {}

	function getTrainers() {
		$http.get(baseUrl + '/trainers')
			.success(function(data) {
				$scope.trainers = data.trainers;
			})
			.error(function(data) {
				alert(data.message);
			});
	}

	getTrainers();

	// Edző törlése
	$scope.deleteTrainer = function(trainerId) {
		$http.delete(baseUrl + '/trainer/' + trainerId)
			.success(function(data) {
				getTrainers();

				alert(data.message);
			})
			.error(function(data) {
				alert(data.message);
			});
	}

	// Edző mentése
	$scope.saveTrainer = function() {
		$http.post(baseUrl + '/trainer', $scope.trainer)
			.success(function(data) {
				$scope.trainer.firstName = null;
				$scope.trainer.lastName = null;
				$scope.trainer.email = null;
				
				getTrainers();

				alert(data.message);
			})
			.error(function(data) {
				alert(data.message);
			});
	}
	
});