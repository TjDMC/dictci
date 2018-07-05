app.controller('employee_nav',function($scope,$rootScope){
    $scope.employees = [];
	$scope.limit = 10;
    $scope.page = 1;
    $scope.filteredEmployees = [];
    $scope.init=function(employees){
        $scope.employees=employees;
    }

	$scope.employeesToArray = function(employees){
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
        var result = $scope.numberToArray($scope.filteredEmployees.length/$scope.limit).length;
        if($scope.page>result){
            $scope.page = result==0?1:result;
        }
        return result;
    }
});

app.controller('employee_display',function($scope,$rootScope,$window){
    $scope.employee = {};
    $scope.leaves = [];
    $scope.bal_date = '';
	$scope.terminal_date = '';
	$scope.lwop
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

    /* Monetization */
    $scope.monetize = {
        date:moment(),
        credits:0,
        special:false
    }

    $scope.submitMonetization = function(){
        //Validation code goes here
        $scope.monetize.credits = parseFloat($scope.monetize.credits.toFixed(3));
		console.log($scope.monetize.credits);
		var balance = $scope.computeBal($scope.monetize.date);
		if( ( !$scope.monetize.special && $scope.monetize.credits>balance[0]-5 )  ||  ( $scope.monetize.special && $scope.monetize.credits>balance[0]+balance[1]-5 ) ){
			$rootScope.showCustomModal('Error','At least 5 vacation leaves should remain.',function(){angular.element('#customModal').modal('hide');},function(){});
			return;
		}
        var data = {
            info:{
                type:($scope.monetize.special?'Special ':'')+'Monetization',
                remarks:$scope.monetize.remarks ? $scope.monetize.remarks:'',
                emp_no:$scope.employee.emp_no
            },
            date_ranges:[
                {
                    start_date:moment($scope.monetize.date,$rootScope.dateFormat).format('YYYY/MM/DD'),
                    end_date:moment($scope.monetize.date,$rootScope.dateFormat).format('YYYY/MM/DD'),
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
    /* end Monetization */

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

	$scope.getBalance = function(){
		var t1 = performance.now();
		var hold = $scope.computeBal($scope.bal_date);
		var t2 = performance.now();
		console.log(t2-t1);
		return "Vacation: " + hold[0] + ", Sick: " + hold[1];
	}

	$scope.computeBal = function(enDate){
		/*
				The numbers are converted to integer for computational accuracy. Displayed and saved values are converted back to three(3) decimal places
		*/
		// As per MC No. 14, s. 1999
		var creditByHalfDay = [0, 21, 42, 62, 83, 104, 125, 146, 167, 187, 208, 229, 250, 271, 292, 312, 333, 354, 375, 396, 417, 437, 458, 479, 500, 521, 542, 562, 583, 604, 625, 646, 667, 687, 708, 729, 750, 771, 792, 813, 833, 854, 875, 896, 917, 938, 958, 979,1000,1021,1042,1063,1083,1104,1125,1146,1167,1188,1208,1229,1250];
		
		
		if(moment(enDate,$rootScope.dateFormat).isSame(moment(enDate,$rootScope.dateFormat).endOf('month'), 'day')) console.log("SAME");
		
		var currV = Math.floor(Number($scope.employee.vac_leave_bal)*1000);
		var currS = Math.floor(Number($scope.employee.sick_leave_bal)*1000);
		var dateEnd = moment(enDate,$rootScope.dateFormat).endOf('month').clone();
		var dateStart = moment($scope.employee.first_day,$rootScope.dateFormat).clone();
		var lwop = 0; // Leave Without Pay
		var fLeave = 0, spLeave = 0, pLeave = 0; // Forced Leave, Special Priviledge Leave, Parental Leave
		var monetized = false;
		// First Month Computation
		if(dateStart.isSame(dateStart.clone().startOf('month'))  &&  currV!=0){ }else{
			fLeave=5000; spLeave=3000; pLeave=7000;
			var firstMC = Math.abs(dateStart.clone().endOf('month').diff(dateStart, 'days'))+1;
			firstMC = creditByHalfDay[2*firstMC];
			currV += firstMC; currS += firstMC;
			dateStart.add(1,'month');
		}
		// #first_month_computation
		
		
		// Computation For Months Other Than The First
		while(dateStart<dateEnd){
			if(moment(dateStart).month()==0){
				console.log("in");
				fLeave=5000;
				spLeave=3000;
				pLeave=7000;
				monetized=false;
			}
			for(var i=0;i<$scope.leaves.length;i++){
				var leave = $scope.leaves[i];
				for(var j=0;j<leave.date_ranges.length;j++){
					var range = leave.date_ranges[j];
					if( moment(range.end_date,$rootScope.dateFormat).isBefore(dateStart.clone().startOf('month')) ||  moment(range.start_date,$rootScope.dateFormat).isAfter(dateStart.clone().endOf('month')) )
						continue;
					var creditUsed = $scope.getCreditEquivalent(leave.info.type,range)*1000;

					if( leave.info.type=="Vacation"||leave.info.type.toLowerCase().includes('force')||leave.info.type.toLowerCase().includes('mandatory') ){
						//	Vacation and Forced Leaves
						currV -= creditUsed;
						fLeave -= creditUsed;
					}else if(leave.info.type=="Sick"){
						//	Sick Leaves
						currS -= creditUsed;
					}else if(leave.info.type.toLowerCase().includes('monet')){
						// Temporal Solution for Monetization of Leaves
						monetized=true;
						currV -= creditUsed;
						if(currV<5000){
							if(!leave.info.type.toLowerCase().includes('special')){
								$rootScope.showCustomModal('Error','Limit for leave monetization exceeded.',function(){angular.element('#customModal').modal('hide');},function(){});
							}else{
								currV -= 5000;
								if(currS+currV<0) $rootScope.showCustomModal('Error','Limit for special leave monetization exceeded.',function(){angular.element('#customModal').modal('hide');},function(){});
								else currS += currV;
							}
							currV=5000;
						}
						// #temporal_solution_for_monetization_of_leaves
					}else if(leave.info.type.toLowerCase().includes('spl') || leave.info.type.toLowerCase().includes('special')){
						//	Special Priviledge Leaves
						spLeave -= creditUsed;
						if(spLeave<0){
							currV+=spLeave;
							spLeave=0;
						}
					}else if(leave.info.type.toLowerCase().includes('parental')){
						//	Parental Leaves	(For Solo Parents)
						pLeave -= creditUsed;
						if(pLeave<0){
							currV+=pLeave;
							pLeave=0;
						}
					}
				}
			}
			if(currS<0){// When the employee is absent due to sickness and run out of sick leave
				currV += currS;
				fLeave += currS;
				currS = 0;
			}
			if(currV<0){// Employee incurring absence without pay
				lwop += Math.abs(currV)/1000;
				var cpd = 1.25/30; // Credit per day: ( 1.25 credits per month )/( 30 days per month )
				var absent = Math.floor(Math.abs(currV)/500);
				var rem = Math.floor(Math.abs(currV)%500);
				currV = Math.floor(creditByHalfDay[60-absent]-(rem*cpd));
				currS += Math.floor(creditByHalfDay[60-absent]-(rem*cpd));
			}else{
				currV += 1250;
				currS += 1250;
			}
			if(moment(dateStart).month()==11 && fLeave>0 && ( monetized || currV>10000 ) ) currV = currV-fLeave;
			dateStart.add(1,'month');
		}
		// #computation_for_other_months
		
		$scope.lwop = lwop;

        return [(currV/1000).toFixed(3),(currS/1000).toFixed(3)];
    }

    $scope.formatBalDate = function(){
        $scope.bal_date = moment($scope.bal_date).endOf('month');
    }

    $scope.getDeductedCredits = function(type,date_range){
		if(type=='Vacation'||type=='Sick'||type.toLowerCase().includes('force')||type.toLowerCase().includes('mandatory')||type.toLowerCase().includes('monet')){
			return $scope.getCreditEquivalent(type,date_range);
		}else{
			return 0;
		}
    }

	$scope.getCreditEquivalent = function(type,date_range){
		var credits = (date_range.hours/8+date_range.minutes/(60*8)).toFixed(3);

		var start = moment(date_range.start_date,$rootScope.dateFormat).clone();
		var end = moment(date_range.end_date,$rootScope.dateFormat).clone();

		if(!type.toLowerCase().includes("monetization")){
			while(start<=end){
				/*if(start.day()==0 || start.day()==6)
					credits--;
				else*/ if($scope.isHoliday(start))
					credits--;
				start = start.add(1,'day');
			}
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

	// Chacking difference between the two
	$scope.terminalBenefit = function(){
		var salary = 100*$scope.employee.salary;
		var balance = $scope.computeBal($scope.terminal_date);
		console.log(balance);
		var credits = Number(balance[0]) + Number(balance[1]);
		var constantFactor = 0.0481927;

		var tlb = salary * credits * constantFactor;

		return (tlb/100).toFixed(2);
	}
	
	$scope.terminalBenefit2 = function(){
		
		//	Credits Earned
		var creditByHalfDay = [0, 21, 42, 62, 83, 104, 125, 146, 167, 187, 208, 229, 250, 271, 292, 312, 333, 354, 375, 396, 417, 437, 458, 479, 500, 521, 542, 562, 583, 604, 625, 646, 667, 687, 708, 729, 750, 771, 792, 813, 833, 854, 875, 896, 917, 938, 958, 979,1000,1021,1042,1063,1083,1104,1125,1146,1167,1188,1208,1229,1250];
		
		var dateStart = moment($scope.employee.first_day,$rootScope.dateFormat).subtract(1, 'days');
		var dateEnd = moment($scope.terminal_date,$rootScope.dateFormat);
		
		var years = dateEnd.diff(dateStart, 'years');
		dateEnd.subtract(years,'years');
		
		var months = dateEnd.diff(dateStart, 'months');
		dateEnd.subtract(months,'months');
		
		var days = dateEnd.diff(dateStart, 'days');
		
		days -= Math.floor($scope.lwop);
		
		while(days<0){
			months--;
			days += 30;
		}
		while(months<0){
			years--;
			months += 12;
		}
		
		var leaveEarned = 15000*years + 1250*months + creditByHalfDay[2*days];
		//	#credits_earned
		
		//	Credits Used
		var creditsUsed = 0;
		for(var i=0;i<$scope.leaves.length;i++){
			var leave = $scope.leaves[i];
			for(var j=0;j<leave.date_ranges.length;j++){
				var range = leave.date_ranges[j];
				if(moment(range.end_date,$rootScope.dateFormat).isSameOrBefore(moment($scope.terminal_date,$rootScope.dateFormat))){
					creditsUsed += range.hours*125 + range.minutes*125/60;		// Nasasama sa bilang yung mga without pay: dapat hindi
				}
			}
		}
		//	#credits_used
		
		var credits = 2*leaveEarned;
		credits -= creditsUsed;
		var salary = 100*$scope.employee.salary;
		var constantFactor = 0.0481927;
		
		var tlb = salary * credits * constantFactor;
		
		return (tlb/100000).toFixed(2);
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
	$scope.events = [];

	$scope.leaveDateRangeTemplate = {
		start_date:'',
		end_date:'',
		hours:0,
		minutes:0
	};

    $scope.init = function(employees="",employee=null,events=""){
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
        $scope.addOrDeleteRange(0);
		$scope.events = events==""?$scope.events:events;
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
        //Set css styling for leaves (ng-class not working :c )
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

    var getTotalDays = function(index){
        var days =  Math.round(moment($scope.leave.date_ranges[index].end_date).diff($scope.leave.date_ranges[index].start_date,'days'))+1;
		console.log($scope.leave);
		if($scope.leave.info.type=="Maternity") return days;
        //Removing weekends and holidays
        var startDate = moment($scope.leave.date_ranges[index].start_date,$rootScope.dateFormat).clone();
        while(startDate.isSameOrBefore($scope.leave.date_ranges[index].end_date,'days')){
            if(startDate.day()===0 || startDate.day()===6){ //0 means sunday, 6 means saturday
                days--;
            }else{
				var events = $scope.events;
				for(var i=0;i<events.length;i++){
					if( startDate.isSameOrAfter(moment(new Date(events[i].start),$rootScope.dateFormat),'day') && startDate.isSameOrBefore(moment(new Date(events[i].end),$rootScope.dateFormat),'day') )
						days--;
				}
			}
            startDate.add(1,'days');
        }
        return days;
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

	$scope.addOrDeleteRange = function(action,index=-1){
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
});
