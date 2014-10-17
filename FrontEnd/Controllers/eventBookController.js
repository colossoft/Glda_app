gildaApp.controller("eventBookCtrl", function($scope, $routeParams, $location, $http, baseUrl) {

	$scope.$on("$routeChangeSuccess", function() {
		$scope.eventId = $routeParams["id"];

		getEventDetails();
	});

	// Esemény adatai
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

	// Vissza gomb
	$scope.backToEventsList = function() {
		$location.path("/events/list");
	}

	// Foglalás partner számára
	$scope.partnerComment = null;

	$scope.bookForPartner = function(sPartner, pComment) {
		console.log(sPartner);
		console.log(pComment);

		var postData = {
			event_id: $scope.eventId, 
			partner_id: sPartner.id, 
			comment: pComment
		}

		$http.post(baseUrl + '/partnerbook', postData)
			.success(function(data) {
				getEventDetails();

				alert(data.message);
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					$scope.bookForPartner(sPartner, pComment);
				} else {
					alert(data.message);
				}
			});
	}

	// Foglalás nem regisztrált ügyfél számára
	$scope.customer = {
		selected: null, 
		details: null, 
		comment: null
	}

	$scope.$watch('customer.selected', function() {
		console.log($scope.customer.selected);

		if(angular.isObject($scope.customer.selected)) {
			$scope.customer.details = $scope.customer.selected.details
		}
	});

	function bookForNewCustomer(customer) {
		console.log("Foglalás indul...");
		
		var postData = {
			eventId: $scope.eventId, 
			customerName: customer.selected, 
			customerDetails: customer.details, 
			comment: customer.comment
		}

		$http.post(baseUrl + '/customerbook/new', postData)
			.success(function(data) {
				getEventDetails();

				alert(data.message);
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					bookForNewCustomer(customer);
				} else {
					alert(data.message);
				}
			});
	}

	function checkExistingCustomer(customer) {
		$http.get(baseUrl + '/checkcustomer/' + customer.selected)
			.success(function(data) {
				if(data.error) {
					if(confirm(data.message)) {
						bookForNewCustomer(customer);
					}
				} else {
					bookForNewCustomer(customer);
				}
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					checkExistingCustomer(customer.selected);
				} else {
					alert(data.message);
				}
			});
	}

	function bookForCustomer(customer) {
		var postData = {
			eventId: $scope.eventId, 
			customerId: customer.selected.id, 
			customerDetails: customer.details, 
			comment: customer.comment
		}

		$http.post(baseUrl + '/customerbook/existing', postData)
			.success(function(data) {
				getEventDetails();

				alert(data.message);
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					bookForCustomer(customer);
				} else {
					alert(data.message);
				}
			});
	}

	$scope.bookForCustomer = function(customer) {
		console.log(customer);

		if(angular.isObject(customer.selected)) {
			bookForCustomer(customer);
		} else if(angular.isString(customer.selected)) {
			checkExistingCustomer(customer);
		}
	}
});