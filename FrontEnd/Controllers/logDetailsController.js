gildaApp.controller("logDetailsCtrl", function($scope, $routeParams, $http, $location, baseUrl) {
	
	$scope.$on("$routeChangeSuccess", function() {
		$scope.partnerId = $routeParams["id"];

		$http.get(baseUrl + '/log/' + $scope.partnerId)
			.success(function(data) {
				$scope.userDetails = data.result.userDetails;
				$scope.logs = data.result.logs;
			})
			.error(function(data) {
				alert(data.message);
			});
	});

	$scope.backToLogPartners = function() {
		$location.path('/log');
	}

});