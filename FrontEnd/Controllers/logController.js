gildaApp.controller("logCtrl", function($scope, $http, $location, baseUrl) {

	function getPartners() {
		$http.get(baseUrl + '/partners')
			.success(function(data) {
				$scope.partners = data.partners;
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					getPartners();
				} else {
					alert(data.message);	
				}
			});
	}

	getPartners();

	$scope.openLogDetails = function(id) {
		$location.path('/log/' + id);
	}

	$scope.banPartner = function(id) {
		function banUser(id) {
			$http.put(baseUrl + '/ban/' + id)
				.success(function(data) {
					getPartners();

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						banUser(id);
					} else {
						alert(data.message);	
					}
				});
		}

		if(confirm("Biztos ki szeretné tiltani a partnert?")) {
			banUser(id);
		}
	}

	$scope.disengagePartner = function(id) {
		function allowUser(id) {
			$http.put(baseUrl + '/disengage/' + id)
				.success(function(data) {
					getPartners();

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						allowUser(id);
					} else {
						alert(data.message);	
					}
				});
		}

		if(confirm("Biztos engedélyezni szeretné a partnert?")) {
			allowUser(id);
		}
	}

});