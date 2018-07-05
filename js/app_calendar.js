app.controller('calendar_display',function($scope,$rootScope,$window){

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
    $scope.events = [
        {
            date:'2018-01-01',
            title:'New Year',
            description:'New Year',
            is_suspension:false
        },
        {
            date:'2018-06-18',
            title:'Best day ever',
            description:'Intern boiis came to town',
            is_suspension:true
        }
    ];

    $scope.calendar;

    $scope.init = function(events){
        $scope.events = events;
        $scope.currentDate = moment();
        $scope.calendar = $scope.getCalendar();
        console.log(events);
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
        switch(action){
            case 'add':
                url = $rootScope.baseURL+'calendar/actionevents/add';
                succMsg = 'Added event successfully.';
                break;
            case 'edit':
                url = $rootScope.baseURL+'calendar/actionevents/edit';
                succMsg = 'Edited event successfully.';
                break;
            case 'delete':
                url = $rootScope.baseURL+'calendar/actionevents/delete';
                succMsg = 'Event deleted.';
                data = $scope.modalEvent.id;
                break;
            default:
                return;
        }

        $rootScope.post(
            url,
            data,
            function(response){
                $rootScope.showCustomModal('Success',succMsg,function(){
                    if(action=='delete'){
                        cache.date.events.splice([cache.index],1);
                    }else{
                        cache.date.events[cache.index] = $scope.modalEvent;
                    }
                    angular.element('#customModal').modal('hide');
                    angular.element('#addOrEditEventModal').modal('hide');
                },function(){});
            },
            function(response){
                $rootScope.showCustomModal('Error',response.msg,function(){angular.element('#customModal').modal('hide');});
            }
        );
    }

    $scope.showAddOrEditModal = function(dateEvent,index=-1){
        if(index>-1){
            $scope.modalEvent = angular.copy(dateEvent.events[index]);
            cache.date = dateEvent;
            cache.index = index;
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
                    if(calendar[i][j].isSame(moment($scope.events[k].date),'day')){
                        calendar[i][j].events.push($scope.events[k]);
                    }
                }
            }
        }
        console.log(calendar);
        return calendar;
    }
});
