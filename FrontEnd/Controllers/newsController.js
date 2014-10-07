gildaApp.controller("newsCtrl", function($scope, $http, $filter, baseUrl) {

	$scope.accordionStatuses = [{open:true}];

	function getLanguages() {
		$http.get(baseUrl + '/languages')
			.success(function(data) {
				$scope.languages = data.languages;

				initNews();
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					getLanguages();
				} else {
					alert(data.message);	
				}
			});
	}

	getLanguages();
	

	function getAllNews() {
		$http.get(baseUrl + '/news/' + '1')
			.success(function(data) {
				$scope.news = data.news;
			})
			.error(function(data) {
				if(angular.isUndefined(data.message)) {
					getAllNews();
				} else {
					alert(data.message);	
				}
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
		function delNews(id) {
			$http.delete(baseUrl + '/news/' + id)
				.success(function(data) {
					getAllNews();

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						delNews(id);
					} else {
						alert(data.message);	
					}
				});
		}

		if(confirm("Biztos törölni akarod ezt a hírt?")) {
			delNews(id);
		}
	}
	
	$scope.saveNews = function() {
		var hasEmpty = false;

		angular.forEach($scope.createNews.news, function(value) {
			if(value.Text === '' || value.Title === '') {
				hasEmpty = true;
			}
		});

		function sNews() {
			$http.post(baseUrl + '/createNews', $scope.createNews)
				.success(function(data) {
					initNews();
					getAllNews();

					alert(data.message);
				})
				.error(function(data) {
					if(angular.isUndefined(data.message)) {
						sNews();
					} else {
						alert(data.message);	
					}	
				});
		}

		if(hasEmpty) {
			alert('Nem töltött ki minden nyelvet megfelelően!');
		} else {
			$scope.createNews.created_date = $filter("date")(new Date(), 'yyyy-MM-dd');

			console.log($scope.createNews);

			sNews();
		}
	}
});