app.controller('calendar_display',function($scope,$rootScope,$window){

    $scope.currentDate = '';
    /*Structure of events:
        events:[
            {
                date:date1,
                title:title1,
                type:type1,
                description:description1
                event_id:id1 (optional)
            },...
        ]
        Date is formatted as: yyyy-mm-dd when coming into and out of angular
    */
    $scope.events = [];

    $scope.calendar;

    $scope.init = function(events){
        $scope.events = events;
        $scope.currentDate = moment();
        $scope.calendar = $scope.getCalendar();
    }

    $scope.modalDate = {};
    $scope.modalEvent = {};
    $scope.moment = moment;
    var cache = { //used for refreshing the page without reloading
        date:'',
        index:''
    };

    $scope.formatCurrentDate = function(){
        return $scope.currentDate.format('MMMM YYYY');
    }

    $scope.addMonth = function(x){
        $scope.currentDate.add(x,'month');
        $scope.calendar = $scope.getCalendar($scope.currentDate);
    }

    $scope.setCurrentDate = function(date){
        $scope.currentDate = moment(date);
        $scope.calendar = $scope.getCalendar(date);
    }

    $scope.showModal = function(dateEvent){ //dateEvent are moment objects that have their respective events associated to them
        $scope.modalDate = dateEvent;

        angular.element('#eventModal').modal('show');
    }

    $scope.actionEvent = function(action){
        $scope.modalEvent.date = $scope.modalDate.format('YYYY-MM-DD');
        var url='';
        var succMsg='';
        var data = $scope.modalEvent;
        var succFunction = function(response){};
        switch(action){
            case 'add':
                url = $rootScope.baseURL+'calendar/actionevents/add';
                succMsg = 'Added event successfully.';
                succFunction = function(response){
                    $scope.modalEvent.event_id = response.event_id;
                    $scope.events.push($scope.modalEvent); //add to global events
                    cache.date.events.push($scope.modalEvent); //add to cache. cache refers to the modal that pops up
                }
                break;
            case 'edit':
                url = $rootScope.baseURL+'calendar/actionevents/edit';
                succMsg = 'Edited event successfully.';
                succFunction = function(response){
                    for(var i = 0 ; i<$scope.events.length ; i++){
                        if($scope.events[i].event_id == $scope.modalEvent.event_id){
                            $scope.events[i] = $scope.modalEvent; //edit global events
                        }
                    }
                    cache.date.events[cache.index] = $scope.modalEvent; //edit cache
                }
                break;
            case 'delete':
                url = $rootScope.baseURL+'calendar/actionevents/delete';
                succMsg = 'Event deleted.';
                data = $scope.modalEvent.event_id;
                succFunction = function(response){
                    for(var i = 0 ; i<$scope.events.length ; i++){
                        if($scope.events[i].event_id == $scope.modalEvent.event_id){
                            $scope.events.splice(i,1); //delete from global events
                        }
                    }
                    cache.date.events.splice(cache.index,1); //delete from cache
                }
                break;
            default:
                return;
        }

        $rootScope.post(
            url,
            data,
            function(response){
                $rootScope.showCustomModal('Success',succMsg,function(){
                    succFunction(response);
                    angular.element('#customModal').modal('hide');
                    angular.element('#addOrEditEventModal').modal('hide');
                },function(){
                    succFunction(response);
                    angular.element('#customModal').modal('hide');
                    angular.element('#addOrEditEventModal').modal('hide');
                });
            },
            function(response){
                $rootScope.showCustomModal('Error',response.msg,function(){angular.element('#customModal').modal('hide');});
            }
        );
    }

    $scope.showAddOrEditModal = function(dateEvent,index=-1){
        $scope.modalEvent = {};
        cache.date = dateEvent;
        cache.index = index;
        if(index>-1){
            $scope.modalEvent = angular.copy(dateEvent.events[index]);
        }
        angular.element('#addOrEditEventModal').modal('show');
    }

    $scope.getCalendar = function(date = null){
        date = date? moment(date):moment();

        if(!date.isValid())
            throw "Invalid date";

        var calendar = [];
        // starting day
        var start = date.clone().startOf('month');

        var row = [];
        var prevMonth = date.clone().add(-1,'month').endOf('month');

        //get days of previous month that are included in the first week of this month
        for (var i = start.day(); i > 0; i--){
            row.push(prevMonth.clone().add(-(i-1),'day'));
        }
        var endOfMonth = start.clone().endOf('month');
        var nextMonth = date.clone().add(1,'month').startOf('month');

        while(start.isSameOrBefore(endOfMonth)){
            if(row.length>=7){
                calendar.push(row);
                row = [];
            }
            row.push(start.clone());
            start.add(1,'day');
        }

        //Fill in the calendar to make 6 columns
        var nextMonthDay = nextMonth.clone();
        while(calendar.length<6){
            while(row.length<7){
                row.push(nextMonthDay.clone());
                nextMonthDay.add(1,'day');
            }
            calendar.push(row);
            row = [];
        }

        //Associate events to dates
        for(var i = 0 ; i<calendar.length ; i++){
            for(var j = 0 ; j < calendar[i].length ; j++){
                calendar[i][j].events = [];
                for(var k = 0 ; k<$scope.events.length ; k++){
                    if($scope.events[k].is_recurring){
                        if(calendar[i][j].dayOfYear()==moment($scope.events[k].date).dayOfYear()){
                            calendar[i][j].events.push($scope.events[k]);
                        }
                    }else{
                        if(calendar[i][j].isSame(moment($scope.events[k].date),'day')){
                            calendar[i][j].events.push($scope.events[k]);
                        }
                    }
                }
            }
        }

        return calendar;
    }
});

