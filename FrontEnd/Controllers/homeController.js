gildaApp.controller("homeCtrl", function($scope, $http, baseUrl, locationService) {

	$http.get(baseUrl + '/locations')
		.success(function(data) {
			$scope.gildaLocations = data.locations;

			if(locationService.getLocationId() === null) {
				$scope.selectedLocation = $scope.gildaLocations[0];
			} else {
				angular.forEach($scope.gildaLocations, function(value) {
					if(value.id == locationService.getLocationId()) {
						$scope.selectedLocation = value;
					}
				});
			}		
		})
		.error(function(data) {
			alert(data.message);
		});
	
	$scope.$watch('selectedLocation', function() {
		console.log("SelectedLocation watch!!!");
		
		if(!angular.isUndefined($scope.selectedLocation)) {
			locationService.setLocationId($scope.selectedLocation.id);
			locationService.setLocationName($scope.selectedLocation.name);

			$scope.$parent.locationName = $scope.selectedLocation.name;
		}
	});
	
	
});