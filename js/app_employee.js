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
    $scope.leaves = [];
    $scope.init = function(employee,leaves){
        $scope.employee = employee;
        $scope.leaves = leaves;

        //sort $leaves
        $scope.leaves.sort(function(a,b){
            return moment(b.start_date).diff(moment(a.start_date));
        });


        for(var i = 0 ; i<$scope.leaves.length ; i++){
            //Compute credits equivalence
            var difference = moment($scope.leaves[i].end_date).diff($scope.leaves[i].start_date)/1000;
            var days = Math.floor(difference/86400);
            var hours = Math.floor((difference/86400 - days)*24);
            var minutes = Math.round(((difference/86400 -  days)*24-hours)*60);
            $scope.leaves[i].time = days+" : "+hours+" : "+minutes;
            $scope.leaves[i].credits = days*1.25+hours*0.125+minutes*0.002

            //format leaves
            $scope.leaves[i].start_date = moment($scope.leaves[i].start_date).format("MMMM DD, YYYY - hh:mm a");
            $scope.leaves[i].end_date = moment($scope.leaves[i].end_date).format("MMMM DD, YYYY - hh:mm a");
        }

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

app.controller('leave_application',function($scope,$rootScope,$window){
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

        var data = angular.copy($scope.leave);
        data.emp_no = $scope.employee.emp_no;
        data.start_date = moment(data.start_date,'MMMM DD, YYYY - hh:mm a').format("YYYY/MM/DD HH:mm");
        data.end_date = moment(data.end_date,'MMMM DD, YYYY - hh:mm a').format("YYYY/MM/DD HH:mm");
        console.log(data);

        $rootScope.post(
            $rootScope.baseURL+"/employee/leaveApplication",
            data,
            function(response){
                alert("Success: "+response.msg);
                $window.location.reload();
            },
            function(response){
                alert("Error: "+response.msg);
            }
        );
    }
});
