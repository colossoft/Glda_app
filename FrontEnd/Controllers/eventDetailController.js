gildaApp.controller("eventDetailCtrl", function($scope, $routeParams, $location) {

	$scope.$on("$routeChangeSuccess", function() {
		$scope.eventId = $routeParams["id"];
	});

	// Esemény foglalásai tesztobjektum
	$scope.testEventDetails = {
		event: {
			Id: 1, 
			StartTime: '07:00', 
			EndTime: '09:00', 
			TrainerName: 'Kiss Pista', 
			TrainingName: 'Pilates', 
			Spots: 30, 
			ReservedSpots: 5, 
			FreeSpots: 25
		}, 
		reservations: [
			{
				date: "2014-09-03", 
				firstName: "Ákos", 
				lastName: "Átyin", 
				email: "atyins@gmail.com"
			}, 
			{
				date: "2014-09-05", 
				firstName: "Laci", 
				lastName: "Maci", 
				email: "maci@gmail.com"
			}, 
			{
				date: "2014-09-10", 
				firstName: "Mekk", 
				lastName: "Elek", 
				email: "elek@gmail.com"
			}, 
			{
				date: "2014-08-30", 
				firstName: "Péter", 
				lastName: "Tóth", 
				email: "peti@gmail.com"
			}
		]
	}

	// Vissza gomb
	$scope.backToEventsList = function() {
		$location.path("/events/list");
	}
});