gildaApp.factory("loginService", function($http, $location, baseUrl, sessionService) {
    
    return {
        login: function (user, scope) {
            var $promise = $http.post(baseUrl + '/login', user);
            
            $promise.then(function (data) {
				var apiKey = data.data.api_key;
				var status = data.data.status;
				var email = data.data.email;
				var userName = data.data.last_name + ' ' + data.data.first_name;
                
                if (apiKey) {
                    console.log('Successful login!');
                    
                    sessionService.set('userApiKey', apiKey);
					sessionService.set('userStatus', status);
					sessionService.set('userEmail', email);
					sessionService.set('userName', userName);

					scope.$parent.isLogged = true;
					scope.$parent.userStatus = status;
					scope.$parent.userName = userName;
					scope.$parent.userEmail = email;

					$http.defaults.headers.common.Authorization = sessionService.get('userApiKey');

					$location.path('/home');
                } else {
                    console.log("Error login!");
                    scope.loginAlertShow = true;
                }
            });
        }, 
		logout: function(scope) {
			sessionService.destroy('userApiKey');
			sessionService.destroy('userStatus');
			sessionService.destroy('userEmail');
			sessionService.destroy('userName');
			sessionService.destroy('locationId');
			sessionService.destroy('locationName');

			scope.isLogged = false;

			$location.path('/login');
		}, 
		setAuthorization: function() {	
			$http.defaults.headers.common.Authorization = sessionService.get('userApiKey');
		}, 
		isLogged: function() {
			if(sessionService.get('userApiKey'))
				return true;
		}, 
		getStatus: function() {
			return sessionService.get('userStatus');
		}, 
		getEmail: function() {
			return sessionService.get('userEmail');
		}, 
		getUserName: function() {
			return sessionService.get('userName');
		}
    }
});