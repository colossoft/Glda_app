gildaApp.controller("eventsListCtrl", function($scope, $rootScope, $location, $http, $filter, baseUrl, eventListService, locationService) {

	// Datepicker beállítások
	$scope.datePickerOpened = [false, false];

	$scope.openDatePicker = function($event, index) {
		$event.preventDefault();
		$event.stopPropagation();

		if(index == 0)
			$scope.datePickerOpened = [true, false];
		
		if(index == 1)
			$scope.datePickerOpened = [false, true];	
	};

	$scope.datePickerOptions = {
		startingDay: 1
	}

	$scope.eventListStartDate = eventListService.getStartDate();
	$scope.eventListEndDate = eventListService.getEndDate();

	// Terem lista beállítások
	function getRooms() {
		$http.get(baseUrl + '/rooms/' + locationService.getLocationId())
			.success(function(data) {
				$scope.eventListRooms = data.rooms;
				$scope.eventListRoom = eventListService.getRoom();
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

	// Alert beállítások
	$scope.closeEventsAlertShow = function() {
		$scope.eventsAlertShow = false;
	}
	
	// Események lekérése
	$scope.getEventsList = function(room, startDate, endDate) {
		$scope.eventsAlertShow = false;
		$scope.events = null;

		if(room == null) {
			$scope.eventsListAlertMessage = "Válassz ki egy termet!";
			$scope.eventsAlertShow = true;
		}
		else if(angular.isUndefined(startDate)) {
			$scope.eventsListAlertMessage = "Helytelen kezdő dátum formátum!";
			$scope.eventsAlertShow = true;
		}
		else if(angular.isUndefined(endDate)) {
			$scope.eventsListAlertMessage = "Helytelen végdátum formátum!";
			$scope.eventsAlertShow = true;
		}
		else {
			var fStartDate = $filter('date')(startDate, 'yyyy-MM-dd');
			var fEndDate = $filter('date')(endDate, 'yyyy-MM-dd');

			function getEvents(room, fStartDate, fEndDate, startDate, endDate) {
				$http.get(baseUrl + '/events/' + room + '/' + fStartDate + '/' + fEndDate)
					.success(function(data) {
						if(data.events.length === 0) {
							$scope.eventsListAlertMessage = "A megadott teremben ebben az időintervallumban nem lesznek edzések!";
							$scope.eventsAlertShow = true;
						}

						$scope.events = data.events;

						eventListService.setRoom(room);
						eventListService.setStartDate(startDate);
						eventListService.setEndDate(endDate);
						eventListService.setEvents($scope.events);
					})
					.error(function(data) {
						if(angular.isUndefined(data.message)) {
							getEvents(room, fStartDate, fEndDate, startDate, endDate);
						} else {
							$scope.eventsListAlertMessage = data.message;
							$scope.eventsAlertShow = true;	
						}
						
					});
			}
			
			getEvents(room, fStartDate, fEndDate, startDate, endDate);
		}
	}

	$scope.events = eventListService.getEvents();
	$scope.eventListFilter = eventListService.getEventListFilter();

	// Esemény részleteinek megnyitása
	$scope.openEventDetail = function(index) {
		eventListService.setEventListFilter($scope.eventListFilter);
		$location.path('/events/' + $scope.events[index].id);
	}
});