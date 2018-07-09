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

app.controller('employee_display',function($scope,$rootScope,$window,$timeout){
    $scope.employee = {};
    $scope.leaves = [];
    $scope.bal_date = '';
	$scope.terminal_date = '';
	$scope.lwop = [];	/* 0: total lwop; 1: lwop due to currV<0 */
	$scope.filter = {every:true,vacation:true,sick:true,maternity:true,paternity:true,others:true};
    $scope.computations = {
        vacation:[/*{amount:number,remarks:'',date:date}*/],
        sick:[],
        bal_history:{}
    };
    $scope.computationsCopy = {};
    $scope.init = function(employee,leaves){
        $scope.employee = employee;
        $scope.leaves = leaves;
        $scope.employee.credits = {
            sick:0,
            vacation:0
        };
        $scope.bal_date = moment().subtract(1,'month').endOf('month');
        //Sort Leaves
        $scope.sortAndFormatLeaves();

        $scope.sick_bal_date = moment().endOf("month");
        $scope.vac_bal_date = moment().endOf("month");
        $scope.employee.first_day = moment($scope.employee.first_day).format($rootScope.dateFormat);
        console.log(leaves);
    }

    $scope.sortAndFormatLeaves = function(format){
        $scope.leaves.sort(function(a,b){
            return moment(b.date_ranges[b.date_ranges.length-1].start_date).diff(moment(a.date_ranges[a.date_ranges.length-1].start_date));
        });

        for(var i = 0 ; i<$scope.leaves.length ; i++){
			var leave = $scope.leaves[i];
            for(var j = 0 ; j<leave.date_ranges.length ; j++){
				var date_range = leave.date_ranges[j];
				date_range.start_date = moment(date_range.start_date).format($rootScope.dateFormat);
				date_range.end_date = moment(date_range.end_date).format($rootScope.dateFormat);
                date_range.credits = parseFloat((date_range.hours/8+ $rootScope.minutesToCredits(date_range.minutes) ).toFixed(3));
			}
        }
    }

	$scope.openLeaveModal = function(index = null){
        if(index == null){
    		$scope.$broadcast('openLeaveModal',null);
        }else{
            $scope.$broadcast('openLeaveModal',$scope.leaves[index]);
        }
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
            $rootScope.baseURL+"/employee/leaverecords",
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

    $scope.showComputationsModal = function(){
        $scope.computationsCopy = angular.copy($scope.computations);
        angular.element('#computationsModal').modal('show');
    }

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
        $scope.computations = {vacation:[],sick:[],bal_history:{}};
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

		var lastDay = moment(enDate,$rootScope.dateFormat);
		var isDistinctEnd = true;
		if(lastDay.isSame(lastDay.clone().endOf('month'), 'day')){ isDistinctEnd=false;}

		var currV = Math.floor(Number($scope.employee.vac_leave_bal)*1000);
		var currS = Math.floor(Number($scope.employee.sick_leave_bal)*1000);
		var dateEnd = lastDay.clone().endOf('month');
		var dateStart = moment($scope.employee.first_day,$rootScope.dateFormat).clone();
		var lwop = 0, wopCtr = 0; // Leave Without Pay
		var fLeave = 0, spLeave = 0, pLeave = 0; // Forced Leave, Special Priviledge Leave, Parental Leave
		var monetized = false;
		// First Month Computation
		if(dateStart.isSame(dateStart.clone().startOf('month'),'day')  &&  currV!=0 && currS!=0){ }else{
			fLeave=5000; spLeave=3000; pLeave=7000;
			var firstMC = Math.abs(dateStart.clone().endOf('month').diff(dateStart, 'days'))+1;
			firstMC = creditByHalfDay[2*firstMC];
			currV += firstMC; currS += firstMC;
            $scope.computations.vacation.push({amount:firstMC,remarks:'First month computation',date:dateStart.clone().endOf('month').format('MMMM DD, YYYY')});
            $scope.computations.sick.push({amount:firstMC,remarks:'First month computation',date:dateStart.clone().endOf('month').format('MMMM DD, YYYY')});
			dateStart.add(1,'month');
		}
		// #first_month_computation


		// Computation For Months Other Than The First
		while(dateStart<dateEnd){
			if(moment(dateStart).month()==0){
				fLeave=5000;
				spLeave=3000;
				pLeave=7000;
				monetized=false;
			}
			var mLWOP = 0;	// month's without pays
			for(var i=0;i<$scope.leaves.length;i++){
				var leave = $scope.leaves[i];
				for(var j=0;j<leave.date_ranges.length;j++){
					var range = leave.date_ranges[j];
					if( moment(range.end_date,$rootScope.dateFormat).isBefore(dateStart.clone().startOf('month')) ||  moment(range.start_date,$rootScope.dateFormat).isAfter(dateStart.clone().endOf('month')) )
						continue;
					var creditUsed = $scope.getCreditEquivalent(leave.info.type,range)*1000;

					//	For testing only
					if( moment(range.end_date,$rootScope.dateFormat).isAfter(lastDay) ){
						continue;
					}
					//	#for_testing_only

					if(leave.info.is_without_pay){
						if(leave.info.type=="Sick")
							lwop += creditUsed;
						else
							mLWOP += creditUsed;
						continue;
					}

					if( leave.info.type=="Vacation"||leave.info.type.toLowerCase().includes('force')||leave.info.type.toLowerCase().includes('mandatory') ){
						//	Vacation and Forced Leaves
						currV -= creditUsed;
						fLeave -= creditUsed;
                        $scope.computations.vacation.push({amount:-creditUsed,remarks:'Vacation and Forced Leaves',date:range.start_date+'-'+range.end_date});
					}else if(leave.info.type=="Undertime"){
						currV -= creditUsed;
					}else if(leave.info.type=="Sick"){
						//	Sick Leaves
						currS -= creditUsed;
                        $scope.computations.sick.push({amount:-creditUsed,remarks:'Sick Leaves'});
					}else if(leave.info.type.toLowerCase().includes('monet')){
						// Temporal Solution for Monetization of Leaves
						monetized=true;
						currV -= creditUsed;
                        $scope.computations.vacation.push({amount:-creditUsed,remarks:'Monetization'});
						if(currV<5000){
							if(!leave.info.type.toLowerCase().includes('special')){
								$rootScope.showCustomModal('Error','Limit for leave monetization exceeded.',function(){angular.element('#customModal').modal('hide');},function(){});
							}else{
								currV -= 5000;
                                $scope.computations.vacation.push({amount:-5000,remarks:'Monetization'});
								if(currS+currV<0) $rootScope.showCustomModal('Error','Limit for special leave monetization exceeded.',function(){angular.element('#customModal').modal('hide');},function(){});
								else{
                                    currS += currV;
                                    $scope.computations.sick.push({amount:currV,remarks:'Monetization'});
                                }
							}
                            $scope.computations.vacation.push({amount:-currV+5000,remarks:'Monetization'});
							currV=5000;
						}
						// #temporal_solution_for_monetization_of_leaves
					}else if(leave.info.type.toLowerCase().includes('spl') || leave.info.type.toLowerCase().includes('special')){
						//	Special Priviledge Leaves
						spLeave -= creditUsed;
						if(spLeave<0){
							currV+=spLeave;
                            $scope.computations.vacation.push({amount:spLeave,remarks:'Special Priviledge Leave'});
							spLeave=0;
						}
					}else if(leave.info.type.toLowerCase().includes('parental')){
						//	Parental Leaves	(For Solo Parents)
						pLeave -= creditUsed;
						if(pLeave<0){
							currV+=pLeave;
                            $scope.computations.vacation.push({amount:pLeave,remarks:'Parental Leave'});
							pLeave=0;
						}
					}
				}
			}
			if(currS<0){// When the employee is absent due to sickness and run out of sick leave
                $scope.computations.vacation.push({amount:currS,remarks:'Absent due to sickness and out of sick leave'});
                $scope.computations.sick.push({amount:-currS,remarks:'Absent due to sickness and out of sick leave'});
				currV += currS;
				fLeave += currS;
				currS = 0;
			}
			if(currV<0 || mLWOP>0){// Employee incurring absence without pay
				var cpd = 1.25/30; // Credit per day: ( 1.25 credits per month )/( 30 days per month )
				var notPresent = mLWOP;
				if(currV<0){
					notPresent += Math.abs(currV);
					wopCtr += Math.abs(currV);
				}
				var absent = Math.floor(notPresent/500);
				var rem = Math.floor(notPresent%500);
				lwop += notPresent;
				if(dateStart.isSame(dateEnd,'month') && isDistinctEnd){
					absent += 2*Math.abs(lastDay.clone().diff(lastDay.clone().endOf('month'),'days'));
				}
				if(currV<0)
					currV=0;
                $scope.computations.vacation.push({amount:-currV+Math.floor(creditByHalfDay[60-absent]-(rem*cpd)),remarks:'Absence without pay'});
                $scope.computations.sick.push({amount:Math.floor(creditByHalfDay[60-absent]-(rem*cpd)),remarks:'Absence without pay'});
				currV += Math.floor(creditByHalfDay[60-absent]-(rem*cpd));
				currS += Math.floor(creditByHalfDay[60-absent]-(rem*cpd));
			}else if(dateStart.isSame(dateEnd,'month') && isDistinctEnd){
				var lastCredit = Math.floor(creditByHalfDay[60-2*Math.abs(lastDay.clone().diff(lastDay.clone().endOf('month'),'days'))]);
                $scope.computations.vacation.push({amount:lastCredit,remarks:'Last Credit'});
                $scope.computations.sick.push({amount:lastCredit,remarks:'Last Credit'});
				currV += lastCredit;
				currS += lastCredit;
			}else{
				currV += 1250;
				currS += 1250;
                $scope.computations.vacation.push({amount:1250,remarks:'Accumulation',date:dateStart.clone().endOf('month').format('MMMM DD, YYYY')});
                $scope.computations.sick.push({amount:1250,remarks:'Accumulation',date:dateStart.clone().endOf('month').format('MMMM DD, YYYY')});
			}
			if(moment(dateStart).month()==11 && fLeave>0 && ( monetized || currV>10000 ) ){
                currV = currV-fLeave;
                $scope.computations.vacation.push({amount:-fLeave,remarks:'Forced Leave'});
            }
            $scope.computations.bal_history[dateStart.clone().endOf('month').format('YYYY-MM-DD')]={vac:currV, sick:currS};
			dateStart.add(1,'month');
		}
		// #computation_for_other_months
		$scope.lwop[0] = lwop/1000;
		$scope.lwop[1] = wopCtr/1000;
        return [(currV/1000).toFixed(3),(currS/1000).toFixed(3)];
    }

    $scope.balDateSet = function(){
        $scope.bal_date = moment($scope.bal_date).endOf('month');
        $rootScope.longComputation(this,'balance',$scope.getBalance);
    }

    $scope.getDeductedCredits = function(type,date_range,withoutPay){
        console.log(type);
		if(!withoutPay && (type=='Vacation'||type=='Sick'||type.toLowerCase().includes('force')||type.toLowerCase().includes('mandatory')||type.toLowerCase().includes('monet')||type=='Undertime')){
			return $scope.getCreditEquivalent(type,date_range);
		}else{
			return 0;
		}
    }

	$scope.getCreditEquivalent = function(type,date_range){
		var HDayEquiv = [0,0.125,0.250,0.375,0.500,0.625,0.750,0.875,1.000];
		var MDayEquiv = [0,0.002,0.004,0.006,0.008,0.010,0.012,0.015,0.017,0.019,0.021,0.023,0.025,0.027,0.029,0.031,0.033,0.035,0.037,0.040,0.042,0.044,0.046,0.048,0.050,0.052,0.054,0.056,0.058,0.060,0.062,0.065,0.067,0.069,0.071,0.073,0.075,0.077,0.079,0.081,0.083,0.085,0.087,0.090,0.092,0.094,0.096,0.098,0.100,0.102,0.104,0.106,0.108,0.110,0.112,0.115,0.117,0.119,0.121,0.123,0.125];
		var hours = date_range.hours;
		var minutes = date_range.minutes;
		hours += Math.floor(minutes/60);
		minutes = minutes%60;
		var credits = Math.floor(hours/8);
		hours = hours%8;
		credits += Number((HDayEquiv[hours] + MDayEquiv[minutes]).toFixed(3));

		// +date_range.minutes/(60*8)

		var start = moment(date_range.start_date,$rootScope.dateFormat).clone();
		var end = moment(date_range.end_date,$rootScope.dateFormat).clone();

		if(!type.toLowerCase().includes("monetization")){
			while(start<=end){
				if($scope.isHoliday(start))
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
		//console.log("\nStart\n");
		var balance = $scope.computeBal($scope.terminal_date);
		//console.log(balance);
		//console.log("\nEnd\n");
		var credits = Number(balance[0]) + Number(balance[1]);
		var constantFactor = 0.0481927;

		var tlb = salary * credits * constantFactor;
		//console.log(credits);
		//console.log("huhu");

		return (tlb/100).toFixed(2);
	}

	$scope.terminalBenefit2 = function(){
		console.log("Start");
		//	Credits Earned
		var creditByHalfDay = [0, 21, 42, 62, 83, 104, 125, 146, 167, 187, 208, 229, 250, 271, 292, 312, 333, 354, 375, 396, 417, 437, 458, 479, 500, 521, 542, 562, 583, 604, 625, 646, 667, 687, 708, 729, 750, 771, 792, 813, 833, 854, 875, 896, 917, 938, 958, 979,1000,1021,1042,1063,1083,1104,1125,1146,1167,1188,1208,1229,1250];

		var dateStart = moment($scope.employee.first_day,$rootScope.dateFormat).subtract(1, 'days');
		var dateEnd = moment($scope.terminal_date,$rootScope.dateFormat);

		var years = dateEnd.diff(dateStart, 'years');
		dateStart.add(years,'years');

		var months = dateEnd.diff(dateStart, 'months');
		dateStart.add(months,'months');

		var days = dateEnd.diff(dateStart, 'days');
		days -= Math.floor($scope.lwop[0]);

		while(days<0){
			months--;
			days += 30;
		}
		while(months<0){
			years--;
			months += 12;
		}

		var currV = Math.floor(Number($scope.employee.vac_leave_bal)*1000);
		var currS = Math.floor(Number($scope.employee.sick_leave_bal)*1000);

		var leaveEarned = 15000*years + 1250*months + creditByHalfDay[2*days];
		//	#credits_earned

		//	Credits Used
		var creditsUsed = 0;
		for(var i=0;i<$scope.leaves.length;i++){
			var leave = $scope.leaves[i];
			for(var j=0;j<leave.date_ranges.length;j++){
				var range = leave.date_ranges[j];
				if(!leave.info.is_without_pay && moment(range.end_date,$rootScope.dateFormat).isSameOrBefore(moment($scope.terminal_date,$rootScope.dateFormat))){
					creditsUsed += range.hours*125 + range.minutes*125/60;		// Nasasama sa bilang yung mga without pay: dapat hindi
				}
			}
		}
		creditsUsed -= $scope.lwop[1]*1000;
		//	#credits_used

		var credits = 2*leaveEarned + currV + currS;
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


/*Requires parent controller: employee_display*/

app.controller('employee_leave_records',function($scope,$rootScope){
    $scope.leave = {
        info:{},
        date_ranges:[]
    }
	$scope.events = [];
    var leaveReference = null; //Stores reference to the original leave (used when openning the leave modal)

	$scope.leaveDateRangeTemplate = {
		start_date:'',
		end_date:'',
		hours:0,
		minutes:0
	};

    $scope.init = function(events=null){
        $scope.addOrDeleteRange(0);
		$scope.events = events===null?$scope.events:events;
    }

	$scope.$on('openLeaveModal',function(event, leave=null){
        $scope.leave = {
            info:{},
            date_ranges:[]
        }
        if(leave !== null){
            leaveReference = leave;
    		$scope.leave = angular.copy(leave);
            //Formatting the passed leave
            for(var i = 0 ; i<$scope.leave.date_ranges.length ; i++){
                var date_range =  $scope.leave.date_ranges[i];
                date_range.start_date = moment(date_range.start_date,$rootScope.dateFormat);
                date_range.end_date = moment(date_range.end_date,$rootScope.dateFormat);
                date_range.hours = parseInt(date_range.hours);
                date_range.minutes = parseInt(date_range.minutes);
            }
            var validLeaves = ["Vacation","Sick","Maternity","Paternity"];
            if(validLeaves.indexOf($scope.leave.info.type)==-1){
                $scope.leave.info.type_others = $scope.leave.info.type;
                $scope.leave.info.type = 'Others';
            }
        }else{
            $scope.addOrDeleteRange(0);
        }
        /* Setting css styling for leave type radio group (ng-class is not working :c)*/
        var leaveTypes = ['Vacation','Sick','Maternity','Paternity','Others'];
        if($scope.leave.info.type){
            angular.element('#leaveType'+$scope.leave.info.type).addClass('active');
            var index = leaveTypes.indexOf($scope.leave.info.type);
            if(index>-1){
                leaveTypes.splice(index,1);
            }
        }
        for(var i = 0 ; i<leaveTypes.length ; i++){
            angular.element('#leaveType'+leaveTypes[i]).removeClass('active');
        }
        angular.element('#addOrEditLeaveModal').modal('show');
	});

    var getTotalDays = function(index){
        var days =  Math.round(moment($scope.leave.date_ranges[index].end_date).diff($scope.leave.date_ranges[index].start_date,'days'))+1;
		if($scope.leave.info.type=="Maternity") return days;
        //Removing weekends and holidays
        var startDate = moment($scope.leave.date_ranges[index].start_date,$rootScope.dateFormat).clone();
        while(startDate.isSameOrBefore($scope.leave.date_ranges[index].end_date,'days')){
            if(startDate.day()===0 || startDate.day()===6){ //0 means sunday, 6 means saturday
                days--;
            }else{
				var events = $scope.events;
				for(var i=0;i<events.length;i++){
					if( startDate.isSameOrAfter(moment(new Date(events[i].date),$rootScope.dateFormat),'day') && startDate.isSameOrBefore(moment(new Date(events[i].date),$rootScope.dateFormat),'day') )
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
			credits += $scope.leave.date_ranges[i].credits;
		}
		return credits;
	}

    $scope.updateCredits = function(date_range,type){
        var hours = date_range.hours;
        var minutes = date_range.minutes;
        var credits = date_range.credits;
        switch(type){
            case 'credits':
                hours = parseInt(credits*8);
                minutes = $rootScope.creditsToMinutes(credits);
                break;
            case 'time':
                credits = hours/8;
                credits += $rootScope.minutesToCredits(minutes);
                break;
            default:
                return;
        }
        date_range.hours = hours;
        date_range.minutes = minutes;
        date_range.credits = parseFloat(credits.toFixed(3));
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
        $scope.updateCredits($scope.leave.date_ranges[index],'time');
    }

	$scope.endDateSet = function(index){
		if(!$scope.leave.date_ranges[index].start_date){
			$scope.leave.date_ranges[index].start_date = $scope.leave.date_ranges[index].end_date;
		}
		$scope.leave.date_ranges[index].hours = getTotalDays(index)*8;
        $scope.updateCredits($scope.leave.date_ranges[index],'time');
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

    $scope.submit = function(addOrEdit){
        var data = angular.copy($scope.leave);
        data.info.emp_no = $scope.employee.emp_no; //$scope.employee is from parent

        var succMsg = '';
        var succFunc = function(response){};
        switch(addOrEdit){
            case 'add':
                succMsg = 'Successfully added leave.';
                succFunc = function(response){
                    $scope.leaves.push(response.leave); //$scope.leaves is from parent
                }
                data.action = 'add';
                break;
            case 'edit':
                succMsg = 'Successfully edited leave.';
                succFunc = function(response){
                    leaveReference.info = response.leave.info;
                    leaveReference.date_ranges = response.leave.date_ranges;
                }
                data.action='edit';
                break;
            default:
                return;
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
            $rootScope.baseURL+"/employee/leaveRecords",
            data,
            function(response){
                //returning date ranges to their original format
                for(var i = 0 ; i<$scope.leaves.length ; i++){
        			var leave = $scope.leaves[i];
                    for(var j = 0 ; j<leave.date_ranges.length ; j++){
        				var date_range = leave.date_ranges[j];
        				date_range.start_date = moment(date_range.start_date,$rootScope.dateFormat);
        				date_range.end_date = moment(date_range.end_date,$rootScope.dateFormat);
        			}
                }
                succFunc(response);
                $scope.sortAndFormatLeaves(); //From parent
                $rootScope.showCustomModal('Success',succMsg,
                    function(){
                        angular.element('#customModal').modal('hide');
                        angular.element('#addOrEditLeaveModal').modal('hide');
                    },
                    function(){
                        angular.element('#addOrEditLeaveModal').modal('hide');
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

/*Requires employee_display controller as parent*/
app.controller('employee_statistics',function($scope,$rootScope){
    $scope.statistics={
        data:[1,3,5,6,1,5,-2,5]
    };
    $scope.statistics.labels = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $scope.bal_history = {};
    $scope.getLeaves = function(startDate,endDate,bal_history){
        var result = [];
        startDate = moment(startDate);
        endDate = moment(endDate);
        console.log(startDate.format('YYYY-MM-DD')+' '+endDate.format('YYYY-MM-DD'));
        var currDate = startDate.clone().endOf('month');
        while(currDate.isSameOrBefore(endDate,'month')){
            var bal = bal_history[currDate.clone().format('YYYY-MM-DD')];
            if(bal)
                result.push(bal);
            currDate.add(1,'months').endOf('month');
            console.log('test');
        }
        var vac=[];
        var sick=[];
        console.log(result);
        for(var i = 0 ; i<result.length ; i++){
            console.log(result[i]);
            vac.push(result[i].vac/1000);
            sick.push(result[i].sick/1000);
        }
        $scope.statistics.data = [vac,sick];
        return result;
    }

    $scope.$on('openStatisticsModal',function(event){
        $rootScope.longComputation($scope,'bal_history',function(){
            $scope.computeBal(moment().add(-1,'month').endOf('month')); //computations history gets set internally in computeBal which is in employee_display
            $scope.getLeaves('2018-01-01','2018-07-01',$scope.computations.bal_history)
            return angular.copy($scope.computations.bal_history);
        });
        console.log(moment('1900-01-01').endOf('month').valueOf());
    });

});
