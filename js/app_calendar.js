app.controller('calendar_display',function($scope,$rootScope,$window){

    $scope.currentDate = '';
    /*Structure of events:
        events:[
            {
                date:date1,
                events:[event1,event2,...]
            },
            {
                date:date2,
                events:[event1,event2,...]
            }...
        ]
        Date is formatted as: yyyy-mm-dd when coming into and out of angular
    */
    $scope.events = [
        {
            date:'2018-01-01',
            events:['New Year']
        },
        {
            date:'2018-06-18',
            events:['Intern boiis came to town','Oh yeah baby']
        }
    ];
    $scope.calendar;
    $scope.selectedDate ={};

    $scope.init = function(events){
        $scope.currentDate = moment();
        $scope.calendar = $scope.getCalendar();
        //$scope.events = events;
    }

    $scope.moment = moment;

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
        $scope.selectedDate = dateEvent;
        if(!$scope.selectedDate.events){
            $scope.selectedDate.events = [];
        }
        angular.element('#eventModal').modal('show');
    }

    $scope.addOrDeleteEvent = function(events,index=-1){

        for(var i = 0 ; i<events.length ; i++){
            for(var j = 0 ; j<events.length ; j++){
                if(i!==j&&events[i]===events[j]){
                    $rootScope.showCustomModal('Warning','Please make sure there are no duplicate events',function(){angular.element('#customModal').modal('hide');},function(){},'Ok');
                    return;
                }
            }
        }

        //delete if index is not -1
        console.log(events);
        if(index>-1){
            events.splice(index,1);
        }else{
            if(events.indexOf('')>-1){
                $rootScope.showCustomModal('Warning','Please fill-in the events first before adding more.',function(){angular.element('#customModal').modal('hide');},function(){},'Ok');
                return;
            }
            events.push('');
        }
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
                for(var k = 0 ; k<$scope.events.length ; k++){
                    if(calendar[i][j].isSame(moment($scope.events[k].date))){
                        calendar[i][j].events = $scope.events[k].events;
                    }
                }
            }
        }
        console.log(calendar);
        return calendar;
    }
});
