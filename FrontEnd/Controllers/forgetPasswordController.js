gildaApp.controller("forgetPasswordCtrl", function($scope, $location, $http, baseUrl) {
	$scope.f = {
		email: null
	}

	$scope.getNewPassword = function() {
		$http.post(baseUrl + '/getnewpassword', $scope.f)
			.success(function(data) {
				$location.path('/login');

				alert(data.message);
			})
			.error(function(data) {
				alert(data.message);
			});
	}

	$scope.backToLogin = function() {
		$location.path('/login');
	}

});