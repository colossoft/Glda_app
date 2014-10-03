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
gildaApp.constant('frontEndUrl', 'http://localhost:8080/Glda_app/FrontEnd');
//gildaApp.constant('baseUrl', 'http://gildamax.atyin.url.ph/v1');
//gildaApp.constant('frontEndUrl', 'http://gildamax.atyin.url.ph/GildaMaxWeb');

gildaApp.config(function($routeProvider, frontEndUrl) {
    $routeProvider.when('/login', {
        templateUrl: frontEndUrl + '/Partials/login.html', 
        controller: 'loginCtrl'
    });
	
	$routeProvider.when('/home', {
        templateUrl: frontEndUrl + '/Partials/home.html', 
        controller: 'homeCtrl'
    });

    $routeProvider.when('/events/list', {
        templateUrl: frontEndUrl + '/Partials/eventsList.html', 
        controller: 'eventsListCtrl'
    });

    $routeProvider.when('/events/create', {
        templateUrl: frontEndUrl + '/Partials/createEvent.html', 
        controller: 'createEventCtrl'
    });

    $routeProvider.when('/events/:id', {
        templateUrl: frontEndUrl + '/Partials/eventDetails.html', 
        controller: 'eventDetailCtrl'
    });

    $routeProvider.when('/trainings', {
        templateUrl: frontEndUrl + '/Partials/trainings.html', 
        controller: 'trainingsCtrl'
    });

    $routeProvider.when('/trainers', {
        templateUrl: frontEndUrl + '/Partials/trainers.html', 
        controller: 'trainersCtrl'
    });

    $routeProvider.when('/rooms', {
        templateUrl: frontEndUrl + '/Partials/rooms.html', 
        controller: 'roomsCtrl'
    });

    $routeProvider.when('/news', {
        templateUrl: frontEndUrl + '/Partials/news.html', 
        controller: 'newsCtrl'
    });

    $routeProvider.when('/specialOffers', {
        templateUrl: frontEndUrl + '/Partials/specialOffers.html', 
        controller: 'specialOffersCtrl'
    });

    $routeProvider.when('/log', {
        templateUrl: frontEndUrl + '/Partials/log.html', 
        controller: 'logCtrl'
    });

    $routeProvider.when('/log/:id', {
        templateUrl: frontEndUrl + '/Partials/logDetails.html', 
        controller: 'logDetailsCtrl'
    });

    $routeProvider.when('/partner/events', {
        templateUrl: frontEndUrl + '/Partials/partnerEvents.html', 
        controller: 'partnerEventsCtrl'
    });

    $routeProvider.when('/partner/reservations', {
        templateUrl: frontEndUrl + '/Partials/partnerReservations.html', 
        controller: 'partnerReservationsCtrl'
    });

    $routeProvider.when('/changePassword', {
        templateUrl: frontEndUrl + '/Partials/changePassword.html', 
        controller: 'changePasswordCtrl'
    });

    $routeProvider.when('/forgetPassword', {
        templateUrl: frontEndUrl + '/Partials/forgetPassword.html', 
        controller: 'forgetPasswordCtrl'
    });

    $routeProvider.otherwise({
        redirectTo: '/login'
    });
});

gildaApp.run(function($location, $rootScope, loginService) {
	var routesPermission = ['/home', '/events/list'];
	
	$rootScope.$on('$routeChangeStart', function() {
		if(routesPermission.indexOf($location.path()) != -1 && !loginService.isLogged()) {
			$location.path('/login');
		}
		
		if($location.path() == '/login' && loginService.isLogged()) {
			$location.path('/home');
		}
	});
});