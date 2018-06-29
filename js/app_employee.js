app.controller('employee_nav',function($scope,$rootScope){
    $scope.employees = [];
	$scope.limit = 10;
    $scope.page = 1;
    $scope.filteredEmployees = [];
    $scope.init=function(employees){
        $scope.employees=employees;
    }

	$scope.getDisplayNumber = function(){
        return $scope.filteredEmployees.length - $scope.getBegin() > $scope.limit ? $scope.limit:$scope.filteredEmployees.length-$scope.getBegin();
    }

    $scope.getBegin = function(){
        return ($scope.page-1)*$scope.limit;
    }

    $scope.numberToArray= function(num) {
        return new Array(Math.ceil(num));
    }

    $scope.getMaxPage = function(){
        console.log($scope.filteredEmployees.length+" "+$scope.limit);
        var result = $scope.numberToArray($scope.filteredEmployees.length/$scope.limit).length;
        if($scope.page>result){
            $scope.page = result==0?1:result;
        }
        return result;
    }
});

app.controller('employee_search',function($scope,$rootScope){

});

app.controller('employee_display',function($scope,$rootScope,$timeout){
    $scope.employee = {};
    $scope.leaves = [];
    $scope.bal_date = '';
    $scope.init = function(employee,leaves){
        $scope.employee = employee;
        $scope.leaves = leaves;
        $scope.employee.credits = {
            sick:0,
            vacation:0
        };
        $scope.bal_date = moment().subtract(1,'month').endOf('month');
        //Sort Leaves
        $scope.leaves.sort(function(a,b){
            return moment(b.date_ranges[b.date_ranges.length-1].start_date).diff(moment(a.date_ranges[a.date_ranges.length-1].start_date));
        });

        for(var i = 0 ; i<$scope.leaves.length ; i++){
			var leave = $scope.leaves[i];
            for(var j = 0 ; j<leave.date_ranges.length ; j++){
				var date_range = leave.date_ranges[j];
				date_range.start_date = moment(date_range.start_date).format($rootScope.dateFormat);
				date_range.end_date = moment(date_range.end_date).format($rootScope.dateFormat);
			}
        }

        $scope.sick_bal_date = moment().endOf("month");
        $scope.vac_bal_date = moment().endOf("month");
        $scope.employee.first_day = moment($scope.employee.first_day).format($rootScope.dateFormat);
    }

	$scope.openModal = function(index){
		angular.element('#editLeaveModal').modal('show');
        var leave = angular.copy($scope.leaves[index]);
        for(var i = 0 ; i<leave.date_ranges.length ; i++){
            var date_range =  leave.date_ranges[i];
            date_range.start_date = moment(date_range.start_date);
            date_range.end_date = moment(date_range.end_date);
            date_range.hours = parseInt(date_range.hours);
            date_range.minutes = parseInt(date_range.minutes);
        }
        var validLeaves = ["Vacation","Sick","Maternity","Paternity"];
        if(validLeaves.indexOf(leave.info.type)==-1){
            leave.info.type_others = leave.info.type;
            leave.info.type = 'Others';
        }
        console.log(leave);
		$scope.$broadcast('openLeaveModal',leave);
	}

	$scope.computeBal = function(){
		/*
				The numbers are converted to integer for computational accuracy. Display and saved values are converted back to three(3) decimal places
		*/
		// As per MC No. 14, s. 1999
		var creditByHalfDay = [0,21,42,62,83,104,125,146,167,187,208,229,250,271,292,312,333,354,375,396,417,437,458,479,500,521,542,562,583,604,
		625,646,667,687,708,729,750,771,792,813,833,854,875,896,917,938,958,979,1000,1021,1042,1063,1083,1104,1125,1146,1167,1188,1208,1229,1250];

		var currV = Math.floor(Number($scope.employee.vac_leave_bal)*1000);
		var currS = Math.floor(Number($scope.employee.sick_leave_bal)*1000);
		var dateEnd = moment($scope.bal_date).clone();
		var dateStart = moment($scope.employee.first_day,$rootScope.dateFormat).clone();
		var fLeave = 0, spLeave = 0, pLeave = 0; // Forced Leave, Special Priviledge Leave, Parental Leave
		// First Month Computation
		var firstMC = 0;
		if(dateStart.isSame(dateStart.clone().startOf('month'))  &&  currV!=0){ }else{
			fLeave=5000;
			firstMC = Math.abs(dateStart.clone().endOf('month').diff(dateStart, 'days'))+1;
			firstMC = creditByHalfDay[2*firstMC];
			currV += firstMC; currS += firstMC;
			dateStart.add(1,'month');
		}
		// #first_month_computation

		// Computation For Other Months
		while(dateStart<dateEnd){
			if(moment(dateStart).month()==1){
				fLeave=5000;
				spLeave=3000;
				pLeave=7000;
			}
			for(var i=0;i<$scope.leaves.length;i++){
				var leave = $scope.leaves[i];
				for(var j=0;j<leave.date_ranges.length;j++){
					var range = leave.date_ranges[j];
					if( moment(range.end_date,$rootScope.dateFormat).isBefore(dateStart.clone().startOf('month'))
							||  moment(range.start_date,$rootScope.dateFormat).isAfter(dateStart.clone().endOf('month')) )
						continue;
					var creditUsed = $scope.getCreditEquivalent(range)*1000;
					
					if( leave.info.type=="Vacation"||leave.info.type.toLowerCase().includes('force')||leave.info.type.toLowerCase().includes('mandatory') ){
						currV -= creditUsed;
						fLeave -= creditUsed;
					}
					if(leave.info.type=="Sick") currS -= creditUsed;
					if(leave.info.type.toLowerCase().includes('spl')||leave.info.type.toLowerCase().includes('special')){
						spLeave -= creditsUsed;
						if(spLeave<0){
							currV+=spLeave;
							spLeave=0;
						}
					}
					if(leave.info.type.toLowerCase().includes('paternal')){
						pLeave -= creditsUsed;
						if(pLeave<0){
							currV+=pLeave;
							pLeave=0;
						}
					}
				}
			}
			if(currS<0){// When the employee is absent due to sickness and run out of sick leave
				currV += currS;
				currS = 0;
			}
			if(currV<0){// Employee incurring absence without pay
				var cpd = 1.25/30; // Credit per day: ( 1.25 credits per month )/( 30 days per month )
				var absent = Math.floor(Math.abs(currV)/500);
				var rem = Math.floor(Math.abs(currV)%500);
				currV = Math.floor(creditByHalfDay[60-absent]-(rem*cpd));
			}else{
				currV += 1250;
			}
			currS+=1250;
			dateStart.add(1,'month');
			if(moment(dateStart).month()==0 && fLeave>0 && currV>=fLeave) currV = currV-fLeave;
		}
		// #computation_for_other_months

        return "Vacation: " + (currV/1000).toFixed(3) + " Sick: " + (currS/1000).toFixed(3);
    }

    $scope.formatBalDate = function(){
        $scope.bal_date = moment($scope.bal_date).endOf('month');
    }

    $scope.getDeductedCredits = function(type,date_range){
		if(type=='Vacation'||type=='Sick'||type.toLowerCase().includes('force')||type.toLowerCase().includes('mandatory')){
			return $scope.getCreditEquivalent(date_range);
		}else{
			return 0;
		}
    }
	
	$scope.getCreditEquivalent = function(date_range){
		var credits = (date_range.hours/8+date_range.minutes/(60*8)).toFixed(3);
		
		if(credits<7){
			if(moment(date_range.end_date,$rootScope.dateFormat).clone().day()<moment(date_range.start_date,$rootScope.dateFormat).clone().day())
				credits -= 2;
			else if(moment(date_range.end_date,$rootScope.dateFormat).clone().day()==6)
				credits -= 1;
			else if(moment(date_range.start_date,$rootScope.dateFormat).clone().day()==0)
				credits -= 1;
		}
		if(typeof credits =='number') credits = credits.toFixed(3);
		return credits;
	}
	
	$scope.checkButton = function(leave){
		console.log(leave.info.type)
		if(leave.info.type="Vacation"){
			console.log("true");
			return false;
		}
		return true;
	}
});

