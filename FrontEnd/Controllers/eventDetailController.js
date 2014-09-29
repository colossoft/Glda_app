gildaApp.controller("eventDetailCtrl", function($scope, $routeParams, $location, $http, baseUrl) {

	$scope.$on("$routeChangeSuccess", function() {
		$scope.eventId = $routeParams["id"];

		// Esemény foglalásai
		$http.get(baseUrl + '/event/' + $scope.eventId)
			.success(function(data) {
				$scope.eventDetails = data.eventDetails;
			})
			.error(function(data) {
				alert(data.message);
			});
	});

	// Vissza gomb
	$scope.backToEventsList = function() {
		$location.path("/events/list");
	}
});