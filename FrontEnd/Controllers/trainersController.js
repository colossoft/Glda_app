gildaApp.controller("trainersCtrl", function($scope, $http, baseUrl) {

	$scope.trainer = {}

	function getTrainers() {
		$http.get(baseUrl + '/trainers')
			.success(function(data) {
				$scope.trainers = data.trainers;
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					getTrainers();
				} else {
					alert(data.message);	
				}
			});
	}

	getTrainers();

	// Edző törlése
	function delTrainer(trainerId) {
		$http.delete(baseUrl + '/trainer/' + trainerId)
			.success(function(data) {
				getTrainers();

				alert(data.message);
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					delTrainer(trainerId);
				} else {
					alert(data.message);	
				}
			});
	}

	$scope.deleteTrainer = function(trainerId) {
		delTrainer(trainerId);
	}

	// Edző mentése
	$scope.saveTrainer = function() {
		function sTrainer() {
			$http.post(baseUrl + '/trainer', $scope.trainer)
				.success(function(data) {
					$scope.trainer.firstName = null;
					$scope.trainer.lastName = null;
					$scope.trainer.email = null;
					
					getTrainers();

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						sTrainer();
					} else {
						alert(data.message);	
					}
				});
		}

		sTrainer();
	}
	
});