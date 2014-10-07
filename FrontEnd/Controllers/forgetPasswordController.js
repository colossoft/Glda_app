gildaApp.controller("forgetPasswordCtrl", function($scope, $location, $http, baseUrl) {
	$scope.f = {
		email: null
	}

	function getNPass() {
		$http.post(baseUrl + '/getnewpassword', $scope.f)
			.success(function(data) {
				$location.path('/login');

				alert(data.message);
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					getNPass();
				} else {
					alert(data.message);	
				}
			});
	}

	$scope.getNewPassword = function() {
		getNPass();
	}

	$scope.backToLogin = function() {
		$location.path('/login');
	}

});