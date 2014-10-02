gildaApp.controller("changePasswordCtrl", function($scope, $http, baseUrl) {
	$scope.cp = {}

	$scope.changePassword = function(cp) {
		$http.put(baseUrl + '/passmodify', cp)
			.success(function(data) {
				$scope.cp = {}

				alert(data.message);
			})
			.error(function(data) {
				alert(data.message);
			});
	}

});