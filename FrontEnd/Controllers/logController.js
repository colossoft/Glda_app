gildaApp.controller("logCtrl", function($scope, $http, $location, baseUrl) {

	function getPartners() {
		$http.get(baseUrl + '/partners')
			.success(function(data) {
				$scope.partners = data.partners;
			})
			.error(function(data) {
				alert(data.message);
			});
	}

	getPartners();

	$scope.openLogDetails = function(id) {
		$location.path('/log/' + id);
	}

	$scope.banPartner = function(id) {
		if(confirm("Biztos ki szeretné tiltani a partnert?")) {
			$http.put(baseUrl + '/ban/' + id)
				.success(function(data) {
					getPartners();

					alert(data.message);
				})
				.error(function(data) {
					alert(data.message);
				});
		}
	}

	$scope.disengagePartner = function(id) {
		if(confirm("Biztos engedélyezni szeretné a partnert?")) {
			$http.put(baseUrl + '/disengage/' + id)
				.success(function(data) {
					getPartners();

					alert(data.message);
				})
				.error(function(data) {
					alert(data.message);
				});
		}
	}

});