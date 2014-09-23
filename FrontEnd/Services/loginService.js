gildaApp.factory("loginService", function($http, $location, baseUrl, sessionService) {
    
    return {
        login: function (user, scope) {
            var $promise = $http.post(baseUrl + '/login', user);
            
            $promise.then(function (data) {
				var apiKey = data.data.api_key;
				var status = data.data.status;
				var userName = data.data.last_name + ' ' + data.data.first_name;
                
                if (apiKey) {
                    console.log('Successful login!');
                    
                    sessionService.set('userApiKey', apiKey);
					sessionService.set('userStatus', status);
					//sessionService.set('locationId', user.selectedLocation.Id);
					//sessionService.set('locationName', user.selectedLocation.Name);
					sessionService.set('userName', userName);

					$location.path('/home');
					
					scope.$parent.isLogged = true;
					scope.$parent.userStatus = status;
					scope.$parent.userName = userName;
					//scope.$parent.locationId = user.selectedLocation.Id;
					//cope.$parent.locationName = user.selectedLocation.Name;

                } else {
                    console.log("Error login!");
                    scope.loginAlertShow = true;
					//$location.path('/login');
                }
            });
        }, 
		logout: function(scope) {
			sessionService.destroy('userApiKey');

			scope.isLogged = false;
			scope.userStatus = null;
			scope.$parent.userName = null;
			//scope.$parent.locationId = null;
			//scope.$parent.locationName = null;

			$location.path('/login');
		}, 
		isLogged: function() {
			if(sessionService.get('userApiKey'))
				return true;
		}, 
		getStatus: function() {
			return sessionService.get('userStatus');
		}, 
		getUserName: function() {
			return sessionService.get('userName');
		}
    }

});