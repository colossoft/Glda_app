gildaApp.controller("mainCtrl", function($scope, $location, $http, loginService, locationService, eventListService) {
	
	$http.defaults.headers.post.Authorization = sessionService.get('userApiKey');

	$scope.gildaLocations = [
        {
            Id: 1, 
            Name: "Allee"
        }, 
        {
            Id: 2, 
            Name: "Óbuda Gate"
        }, 
        {
            Id: 3, 
            Name: "River Estates"
        }, 
        {
            Id: 4, 
            Name: "Hermina Towers"
        }, 
        {
            Id: 5, 
            Name: "Flórián"
        }, 
        {
            Id: 6, 
            Name: "Savoya Park"
        }
    ];

	$scope.isLogged = loginService.isLogged();
	$scope.userStatus = loginService.getStatus();
	$scope.locationId = locationService.getLocationId();
	$scope.locationName = locationService.getLocationName();
	$scope.userName = loginService.getUserName();
	
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