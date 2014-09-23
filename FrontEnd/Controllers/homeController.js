gildaApp.controller("homeCtrl", function($scope, locationService) {

	console.log(locationService.getLocationId());

	if(locationService.getLocationId() === null) {
		$scope.selectedLocation = $scope.$parent.gildaLocations[0];
	} else {
		angular.forEach($scope.$parent.gildaLocations, function(value) {
			if(value.Id == locationService.getLocationId()) {
				$scope.selectedLocation = value;
			}
		});
	}
	
	
	$scope.$watch('selectedLocation', function() {
		console.log("SelectedLocation watch!!!");
		
		$scope.$parent.locationId = $scope.selectedLocation.Id;
		$scope.$parent.locationName = $scope.selectedLocation.Name;

		locationService.setLocationId($scope.selectedLocation.Id);
		locationService.setLocationName($scope.selectedLocation.Name);
	});
	
	
});