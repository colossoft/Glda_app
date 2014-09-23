gildaApp.controller("createEventCtrl", function($scope) {

	// Termek
	$scope.eventRooms = [
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

	// Datepicker beállítások
	$scope.openDatePicker = function($event) {
		$event.preventDefault();
		$event.stopPropagation();

		$scope.datePickerOpened = true;
	};

	$scope.datePickerOptions = {
		startingDay: 1
	}

	// Edzők nevei
	$scope.trainers = [
		{
			Id: 1, 
			Name: "Kiss Pista"
		}, 
		{
			Id: 1, 
			Name: "Nagy Laci"
		},
		{
			Id: 1, 
			Name: "Kovács Béla"
		},
		{
			Id: 1, 
			Name: "Lakatos József"
		}
	];

	//Edzés típusok
	$scope.trainings = [
		{
			Id: 1, 
			Name: "Aerobic"
		}, 
		{
			Id: 1, 
			Name: "Pilates"
		},
		{
			Id: 1, 
			Name: "Crossfit"
		},
		{
			Id: 1, 
			Name: "Spinning"
		}
	];

	// Esemény mentése
	$scope.saveEvent = function() {
		// TODO
	}
});