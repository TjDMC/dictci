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
		$rootScope.busy = true;
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
			$rootScope.busy = false;
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

	/*Omnibus minute-credit equivalence*/
	$rootScope.minuteCreditTable = [0,0.002,0.004,0.006,0.008,0.010,0.012,0.015,0.017,0.019,0.021,0.023,0.025,0.027,0.029,0.031,0.033,0.035,0.037,0.040,0.042,0.044,0.046,0.048,0.050,0.052,0.054,0.056,0.058,0.060,0.062,0.065,0.067,0.069,0.071,0.073,0.075,0.077,0.079,0.081,0.083,0.085,0.087,0.090,0.092,0.094,0.096,0.098,0.100,0.102,0.104,0.106,0.108,0.110,0.112,0.115,0.117,0.119,0.121,0.123,0.125];
	$rootScope.creditsToMinutes = function(x){
		var y = x%0.125;
		var closest = 0.125;
		var minute = 60;
		for(var i = 0 ; i<$rootScope.minuteCreditTable.length ; i++){
			if(Math.abs(y-$rootScope.minuteCreditTable[i])<Math.abs(y-closest)){
				closest = $rootScope.minuteCreditTable[i];
				minute = i;
			}
		}
		return minute;
	};
	$rootScope.minutesToCredits = function(x){
		return $rootScope.minuteCreditTable[x%60]+parseInt(x/60)*0.125;
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
				$rootScope.showCustomModal('Error','Login failed. Please make sure you input the correct login credentials.',
				function(){
					angular.element('#customModal').modal('hide');
				},function(){});
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

app.controller('init_db',function($http,$scope,$rootScope,$window){
	$scope.initialize = function(){
		$http({
			method: 'GET',
			url: $rootScope.baseURL+'db/initdb'
		}).then(function(response){
			$rootScope.showCustomModal(
				'Success',
				'Database initialized Successfully',
				function(){
					$window.location.reload();
				},
				function(){
					$window.location.reload();
				}
			);
		},function(response){
			$rootScope.showCustomModal(
				'Error',
				response.msg,
				function(){
				},
				function(){
				}
			);
		});
	}

	$scope.populate = function(){
		$http({
			method: 'GET',
			url:  $rootScope.baseURL+'db/populate',
		}).then(function(response){
			$rootScope.showCustomModal(
				'Success',
				'Database populated Successfully',
				function(){
					$window.location.reload();
				},
				function(){
					$window.location.reload();
				}
			);
		},function(response){
			$rootScope.showCustomModal(
				'Error',
				response.msg,
				function(){
				},
				function(){
				}
			);
		});
	}
});
