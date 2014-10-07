gildaApp.controller("partnerReservationsCtrl", function($scope, $http, baseUrl) {

	function getReservations() {
		$http.get(baseUrl + '/reservation')
			.success(function(data) {
				$scope.reservations = data.reservations;
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					getReservations();
				} else {
					alert(data.message);	
				}
			});
	}

	getReservations();

	$scope.deleteReservation = function(id) {
		function delRes(id) {
			$http.delete(baseUrl + '/reservation/' + id)
				.success(function(data) {
					getReservations();
					
					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						delRes(id);
					} else {
						alert(data.message);	
					}
				});
		}

		if(confirm("Biztos szeretnéd lemondani a foglalást?")) {
			delRes(id);
		}
	}

});