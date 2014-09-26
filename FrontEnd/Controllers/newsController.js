gildaApp.controller("newsCtrl", function($scope, $http, $filter, baseUrl) {

	$scope.accordionStatuses = [{open:true}];

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

	$scope.news = [
		{
			Id: 1, 
			NewsId: 1, 
			Title: 'Első Hír', 
			Text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 
			Date: '2014-09-21'
		}, 
		{
			Id: 2, 
			NewsId: 2, 
			Title: 'Második hosszabb Hír', 
			Text: 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?', 
			Date: '2014-09-19'
		},
		{
			Id: 3, 
			NewsId: 3, 
			Title: 'Harmadik', 
			Text: 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?', 
			Date: '2014-09-15'
		},
		{
			Id: 4, 
			NewsId: 4, 
			Title: 'Utolsó hír', 
			Text: 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga', 
			Date: '2014-09-10'
		}	
	];

	function initNews() {
		$scope.createNews = {
			created_date: null, 
			news: []
		}

		angular.forEach($scope.languages, function(value) {
			$scope.createNews.news.push({
				LanguageId: value.Id, 
				Title: '', 
				Text: ''
			});
		});
	}

	initNews();
	
	$scope.saveNews = function() {
		var hasEmpty = false;

		angular.forEach($scope.createNews.news, function(value) {
			if(value.Text === '' || value.Title === '') {
				hasEmpty = true;
			}
		});

		if(hasEmpty) {
			alert('Nem töltött ki minden nyelvet megfelelően!');
		} else {
			$scope.createNews.created_date = $filter("date")(new Date(), 'yyyy-MM-dd');

			console.log($scope.createNews);

			$http.post(baseUrl + '/createNews', $scope.createNews)
				.then(function(data) {
					console.log(data.data);
					alert(data.data.message);

					initNews();
				});
		}

		// $scope.news.push({
		// 	Id: 5, 
		// 	NewsId: 5, 
		// 	Title: $scope.createNews.news[0].Title, 
		// 	Text: $scope.createNews.news[0].Text, 
		// 	Date: '2014-09-21'
		// });
	}
});