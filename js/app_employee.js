app.controller('employee_nav',function($scope,$rootScope){
    $scope.employees = [];
    $scope.init=function(employees){
        $scope.employees=employees;
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
				date_range.start_date = moment(date_range.start_date).format("MMMM DD, YYYY");
				date_range.end_date = moment(date_range.end_date).format("MMMM DD, YYYY");
			}
        }

        $scope.sick_bal_date = moment().endOf("month");
        $scope.vac_bal_date = moment().endOf("month");
        $scope.employee.first_day = moment($scope.employee.first_day).format("MMMM DD, YYYY");
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
		// As per MC No. 14, s. 1999
		var dayCredits = [0.042,0.083,0.125,0.167,0.208,0.250,0.292,0.333,0.375,0.417,0.458,0.500,0.542,0.583,0.625,0.667,0.708,0.750,0.792,0.833,0.875,0.917,0.958,1.000,1.042,1.083,1.125,1.167,1.208,1.250];

		var creditByHalfDay = [0.000,0.021,0.042,0.062,0.083,0.104,0.125,0.146,0.167,0.187,0.208,0.229,0.250,0.271,0.292,0.312,0.333,0.354,0.375,0.396,417,0.437,0.458,0.479,0.500,0.521,0.542,0.562,0.583,
		0.604,0.625,0.646,0.667,0.687,0.708,0.729,0.750,0.771,0.792,0.813,0.833,0.854,0.875,0.896,0.917,0.938,0.958,0.979,1.000,1.021,1.042,1.063,1.083,1.104,1.125,1.146,1.167,1.188,1.208,1.229,1.250];

		var currV = Number($scope.employee.vac_leave_bal);
		var currS = Number($scope.employee.sick_leave_bal);
		var dateEnd = moment($scope.bal_date).clone();
		var dateStart = moment($scope.employee.first_day).clone();
		var fLeave = 0;
		// First Month Computation
		var firstMC=0;
		if(moment($scope.employee.first_day).isSame(moment($scope.employee.first_day).clone().startOf('month'))  &&  currV==0){ }else{
			fLeave=5;
			firstMC = Math.abs(moment($scope.employee.first_day).endOf('month').diff($scope.employee.first_day, 'days'))+1;
			firstMC = creditByHalfDay[2*firstMC];
			firstMC = Number(firstMC.toFixed(3));
			currV += firstMC; currS += firstMC;
			dateStart.add(1,'month');
		}
		// #first_month_computation

		// Computation For Other Months
		while(dateStart<dateEnd){
			if(moment(dateStart).month()==0){fLeave=5;}
			for(var i=0;i<$scope.leaves.length;i++){
				var leave = $scope.leaves[i];
				for(var j=0;j<leave.date_ranges.length;j++){
					var range = leave.date_ranges[j];
					if( moment(range.end_date).isBefore(moment(dateStart).startOf('month'))  ||  moment(range.start_date).isAfter(moment(dateStart).endOf('month')) )
						continue;
					var creditUsed = $scope.getDeductedCredits(leave.info.type,range);
					if(leave.info.type=="Vacation"||leave.info.type.toLowerCase()=='forced'||leave.info.type.toLowerCase()=='forced leave'){
						currV -= creditUsed;
						fLeave -= creditUsed;
					}
					if(leave.info.type=="Sick") currS -= creditUsed;
				}
			}
			if(currS<0){// When the employee is absent due to sickness and run out of sick leave
				currV += currS;
				currS = 0;
			}
			if(currV<0){// Employee incurring absence without pay
				var absent = Math.floor(2*Math.abs(currV));
				var rem = 2*Math.abs(currV)%1;
				rem = Math.round(1000*rem)/2000;
				currV = 0;
				currV = creditByHalfDay[60-absent]-(Math.round(420*rem)/1000);
			}else{
				currV = Number((currV + 1.25).toFixed(3));
			}
			currS+=1.25;
			dateStart.add(1,'month');
			if(moment(dateStart).month()==12 && fLeave>0 && currV>fLeave) currV = Number((currV-fLeave).toFixed(3));
		}
		// #computation_for_other_months

        return "Vacation: " + currV + " Sick: " + currS;
    }

    var enumerateDaysBetweenDates = function(startDate, endDate) {
        var now = startDate.clone(), dates = [];

        while (now.isSameOrBefore(endDate,'day')) {
            dates.push(now.format('YYYY-MM-DD'));
            now.add(1, 'days');
        }
        return dates;
    };

    $scope.formatDate = function(){
        $scope.bal_date = moment($scope.bal_date).endOf('month');
    }

    $scope.getDeductedCredits = function(type,date_range){
		if(type=='Vacation'||type=='Sick'||type.toLowerCase()=='forced'){
			return (date_range.hours/8+date_range.minutes/(60*8)).toFixed(3);
		}else{
			return 0;
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
                alert("Success: "+response.msg);
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
	$scope.dateFormat = 'MMMM DD, YYYY';

    $scope.init = function(employees="",employee=null){
        $scope.employees = employees==""?$scope.employees:employees;
        if(employee!=null){
            $scope.employee = employee;
			$scope.leave.info.emp_no = employee.emp_no;
            $scope.employee.name = employee.last_name+", "+employee.first_name+" "+employee.middle_name;
        }else{
            employee={};
        }
        $scope.rangeAction(0);
    }

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
			if(moment($scope.leave.date_ranges[index].end_date,$scope.dateFormat).diff(moment($scope.leave.date_ranges[index].start_date,$scope.dateFormat))<0
                || moment($scope.leave.date_ranges[index].end_date,$scope.dateFormat).month()!=moment($scope.leave.date_ranges[index].start_date,$scope.dateFormat).month()){
				$scope.leave.date_ranges[index].end_date = $scope.leave.date_ranges[index].start_date;
			}
		}else{
			$scope.leave.date_ranges[index].end_date = $scope.leave.date_ranges[index].start_date;
		}
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
		if(isModal){
			data.action = "edit";
		}
		for(var i = 0 ; i<data.date_ranges.length ; i++){
			data.date_ranges[i].start_date = moment(data.date_ranges[i].start_date,$scope.dateFormat).format("YYYY/MM/DD");
			data.date_ranges[i].end_date = moment(data.date_ranges[i].end_date,$scope.dateFormat).format("YYYY/MM/DD");
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
					/*item.addEventListener("mouseenter", function(){
						var x = document.getElementById(inp.id + "autocomplete-list");
						if(x) x = x.getElementsByTagName("div");
						if(x==null || x.length==0) return;
						for(var i=0; i<x.length;i++){
							if(x[i].innerHTML==this.innerHTML){
								currFocus=i;
								addActive(x);
								break;
							}
						}
					});*/
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
	}
});
