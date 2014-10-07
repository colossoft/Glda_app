gildaApp.controller("partnerEventsCtrl", function($scope, $http, $filter, baseUrl, locationService) {

	// Datepicker beállítások
	$scope.openDatePicker = function($event) {
		$event.preventDefault();
		$event.stopPropagation();

		$scope.datePickerOpened = true;
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

	$scope.getEventsList = function(room, date) {
		$scope.eventsAlertShow = false;
		$scope.events = null;

		function getEvents(room, fDate) {
			$http.get(baseUrl + '/events/' + room + '/' + fDate)
				.success(function(data) {
					if(data.events.length === 0) {
						$scope.eventsListAlertMessage = "A megadott teremben ebben az időpontban nem lesznek edzések!";
						$scope.eventsAlertShow = true;
					}

					$scope.events = data.events;
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						getEvents(room, fDate);
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
		else if(angular.isUndefined(date)) {
			$scope.eventsListAlertMessage = "Helytelen dátumformátum!";
			$scope.eventsAlertShow = true;
		}
		else {
			var fDate = $filter('date')(date, 'yyyy-MM-dd');

			getEvents(room, fDate);
		}
	}

	$scope.makeReservation = function(id) {
		function mRes(id) {
			$http.post(baseUrl + '/reservation', { event_id: id })
				.success(function(data) {
					$scope.getEventsList($scope.eventListRoom, $scope.eventListDate);

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
					$scope.getEventsList($scope.eventListRoom, $scope.eventListDate);

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