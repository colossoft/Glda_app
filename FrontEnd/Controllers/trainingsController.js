gildaApp.controller("trainingsCtrl", function($scope, $http, baseUrl) {

	$scope.training = {}

	//Edzés típusok
	function getTrainings() {
		$http.get(baseUrl + '/trainings')
			.success(function(data) {
				$scope.trainings = data.trainings;
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					getTrainings();
				} else {
					alert(data.message);	
				}
			});
	}

	getTrainings();

	// Edzés törlése
	function delTraining(trainingId) {
		$http.delete(baseUrl + '/training/' + trainingId)
			.success(function(data) {
				getTrainings();

				alert(data.message);
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					delTraining(trainingId);
				} else {
					alert(data.message);	
				}
			});
	}

	$scope.deleteTraining = function(trainingId) {
		delTraining(trainingId);
	}

	// Edzés mentése
	$scope.saveTraining = function() {
		function sTraining() {
			$http.post(baseUrl + '/training', $scope.training)
				.success(function(data) {
					$scope.training.name = null;
					getTrainings();

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						sTraining();
					} else {
						alert(data.message);	
					}
				});
		}

		sTraining();
	}
});