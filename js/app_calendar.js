app.controller('calendar_display',function($scope,$rootScope,$window){

    $scope.view = 1; // 0=year, 1=month, 2=day
    $scope.currentDate = '';
    $scope.calendar;
    $scope.init = function(){
        $scope.calendar = $scope.getCalendar();
    }

    $scope.getCalendar = function(view=1,date = null){
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

        /*for(var i = 0 ; i<calendar.length ; i++){
            var string = '';
            for(var j = 0 ; j<calendar[i].length ; j++){
                string+='  '+calendar[i][j].date()<10 ? '  '+calendar[i][j].date():' '+calendar[i][j].date();
            }
            console.log(string);
        }*/
        return calendar;
    }
});
