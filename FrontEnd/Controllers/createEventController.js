gildaApp.controller("createEventCtrl", function($scope, $http, $filter, baseUrl, locationService) {

	// Datepicker beállítások
	$scope.openDatePicker = function($event) {
		$event.preventDefault();
		$event.stopPropagation();

		$scope.datePickerOpened = true;
	};

	$scope.datePickerOptions = {
		startingDay: 1
	}

	$scope.startTime = new Date();
	$scope.endTime = new Date();

	// Termek
	function getRooms() {
		$http.get(baseUrl + '/rooms/' + locationService.getLocationId())
			.success(function(data) {
				$scope.eventRooms = data.rooms;
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

	// Edzők nevei
	function getTrainers() {
		$http.get(baseUrl + '/trainers')
			.success(function(data) {
				console.log(data);
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
	
	//Edzés típusok
	function getTrainings() {
		$http.get(baseUrl + '/trainings')
			.success(function(data) {
				console.log(data);
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
	

	// Esemény mentése
	$scope.saveEvent = function() {
		var newEvent = {
			roomId: $scope.eventRoom, 
			date: $filter('date')($scope.eventDate, 'yyyy-MM-dd'), 
			startTime: $filter('date')($scope.startTime, 'HH:mm'), 
			endTime: $filter('date')($scope.endTime, 'HH:mm'), 
			trainerId: $scope.selectedTrainer.id, 
			trainingId: $scope.selectedTraining.id, 
			spots: $scope.spots
		}

		console.log(newEvent);

		function sEvent(newEvent) {
			$http.post(baseUrl + '/event', newEvent)
				.success(function(data) {
					console.log(data);

					$scope.eventRoom = null;
					$scope.selectedTrainer = null;
					$scope.selectedTraining = null;
					$scope.spots = null;

					alert(data.message);			
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						sEvent(newEvent);
					} else {
						alert(data.message);	
					}
				});
		}

		sEvent(newEvent);
	}
});