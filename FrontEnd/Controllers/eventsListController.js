gildaApp.controller("eventsListCtrl", function($scope, $rootScope, $location, eventListService) {

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
	$scope.eventListRooms = [
		{
			Id: 1, 
			Name: "Nagyterem"
		}, 
		{
			Id: 2, 
			Name: "Kisterem"
		}, 
		{
			Id: 3, 
			Name: "Boxterem"
		}
	];

	$scope.eventListRoom = eventListService.getRoom();

	$scope.$watch('eventListRoom', function() {
		console.log("Room:");
		console.log($scope.eventListRoom);
	});

	// Alert beállítások
	$scope.closeEventsAlertShow = function() {
		$scope.eventsAlertShow = false;
	}


	// Események tábla tesztadatai
	var testEvents = [
		{
			Id: 1, 
			StartTime: '07:00', 
			EndTime: '09:00', 
			TrainerName: 'Kiss Pista', 
			TrainingName: 'Pilates', 
			Spots: 30, 
			ReservedSpots: 5, 
			FreeSpots: 25
		}, 
		{
			Id: 2, 
			StartTime: '10:00', 
			EndTime: '01:00', 
			TrainerName: 'Nagy Mónika', 
			TrainingName: 'Aerobic', 
			Spots: 20, 
			ReservedSpots: 5, 
			FreeSpots: 15
		}, 
		{
			Id: 3, 
			StartTime: '12:00', 
			EndTime: '14:00', 
			TrainerName: 'Muja Zzolt', 
			TrainingName: 'Box', 
			Spots: 12, 
			ReservedSpots: 1, 
			FreeSpots: 11
		}, 
		{
			Id: 4, 
			StartTime: '18:00', 
			EndTime: '20:00', 
			TrainerName: 'Torna András', 
			TrainingName: 'Spinning', 
			Spots: 45, 
			ReservedSpots: 12, 
			FreeSpots: 33
		}
	];

	
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
			$scope.events = testEvents;

			eventListService.setRoom(room);
			eventListService.setDate(date);
			eventListService.setEvents(testEvents);
		}
	}

	$scope.events = eventListService.getEvents();
	//$scope.getEventsList($scope.eventListRoom, $scope.eventListDate);

	// Esemény részleteinek megnyitása
	$scope.openEventDetail = function(index) {
		$location.path('/events/' + $scope.events[index].Id);
	}
});