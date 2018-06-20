app.controller('employee_nav',function($scope,$rootScope){
    $scope.employees = [];
    $scope.init=function(employees){
        $scope.employees=employees;
    }
});

app.controller('employee_search',function($scope,$rootScope){

});

app.controller('employee_display',function($scope,$rootScope){
    $scope.employee = {};
    $scope.init = function(employee){
        $scope.employee = employee;
    }
});

app.controller('leave_application',function($scope,$rootScope){

    $scope.init = function(employees,employeeNo=0){

    }
});
