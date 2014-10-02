gildaApp.controller("partnerReservationsCtrl", function($scope, $http, baseUrl) {

	function getReservations() {
		$http.get(baseUrl + '/reservation')
			.success(function(data) {
				$scope.reservations = data.reservations;
			})
			.error(function(data) {
				alert(data.message);
			});
	}

	getReservations();

	$scope.deleteReservation = function(id) {
		if(confirm("Biztos szeretnéd lemondani a foglalást?")) {
			$http.delete(baseUrl + '/reservation/' + id)
				.success(function(data) {
					getReservations();
					
					alert(data.message);
				})
				.error(function(data) {
					alert(data.message);
				});
		}
	}

});