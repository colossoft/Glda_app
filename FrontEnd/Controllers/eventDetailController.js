gildaApp.controller("eventDetailCtrl", function($scope, $routeParams, $location, $http, baseUrl) {

	// Esemény foglalásai
	function getEventDetails() {
		$http.get(baseUrl + '/event/' + $scope.eventId)
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

	$scope.$on("$routeChangeSuccess", function() {
		$scope.eventId = $routeParams["id"];

		getEventDetails();
	});

	// Vissza gomb
	$scope.backToEventsList = function() {
		$location.path("/events/list");
	}

	// Foglalás törlése
	function delRes(resId, eventId, clientId, clientType) {
		$http.delete(baseUrl + '/reservation/user/' + resId + '/' + eventId + '/' + clientId + '/' + clientType)
			.success(function(data) {
				getEventDetails();

				alert(data.message);
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					delRes(resId, eventId, clientId, clientType);
				} else {
					alert(data.message);	
				}
			});
	}

	$scope.deleteReservation = function(reservationId, clientId, clientType) {
		if($scope.eventDetails.event.resDelete) {
			if(confirm('Valóban törölni akarod a foglalást?')) {
				delRes(reservationId, $scope.eventId, clientId, clientType);
			}
		}
	}
});