app.controller('event_display',function($scope,$rootScope,$window){
	$scope.currentDate = '';
    /*Structure of events:
        events:[
            {
                date:date1,
                title:title1,
                type:type1,
                description:description1
                id:id1 (optional)
            },...
        ]
        Date is formatted as: yyyy-mm-dd when coming into and out of angular
    */
    $scope.events = [];

    $scope.calendar;

    $scope.init = function(events){
        $scope.events = events;
        $scope.currentDate = moment();
        $scope.calendar = $scope.getCalendar();
    }

	$scope.actionEvent = function(action){
        $scope.modalEvent.date = $scope.modalDate.format('YYYY-MM-DD');
        var url='';
        var succMsg='';
        var data = $scope.modalEvent;
        var succFunction = function(response){};
        switch(action){
            case 'add':
                url = $rootScope.baseURL+'calendar/actionevents/add';
                succMsg = 'Added event successfully.';
                succFunction = function(response){
                    $scope.modalEvent.id = response.id;
                    $scope.events.push($scope.modalEvent); //add to global events
                    cache.date.events.push($scope.modalEvent); //add to cache. cache refers to the modal that pops up
                }
                break;
            case 'edit':
                url = $rootScope.baseURL+'calendar/actionevents/edit';
                succMsg = 'Edited event successfully.';
                succFunction = function(response){
                    for(var i = 0 ; i<$scope.events.length ; i++){
                        if($scope.events[i].id == $scope.modalEvent.id){
                            $scope.events[i] = $scope.modalEvent; //edit global events
                        }
                    }
                    cache.date.events[cache.index] = $scope.modalEvent; //edit cache
                }
                break;
            case 'delete':
                url = $rootScope.baseURL+'calendar/actionevents/delete';
                succMsg = 'Event deleted.';
                data = $scope.modalEvent.id;
                succFunction = function(response){
                    for(var i = 0 ; i<$scope.events.length ; i++){
                        if($scope.events[i].id == $scope.modalEvent.id){
                            $scope.events.splice(i,1); //delete from global events
                        }
                    }
                    cache.date.events.splice(cache.index,1); //delete from cache
                }
                break;
            default:
                return;
        }

        $rootScope.post(
            url,
            data,
            function(response){
                $rootScope.showCustomModal('Success',succMsg,function(){
                    succFunction(response);
                    angular.element('#customModal').modal('hide');
                    angular.element('#addOrEditEventModal').modal('hide');
                },function(){
                    succFunction(response);
                    angular.element('#customModal').modal('hide');
                    angular.element('#addOrEditEventModal').modal('hide');
                });
            },
            function(response){
                $rootScope.showCustomModal('Error',response.msg,function(){angular.element('#customModal').modal('hide');});
            }
        );
    }

	$scope.differentModal = function(dateEvent){
		$scope.modalDate = moment(dateEvent.date,$rootScope.dateFormat).clone();
		$scope.modalEvent = angular.copy(dateEvent);
		angular.element('#addOrEditEventModal').modal('show');
	}

    $scope.getCalendar = function(date = null){
        date = date? moment(date):moment();

        if(!date.isValid())
            throw "Invalid date";

        var calendar = [];
        // starting day
        var start = date.clone().startOf('month');

        var row = [];
        var prevMonth = date.clone().add(-1,'month').endOf('month');

        //get days of previous month that are included in the first week of this month
        for (var i = start.day(); i > 0; i--){
            row.push(prevMonth.clone().add(-(i-1),'day'));
        }
        var endOfMonth = start.clone().endOf('month');
        var nextMonth = date.clone().add(1,'month').startOf('month');

        while(start.isSameOrBefore(endOfMonth)){
            if(row.length>=7){
                calendar.push(row);
                row = [];
            }
            row.push(start.clone());
            start.add(1,'day');
        }

        //Fill in the calendar to make 6 columns
        var nextMonthDay = nextMonth.clone();
        while(calendar.length<6){
            while(row.length<7){
                row.push(nextMonthDay.clone());
                nextMonthDay.add(1,'day');
            }
            calendar.push(row);
            row = [];
        }

        //Associate events to dates
        for(var i = 0 ; i<calendar.length ; i++){
            for(var j = 0 ; j < calendar[i].length ; j++){
                calendar[i][j].events = [];
                for(var k = 0 ; k<$scope.events.length ; k++){
                    if($scope.events[k].is_recurring){
                        if(calendar[i][j].dayOfYear()==moment($scope.events[k].date).dayOfYear()){
                            calendar[i][j].events.push($scope.events[k]);
                        }
                    }else{
                        if(calendar[i][j].isSame(moment($scope.events[k].date),'day')){
                            calendar[i][j].events.push($scope.events[k]);
                        }
                    }
                }
            }
        }

        return calendar;
    }
});

