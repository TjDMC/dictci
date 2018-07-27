/**
Table of Contents
1.0 Employee Home Page - employee_nav
2.0 Employee Addition - employee_add
3.0 Employee Display Page - employee_display
    3.1 Initialization
    3.2 Leave Credit Computation
    3.3 Record of Leaves
    3.4 Monetization
    3.5 Terminal Benefit Computations
    3.6 Leave History
4.0 Employee Leave Records - employee_leave_records
5.0 Employee Statistics -  employee_statistics
**/

//<editor-fold> Section 1.0 Employee Home Page
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
			if(employees[i].middle_name==null){
				employees[i].middle_name = '';
			}
			result.push({
				emp_no:employees[i].emp_no,
				emp_name:employees[i].surname+", "+employees[i].first_name+" "+employees[i].middle_name,
				string:employees[i].emp_no+" - "+employees[i].surname+", "+employees[i].first_name+" "+employees[i].middle_name
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
//</editor-fold> end Employee Home Page*/

//<editor-fold> Section 2.0 Employee Addition
app.controller('employee_add',function($scope,$rootScope,$window){
    $scope.employee={};
    $scope.add = function(){
        $scope.employee.first_day_compute = moment($scope.employee.first_day_compute).format('YYYY-MM-DD');
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
//</editor-fold> end Employee Addition

//<editor-fold> Section 3.0 Employee Display
app.controller('employee_display',function($scope,$rootScope,$window,$timeout){
    //#region Section 3.1 Initialization/
	$scope.edit=true;
    $scope.employee = {};
    $scope.leaves = [];
    $scope.init = function(employee,leaves){
        $scope.employee = employee;
		$scope.employee.vac_leave_bal = parseFloat($scope.employee.vac_leave_bal);
		$scope.employee.sick_leave_bal = parseFloat($scope.employee.sick_leave_bal);
        $scope.employee.highest_salary = parseFloat($scope.employee.highest_salary);
        $scope.leaves = leaves;
        $scope.employee.credits = {
            sick:0,
            vacation:0
        };
		if(moment().subtract(1,'month').endOf('month').isBefore(moment(employee.first_day,'YYYY-MM-DD'))){
			$scope.bal_date = moment().endOf('month');
		}else{
			$scope.bal_date = moment().subtract(1,'month').endOf('month');
		}
        //Sort Leaves
        $scope.sortAndFormatLeaves();
        $scope.employee.first_day_compute = moment($scope.employee.first_day_compute);
        $scope.employee.first_day_employ = moment($scope.employee.first_day_employ);
    }

	$scope.startDateRender = function($view,$dates,monet = false){
        var activeDate = $scope.employee.first_day_compute.clone().subtract(1, $view).add(1, 'minute');

        $dates.filter(function(date){
            return monet ? date.localDateValue() <= activeDate.valueOf() || date.localDateValue() > moment().endOf('month') : date.localDateValue() <= activeDate.valueOf();
        }).forEach(function(date){
            date.selectable = false;
        });
    }

    $scope.sortAndFormatLeaves = function(){
        $scope.leaves.sort(function(a,b){
            return moment(b.date_ranges[b.date_ranges.length-1].start_date).diff(moment(a.date_ranges[a.date_ranges.length-1].start_date));
        });

        for(var i = 0 ; i<$scope.leaves.length ; i++){
			var leave = $scope.leaves[i];
            for(var j = 0 ; j<leave.date_ranges.length ; j++){
				var date_range = leave.date_ranges[j];
				date_range.start_date = moment(date_range.start_date);
				date_range.end_date = moment(date_range.end_date);
                date_range.credits = parseFloat(date_range.credits.toFixed(3));
                date_range.hours = $rootScope.creditsToTime(date_range.credits).hours;
                date_range.minutes = $rootScope.creditsToTime(date_range.credits).minutes;
			}
        }
    }

    $scope.editEmployee = function(){
        $scope.clone_employee.old_emp_no = $scope.employee.emp_no;
        $scope.clone_employee.first_day_employ = moment($scope.clone_employee.first_day_employ).format('YYYY-MM-DD');
        $scope.clone_employee.first_day_compute = moment($scope.clone_employee.first_day_compute).format('YYYY-MM-DD');
        $rootScope.post(
            $rootScope.baseURL+'employee/edit',
            $scope.clone_employee,
            function(response){
                $rootScope.showCustomModal('Success',response.msg,
                    function(){
                        $window.location.href=$rootScope.baseURL+'employee/display/'+$scope.clone_employee.emp_no;
                    },
                    function(){
                        $window.location.href=$rootScope.baseURL+'employee/display/'+$scope.clone_employee.emp_no;
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
    $scope.deleteEmployee = function(){
        $rootScope.post(
            $rootScope.baseURL+'employee/delete',
            {emp_no:$scope.employee.emp_no,password:$scope.password},
            function(response){
                $rootScope.showCustomModal('Success',response.msg,
                    function(){
                        $window.location.href = $rootScope.baseURL+'employee';
                    },
                    function(){
                        $window.location.href = $rootScope.baseURL+'employee';
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
    $scope.showEditEmployeeModal = function(){
        $scope.clone_employee = angular.copy($scope.employee);
        angular.element('#editEmployeeModal').modal('show');
    }
    //#endregion end Initialization

    //#region Section 3.2 Leave Credit Computation
    $scope.computations = {
        factors:[/*type:(either vacation or sick),amount:number,start_date:(date),end_date:(date)*/],
        bal_history:{},
        year_filter:moment()
    };
    $scope.bal_date = '';
	$scope.lwop = [];		/* 0: total lwop; 1: lwop due to currV<0 */
	$scope.cEnjoyed = {};	/* 0: vacation; 1: sick */
	$scope.cEarned = {};		/* 0: vacation; 1: sick */
	$scope.totalDays = {years:0,months:0,days:0};
    $scope.getBalance = function(){
		var hold = $scope.computeBal($scope.bal_date);
        $scope.computations.year_filter = moment();
        $scope.computations.table = $scope.getComputationsTable(moment().year());
		return "Vacation: " + hold[0] + ", Sick: " + hold[1];
	}
	$scope.computeBal = function(enDate){
		/*
				The numbers are converted to integer for computational accuracy. Displayed and saved values are converted back to three(3) decimal places
		*/
		// As per MC No. 14, s. 1999
		var creditByHalfDay = [0, 21, 42, 62, 83, 104, 125, 146, 167, 187, 208, 229, 250, 271, 292, 312, 333, 354, 375, 396, 417, 437, 458, 479, 500, 521, 542, 562, 583, 604, 625, 646, 667, 687, 708, 729, 750, 771, 792, 813, 833, 854, 875, 896, 917, 938, 958, 979,1000,1021,1042,1063,1083,1104,1125,1146,1167,1188,1208,1229,1250];

		var leaves = angular.copy($scope.leaves);

		var lastDay = moment(enDate);
		var isDistinctEnd = true;
		if(lastDay.isSame(lastDay.clone().endOf('month'), 'day')){ isDistinctEnd=false;}

		var enjoyed = {v:0,s:0};
		var earned = {v:0,s:0};

		var currV = Math.floor(Number($scope.employee.vac_leave_bal)*1000);
		var currS = Math.floor(Number($scope.employee.sick_leave_bal)*1000);
		var dateEnd = lastDay.clone().endOf('month');
		var dateStart = $scope.employee.first_day_compute.clone().subtract(1,'days');
		var lwop = 0, wopCtr = 0; // Leave Without Pay
		var fLeave = 0, spLeave = 0, pLeave = 0; // Forced Leave, Special Priviledge Leave, Parental Leave
		var monetized = false;

		var years = lastDay.clone().diff(dateStart.clone(), 'years');
		dateStart.add(years,'years');

		var months = lastDay.clone().diff(dateStart.clone(), 'months');
		dateStart.add(months,'months');

		var days = lastDay.clone().diff(dateStart.clone(), 'days');

		$scope.totalDays.years = years;
		$scope.totalDays.months = months;
		$scope.totalDays.days = days;

		dateStart =$scope.employee.first_day_compute.clone();

		//(Side effect) Storage for computation factors
        $scope.computations.factors=[];
        $scope.computations.bal_history={};

		// First Month Computation
		if(dateStart.isSame(dateStart.clone().startOf('month'),'day')){

		}else{
			fLeave=5000; spLeave=3000; pLeave=7000; monetized=false;
			var firstMC;
			if(dateStart.isSame(dateEnd,'month') && isDistinctEnd){
				firstMC = Math.abs(lastDay.clone().diff(dateStart, 'days'))+1;
			}else{
				firstMC = Math.abs(dateStart.clone().endOf('month').diff(dateStart, 'days'))+1;
			}
			firstMC = Math.round(firstMC)*1000;
			// Consider absences as without pay
			for(var i=0;i<leaves.length;i++){
				var leave = leaves[i];
				for(var j=0;j<leave.date_ranges.length;j++){
					var range = leave.date_ranges[j];
					if(range.end_date.isBefore(dateStart.clone().startOf('month')) || range.start_date.isAfter(dateStart.clone().endOf('month')) || range.end_date.isAfter(lastDay) )
						continue;
					var creditUsed = (range.credits-range.holiday_conflicts<0?0:range.credits-range.holiday_conflicts)*1000;
					if( leave.info.type=="Vacation"||leave.info.type.toLowerCase().includes('force')||leave.info.type.toLowerCase().includes('mandatory')||leave.info.type=="Sick"||leave.info.type=="Undertime" ){
						firstMC -= creditUsed;
						lwop += creditUsed;
						wopCtr += creditUsed;

                        var leave_info_copy = angular.copy(leave.info);
                        var range_copy  = angular.copy(range);
                        range_copy.credits *= 1000;
                        leave_info_copy.is_without_pay = true;
                        $scope.computations.factors.push({type:'Vacation and Sick',amount:{v:0,s:0},balance:{v:0,s:0},remarks:'Vacation, Forced, Mandatory, and Sick Leaves (WOP)',date:range.start_date.clone(),leave_info:leave_info_copy,date_range:range_copy});
					}else if(leave.info.type.toLowerCase().includes('monet')){
						$rootScope.showCustomModal('Error','Employee may not monetize yet.',function(){angular.element('#customModal').modal('hide');},function(){});
					}else if(leave.info.type.toLowerCase().includes('spl') || leave.info.type.toLowerCase().includes('special')){
						//	Special Priviledge Leaves
						spLeave -= creditUsed;
						if(spLeave<0){
							firstMC+=spLeave;
							spLeave=0;
						}
                        $scope.computations.factors.push({type:'Special Priviledge',amount:{v:0,s:0},balance:{v:0,s:0},remarks:'Special Priviledge Leave (WOP)',date:range.start_date.clone(),leave_info:leave.info,date_range:range});
					}else if(leave.info.type.toLowerCase().includes('parental')){
						//	Parental Leaves	(For Solo Parents)
						pLeave -= creditUsed;
						if(pLeave<0){
							firstMC+=pLeave;
							pLeave=0;
						}
                        $scope.computations.factors.push({type:'Parental Leave',amount:{v:0,s:0},balance:{v:0,s:0},remarks:'Parental Leave (WOP)',date:range.start_date.clone(),leave_info:leave.info,date_range:range});
					}
					leave.date_ranges.splice(j,1);
					j--;
				}
				if(leave.date_ranges.length==0){
					leaves.splice(i,1);
					i--;
				}
			}
			var keep = (500 - (firstMC%500))%500;
			firstMC = Math.floor(firstMC/500);
			firstMC = creditByHalfDay[firstMC] - (keep*1.25/30);
			currV += firstMC; currS += firstMC;
			earned.v += firstMC; earned.s += firstMC;
            $scope.computations.factors.push({type:'Vacation and Sick',amount:{v:firstMC,s:firstMC},balance:{v:currV,s:currS},remarks:'End of Month Accumulation.',date:dateStart.clone().endOf('month')});
            $scope.computations.bal_history[dateStart.clone().endOf('month').format('YYYY-MM-DD')] = {vac:currV,sick:currS};
            dateStart.add(1,'month');
		}
		// #first_month_computation

		earned.v = currV;
		earned.s = currS;

		// Computation For Months Other Than The First
		while(dateStart<dateEnd){
			if(moment(dateStart).month()==0){
				fLeave=5000;
				spLeave=3000;
				pLeave=7000;
				monetized=false;
			}
			var mLWOP = 0;	// month's without pays
			for(var i=leaves.length-1;i>=0;i--){
				var leave = leaves[i];
				for(var j=leave.date_ranges.length-1;j>=0;j--){
					var range = leave.date_ranges[j];
					if( range.end_date.isBefore(dateStart.clone().startOf('month')) ||  range.start_date.isAfter(dateStart.clone().endOf('month')) )
						continue;
					var creditUsed = (range.credits-range.holiday_conflicts<0?0:range.credits-range.holiday_conflicts)*1000;

					//	For testing only
					if( range.end_date.isAfter(lastDay) ){
						continue;
					}
					//	#for_testing_only

					if(leave.info.is_without_pay){
						if(leave.info.type=="Sick"){
							lwop += creditUsed;
                            $scope.computations.factors.push({type:'Sick',amount:{v:0,s:0},balance:{v:currV,s:currS},remarks:'Vacation and Forced Leaves (WOP)',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
                        }else{
							mLWOP += creditUsed;
                            $scope.computations.factors.push({type:leave.info.type,amount:{v:0,s:0},balance:{v:currV,s:currS},remarks:'Vacation and Forced Leaves (WOP)',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
                        }
						leave.date_ranges.splice(j,1);
						continue;
					}

					if( leave.info.type=="Vacation"||leave.info.type.toLowerCase().includes('force')||leave.info.type.toLowerCase().includes('mandatory') ){
						//	Vacation and Forced Leaves
						currV -= creditUsed;
						fLeave -= creditUsed;
						enjoyed.v += creditUsed;
                        $scope.computations.factors.push({type:'Vacation',amount:{v:-creditUsed,s:0},balance:{v:currV,s:currS},remarks:'Vacation and Forced Leaves',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
					}else if(leave.info.type=="Undertime"){
						currV -= creditUsed;
						enjoyed.v += creditUsed;
                        $scope.computations.factors.push({type:'Vacation',amount:{v:-creditUsed,s:0},balance:{v:currV,s:currS},remarks:'Undertime',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
					}else if(leave.info.type=="Sick"){
						//	Sick Leaves
						currS -= creditUsed;
						enjoyed.s += creditUsed;
                        $scope.computations.factors.push({type:'Sick',amount:{v:0,s:-creditUsed},balance:{v:currV,s:currS},remarks:'Sick Leaves',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
					}else if(leave.info.type.toLowerCase().includes('monet')){
						// Temporal Solution for Monetization of Leaves
						monetized=true;
						currV -= creditUsed;
						enjoyed.v += creditUsed;
						if(currV<5000){
							if(!leave.info.type.toLowerCase().includes('special')){
								$rootScope.showCustomModal('Error','Limit for leave monetization exceeded.',function(){angular.element('#customModal').modal('hide');},function(){});
							}else{
								currV -= 5000;
								if(currS+currV<0) $rootScope.showCustomModal('Error','Limit for special leave monetization exceeded.',function(){angular.element('#customModal').modal('hide');},function(){});
								else{/* currV here is negative */
                                    currS += currV;
									enjoyed.v += currV;
									enjoyed.s -= currV;
                                    $scope.computations.factors.push({type:'Vacation and Sick',amount:{s:currV,v:creditUsed-currS},balance:{s:currS,v:5000},remarks:'Monetization',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
                                }
							}
							currV=5000;
						}else{
                            $scope.computations.factors.push({type:'Vacation',amount:{s:0,v:-creditUsed},balance:{v:currV,s:currS},remarks:'Monetization',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
                        }
						// #temporal_solution_for_monetization_of_leaves
					}else if(leave.info.type.toLowerCase().includes('spl') || leave.info.type.toLowerCase().includes('special')){
						//	Special Priviledge Leaves
						spLeave -= creditUsed;
						if(spLeave<0){
							currV+=spLeave;
							enjoyed.v -= spLeave;
                            $scope.computations.factors.push({type:'Special Priviledge',amount:{v:spLeave,s:0},balance:{s:currS,v:currV},remarks:'Special Priviledge Leave',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
							spLeave=0;
						}else{
                            $scope.computations.factors.push({type:'Special Priviledge',amount:{v:0,s:0},balance:{s:currS,v:currV},remarks:'Special Priviledge Leave',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
                        }
					}else if(leave.info.type.toLowerCase().includes('parental')){
						//	Parental Leaves	(For Solo Parents)
						pLeave -= creditUsed;
						if(pLeave<0){
							currV+=pLeave;
							enjoyed.v -= pLeave;
                            $scope.computations.factors.push({type:'Parental Leave',amount:{v:pLeave,s:0},balance:{s:currS,v:currV},remarks:'Parental Leave',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
							pLeave=0;
						}else{
                            $scope.computations.factors.push({type:'Parental Leave',amount:{v:0,s:0},balance:{s:currS,v:currV},remarks:'Parental Leave',date:range.start_date.clone(),date_range:range,leave_info:leave.info});
                        }
					}
					leave.date_ranges.splice(j,1);
				}
				if(leave.date_ranges.length==0){
					leaves.splice(i,1);
				}
			}
			if(moment(dateStart).month()==11){
				console.log(fLeave);
				console.log(moment(dateStart).month()==11 && fLeave>0 && ( monetized || currV>10000 ) && ( lastDay.clone().month()!=11 || !isDistinctEnd ));
			}
			if(moment(dateStart).month()==11 && fLeave>0 && ( monetized || currV>10000 ) && ( lastDay.clone().month()!=11 || !isDistinctEnd )){
                currV = currV-fLeave;
				enjoyed.v += fLeave;
                $scope.computations.factors.push({type:'Vacation',amount:{v:-fLeave,s:0},balance:{v:currV,s:currS},remarks:'Forced Leave',date:dateStart.clone().endOf('year')});
            }
			if(currS<0){// When the employee is absent due to sickness and run out of sick leave
				currV += currS;
				fLeave += currS;
				enjoyed.v -= currS;
				enjoyed.s += currS;
				currS = 0;
                $scope.computations.factors.push({type:'Vacation and Sick',amount:{v:currS,s:0},balance:{v:currV,s:0},remarks:'Sick leave balance is negative. Deducting credits from vacation.',date:dateStart.clone()});
			}
			if(currV<0 || mLWOP>0){// Employee incurring absence without pay
				var cpd = 1.25/30; // Credit per day: ( 1.25 credits per month )/( 30 days per month )
				var notPresent = mLWOP;
				if(currV<0){
					notPresent += Math.abs(currV);
					wopCtr += Math.abs(currV);
					enjoyed.v += currV;
				}
				var absent = Math.floor(notPresent/500);
				var rem = Math.floor(notPresent%500);
				lwop += notPresent;
				if(dateStart.isSame(dateEnd,'month') && isDistinctEnd){
					absent += 2*Math.abs(lastDay.clone().diff(lastDay.clone().endOf('month'),'days'));
				}
				if(currV<0){
					currV=0;
                    $scope.computations.factors.push({type:'Vacation',amount:{v:0,s:0},balance:{s:currS,v:currV},remarks:'Vacation leave balance is negative. Incurring absence without pay.',date:dateStart.clone().endOf('month')});
                }
				var tmp = Math.floor(creditByHalfDay[60-absent]-(rem*cpd));
				currV += tmp;
				currS += tmp;
				earned.v += tmp;
				earned.s += tmp;
                $scope.computations.factors.push({type:'Vacation and Sick',amount:{v:Math.floor(creditByHalfDay[60-absent]-(rem*cpd)),s:Math.floor(creditByHalfDay[60-absent]-(rem*cpd))},balance:{s:currS,v:currV},remarks:'End of Month Accumulation w/ Absence Without Pay',date:dateStart.clone().endOf('month')});
			}else if(dateStart.isSame(dateEnd,'month') && isDistinctEnd){
				var lastCredit = Math.floor(creditByHalfDay[60-2*Math.abs(lastDay.clone().diff(lastDay.clone().endOf('month'),'days'))]);
				currV += lastCredit;
				currS += lastCredit;
				earned.v += lastCredit;
				earned.s += lastCredit;
                $scope.computations.factors.push({type:'Vacation and Sick',amount:{v:lastCredit,s:lastCredit},balance:{s:currS,v:currV},remarks:'End of Month Accumulation',date:dateStart.clone().endOf('month')});
			}else{
				currV += 1250;
				currS += 1250;
				earned.v += 1250;
				earned.s += 1250;
                $scope.computations.factors.push({type:'Vacation and Sick',amount:{v:1250,s:1250},balance:{s:currS,v:currV},remarks:'End of Month Accumulation',date:dateStart.clone().endOf('month')});
			}
            $scope.computations.bal_history[dateStart.clone().endOf('month').format('YYYY-MM-DD')]={vac:currV, sick:currS};
			dateStart.add(1,'month');
		}
		// #computation_for_other_months

		$scope.lwop[0] = lwop/1000;
		$scope.lwop[1] = wopCtr/1000;

		$scope.cEarned.v = earned.v/1000;
		$scope.cEarned.s = earned.s/1000;

		$scope.cEnjoyed.v = enjoyed.v/1000;
		$scope.cEnjoyed.s = enjoyed.s/1000;

        return [(currV/1000).toFixed(3),(currS/1000).toFixed(3)];
    }
    $scope.balDateSet = function(){
        $scope.bal_date = moment($scope.bal_date).endOf('month');
        $rootScope.longComputation(this,'balance',$scope.getBalance);
    }
    $scope.getComputationsTable = function(year){
        var factorsCopy = angular.copy($scope.computations.factors);
        var table = factorsCopy.filter(function(factor){
            return factor.date.year() == year;
        });
        var months = {};
        for(var i = 0 ; i<12 ; i++){
            var monthName = moment(i+1,'MM').format('MMMM');
            months[monthName] = [];
            for(var j = 0 ; j<table.length ; j++){
                if(table[j].date.month()==i){
                    months[monthName].push(table[j]);
                    table.splice(j,1);
                    j--;
                }
            }
        }

        angular.forEach(months,function(month,key){
            if(month.length == 0){
                delete months[key];
            }
        });

        return months;
    }

    $scope.computationsDateRender = function($view,$dates){
        $dates.filter(function(date){
            return date.localDateValue()<$scope.employee.first_day_compute.clone().startOf('year').valueOf() || date.localDateValue() > $scope.bal_date.valueOf();
        }).forEach(function(date){
            date.selectable = false;
        });
    }
    //#endregion end Leave Credit Computation

    //#region Section 3.3 Record Of Leaves
    $scope.rol = {
        start_date:null,
        end_date:null,
        factors:{}
    }
    $scope.initRecordOfLeaves = function(){
        $scope.rol.start_date = moment().startOf('year').isBefore($scope.employee.first_day_compute,'months') ? $scope.employee.first_day_compute.clone():moment().startOf('year');
        $scope.rol.end_date = $scope.rol.start_date.clone().endOf('year');
        updateROL();
    }

    $scope.setAllTime = function(){
        $scope.rol.start_date = $scope.employee.first_day_compute.clone();
        $scope.rol.end_date = moment().endOf('year');
        updateROL();
    }

    var updateROL = function(){
        $rootScope.longComputation($scope.rol,'factors',function(){
            var ans = $scope.computeBal($scope.rol.end_date);
            return formatROL(angular.copy($scope.computations.factors));
        });
    }

    var formatROL = function(factors){
        var rol = {};
		var addToROL = function(rol,date,factor){ // assign a computation factor to a date (MMMM YYYY)
			if(rol.hasOwnProperty(date)){
				rol[date].push(factor);
			}else{
				rol[date] = [factor];
			}
		}
        var leaveROL = {}; //leaves are separated from otherROL because it needs to be formatted.
		var otherROL = {}; //storage for none-leave factors. to be added to rol after firstfactor is determined.
		var firstFactor = { //first factor to be displayed in the record of leaves. this displays the credit balance of the month preceding the selected starting date
			leaves_earned:{v:'',s:''},
			when_taken:'',
			leaves_taken:{v:'',s:''},
			undertime:{hour:'',min:'',total:''},
			without_pay:{hour:'',min:'',total:''},
			balance:{v:$scope.employee.vac_leave_bal.toFixed(3),s:$scope.employee.sick_leave_bal.toFixed(3)},
			remarks:'bal. as of '+$scope.employee.first_day_compute.format('MMM. DD, YYYY'),
			date:moment($scope.employee.first_day_compute).format('MMMM YYYY'),//not displayed
            eoma:true //not displayed
		};
        for(var i = 0;i<factors.length;i++){
            var factor = factors[i];
            if(!factor.leave_info && !factor.remarks.toLowerCase().includes("accumulation") && !factor.remarks.toLowerCase().includes("forced"))
                continue; //skip factors without leave info, not an end-of-month-accumulation, and not a forced leave
            factor.date = moment(factor.date);
            if(factor.leave_info){
                addToROL(leaveROL,factor.date.format('MMMM YYYY'),factor);
            }else if(factor.remarks.toLowerCase().includes("accumulation")){
				var eoma = { //end of month accumulation
                    leaves_earned:{v:(factor.amount.v/1000).toFixed(3),s:(factor.amount.s/1000).toFixed(3)},
                    when_taken:'',
                    leaves_taken:{v:'',s:''},
                    undertime:{hour:'',min:'',total:''},
                    without_pay:{hour:'',min:'',total:''},
                    balance:{v:(factor.balance.v/1000).toFixed(3),s:(factor.balance.s/1000).toFixed(3)},
                    remarks:'bal. As of '+factor.date.format('MMM. DD, YYYY'),
					date:factor.date.format('MMMM YYYY'),//not displayed,
                    eoma:true //not displayed
                };
				if(factor.date.isBefore($scope.rol.start_date,'month'))
					firstFactor = eoma;
                addToROL(otherROL,factor.date.format('MMMM YYYY'),eoma);
            }else if(factor.remarks.toLowerCase().includes("forced")){
                addToROL(otherROL,factor.date.format('MMMM YYYY'),{
					when_taken:factor.date.format('MMM. DD, YYYY'),
					leaves_taken:{v:(-factor.amount.v/1000).toFixed(3),s:(-factor.amount.s/1000).toFixed(3)},
					balance:{v:(factor.balance.v/1000).toFixed(3),s:(factor.balance.s/1000).toFixed(3)},
					remarks:'Forced Leave'
				});
            }
        }

		//formatting leaveROL
		var nLeaveROL = {};
        var resolvedNegatives = {}; //this stores the negative balances that have been resolved. it is used to prevent negative balances to be resolved again
		angular.forEach(leaveROL,function(factors,date){
			var leaveIDs = new Map();
            resolvedNegatives[date] = {v:0,s:0};
			for(var i=0;i<factors.length;i++){ //group together all date ranges with the same leave id
				var leave_id = factors[i].leave_info.leave_id;
				var start_date = factors[i].date_range.start_date.clone();
				var end_date = factors[i].date_range.end_date.clone();
				var when_taken = start_date.isSame(end_date,'day') ? start_date.date()+',' : start_date.date()+'-'+end_date.date()+',';

				var leaves_taken ={v:-factors[i].amount.v,s:-factors[i].amount.s};

				var undertime={hour:0,min:0,total:0};
				var ut_credits = factors[i].date_range.credits;
				if(factors[i].leave_info.type.toLowerCase() == 'undertime'){ //undertime
					undertime.total=factors[i].date_range.credits;
				}

				var without_pay={hour:0,min:0,total:0};
				if(factors[i].leave_info.is_without_pay){ //without pay
					without_pay.total=factors[i].date_range.credits;
				}

                var remarks = factors[i].leave_info.remarks ? factors[i].leave_info.remarks:"";
                if(factors[i].leave_info.type.toLowerCase().includes("special leave")) //remarks for special and parental leaves and monetization
                    remarks = "(Special)"+remarks;
                if(factors[i].leave_info.type.toLowerCase().includes("parental"))
                    remarks = "(Parental)"+remarks;
                if(factors[i].leave_info.type.toLowerCase().includes("special monetization")){
                    remarks = "(Spl. Monetization)"+remarks;
                }else if(factors[i].leave_info.type.toLowerCase().includes("monetization")){
                    remarks = "(Monetization)"+remarks;
                }

				if(leaveIDs.has(leave_id)){
                    var factor_info = leaveIDs.get(leave_id);
					factor_info.when_taken+=when_taken;
					factor_info.leaves_taken.v+=leaves_taken.v;
					factor_info.leaves_taken.s+=leaves_taken.s;
					factor_info.undertime.total+=undertime.total;
					factor_info.without_pay.total+=without_pay.total;
					factor_info.balance = factors[i].balance;
				}else{
					leaveIDs.set(leave_id,{
						when_taken:factors[i].date.format('MMM. ')+when_taken,
						leaves_taken:leaves_taken,
						undertime:undertime,
						without_pay:without_pay,
						balance:factors[i].balance,
						remarks:remarks,
						date:date,
                        precise_date:factors[i].date
					});
				}
			}
            //sort leaves
            leaveIDs = new Map([...leaveIDs.entries()].sort( (a,b)=>(a.valueOf()-b.valueOf()) ));

            //format factors for display
			leaveIDs.forEach(function(factor){
                var stvFlag = false; //sick to vac
				factor.when_taken += ' '+moment(date,'MMMM YYYY').year();
                factor.balance.s += resolvedNegatives[date].s;
                if(factor.balance.s<0 ){ //negative sick balance. deduct from vac
                    resolvedNegatives[date].s -= factor.balance.s;
					factor.balance.v+=factor.balance.s;
                    factor.leaves_taken.s+=factor.balance.s;
                    factor.leaves_taken.v-=factor.balance.s; //add to leaves vac taken.
					factor.balance.s = 0;
                    stvFlag = true;
				}
                factor.balance.v += resolvedNegatives[date].v;
				if(factor.balance.v<0){//negative vac balance
                    resolvedNegatives[date].v -= factor.balance.v;
					factor.without_pay.total-=factor.balance.v;
                    factor.leaves_taken.v += factor.balance.v; //deduct leaves taken.
                    if(stvFlag){
                        resolvedNegatives[date].v += factor.balance.v-factor.leaves_taken.v;
                    }
                    factor.balance.v = 0;
				}
				factor.leaves_taken.v = (factor.leaves_taken.v/1000).toFixed(3);
				factor.leaves_taken.s = (factor.leaves_taken.s/1000).toFixed(3);
				var ut_time = $rootScope.creditsToTime(factor.undertime.total/1000);
				factor.undertime.total = (factor.undertime.total/1000).toFixed(3);
				factor.undertime.hour = ut_time.hours;
				factor.undertime.min = ut_time.minutes;
				var wp_time = $rootScope.creditsToTime(factor.without_pay.total/1000);
				factor.without_pay.total=(factor.without_pay.total/1000).toFixed(3);
				factor.without_pay.hour = wp_time.hours;
				factor.without_pay.min = wp_time.minutes;
				factor.balance.v = (factor.balance.v/1000).toFixed(3);
				factor.balance.s = (factor.balance.s/1000).toFixed(3);

				addToROL(nLeaveROL,date,factor);
			});
		});
		leaveROL = nLeaveROL;

		//adding to rol
		addToROL(rol,firstFactor.date,firstFactor);
		var startDate = $scope.rol.start_date.clone();
		while(startDate.isSameOrBefore($scope.rol.end_date)){
			var month = startDate.format('MMMM YYYY');
			if(rol.hasOwnProperty(month)){
				if(leaveROL.hasOwnProperty(month)){
					leaveROL[month].forEach(function(item){
						rol[month].push(item);
					});
				}
				if(otherROL.hasOwnProperty(month)){
					otherROL[month].forEach(function(item){
						rol[month].push(item);
					});
				}
			}else{
				rol[month] = [];
				if(leaveROL.hasOwnProperty(month)){
					leaveROL[month].forEach(function(item){
						rol[month].push(item);
					});
				}
				if(otherROL.hasOwnProperty(month)){
					otherROL[month].forEach(function(item){
						rol[month].push(item);
					});
				}
			}
			startDate.add(1,'month');
		}

        return rol;
    }

    $scope.printROLTable = function(){
        var printContents = document.getElementById('rolTable').innerHTML;
        var popupWin = window.open('', '_blank', 'width=1000,height=700');
        popupWin.document.open();
        popupWin.document.write('<html><body onload="window.print()">' + printContents + '</body></html>');
        popupWin.document.close();
    }

    $scope.exportROLTable = function(){
        var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        tab_text = tab_text + '<head><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';

        tab_text = tab_text + '<x:Name>Test Sheet</x:Name>';

        tab_text = tab_text + '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
        tab_text = tab_text + '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';

        tab_text = tab_text + "<table border='1px'>";
        tab_text = tab_text + $('#rol-table').html(); //rol table id
        tab_text = tab_text + '</table></body></html>';

        var data_type = 'data:application/vnd.ms-excel';

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
            if (window.navigator.msSaveBlob) {
                var blob = new Blob([tab_text], {
                    type: "application/csv;charset=utf-8;"
                });
                navigator.msSaveBlob(blob, $scope.employee.surname+"_"+$scope.rol.start_date.format("YYYYMMDD")+"-"+$scope.rol.end_date.format("YYYYMMDD")+".xls");
            }
        } else {
            $('#rol-export').attr('href', data_type + ', ' + encodeURIComponent(tab_text)); //export button id
            $('#rol-export').attr('download', $scope.employee.surname+"_"+$scope.rol.start_date.format("YYYYMMDD")+"-"+$scope.rol.end_date.format("YYYYMMDD")+".xls");
        }

    }

    $scope.rolStartDateSet = function(){
		$scope.rol.start_date = moment($scope.rol.start_date);
        $scope.$broadcast('rol-start-date-set');
        updateROL();
    }
    $scope.rolEndDateSet = function(){
		$scope.rol.end_date = moment($scope.rol.end_date).endOf('month');
        $scope.$broadcast('rol-end-date-set');
        updateROL();
    }

    $scope.rolStartDateRender = function($view,$dates) {
		var limitDate = $scope.employee.first_day_compute.clone().subtract(1,$view).add(1,'minute');
        var activeDate = moment($scope.rol.end_date);
		$dates.filter(function (date) {
			return date.localDateValue() < limitDate.valueOf() ||  date.localDateValue() > activeDate.valueOf();
		}).forEach(function (date) {
			date.selectable = false;
		});
	}

	$scope.rolEndDateRender = function($view, $dates) {
		var limitDate = $scope.employee.first_day_compute.clone().subtract(1,$view).add(1,'minute');
        var activeDate = moment($scope.rol.start_date).subtract(1, $view).add(1, 'minute');
		$dates.filter(function (date) {
			return date.localDateValue() < limitDate.valueOf() || date.localDateValue() <= activeDate.valueOf();
		}).forEach(function (date) {
			date.selectable = false;
		});
	}
    //#endregion end Record Of Leaves

    //#region Section 3.4 Monetization
    $scope.monetize = {
        date:moment(),
        credits:0,
        special:false
    }

    $scope.submitMonetization = function(){
        //Validation code goes here
        $scope.monetize.credits = parseFloat($scope.monetize.credits.toFixed(3));
		var balance = $scope.computeBal($scope.monetize.date);
		
		//	As per MC 41, s. 1998: Sec 22
		
		//		"who have accumulated fifteen (15) days of vacation leave credits shall be allowed to monetize a minimum of ten (10) days"
		if(balance[0]<15){
			$rootScope.showCustomModal('Error','The employee should have accumulated at least 15 vacation leave credits before monetization of leave credits is allowed.',function(){angular.element('#customModal').modal('hide');},function(){});
			return;
		}
		if($scope.monetize.credits<10){
			$rootScope.showCustomModal('Error','At least 10 vacation leaves should be monetized.',function(){angular.element('#customModal').modal('hide');},function(){});
			return;
		}
		
		//		At least 5 days is retained after monetization
		if( ( !$scope.monetize.special && $scope.monetize.credits>balance[0]-5 )  ||  ( $scope.monetize.special && $scope.monetize.credits>Number(balance[0])+Number(balance[1])-5 ) ){
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
                    start_date:$scope.monetize.date.format('YYYY/MM/DD'),
                    end_date:$scope.monetize.date.format('YYYY/MM/DD'),
                    credits:$scope.monetize.credits
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
    //#endregion end Monetization

    //#region Section 3.5 Terminal Benefit computations
	$scope.terminal_date = '';
	// Checking difference between the two
    $scope.terBenefit = {};
    $scope.terBenefit2 = {};
    $scope.setTerminalDate = function(date){
        $scope.terminal_date = moment(date);
        $rootScope.longComputation($scope.terBenefit,'value',$scope.terminalBenefit);
        $rootScope.longComputation($scope.terBenefit2,'value',$scope.terminalBenefit2);
    }
	$scope.terminalBenefit = function(){

		var salary = 100*$scope.employee.highest_salary;
		var balance = $scope.computeBal($scope.terminal_date);
		var credits = Number(balance[0])*1000 + Number(balance[1])*1000;
		var constantFactor = 0.0481927;

		var tlb = salary * credits * constantFactor;
        $scope.terBenefit.computation = $scope.employee.highest_salary+" * "+credits/1000+" * "+constantFactor+" = "+(tlb/100000).toFixed(2);
		//return (tlb/100000).toFixed(2);
	}

	$scope.terminalBenefit2 = function(){
		var balance = $scope.computeBal($scope.terminal_date);
		//	Credits Earned
		var creditByHalfDay = [0, 21, 42, 62, 83, 104, 125, 146, 167, 187, 208, 229, 250, 271, 292, 312, 333, 354, 375, 396, 417, 437, 458, 479, 500, 521, 542, 562, 583, 604, 625, 646, 667, 687, 708, 729, 750, 771, 792, 813, 833, 854, 875, 896, 917, 938, 958, 979,1000,1021,1042,1063,1083,1104,1125,1146,1167,1188,1208,1229,1250];

		var dateStart = $scope.employee.first_day_employ.clone().subtract(1, 'days');
		var dateEnd = $scope.terminal_date.clone();

		var years = $scope.totalDays.years;

		var months = $scope.totalDays.months;

		var days = $scope.totalDays.days;
		days -= $scope.lwop[0];

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

		var leaveEarned = 15000*years + 1250*months + creditByHalfDay[Math.floor(2*days)] + Math.floor(((days*1000)%500)*1.25/30);
		//	#credits_earned

		//	Credits Used
		var creditsUsed = ($scope.cEnjoyed.v + $scope.cEnjoyed.s)*1000;
		//	#credits_used

		var credits = 2*leaveEarned + currV + currS;
		credits -= creditsUsed;
		var salary = 100*$scope.employee.highest_salary;
		var constantFactor = 0.0481927;

		var tlb = salary * credits * constantFactor;
        $scope.terBenefit2.computation = $scope.employee.highest_salary+" * "+credits/1000+" * "+constantFactor+" = "+(tlb/100000).toFixed(2);
		//return (tlb/100000).toFixed(2);
	}
    //#endregion end Terminal Benefit Computations

    //#region Section 3.6 leave history
    $scope.type_filters = ['vacation','sick','maternity','paternity','others']; //should not contain 'every'. 'others' is essential
    $scope.filter = {
        type:{
            every:true
        },
        date:{
            enabled:true,
            precision:'year', //year, month, or day
            date:moment(),
            format:'YYYY'
        }
    };
    $scope.initFilters = function(){
        for(var i = 0 ; i<$scope.type_filters.length ; i++){
            $scope.filter.type[$scope.type_filters[i]] = true;
        }
        for(var i=0;i<$scope.leaves.length;i++){
            for(var j=0;j<$scope.leaves[i].date_ranges.length ; j++){
                $scope.leaves[i].date_ranges[j].show = true;
            }
        }
        $scope.changeDateFilter();
    }
    $scope.filterLeave = function(leave){ //returns true if leave can be shown
        if($scope.type_filters.indexOf(leave.info.type.toLowerCase())>-1){
            return $scope.filter.type[leave.info.type.toLowerCase()];
        }else{
            return $scope.filter.type['others'];
        }
    }

    $scope.changeDateFilter = function(){
        switch($scope.filter.date.precision){
            case 'year':
                $scope.filter.date.format = 'YYYY';
                break;
            case 'month':
                $scope.filter.date.format = 'MMMM YYYY';
                break;
            case 'day':
                $scope.filter.date.format = 'MMMM DD, YYYY';
                break;
        }
        $scope.filter.date.date = moment($scope.filter.date.date);
		if($scope.filter.date.date.clone().isBefore(moment($scope.employee.first_day))){
			$scope.filter.date.date = moment($scope.employee.first_day);
		}
        for(var i=0;i<$scope.leaves.length;i++){
            var show = false;
            for(var j=0;j<$scope.leaves[i].date_ranges.length ; j++){
                if(!$scope.leaves[i].date_ranges[j].start_date.isSame($scope.filter.date.date,$scope.filter.date.precision)){
                    $scope.leaves[i].date_ranges[j].show = false;
                }else {
                    $scope.leaves[i].date_ranges[j].show = true;
                }
                show = show||$scope.leaves[i].date_ranges[j].show;
            }
            $scope.leaves[i].show = show;
        }
        $scope.$broadcast('configDateFilter');
    }

	$scope.reFilter = function(type_filter){
		if(type_filter=='every'){
            $scope.filter.type.every = !$scope.filter.type.every;
            for(var i=0 ; i<$scope.type_filters.length ; i++){
                $scope.filter.type[$scope.type_filters[i]] = $scope.filter.type.every;
            }
        }else{
            if($scope.type_filters.indexOf(type_filter)>-1)
                $scope.filter.type[type_filter] = !$scope.filter.type[type_filter];
            var every = true;
            for(var i = 0; i<$scope.type_filters.length ; i++){
                every = every && $scope.filter.type[$scope.type_filters[i]];
            }
            $scope.filter.type.every = every;
        }
	}
    $scope.getDeductedCredits = function(type,date_range){
		if(type=='Vacation'||type=='Sick'||type.toLowerCase().includes('force')||type.toLowerCase().includes('mandatory')||type.toLowerCase().includes('monet')||type=='Undertime'){
			return date_range.credits - date_range.holiday_conflicts < 0 ? 0 :date_range.credits-date_range.holiday_conflicts;
		}else{
			return 0;
		}
    }
    //#endregion end Leave history
});
//</editor-fold> end Employee Display

//<editor-fold> Section 4.0 Employee Leave Records (requires parent controller: employee_display)
app.controller('employee_leave_records',function($scope,$rootScope,$window){
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

        /*Convert events to object to utilize hashmapping*/
        var newEvents = {
            recurring:{}
        };
        for(var i=0;i<$scope.events.length ; i++){
            var event = $scope.events[i];
            if(event.is_recurring){
                if(event.is_suspension && !newEvents.recurring[moment(event.date).format('MM-DD')]){
                    newEvents.recurring[moment(event.date).format('MM-DD')] = 'suspension';
                }else{
                    newEvents.recurring[moment(event.date).format('MM-DD')] = event;
                }
            }else{
                if(event.is_suspension && !newEvents[moment(event.date).format('YYYY-MM-DD')]){
                    newEvents[moment(event.date).format('YYYY-MM-DD')] = 'suspension';
                }else{
                    newEvents[moment(event.date).format('YYYY-MM-DD')] = event;
                }
            }
        }
        $scope.events = newEvents;
        /*end events*/
    }

	$scope.$on('openLeaveModal',function(event, leave=null){
        $scope.leave.info = {};
        $scope.leave.date_ranges = [];

        if(leave !== null){
            leaveReference = leave;
    		$scope.leave = angular.copy(leave);
            //Formatting the passed leave
            for(var i = 0 ; i<$scope.leave.date_ranges.length ; i++){
                var date_range =  $scope.leave.date_ranges[i];
                date_range.start_date = moment(date_range.start_date);
                date_range.end_date = moment(date_range.end_date);
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

    var getTotalDays = function(index,checkForHolidays = false){
        var days =  Math.round(moment($scope.leave.date_ranges[index].end_date).diff($scope.leave.date_ranges[index].start_date,'days'))+1;
		if($scope.leave.info.type=="Maternity") return days;
        //Removing weekends and holidays
        var startDate = $scope.leave.date_ranges[index].start_date.clone();
        $scope.leave.date_ranges[index].holiday_conflicts = 0;
        while(startDate.isSameOrBefore($scope.leave.date_ranges[index].end_date,'days')){
            if(startDate.day()===0 || startDate.day()===6){ //0 means sunday, 6 means saturday
                days--;
            }else{
                var eventAtDate = $scope.events[startDate.format('YYYY-MM-DD')];
                var recurringEventAtDate = $scope.events.recurring[startDate.format('MM-DD')];
                if(eventAtDate && eventAtDate!='suspension'){ //Check for existence of an event at that date and make sure its not a suspension
                    $scope.leave.date_ranges[index].holiday_conflicts++;
                }else if(recurringEventAtDate && recurringEventAtDate!='suspension'){  //Check for recurring events
                    $scope.leave.date_ranges[index].holiday_conflicts++;
                }
			}
            startDate.add(1,'days');
        }
        return days;
    }

	$scope.getTotalCredits = function(){
		var credits = 0;
        var deductions = 0;
		for(var i = 0 ; i<$scope.leave.date_ranges.length ; i++){
			if($scope.leave.date_ranges[i].end_date=='' || $scope.leave.date_ranges[i].start_date==''){
				continue;
			}
			credits += $scope.leave.date_ranges[i].credits;
            credits = credits-($scope.leave.date_ranges[i].holiday_conflicts?$scope.leave.date_ranges[i].holiday_conflicts:0);
		}
		return credits<0?0:credits;
	}

    $scope.updateCredits = function(date_range,type){
        var hours = date_range.hours;
        var minutes = date_range.minutes;
        var credits = date_range.credits;
        switch(type){
            case 'credits':
                var time = $rootScope.creditsToTime(credits);
                hours = time.hours;
                minutes = time.minutes;
                break;
            case 'time':
                credits = $rootScope.timeToCredits(hours,minutes);
                break;
            default:
                return;
        }
        date_range.hours = hours;
        date_range.minutes = minutes;
        date_range.credits = credits ? parseFloat(credits.toFixed(3)):null;
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

    $scope.startDateSet = function (index) {
        $scope.leave.date_ranges[index].start_date = moment($scope.leave.date_ranges[index].start_date);
		if($scope.leave.date_ranges[index].end_date){
			if($scope.leave.date_ranges[index].end_date.clone().diff($scope.leave.date_ranges[index].start_date)<0
                || $scope.leave.date_ranges[index].end_date.month()!=$scope.leave.date_ranges[index].start_date.month()){
				$scope.leave.date_ranges[index].end_date = $scope.leave.date_ranges[index].start_date;
			}
		}else{
			$scope.leave.date_ranges[index].end_date = $scope.leave.date_ranges[index].start_date;
		}
        $scope.$broadcast('startDateSet');
		$scope.leave.date_ranges[index].credits = getTotalDays(index);
        $scope.updateCredits($scope.leave.date_ranges[index],'credits');
    }

    $scope.startDateRender = function($view,$dates,index){
        var activeDate = $scope.employee.first_day_compute.clone().subtract(1, $view).add(1, 'minute');

        $dates.filter(function(date){
            return date.localDateValue() <= activeDate.valueOf();
        }).forEach(function(date){
            date.selectable = false;
        });
    }

	$scope.endDateSet = function(index){
        $scope.leave.date_ranges[index].end_date = moment($scope.leave.date_ranges[index].end_date);
		if(!$scope.leave.date_ranges[index].start_date){
			$scope.leave.date_ranges[index].start_date = $scope.leave.date_ranges[index].end_date;
		}
		$scope.leave.date_ranges[index].credits = getTotalDays(index);
        $scope.updateCredits($scope.leave.date_ranges[index],'credits');
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

                    if($scope.leave.hasOwnProperty('collision_events')){  //emit to calendar_collisions
                        response.leave.collision_event_id = $scope.leave.collision_event_id;
                        $scope.$emit('editLeave',response.leave);
                    }
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
				if( data.date_ranges[i].start_date.isSameOrBefore(data.date_ranges[j].end_date) && data.date_ranges[i].end_date.isSameOrAfter(data.date_ranges[j].start_date) ){
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
			data.date_ranges[i].start_date = data.date_ranges[i].start_date.format("YYYY-MM-DD");
			data.date_ranges[i].end_date = data.date_ranges[i].end_date.format("YYYY-MM-DD");
		}

        $rootScope.post(
            $rootScope.baseURL+"/employee/leaveRecords",
            data,
            function(response){
                succFunc(response);
                $scope.sortAndFormatLeaves(); //From parent
                $scope.changeDateFilter(); //Updating filters
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
//</editor-fold> end Employee Leave Records

//<editor-fold> Section 5.0 Employee Statistics (requires parent controller: employee_display)
app.controller('employee_statistics',function($scope,$rootScope){
    $scope.statistics={
        labels:['January','February','March','April','May','June','July','August','September','October','November','December'],
        series:['Vacation','Sick'],
        options:{
            scales:{
                xAxes:[
                    {
                        ticks:{
                            autoSkip:false
                        }
                    }
                ],
                yAxes:[
                    {
                        ticks:{
                            beginAtZero:true
                        }
                    }
                ]
            }
        },
        colors:["rgba(0,100,255,1)","rgba(0,255,0,1)"]
    };
    $scope.year = moment();
    $scope.bal_history = {};

    $scope.addYear = function(amt){
        if($scope.year.year()+amt > moment().year() || $scope.year.year()+amt < $scope.employee.first_day_compute.year())
            return;
        $scope.year.add(amt,'year');
        var endDate = $scope.year.clone().endOf('year').isSameOrAfter(moment(),'month') ? moment():$scope.year.clone().endOf('year');
        updateGraph($scope.year.clone().startOf('year'),endDate);
    }

    $scope.setYear = function(){
        $scope.year = moment($scope.year);
        var endDate = $scope.year.clone().endOf('year').isSameOrAfter(moment(),'month') ? moment():$scope.year.clone().endOf('year');
        updateGraph($scope.year.clone().startOf('year'),endDate);
    }

    $scope.statisticsDateRender = function($view,$dates){
        $dates.filter(function(date){
            return date.localDateValue()<$scope.employee.first_day_compute.clone().startOf('year').valueOf() || date.localDateValue() > moment().endOf('year').valueOf();
        }).forEach(function(date){
            date.selectable = false;
        });
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

    var updateGraph = function(startDate,endDate,bal_history = null){
        bal_history = bal_history?bal_history:$scope.computations.bal_history;
        startDate = moment(startDate);
        endDate = moment(endDate);
        var currDate = startDate.clone().endOf('month');
        var vac=[];
        var sick=[];
        while(currDate.isSameOrBefore(endDate,'month')){

            var bal = bal_history[currDate.clone().format('YYYY-MM-DD')];
            if(bal){
                vac.push(bal.vac/1000);
                sick.push(bal.sick/1000);
            }else {
                vac.push(0);
                sick.push(0);
            }
            currDate.add(1,'months').endOf('month');
        }

        $scope.statistics.data = [vac,sick];
    }

    $scope.$on('openStatisticsModal',function(event){
        $rootScope.longComputation($scope,'bal_history',function(){
            $scope.year = moment();
            $scope.computeBal(moment().add(1,'month').endOf('month')); //computations history gets set internally in computeBal which is in employee_display
            updateGraph(moment().startOf('year'),moment(),$scope.computations.bal_history)
            return angular.copy($scope.computations.bal_history);
        });
    });

});
//</editor-fold> end Employee Statistics
