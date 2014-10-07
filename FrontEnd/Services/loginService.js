gildaApp.factory("loginService", function($http, $location, baseUrl, sessionService) {
    
    return {
        login: function (user, scope) {
        	function log_in(user, scope) {
        		var $promise = $http.post(baseUrl + '/login', user);
            
	            $promise.success(function (data) {
					var apiKey = data.api_key;
					var status = data.status;
					var email = data.email;
					var userName = data.last_name + ' ' + data.first_name;
	                
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
	                    scope.loginAlertMessage = "Váratlan hiba történt! Kérjük próbáld újra";
	                    scope.loginAlertShow = true;
	                }
	            })
	            .error(function(data) {
	            	if(angular.isUndefined(data.message)) {
						log_in(user, scope);
					} else {
						console.log("Error login!");
	            		scope.loginAlertMessage = data.message;
	                	scope.loginAlertShow = true;	
					}
	            });
        	}
            
            log_in(user, scope);
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