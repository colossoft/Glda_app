gildaApp.controller("changePasswordCtrl", function($scope, $http, baseUrl) {
	$scope.cp = {}

	function cPass(cp) {
		$http.put(baseUrl + '/passmodify', cp)
			.success(function(data) {
				$scope.cp = {}

				alert(data.message);
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					cPass(cp);
				} else {
					alert(data.message);	
				}
			});
	}

	$scope.changePassword = function(cp) {
		cPass(cp);
	}

});