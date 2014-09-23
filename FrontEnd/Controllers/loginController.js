gildaApp.controller("loginCtrl", function($scope, loginService) {

	$scope.loginAlertShow = false;

    $scope.closeLoginAlertShow = function() {
        $scope.loginAlertShow = false;
    }

    $scope.login = function(user) {
    	$scope.loginAlertShow = false;
        loginService.login(user, $scope);
    };

    $scope.register = function(regUser) {
    	// TODO
    }

});