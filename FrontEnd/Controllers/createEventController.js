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
	$http.get(baseUrl + '/rooms/' + locationService.getLocationId())
		.success(function(data) {
			$scope.eventRooms = data.rooms;
		})
		.error(function(data) {
			alert(data.message);
		});

	// Edzők nevei
	$http.get(baseUrl + '/trainers')
		.success(function(data) {
			console.log(data);
			$scope.trainers = data.trainers;
		})
		.error(function(data) {
			alert(data.message);
		});

	//Edzés típusok
	$http.get(baseUrl + '/trainings')
		.success(function(data) {
			console.log(data);
			$scope.trainings = data.trainings;
		})
		.error(function() {
			alert(data.message);
		});

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
				alert(data.message);
			});
	}
});