var app = angular.module("app",['ui.bootstrap.datetimepicker','ui.dateTimeInput']);

app.run(function($rootScope,$http,$httpParamSerializer){
	moment.tz.add("Asia/Manila|+08 +09|-80 -90|010101010|-1kJI0 AL0 cK10 65X0 mXB0 vX0 VK10 1db0|24e6");
	moment.tz.setDefault("Asia/Manila");

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

	$rootScope.employeesToArray = function(employees){
		var result = [];
		for(var i = 0 ; i<employees.length ; i++){
			result.push({
				string:employees[i].emp_no+" - "+employees[i].last_name+", "+employees[i].first_name+" "+employees[i].middle_name,
				emp_no:employees[i].emp_no
			});
		}
		return result;
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
			$window.location.reload();
		},function(){
			alert("Logout Error");
		});
	}
});
