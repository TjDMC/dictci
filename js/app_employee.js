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

app.controller('employee_add',function($scope,$rootScope,$window){
    $scope.employee={};
    $scope.add = function(){
        $rootScope.post(
            $rootScope.baseURL+"employee/add/",
            $scope.employee,
            function (response){
                alert("Success: "+response.msg)
                $window.location.reload();
            },
            function(response){
                alert("Error: "+response.msg);
            }
        );
    }
})

app.controller('leave_application',function($scope,$rootScope){
    $scope.employees = [];
    $scope.employee={
        "emp_no":"",
        "last_name":"",
        "first_name":"",
        "middle_name":"",
        "name":""
    };
    $scope.leave={
        start_date:"",
        end_date:""
    };

    $scope.init = function(employees,employee=null){
        $scope.employees = employees;
        if(employee!=null){
            $scope.employee = employee;
            $scope.employee.name = employee.last_name+", "+employee.first_name+" "+employee.middle_name;
        }

    }

    $scope.startDateSet = function () {
        $scope.leave.end_date = $scope.leave.start_date;
    }

    $scope.endDateRender = function($view,$dates){
        if($scope.leave.start_date){
            var activeDate = moment($scope.leave.start_date).subtract(1, $view).add(1, 'minute');

            $dates.filter(function(date){
                return date.localDateValue() <= activeDate.valueOf();
            }).forEach(function(date){
                date.selectable = false;
            });
        }
    }

    $scope.debug = function(){
        console.log($scope.leave);
    }

    $scope.submit = function(){

    }
});
