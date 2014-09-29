gildaApp.controller("eventsListCtrl", function($scope, $rootScope, $location, $http, $filter, baseUrl, eventListService, locationService) {

	// Datepicker beállítások
	$scope.openDatePicker = function($event) {
		$event.preventDefault();
		$event.stopPropagation();

		$scope.datePickerOpened = true;
	};

	$scope.datePickerOptions = {
		startingDay: 1
	}

	$scope.$watch('eventListDate', function() {
		console.log("Date:");
		console.log($scope.eventListDate);
	});

	$scope.eventListDate = eventListService.getDate();

	// Terem lista beállítások
	$http.get(baseUrl + '/rooms/' + locationService.getLocationId())
		.success(function(data) {
			$scope.eventListRooms = data.rooms;
			$scope.eventListRoom = eventListService.getRoom();
		})
		.error(function(data) {
			alert(data.message);
		});

	$scope.$watch('eventListRoom', function() {
		console.log("Room:");
		console.log($scope.eventListRoom);
	});

	// Alert beállítások
	$scope.closeEventsAlertShow = function() {
		$scope.eventsAlertShow = false;
	}
	
	// Események lekérése
	$scope.getEventsList = function(room, date) {
		$scope.eventsAlertShow = false;
		$scope.events = null;

		if(room == null) {
			$scope.eventsListAlertMessage = "Válassz ki egy termet!";
			$scope.eventsAlertShow = true;
		}
		else if(angular.isUndefined(date)) {
			$scope.eventsListAlertMessage = "Helytelen dátumformátum!";
			$scope.eventsAlertShow = true;
		}
		else {
			var fDate = $filter('date')(date, 'yyyy-MM-dd');

			$http.get(baseUrl + '/events/' + room + '/' + fDate)
				.success(function(data) {
					if(data.events.length === 0) {
						$scope.eventsListAlertMessage = "A megadott teremben ebben az időpontban nem lesznek edzések!";
						$scope.eventsAlertShow = true;
					}

					$scope.events = data.events;

					eventListService.setRoom(room);
					eventListService.setDate(date);
					eventListService.setEvents($scope.events);
				})
				.error(function(data) {
					$scope.eventsListAlertMessage = data.message;
					$scope.eventsAlertShow = true;
				});
		}
	}

	$scope.events = eventListService.getEvents();

	// Esemény részleteinek megnyitása
	$scope.openEventDetail = function(index) {
		$location.path('/events/' + $scope.events[index].id);
	}
});