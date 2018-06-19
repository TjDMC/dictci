var app = angular.module("app",[]);

app.run(function($rootScope,$http,$httpParamSerializer){
	$rootScope.busy = false;
	$rootScope.post = function(url,inputData,onSuccess,onFailure){

		var data = {
			'data':inputData,
			[$rootScope.csrf.tokenName]:$rootScope.csrf.hash
		};
		data = $httpParamSerializer(data);

		success = function(response) {
			var responseData = {};
			responseData = response.data;
			if(!responseData || !('success' in responseData) || !('msg' in responseData)){
				alert('ERROR Something is missing');
				return;
			}
			if(responseData.success){
				onSuccess(responseData);
			}else{
				onFailure(responseData);
			}
		}

		error = function(response){
			alert('ERROR Something went wrong');
			$rootScope.busy = false;
		}

		$http({
			method: 'POST',
			url: url,
			data: data,
			headers : {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(success,error);

	}
});

app.controller('initializer',function($scope,$rootScope){
	$scope.init = function(baseURL,csrfTokenName,csrfHash){
		$rootScope.baseURL = baseURL;
		$rootScope.csrf = {};
		$rootScope.csrf.tokenName = csrfTokenName;
		$rootScope.csrf.hash = csrfHash;
	}
});

app.controller('login',function($scope,$rootScope,$http,$window){

	$scope.login = function(){
		$rootScope.post(
			$rootScope.baseURL+"main/login",
			{
				'username':$scope.username,
				'password':$scope.password
			},
			function(response){
				alert("Successfully logged in");
				$window.location.reload();
			},
			function(response){
				alert("Login Failed: "+response.msg);
			}
		);
	}

	$scope.logout = function(){
		$http({
			method: 'GET',
			url: $rootScope.baseURL+"main/logout"
		}).then(function(){
			alert("Successfully logged out.");
			$window.location.reload();
		},function(){
			alert("Logout Error");
		});
	}
});
