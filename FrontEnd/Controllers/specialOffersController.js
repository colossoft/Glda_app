gildaApp.controller("specialOffersCtrl", function($scope, $filter, $http, baseUrl) {

	$scope.accordionStatuses = [{open:true}];

	// Datepicker beállítások
	$scope.datePickerOpened = [];

	$scope.openDatePicker = function($event, index) {
		$event.preventDefault();
		$event.stopPropagation();

		$scope.datePickerOpened = [false, false];
		$scope.datePickerOpened[index] = true;
	};

	$scope.datePickerOptions = {
		startingDay: 1
	}

	// Nyelvek
	function getLanguages() {
		$http.get(baseUrl + '/languages')
			.success(function(data) {
				$scope.languages = data.languages;

				initSpecialOffers();
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					getLanguages();
				} else {
					alert(data.message);	
				}
			});
	}
	
	getLanguages();

	// Visszakapott akciók magyarul
	function getAllSpecialOffers() {
		$http.get(baseUrl + '/devaluations/' + '1')
			.success(function(data) {
				$scope.specialOffers = data.specialOffers;
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					getAllSpecialOffers();
				} else {
					alert(data.message);	
				}
			});
	}

	getAllSpecialOffers();


	function initSpecialOffers() {
		$scope.createSpecialOffers = {
			startDate: null, 
			endDate: null, 
			offers: []
		}

		angular.forEach($scope.languages, function(value) {
			$scope.createSpecialOffers.offers.push({
				LanguageId: value.id, 
				Title: '', 
				Text: ''
			});
		});
	}

	function delSpecOffer(id) {
		$http.delete(baseUrl + '/devaluation/' + id)
			.success(function(data) {
				getAllSpecialOffers();

				alert(data.message);
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					delSpecOffer(id);
				} else {
					alert(data.message);	
				}
			});
	}

	$scope.deleteSpecialOffer = function(id) {
		if(confirm("Biztos törölni akarod ezt az akciót?")) {
			delSpecOffer(id);
		}
	}

	$scope.saveSpecialOffer = function() {
		console.log($scope.createSpecialOffers);

		var hasEmpty = false;

		angular.forEach($scope.createSpecialOffers.offers, function(value) {
			if(value.Text === '' || value.Title === '') {
				hasEmpty = true;
			}
		});

		if(angular.isUndefined($scope.createSpecialOffers.startDate) || $scope.createSpecialOffers.startDate === null
			 || angular.isUndefined($scope.createSpecialOffers.endDate) || $scope.createSpecialOffers.endDate === null) {
			hasEmpty = true;
		}

		function sSpecOffer() {
			$http.post(baseUrl + '/createDevaluation', $scope.createSpecialOffers)
				.success(function(data) {
					initSpecialOffers();
					getAllSpecialOffers();

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						sSpecOffer();
					} else {
						alert(data.message);	
					}
				});
		}

		if(hasEmpty) {
			alert('Nem töltött ki minden nyelvet vagy dátumot megfelelően!');
		} else {
			$scope.createSpecialOffers.startDate = $filter('date')($scope.createSpecialOffers.startDate, 'yyyy-MM-dd');
			$scope.createSpecialOffers.endDate = $filter('date')($scope.createSpecialOffers.endDate, 'yyyy-MM-dd');

			sSpecOffer();
		}
	}

});