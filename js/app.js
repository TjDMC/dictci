var app = angular.module("app",['ui.bootstrap.datetimepicker','ui.dateTimeInput']);

app.run(function($rootScope,$http,$httpParamSerializer){
	moment.tz.add("Asia/Manila|+08 +09|-80 -90|010101010|-1kJI0 AL0 cK10 65X0 mXB0 vX0 VK10 1db0|24e6");
	moment.tz.setDefault("Asia/Manila");

	$rootScope.dateFormat = 'MMMM DD, YYYY';
	$rootScope.busy = false;

	$rootScope.customModalData = {
		content:{
			header:'',
			body:'',
			confirmName:'',
			closeName:''
		},
		action:{
			confirm:function(){},
			close:function(){}
		}
	}

	$rootScope.showCustomModal = function(header,body,onConfirm,onClose,confirmName='Confirm',closeName='Close'){
		$rootScope.customModalData={
			content:{
				header:header,
				body:body,
				confirmName:confirmName,
				closeName:closeName
			},
			action:{
				confirm:onConfirm,
				close:onClose
			}
		}
		angular.element('#customModal').modal('show');
	}
	$rootScope.post = function(url,inputData,onSuccess,onFailure){

		var data = {
			'data':inputData,
			[$rootScope.csrf.tokenName]:$rootScope.csrf.hash
		};
		data = $httpParamSerializer(data);

		success = function(response) {
			var responseData = {};
			responseData = response.data;
			if(!responseData || typeof responseData !== 'object' || !('success' in responseData) || !('msg' in responseData)){
				alert(typeof responseData === 'object'?'PHP error':'ERROR Something is missing');
				console.log(responseData);
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
				emp_no:employees[i].emp_no,
				emp_name:employees[i].last_name+", "+employees[i].first_name+" "+employees[i].middle_name,
				string:employees[i].emp_no+" - "+employees[i].last_name+", "+employees[i].first_name+" "+employees[i].middle_name
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
				$rootScope.showCustomModal('Error','Login failed. Please make sure you input the correct login credentials.',function(){},function(){});
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

app.controller('admin',function($scope,$rootScope,$window){
	$scope.credential = '';
	$scope.username = '';
	$scope.newPassword1 = '';
	$scope.newPassword2 = '';
	$scope.password = '';

	$scope.confirmPassword = function(credential){
		if(credential == 'password'&&$scope.newPassword1!=$scope.newPassword2){
			$rootScope.showCustomModal(
				'Error',
				'Passwords do not match',
				function(){},
				function(){},
				'OK'
			);
			return;
		}
		angular.element('#confirmPasswordModal').modal('show');
		$scope.credential = credential;
	}

	$scope.submit = function(){
		$rootScope.post(
			$rootScope.baseURL+'admin/changeLoginCredentials/'+$scope.credential,
			{
				password:$scope.password,
				username:$scope.username,
				new_password1:$scope.newPassword1,
				new_password2:$scope.newPassword2
			},
			function(response){
				$rootScope.showCustomModal('Success',response.msg,
					function(){
						$window.location.reload();
					},
					function(){
						$window.location.reload();
					}
				);
			},
			function(response){
				$rootScope.showCustomModal('Error',response.msg,function(){},function(){});
			}
		);
	}
});

app.filter('employeeSearch', function() {
	return function(arr,field,query) {
		if (!query) {
			return arr;
		}
		var results = [];
		query = query.toLowerCase();
		angular.forEach(arr, function(item) {
			if (item[field].toLowerCase().indexOf(query) !== -1) {
				results.push(item);
			}
		});
		return results;
	};
});
