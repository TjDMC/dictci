app.controller('employee_nav',function($scope,$rootScope){
    $scope.employees = [];
    $scope.init=function(employees){
        $scope.employees=employees;
    }
});

app.controller('employee_search',function($scope,$rootScope){

});

app.controller('employee_display',function($scope,$rootScope,$sce){
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
            var difference = moment($scope.leaves[i].end_date).diff($scope.leaves[i].start_date,'days') + 1;
            $scope.leaves[i].time = difference;
            $scope.leaves[i].credits = difference;
            switch($scope.leaves[i].type){
                case 'Vacation':
                    $scope.leaves[i].deducted =  $sce.trustAsHtml("<span style='color:green'>"+$scope.leaves[i].credits+"</span> : <span style='color:red'>0</span>");
                    break;
                case 'Sick':
                    $scope.leaves[i].deducted = $sce.trustAsHtml("<span style='color:green'>0</span> : <span style='color:red'>"+$scope.leaves[i].credits+"</span>");
                    break;
                default:
                    $scope.leaves[i].deducted =  $sce.trustAsHtml("<span style='color:green'>0</span> : <span style='color:red'>0</span>");
            }

            //format leaves
            $scope.leaves[i].start_date = moment($scope.leaves[i].start_date).format("MMMM DD, YYYY");
            $scope.leaves[i].end_date = moment($scope.leaves[i].end_date).format("MMMM DD, YYYY");
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

    $scope.computeDays = function(){
        if($scope.leave.start_date==''){
            return 0;
        }else{
            return (moment($scope.leave.end_date).diff($scope.leave.start_date))/86400000+1;
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
        data.start_date = moment(data.start_date,'MMMM DD, YYYY').format("YYYY/MM/DD");
        data.end_date = moment(data.end_date,'MMMM DD, YYYY').format("YYYY/MM/DD");
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

    $scope.autocomplete = function(){
		// Script taken from: https://www.w3schools.com/howto/howto_js_autocomplete.asp

		inp = document.getElementById("empNo");
		var currFocus;
		inp.addEventListener("input", function(e){
			var divList, item, val=this.value;
			closeList();
			if(!val){return false;}
			currFocus = -1;
			divList = document.createElement("DIV");
			divList.setAttribute("id", this.id + "autocomplete-list");
			divList.setAttribute("class", "autocomplete-items");
			this.parentNode.appendChild(divList);
			for(var i=0; i<$scope.employees.length; i++){
				if($scope.employees[i].emp_no.toLowerCase().includes(val.toLowerCase())){
					item = document.createElement("DIV");
					item.innerHTML = $scope.employees[i].emp_no;
					item.innerHTML += "<input type='hidden' value='"+$scope.employees[i].emp_no+"'>";
					item.addEventListener("click", function(e){
						inp.value = this.getElementsByTagName("input")[0].value;
						closeList();
					});
					divList.appendChild(item);
				}
			}
		});

		inp.addEventListener("keydown", function(e){
			var x = document.getElementById(this.id + "autocomplete-list");
			if(x) x = x.getElementsByTagName("div");
			if(x==null || x.length==0) return;
			if(e.keyCode == 40){
				currFocus++;
				addActive(x);
			}else if(e.keyCode == 38){
				currFocus--;
				addActive(x);
			}else if(e.keyCode == 13){
				e.preventDefault();
				if(currFocus>-1){ if(x) x[currFocus].click();}
			}
		});

		function addActive(elem){
			if(!elem) return false;
			remActive(elem);
			console.log(elem.length)
			if(currFocus>=elem.length) currFocus=0;
			if(currFocus<0) currFocus=(elem.length-1);
			console.log(currFocus);
			if(isNaN(currFocus)) return;
			elem[currFocus].classList.add("autocom-active");
			console.log("should work");
		}

		function remActive(elem){
			for(var i=0; i<elem.length;i++){
				elem[i].classList.remove("autocom-active");
			}
		}

		function closeList(elem){
			var remove = document.getElementsByClassName("autocomplete-items");
			for(var i=0; i<remove.length; i++){
				if(elem!=remove[i] && elem!=inp){
					remove[i].parentNode.removeChild(remove[i]);
				}
			}
		}

		document.addEventListener("click", function(e){
			closeList();
		});
	}

	$scope.fillName = function(){
		number = document.getElementById("empNo");
		var i;
		for(i=0; i<$scope.employees.length;i++){
			if(number.value==$scope.employees[i].emp_no) break;
			if(i==($scope.employees.length-1)) return;
		}
		for(var key in $scope.employees[i]){
			if($scope.employees[i].hasOwnProperty(key)){
				$scope.employee[key] = $scope.employees[i][key];
			}
		}
		$scope.employee.name = $scope.employee.last_name+", "+$scope.employee.first_name+" "+$scope.employee.middle_name;
	}
});
