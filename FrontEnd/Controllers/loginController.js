gildaApp.controller("loginCtrl", function($scope, $http, baseUrl, loginService) {

	$scope.loginAlertShow = false;
    $scope.regUser = {}
    $scope.actives = {}
    $scope.actives.loginTabActive = true;

    $scope.closeLoginAlertShow = function() {
        $scope.loginAlertShow = false;
    }

    $scope.closeRegAlertShow = function() {
        $scope.regAlertShow = false;
    }

    $scope.login = function(user) {
    	$scope.loginAlertShow = false;
        loginService.login(user, $scope);
    };

    $scope.register = function(regUser) {
        $scope.regAlertShow = false;

    	$http.post(baseUrl + '/register', $scope.regUser)
            .success(function(data) {
                $scope.regUser = {}
                $scope.actives.loginTabActive = true;

                alert(data.message + ' Lépj be a megadott adatokkal!');
            })
            .error(function(data) {
                $scope.regAlertShow = true;
                $scope.regAlertMessage = data.message;
            });
    }

});