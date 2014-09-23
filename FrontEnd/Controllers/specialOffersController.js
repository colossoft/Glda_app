gildaApp.controller("specialOffersCtrl", function($scope, $filter) {

	$scope.accordionStatuses = [{open:true}];

	// Datepicker beállítások
	$scope.datePickerOpened = [];

	$scope.openDatePicker = function($event, index) {
		$event.preventDefault();
		$event.stopPropagation();

		$scope.datePickerOpened = [false, false];
		$scope.datePickerOpened[index] = true;
	};

	$scope.datePickerOptions = {
		startingDay: 1
	}

	// Nyelvek
	$scope.languages = [
		{
			Id: 1, 
			Name: 'Magyar'
		}, 
		{
			Id: 2, 
			Name: 'Angol'
		}, 
		{
			Id: 3, 
			Name: 'Német'
		}
	];

	// Visszakapott akciók magyarul
	$scope.specialOffers = [
		{
			Id: 1, 
			SpecialOfferId: 1, 
			Title: 'Első Akció', 
			Text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 
			StartDate: '2014-09-21', 
			EndDate: '2014-10-01'
		}, 
		{
			Id: 2, 
			SpecialOfferId: 2, 
			Title: 'Második hosszabb Akció', 
			Text: 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?', 
			StartDate: '2014-09-25', 
			EndDate: '2014-10-15'
		},
		{
			Id: 3, 
			SpecialOfferId: 3, 
			Title: 'Harmadik', 
			Text: 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 
			StartDate: '2014-09-15', 
			EndDate: '2014-10-06'
		},
		{
			Id: 4, 
			SpecialOfferId: 4, 
			Title: 'Utolsó akció', 
			Text: 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga', 
			StartDate: '2014-09-10', 
			EndDate: '2014-09-30'
		}	
	];

	$scope.createSpecialOffers = {
		StartDate: null, 
		EndDate: null, 
		Offers: []
	}

	angular.forEach($scope.languages, function(value) {
		$scope.createSpecialOffers.Offers.push({
			LanguageId: value.Id, 
			Title: '', 
			Text: ''
		});
	});

	$scope.saveSpecialOffer = function() {
		console.log($scope.createSpecialOffers);

		$scope.specialOffers.push({
			Id: 5, 
			SpecialOfferId: 5, 
			Title: $scope.createSpecialOffers.Offers[0].Title, 
			Text: $scope.createSpecialOffers.Offers[0].Text, 
			StartDate: $filter('date')($scope.createSpecialOffers.StartDate, 'yyyy-MM-dd'), 
			EndDate: $filter('date')($scope.createSpecialOffers.EndDate, 'yyyy-MM-dd')
		});
	}

});