app.controller('employee_add',function($scope,$rootScope,$window){
    $scope.employee={};
    $scope.add = function(){
        $rootScope.post(
            $rootScope.baseURL+"employee/add/",
            $scope.employee,
            function (response){
                alert("Success: "+response.msg);
                $window.location.reload();
            },
            function(response){
                alert("Error: "+response.msg);
            }
        );
    }
})

app.controller('leave_application',function($scope,$rootScope,$window,$filter,employeeSearchFilter){
    $scope.employees = [];
    $scope.employee = {};
    $scope.leave = {
        info:{},
        date_ranges:[]
    }

	$scope.leaveDateRangeTemplate = {
		start_date:'',
		end_date:'',
		hours:0,
		minutes:0
	};

    $scope.init = function(employees="",employee=null){
        $scope.employees = employees==""?$scope.employees:employees;
        for(var i = 0 ; i<$scope.employees.length ; i++){
            var currEmployee = $scope.employees[i];
            currEmployee.name = currEmployee.last_name+", "+currEmployee.first_name+" "+currEmployee.middle_name;
        }
        if(employee!=null){
            $scope.employee = employee;
            $scope.employee.name = employee.last_name+", "+employee.first_name+" "+employee.middle_name;
        }else{
            employee={};
        }
        $scope.rangeAction(0);
    }
    /*Employee Live Search*/
    $scope.focusedEmployeeIndex = 0;
    $scope.onMouseOver = function(index){
        $scope.focusedEmployeeIndex = index;
    }
    $scope.onKeyDown = function($event,filterArg){
        if ($event.keyCode == 38)
            $scope.focusedEmployeeIndex=$scope.focusedEmployeeIndex-1<0?0:$scope.focusedEmployeeIndex-1;
        else if ($event.keyCode == 40)
            $scope.focusedEmployeeIndex=$scope.focusedEmployeeIndex+1>=$scope.employees.length?$scope.employees.length-1:$scope.focusedEmployeeIndex+1;
        else if($event.keyCode == 13){
            $scope.setEmployee($filter('employeeSearch')($scope.employees,filterArg,$scope.employee[filterArg])[$scope.focusedEmployeeIndex].emp_no);
            $event.preventDefault();
        }
    }
    $scope.setEmployee = function(emp_no){
        for(var i = 0 ; i<$scope.employees.length ; i++){
            if(emp_no==$scope.employees[i].emp_no){
                $scope.employee = angular.copy($scope.employees[i]);
            }
        }
        $scope.searchFocusName = false;
        $scope.searchFocusEmpNo = false;
    }
    /*end Employee Live Search*/

	$scope.$on('openLeaveModal',function(event, leave){
        angular.element('#leaveType'+leave.info.type).addClass('active');
        var leaveTypes = ['Vacation','Sick','Maternity','Paternity','Others'];
        var index = leaveTypes.indexOf(leave.info.type);
        if(index>-1){
            leaveTypes.splice(index,1);
        }
        for(var i = 0 ; i<leaveTypes.length ; i++){
            angular.element('#leaveType'+leaveTypes[i]).removeClass('active');
        }
		$scope.leave = leave;
	});

    var getTotalDays = function(index = -1){
		if(index==-1){
			var days = 0;

			for(var i = 0 ; i<$scope.leave.date_ranges.length ; i++){
				if($scope.leave.date_ranges[i].end_date=='' || $scope.leave.date_ranges[i].start_date==''){
					continue;
				}
				days += Math.round((moment($scope.leave.date_ranges[i].end_date).diff($scope.leave.date_ranges[i].start_date))/86400000)+1;
				days += $scope.leave.date_ranges[i].minutes/(8*60);
			}
			return days;
		}else{
			return Math.round((moment($scope.leave.date_ranges[index].end_date).diff($scope.leave.date_ranges[index].start_date))/86400000)+1;
		}
    }

	$scope.getTotalCredits = function(){
		var credits = 0;
		for(var i = 0 ; i<$scope.leave.date_ranges.length ; i++){
			if($scope.leave.date_ranges[i].end_date=='' || $scope.leave.date_ranges[i].start_date==''){
				continue;
			}
			credits += $scope.leave.date_ranges[i].hours/8;
			credits += $scope.leave.date_ranges[i].minutes/(8*60);
		}
		return credits;
	}

	$scope.rangeAction = function(action,index=-1){
		switch(action){
			case 0://add
				$scope.leave.date_ranges.push(angular.copy($scope.leaveDateRangeTemplate));
				return;
			case 1://delete
				if($scope.leave.date_ranges.length<=1){
					alert("Date ranges must have at least 1 range.");
					return;
				}
				$scope.leave.date_ranges.splice(index==-1?$scope.leave.date_ranges.length-1:index,1);
				return;
		}
	}

    $scope.startDateSet = function (index) {
		if($scope.leave.date_ranges[index].end_date){
			if(moment($scope.leave.date_ranges[index].end_date,$rootScope.dateFormat).diff(moment($scope.leave.date_ranges[index].start_date,$rootScope.dateFormat))<0
                || moment($scope.leave.date_ranges[index].end_date,$rootScope.dateFormat).month()!=moment($scope.leave.date_ranges[index].start_date,$rootScope.dateFormat).month()){
				$scope.leave.date_ranges[index].end_date = $scope.leave.date_ranges[index].start_date;
			}
		}else{
			$scope.leave.date_ranges[index].end_date = $scope.leave.date_ranges[index].start_date;
		}
        $scope.$broadcast('startDateSet');
		$scope.leave.date_ranges[index].hours = getTotalDays(index)*8;
    }

	$scope.endDateSet = function(index){
		if(!$scope.leave.date_ranges[index].start_date){
			$scope.leave.date_ranges[index].start_date = $scope.leave.date_ranges[index].end_date;
		}
		$scope.leave.date_ranges[index].hours = getTotalDays(index)*8;
	}

    $scope.endDateRender = function($view,$dates,index){
        if($scope.leave.date_ranges[index].start_date){
            var activeDate = moment($scope.leave.date_ranges[index].start_date).subtract(1, $view).add(1, 'minute');

            $dates.filter(function(date){
                return date.localDateValue() <= activeDate.valueOf() || date.localDateValue() > moment($scope.leave.date_ranges[index].start_date).endOf('month').valueOf();
            }).forEach(function(date){
                date.selectable = false;
            });
        }
    }

    $scope.submit = function(isModal = false){
        var data = angular.copy($scope.leave);
        data.info.emp_no = $scope.employee.emp_no;
		if(isModal){
			data.action = "edit";
		}
		
		for(var i=0; i<data.date_ranges.length-1; i++){
			for(var j=i+1; j<data.date_ranges.length; j++){
				if( moment(data.date_ranges[i].start_date,$rootScope.dateFormat).isSameOrBefore(moment(data.date_ranges[j].end_date,$rootScope.dateFormat)) && moment(data.date_ranges[i].end_date,$rootScope.dateFormat).isSameOrAfter(moment(data.date_ranges[j].start_date,$rootScope.dateFormat)) ){
					alert("Conflict in date range.");
					return;
				}
			}
		}
		var credits = $scope.getTotalCredits();
		//	As per MC 41, s. 1998: Sec 25
		//	On the assumption of one 'data' per delivery
		if(data.info.type.toLowerCase()=="others" && (data.info.type_others.toLowerCase().includes("force") || data.info.type_others.toLowerCase().includes("mandat")) && credits>5){
			alert("An employee is only entitled to FIVE(5) forced/mandatory leaves. \n Record the excess as vacation leave.");
			return;
		}
		//	As per MC 41, s. 1998: Sec 20
		//	On the assumption of one 'data' per delivery
		if(data.info.type.toLowerCase()=="paternity" && credits>7){
			alert("A married male employee is only entitled to leave of SEVEN(7) working days only per delivery/miscarriage of his legitimate spouse.");
			return;
		}
		//	As per MC 41, s. 1998: Sec 11
		//	On the assumption of one 'data' per delivery
		if(data.info.type.toLowerCase()=="maternity" && credits>60){
			alert("A married woman is only entitled to leave of SIXTY(60) calendar days.");
			return;
		}

        //format ranges for posting
        for(var i = 0 ; i<data.date_ranges.length ; i++){
			data.date_ranges[i].start_date = moment(data.date_ranges[i].start_date,$rootScope.dateFormat).format("YYYY/MM/DD");
			data.date_ranges[i].end_date = moment(data.date_ranges[i].end_date,$rootScope.dateFormat).format("YYYY/MM/DD");
		}
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
    /*DEPRECATED
    $scope.autocomplete = function(){
		// Script taken from: https://www.w3schools.com/howto/howto_js_autocomplete.asp

		inp = document.getElementById("empNo");
        if(inp == null) return;
		var currFocus=-1;
		inp.addEventListener("input", function(e){
			var divList, item, val=this.value;
			closeList();
			if(!val){return false;}
			currFocus = -1;
			divList = document.createElement("DIV");
			divList.setAttribute("id", this.id + "autocomplete-list");
			divList.setAttribute("class", "autocomplete-items");
			divList.classList.add("form-group");
			this.parentNode.appendChild(divList);
			for(var i=0; i<$scope.employees.length; i++){
				if($scope.employees[i].emp_no.toLowerCase().includes(val.toLowerCase())){
					item = document.createElement("DIV");
					item.innerHTML = $scope.employees[i].emp_no;
					item.innerHTML += "<input type='hidden' value='"+$scope.employees[i].emp_no+"'>";
					item.addEventListener("click", function(e){
						inp.value = this.getElementsByTagName('input')[0].value;
						closeList();
						$scope.fillName();
					});
					divList.appendChild(item);
					if(divList.children.length==5) break;
				}
			}
		});

		inp.addEventListener("keydown", function(e){
			var x = document.getElementById(this.id + "autocomplete-list");
			if(x) x = x.getElementsByTagName("div");
			if(x==null || x.length==0) return;
			if(e.which == 40){
				currFocus++;
				addActive(x);
			}else if(e.which == 38){
				currFocus--;
				addActive(x);
			}else if(e.which == 13){
				e.preventDefault();
				if(currFocus>-1) if(x) x[currFocus].click();
			}
		});

		function addActive(elem){
			if(!elem) return false;
			remActive(elem);
			if(currFocus>=elem.length) currFocus=0;
			if(currFocus<0) currFocus=(elem.length-1);
			elem[currFocus].classList.add("autocom-active");
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
		if(number.value.length!=7) return;
		//var i;
		for(i=0; i<$scope.employees.length;i++){
			if(number.value==$scope.employees[i].emp_no) break;
			if(i==($scope.employees.length-1)){
				return;
			}
		}

		$scope.employee = angular.copy($scope.employees[i]);
		$scope.employee.name = $scope.employee.last_name+", "+$scope.employee.first_name+" "+$scope.employee.middle_name;
		$scope.$apply();
	}*/
});