app.controller('calendar_collisions',function($scope,$rootScope,$window){
    $scope.collisions = [];
    $scope.moment = moment;
    $scope.init = function(collisions){
        $scope.collisions = collisions;
        for(var i = 0 ; i<$scope.collisions.length ; i++){
            $scope.collisions[i].leave.info.leave_id = parseInt($scope.collisions[i].leave.info.leave_id);
        }
    }

    $scope.openLeaveModal = function(event_id,leave){
        for(var i = 0 ; i<leave.date_ranges.length ; i++){
            leave.date_ranges[i].range_id = parseInt(leave.date_ranges[i].range_id);
            leave.date_ranges[i].start_date = moment(leave.date_ranges[i].start_date).format($rootScope.dateFormat);
            leave.date_ranges[i].end_date = moment(leave.date_ranges[i].end_date).format($rootScope.dateFormat);
            leave.date_ranges[i].credits = parseFloat(leave.date_ranges[i].credits);
            leave.date_ranges[i].hours = $rootScope.creditsToTime(leave.date_ranges[i].credits).hours;
            leave.date_ranges[i].minutes = $rootScope.creditsToTime(leave.date_ranges[i].credits).minutes;
        }
        leave.collision_event_id = event_id;
        $scope.$broadcast('openLeaveModal',leave);
	}

    var markAsResolved = function(collision){
        var data = {};

        if(Array.isArray(collision)){
            data.collision = collision;
            data.batch = true;
        }else{
            data = {
                event_id:collision.event_id,
                range_id:collision.range_id
            }
        }
        $rootScope.post(
            $rootScope.baseURL+'calendar/managecollisions',
            data,
            function(){
                $rootScope.showCustomModal('Success','Successfully resolved collision.',function(){
                    $window.location.reload();
                },function(){
                    $window.location.reload();
                });
            },
            function(response){
                $rootScope.showCustomModal('Error',response.msg,function(){
                    angular.element('#customModal').modal('hide');
                },function(){});
            }
        );
    }

    $scope.$on('editLeave',function(event,leave){ //Coming from employee_leave_records
        var data = [];
        for(var i = 0 ; i<leave.date_ranges.length ; i++){
            data.push({
                event_id:leave.collision_event_id,
                range_id:leave.date_ranges[i].range_id
            });
        }
        markAsResolved(data);
    });

    $scope.promptResolve = function(index){
        $rootScope.showCustomModal('Warning','Are you sure you want to dismiss this collision without editing the leave record?',function(){
            markAsResolved({
                event_id:$scope.collisions[index].event_id,
                range_id:$scope.collisions[index].range_id
            });
        },function(){});
    }
});
