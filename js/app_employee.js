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

app.controller('employee_display',function($scope,$rootScope,$window){
    $scope.employee = {};
    $scope.leaves = [];
    $scope.bal_date = '';
	$scope.creditBalance = {vac:0,sick:0};
	$scope.filter = {every:true,vacation:true,sick:true,maternity:true,paternity:true,others:true};
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
            date_range.start_date = moment(date_range.start_date,$rootScope.dateFormat);
            date_range.end_date = moment(date_range.end_date,$rootScope.dateFormat);
            date_range.hours = parseInt(date_range.hours);
            date_range.minutes = parseInt(date_range.minutes);
        }
        var validLeaves = ["Vacation","Sick","Maternity","Paternity"];
        if(validLeaves.indexOf(leave.info.type)==-1){
            leave.info.type_others = leave.info.type;
            leave.info.type = 'Others';
        }
		$scope.$broadcast('openLeaveModal',leave);
	}

    // Monetization
    $scope.monetize = {
        date:moment(),
        credits:0,
        special:false
    }

    $scope.submitMonetization = function(){
        //Validation code goes here

        $scope.monetize.credits = parseFloat($scope.monetize.credits.toFixed(3));
        var data = {
            info:{
                type:($scope.monetize.special?'Special ':'')+'Monetization: '+$scope.monetize.type,
                remarks:$scope.monetize.remarks ? $scope.monetize.remarks:'',
                emp_no:$scope.employee.emp_no
            },
            date_ranges:[
                {
                    start_date:moment($scope.monetize.date,$rootScope.dateFormat),
                    end_date:moment($scope.monetize.date,$rootScope.dateFormat),
                    hours:parseInt($scope.monetize.credits)*8,
                    minutes:($scope.monetize.credits - parseInt($scope.monetize.credits))*60*8
                }
            ]
        }

        $rootScope.post(
            $rootScope.baseURL+"/employee/leaveApplication",
            data,
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
                $rootScope.showCustomModal('Error',response.msg,
                    function(){
                        angular.element('#customModal').modal('hide');
                    },
                    function(){
                    }
                );
            }
        );
    }
    // end Monetization

    $scope.deleteLeave = function(leaveID){
        $rootScope.showCustomModal(
            'Warning',
            'Are you sure you want to delete this leave record?',
            function(){
                $rootScope.post(
                    $rootScope.baseURL+'employee/deleteLeave',
                    leaveID,
                    function(){
                        $window.location.reload();
                    },
                    function(){
                    }
                );
            },
            function(){
            },
            'Yes',
            'No'
        );
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
		var lwop = 0; // Leave Without Pay
		var fLeave = 0, spLeave = 0, pLeave = 0; // Forced Leave, Special Priviledge Leave, Parental Leave
		// First Month Computation
		var firstMC = 0;
		var monetized = false;
		if(dateStart.isSame(dateStart.clone().startOf('month'))  &&  currV!=0){ }else{
			fLeave=5000; spLeave=3000; pLeave=7000;
			monetized=false;
			firstMC = Math.abs(dateStart.clone().endOf('month').diff(dateStart, 'days'))+1;
			firstMC = creditByHalfDay[2*firstMC];
			currV += firstMC; currS += firstMC;
			dateStart.add(1,'month');
		}
		// #first_month_computation

		// Computation For Months Other Than The First
		while(dateStart<dateEnd){
			lwop = 0;
			if(moment(dateStart).month()==1){
				fLeave=5000;
				spLeave=3000;
				pLeave=7000;
				monetized=false;
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
					if(leave.info.type.toLowerCase().includes('parental')){
						pLeave -= creditUsed;
						if(pLeave<0){
							currV+=pLeave;
							pLeave=0;
						}
					}

					// Temporal Solution for Monetization of Leaves
					if(leave.info.type.toLowerCase().includes('monet') && !leave.info.type.toLowerCase().includes('special')){
						monetized=true;
						currV -= creditUsed;
						if(currV<5000){
							$rootScope.showCustomModal('Error','Limit for leave monetization exceeded.',function(){angular.element('#customModal').modal('hide');},function(){});
							currV=5000;
						}else{console.log("safe");}
					}
					if(leave.info.type.toLowerCase().includes('monet') && leave.info.type.toLowerCase().includes('special')){
						monetized=true;
						vacation -= creditUsed;
						if(currV<5000){
							currV -= 5000;
							currS += currV;
							currV=5000;
							if(currS<0) $rootScope.showCustomModal('Error','Limit for special leave monetization exceeded.',function(){angular.element('#customModal').modal('hide');},function(){});
						}
					}
					// #temporal_solution_for_monetization_of_leaves
				}
			}
			if(currS<0){// When the employee is absent due to sickness and run out of sick leave
				currV += currS;
				fLeave += currS;
				currS = 0;
			}
			if(currV<0){// Employee incurring absence without pay
				lwop = Math.floor(Math.abs(currV)/1000);
				var cpd = 1.25/30; // Credit per day: ( 1.25 credits per month )/( 30 days per month )
				var absent = Math.floor(Math.abs(currV)/500);
				var rem = Math.floor(Math.abs(currV)%500);
				currV = Math.floor(creditByHalfDay[60-absent]-(rem*cpd));
			}else{
				currV += 1250;
			}
			currS+=1250;
			dateStart.add(1,'month');
			if(moment(dateStart).month()==0 && fLeave>0 && ( (!monetized && currV>10000) || (monetized && currV>5000) ) ) currV = currV-fLeave;
		}
		// #computation_for_other_months

		$scope.creditBalance.vac = (currV/1000).toFixed(3);
		$scope.creditBalance.sick = (currS/1000).toFixed(3);

        return "Vacation: " + (currV/1000).toFixed(3) + " Sick: " + (currS/1000).toFixed(3);// + ( false ? " LWOP: "+lwop:"" );
    }

    $scope.formatBalDate = function(){
        $scope.bal_date = moment($scope.bal_date).endOf('month');
    }

    $scope.getDeductedCredits = function(type,date_range){
		if(type=='Vacation'||type=='Sick'||type.toLowerCase().includes('force')||type.toLowerCase().includes('mandatory')||type.toLowerCase().includes('monet')){
			return $scope.getCreditEquivalent(date_range);
		}else{
			return 0;
		}
    }

	$scope.getCreditEquivalent = function(date_range){
		var credits = (date_range.hours/8+date_range.minutes/(60*8)).toFixed(3);

		var start = moment(date_range.start_date,$rootScope.dateFormat).clone();
		var end = moment(date_range.end_date,$rootScope.dateFormat).clone();

		while(start<=end){
			if(start.day()==0 || start.day()==6)
				credits--;
			else if($scope.isHoliday(start))
				credits--;
			start = start.add(1,'day');
		}

		if(typeof credits =='number') credits = credits.toFixed(3);
		return credits;
	}

	$scope.isHoliday = function(date){
		// Need a set of holidays
		return false;
	}

	$scope.reFilter = function(filter){
		switch(filter){
			case 'Every':
				if($scope.filter.every){
					$scope.filter.every = false;
					$scope.filter.vacation = false;
					$scope.filter.sick = false;
					$scope.filter.maternity = false;
					$scope.filter.paternity = false;
					$scope.filter.others = false;
				}else{
					$scope.filter.every = true;
					$scope.filter.vacation = true;
					$scope.filter.sick = true;
					$scope.filter.maternity = true;
					$scope.filter.paternity = true;
					$scope.filter.others = true;
				}
				break;

			case 'Vacation':
				if($scope.filter.vacation)
					$scope.filter.every=false;
				$scope.filter.vacation = !$scope.filter.vacation;
				if($scope.filter.vacation && $scope.filter.sick && $scope.filter.maternity && $scope.filter.paternity && $scope.filter.others)
					$scope.filter.every=true;
				break;

			case 'Sick':
				if($scope.filter.sick)
					$scope.filter.every=false;
				$scope.filter.sick = !$scope.filter.sick;
				if($scope.filter.vacation && $scope.filter.sick && $scope.filter.maternity && $scope.filter.paternity && $scope.filter.others)
					$scope.filter.every=true;
				break;

			case 'Maternity':
				if($scope.filter.maternity)
					$scope.filter.every=false;
				$scope.filter.maternity = !$scope.filter.maternity;
				if($scope.filter.vacation && $scope.filter.sick && $scope.filter.maternity && $scope.filter.paternity && $scope.filter.others)
					$scope.filter.every=true;
				break;

			case 'Paternity':
				if($scope.filter.paternity)
					$scope.filter.every=false;
				$scope.filter.paternity = !$scope.filter.paternity;
				if($scope.filter.vacation && $scope.filter.sick && $scope.filter.maternity && $scope.filter.paternity && $scope.filter.others)
					$scope.filter.every=true;
				break;

			default:
				if($scope.filter.others)
					$scope.filter.every=false;
				$scope.filter.others = !$scope.filter.others;
				if($scope.filter.vacation && $scope.filter.sick && $scope.filter.maternity && $scope.filter.paternity && $scope.filter.others)
					$scope.filter.every=true;
				break;
		}
	}

	$scope.terminalBenefit = function(){
		var salary = 100*$scope.employee.salary;
		var credits = Number($scope.creditBalance.vac) + Number($scope.creditBalance.sick);
<<<<<<< HEAD
		var constantFactor = 4.81927; // multiplied by 100

=======
		var constantFactor = 0.0481927;
		
>>>>>>> e380864e839b197ba2b3ceff49e5d943d5716ff7
		var tlb = salary * credits * constantFactor;

		return (tlb/100).toFixed(2);
	}
});

