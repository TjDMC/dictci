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
    $scope.bal_date = '';
    $scope.init = function(employee,leaves){
        $scope.employee = employee;
        $scope.leaves = leaves;
        $scope.employee.credits = {
            sick:0,
            vacation:0
        };
        $scope.bal_date = moment().endOf('month');
		
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
	
	$scope.computeBal = function(){
		var currV = Number($scope.employee.vac_leave_bal);
		var currS = Number($scope.employee.sick_leave_bal);
		var dateEnd = moment($scope.bal_date).clone();
		var dateStart = moment($scope.employee.first_day).clone();
		// First Month Computation
		var firstMC=0;
		if(moment($scope.employee.first_day).isSame(moment($scope.employee.first_day).clone().startOf('month'))  &&  currV==0){ firstMC = 1.25; }else{
			firstMC = Math.abs(moment($scope.employee.first_day).endOf('month').diff($scope.employee.first_day, 'days'))+1;
			firstMC = firstMC*1.25/moment($scope.employee.first_day).daysInMonth();
			firstMC = Number(firstMC.toFixed(3));
		}
		currV += firstMC; currS += firstMC;
		dateStart.add(1,'month');
		// #first_month_computation
		
		// Computation For other Months
		console.log($scope.leaves);
		while(dateStart<dateEnd){
			for(var i=0;i<$scope.leaves.length;i++){
				var leave = $scope.leaves[i];
				for(var j=0;j<leave.date_ranges.length;j++){
					var range = leave.date_ranges[j];
					if( moment(range.end_date).isBefore(moment(dateStart).startOf('month'))  ||  moment(range.start_date).isAfter(moment(dateStart).endOf('month')) )
						continue;
					var creditUsed = $scope.getDeductedCredits(leave.info.type,range);
					console.log(creditUsed);
					if(leave.info.type=="Vacation") currV -= creditUsed;
					if(leave.info.type=="Sick") currS -= creditUsed;
				}
				console.log(leave);
			}
			currV+=1.25;
			currS+=1.25;
			dateStart.add(1,'month');
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
		if(type=='Vacation'||type=='Sick'){
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
    $scope.leaves = [];
	$scope.leaveData = {};

	$scope.leaveTemplate = {
		start_date:'',
		end_date:'',
		type:'',
		remarks:'',
		hours:0,
		minutes:0
	};
	$scope.dateFormat = 'MMMM DD, YYYY';

    $scope.init = function(employees,employee=null){
        $scope.employees = employees;
        if(employee!=null){
            $scope.employee = employee;
            $scope.employee.name = employee.last_name+", "+employee.first_name+" "+employee.middle_name;
        }
		$scope.rangeAction(0);
    }

    var getTotalDays = function(index = -1){
		if(index==-1){
			var days = 0;

			for(var i = 0 ; i<$scope.leaves.length ; i++){
				if($scope.leaves[i].end_date=='' || $scope.leaves[i].start_date==''){
					continue;
				}
				days += Math.round((moment($scope.leaves[i].end_date).diff($scope.leaves[i].start_date))/86400000)+1;
				days += $scope.leaves[i].minutes/(8*60);
			}
			return days;
		}else{
			return Math.round((moment($scope.leaves[index].end_date).diff($scope.leaves[index].start_date))/86400000)+1;
		}
    }

	$scope.getTotalCredits = function(){
		var credits = 0;
		for(var i = 0 ; i<$scope.leaves.length ; i++){
			if($scope.leaves[i].end_date=='' || $scope.leaves[i].start_date==''){
				continue;
			}
			credits += $scope.leaves[i].hours/8;
			credits += $scope.leaves[i].minutes/(8*60);
		}
		return credits;
	}

	$scope.rangeAction = function(action,index=-1){
		switch(action){
			case 0://add
				$scope.leaves.push(angular.copy($scope.leaveTemplate));
				return;
			case 1://delete
				if($scope.leaves.length<=1){
					alert("Date ranges must have at least 1 range.");
					return;
				}
				$scope.leaves.splice(index==-1?$scope.leaves.length-1:index,1);
				return;
		}
	}

    $scope.startDateSet = function (index) {
		if($scope.leaves[index].end_date){
			if(moment($scope.leaves[index].end_date,$scope.dateFormat).diff(moment($scope.leaves[index].start_date,$scope.dateFormat))<0){
				$scope.leaves[index].end_date = $scope.leaves[index].start_date;
			}
		}else{
			$scope.leaves[index].end_date = $scope.leaves[index].start_date;
		}
		$scope.leaves[index].hours = getTotalDays(index)*8;
    }

	$scope.endDateSet = function(index){
		if(!$scope.leaves[index].start_date){
			$scope.leaves[index].start_date = $scope.leaves[index].end_date;
		}
		$scope.leaves[index].hours = getTotalDays(index)*8;
	}

    $scope.endDateRender = function($view,$dates,index){
        if($scope.leaves[index].start_date){
            var activeDate = moment($scope.leaves[index].start_date).subtract(1, $view).add(1, 'minute');

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
        var data = {
			emp_no:$scope.employee.emp_no,
			leaves:angular.copy($scope.leaves),
			type:$scope.leaveData.type,
			remarks:$scope.leaveData.remarks
		};
		for(var i = 0 ; i<data.leaves.length ; i++){
			data.leaves[i].start_date = moment(data.leaves[i].start_date,$scope.dateFormat).format("YYYY/MM/DD");
			data.leaves[i].end_date = moment(data.leaves[i].end_date,$scope.dateFormat).format("YYYY/MM/DD");
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
