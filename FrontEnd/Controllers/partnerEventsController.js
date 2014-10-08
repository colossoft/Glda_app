gildaApp.controller("partnerEventsCtrl", function($scope, $http, $filter, baseUrl, locationService) {

	// Datepicker beállítások
	$scope.datePickerOpened = [false, false];
	$scope.eventListStartDate = new Date();
	$scope.eventListEndDate = new Date();

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

	// Terem lista beállítások
	function getRooms() {
		$http.get(baseUrl + '/rooms/' + locationService.getLocationId())
			.success(function(data) {
				$scope.eventListRooms = data.rooms;
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

	$scope.getEventsList = function(room, startDate, endDate) {
		$scope.eventsAlertShow = false;
		$scope.events = null;

		function getEvents(room, fStartDate, fEndDate) {
			$http.get(baseUrl + '/events/' + room + '/' + fStartDate + '/' + fEndDate)
				.success(function(data) {
					if(data.events.length === 0) {
						$scope.eventsListAlertMessage = "A megadott teremben ebben az időintervallumban nem lesznek edzések!";
						$scope.eventsAlertShow = true;
					}

					$scope.events = data.events;
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						getEvents(room, fStartDate, fEndDate);
					} else {
						$scope.eventsListAlertMessage = data.message;
						$scope.eventsAlertShow = true;	
					}
				});
		}

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

			getEvents(room, fStartDate, fEndDate);
		}
	}

	$scope.makeReservation = function(id) {
		function mRes(id) {
			$http.post(baseUrl + '/reservation', { event_id: id })
				.success(function(data) {
					$scope.getEventsList($scope.eventListRoom, $scope.eventListStartDate, $scope.eventListEndDate);

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						mRes(id);
					} else {
						alert(data.message);	
					}
				});
		}

		if(confirm("Biztos szeretnél helyet foglalni erre az edzésre?")) {
			mRes(id);
		}
	}

	$scope.deleteReservation = function(id) {
		function dRes(id) {
			$http.delete(baseUrl + '/reservation/' + id)
				.success(function(data) {
					$scope.getEventsList($scope.eventListRoom, $scope.eventListStartDate, $scope.eventListEndDate);

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						dRes(id);
					} else {
						alert(data.message);	
					}
				});
		}

		if(confirm("Biztos szeretnéd lemondani a foglalást?")) {
			dRes(id);
		}
	}
});