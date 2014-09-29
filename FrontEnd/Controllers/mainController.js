gildaApp.controller("mainCtrl", function($scope, $location, $http, sessionService, loginService, locationService, eventListService) {

	// Set authorization
	loginService.setAuthorization();

	$scope.isLogged = loginService.isLogged();
	$scope.userName = loginService.getUserName();
	$scope.userStatus = loginService.getStatus();
	$scope.userEmail = loginService.getEmail();

	$scope.locationName = locationService.getLocationName();

	$scope.logout = function() {
		loginService.logout($scope);
	}

	$scope.eventsList = function() {
		eventListService.reset();
		
		$location.path('/events/list');
	}

	$scope.createEvent = function() {
		$location.path('/events/create');
	}

	$scope.trainings = function() {
		$location.path('/trainings');	
	}
	
	$scope.trainers = function() {
		$location.path('/trainers');	
	}

	$scope.rooms = function() {
		$location.path('/rooms');	
	}

	$scope.news = function() {
		$location.path('/news');
	}

	$scope.specialOffers = function() {
		$location.path('/specialOffers');
	}
});