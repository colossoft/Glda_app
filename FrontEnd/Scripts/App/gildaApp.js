var gildaApp = angular.module('gildaApp', ['ngRoute', 'ui.bootstrap'], function($httpProvider) {
	// Use x-www-form-urlencoded Content-Type
	$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

	var param = function(obj) {
	var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

	for(name in obj) {
		value = obj[name];

		if(value instanceof Array) {
			for(i=0; i<value.length; ++i) {
				subValue = value[i];
				fullSubName = name + '[' + i + ']';
				innerObj = {};
				innerObj[fullSubName] = subValue;
				query += param(innerObj) + '&';
			}
		}
		else if(value instanceof Object) {
			for(subName in value) {
				subValue = value[subName];
				fullSubName = name + '[' + subName + ']';
				innerObj = {};
				innerObj[fullSubName] = subValue;
				query += param(innerObj) + '&';
			}
		}
		else if(value !== undefined && value !== null)
			query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
		}

		return query.length ? query.substr(0, query.length - 1) : query;
	};

	// Override $http service's default transformRequest
	$httpProvider.defaults.transformRequest = [function(data) {
		return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
	}];
});

gildaApp.constant('baseUrl', 'http://localhost:8080/Glda_app/BackEnd/v1');
//gildaApp.constant('baseUrl', 'http://gildamax.atyin.url.ph/v1');

gildaApp.config(function($routeProvider) {
    $routeProvider.when('/login', {
        templateUrl: '/Glda_app/FrontEnd/Partials/login.html', 
        controller: 'loginCtrl'
    });
	
	$routeProvider.when('/home', {
        templateUrl: '/Glda_app/FrontEnd/Partials/home.html', 
        controller: 'homeCtrl'
    });

    $routeProvider.when('/events/list', {
        templateUrl: '/Glda_app/FrontEnd/Partials/eventsList.html', 
        controller: 'eventsListCtrl'
    });

    $routeProvider.when('/events/create', {
        templateUrl: '/Glda_app/FrontEnd/Partials/createEvent.html', 
        controller: 'createEventCtrl'
    });

    $routeProvider.when('/events/:id', {
        templateUrl: '/Glda_app/FrontEnd/Partials/eventDetails.html', 
        controller: 'eventDetailCtrl'
    });

    $routeProvider.when('/trainings', {
        templateUrl: '/Glda_app/FrontEnd/Partials/trainings.html', 
        controller: 'trainingsCtrl'
    });

    $routeProvider.when('/trainers', {
        templateUrl: '/Glda_app/FrontEnd/Partials/trainers.html', 
        controller: 'trainersCtrl'
    });

    $routeProvider.when('/rooms', {
        templateUrl: '/Glda_app/FrontEnd/Partials/rooms.html', 
        controller: 'roomsCtrl'
    });

    $routeProvider.when('/news', {
        templateUrl: '/Glda_app/FrontEnd/Partials/news.html', 
        controller: 'newsCtrl'
    });

    $routeProvider.when('/specialOffers', {
        templateUrl: '/Glda_app/FrontEnd/Partials/specialOffers.html', 
        controller: 'specialOffersCtrl'
    });

    $routeProvider.when('/log', {
        templateUrl: '/Glda_app/FrontEnd/Partials/log.html', 
        controller: 'logCtrl'
    });

    $routeProvider.when('/log/:id', {
        templateUrl: '/Glda_app/FrontEnd/Partials/logDetails.html', 
        controller: 'logDetailsCtrl'
    });

    $routeProvider.when('/partner/events', {
        templateUrl: '/Glda_app/FrontEnd/Partials/partnerEvents.html', 
        controller: 'partnerEventsCtrl'
    });

    $routeProvider.when('/partner/reservations', {
        templateUrl: '/Glda_app/FrontEnd/Partials/partnerReservations.html', 
        controller: 'partnerReservationsCtrl'
    });

    $routeProvider.when('/changePassword', {
        templateUrl: '/Glda_app/FrontEnd/Partials/changePassword.html', 
        controller: 'changePasswordCtrl'
    });

    $routeProvider.otherwise({
        redirectTo: '/login'
    });
});

gildaApp.run(function($location, $rootScope, loginService) {
	var routesPermission = ['/home'];
	
	$rootScope.$on('$routeChangeStart', function() {
		if(routesPermission.indexOf($location.path()) != -1 && !loginService.isLogged()) {
			$location.path('/login');
		}
		
		if($location.path() == '/login' && loginService.isLogged()) {
			$location.path('/home');
		}
	});
});