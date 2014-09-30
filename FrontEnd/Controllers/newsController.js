gildaApp.controller("newsCtrl", function($scope, $http, $filter, baseUrl) {

	$scope.accordionStatuses = [{open:true}];

	$http.get(baseUrl + '/languages')
		.success(function(data) {
			$scope.languages = data.languages;

			initNews();
		})
		.error(function(data) {
			alert(data.message);
		});

	function getAllNews() {
		$http.get(baseUrl + '/news/' + '1')
			.success(function(data) {
				$scope.news = data.news;
			})
			.error(function(data) {
				alert(data.message);
			});
	}

	getAllNews();

	function initNews() {
		$scope.createNews = {
			created_date: null, 
			news: []
		}

		angular.forEach($scope.languages, function(value) {
			$scope.createNews.news.push({
				LanguageId: value.id, 
				Title: '', 
				Text: ''
			});
		});
	}

	$scope.deleteNews = function(id) {
		if(confirm("Biztos törölni akarod ezt a hírt?")) {
			$http.delete(baseUrl + '/news/' + id)
				.success(function(data) {
					getAllNews();

					alert(data.message);
				})
				.error(function(data) {
					alert(data.message);
				});
		}
	}
	
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
				.success(function(data) {
					initNews();
					getAllNews();

					alert(data.data.message);
				})
				.error(function(data) {
					alert(data.data.message);	
				});
		}
	}
});