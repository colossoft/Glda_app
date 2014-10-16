gildaApp.controller("eventBookCtrl", function($scope, $routeParams, $location, $http, baseUrl) {

	$scope.$on("$routeChangeSuccess", function() {
		$scope.eventId = $routeParams["id"];

		// Esemény foglalásai
		function getEventDetails() {
			$http.get(baseUrl + '/event/book/' + $scope.eventId)
				.success(function(data) {
					$scope.eventDetails = data.eventDetails;
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						getEventDetails();
					} else {
						alert(data.message);	
					}
				});	
		}

		getEventDetails();
	});

	// Vissza gomb
	$scope.backToEventsList = function() {
		$location.path("/events/list");
	}

});