app.controller('employee_add',function($scope,$rootScope,$window){
    $scope.employee={};
    $scope.add = function(){
        $rootScope.post(
            $rootScope.baseURL+"employee/add/",
            $scope.employee,
            function (response){
                $rootScope.showCustomModal(
                    'Success',
                    response.msg,
                    function(){
                        $window.location.reload();
                    },
                    function(){
                        $window.location.reload();
                    },
                    'OK'
                );
            },
            function(response){
                $rootScope.showCustomModal(
                    'Error',
                    response.msg,
                    function(){angular.element('#customModal').modal('hide');},
                    function(){}
                );
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
                    $rootScope.showCustomModal('Error','Date ranges must have at least 1 range.',function(){angular.element('#customModal').modal('hide');},function(){});
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

		for(var i=0; i<data.date_ranges.length; i++){
			if( data.date_ranges[i].start_date=="" || data.date_ranges[i].start_date==null || data.date_ranges[i].end_date=="" || data.date_ranges[i].end_date==null ){
				$rootScope.showCustomModal('Error','Please fill up date range',function(){angular.element('#customModal').modal('hide');},function(){});
				return;
			}
		}

		for(var i=0; i<data.date_ranges.length-1; i++){
			for(var j=i+1; j<data.date_ranges.length; j++){
				if( moment(data.date_ranges[i].start_date,$rootScope.dateFormat).isSameOrBefore(moment(data.date_ranges[j].end_date,$rootScope.dateFormat)) && moment(data.date_ranges[i].end_date,$rootScope.dateFormat).isSameOrAfter(moment(data.date_ranges[j].start_date,$rootScope.dateFormat)) ){
                    $rootScope.showCustomModal('Error','Conflict in date range',function(){angular.element('#customModal').modal('hide');},function(){});
					return;
				}
			}
		}

		var credits = $scope.getTotalCredits();
		//	As per MC 41, s. 1998: Sec 55
		//	On the assumption of one 'data' per rahabilitation
		if(data.info.type.toLowerCase()=="others" && data.info.type_others.toLowerCase().includes("rehab") && ( credits>184 || data.date_ranges.length>7 ) ){
			$rootScope.showCustomModal('Error','An employee who incured injuries or wounds in the performance of duty is only entitled up to SIX(6) MONTHS of rehabilitation leave. \n Record the excess as vacation leave.',function(){angular.element('#customModal').modal('hide');},function(){});
			return;
		}
		//	As per MC 41, s. 1998: Sec 25
		//	On the assumption of one 'data' per delivery
		if(data.info.type.toLowerCase()=="others" && (data.info.type_others.toLowerCase().includes("force") || data.info.type_others.toLowerCase().includes("mandat")) && credits>5){
            $rootScope.showCustomModal('Error','An employee is only entitled to FIVE(5) forced/mandatory leaves. \n Record the excess as vacation leave.',function(){angular.element('#customModal').modal('hide');},function(){});
			return;
		}
		//	As per MC 41, s. 1998: Sec 20
		//	On the assumption of one 'data' per delivery
		if(data.info.type.toLowerCase()=="paternity" && credits>7){
            $rootScope.showCustomModal('Error','A married male employee is only entitled to leave of SEVEN(7) working days only per delivery/miscarriage of his legitimate spouse.',function(){angular.element('#customModal').modal('hide');},function(){});
			return;
		}
		//	As per MC 41, s. 1998: Sec 11
		//	On the assumption of one 'data' per delivery
		if(data.info.type.toLowerCase()=="maternity" && credits>60){
            $rootScope.showCustomModal('Error','A married woman is only entitled to leave of SIXTY(60) calendar days.',function(){angular.element('#customModal').modal('hide');},function(){});
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
                $rootScope.showCustomModal('Error',response.msg,
                    function(){
                        angular.element('#customModal').modal('hide');
                    },
                    function(){
                    }
                );
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
