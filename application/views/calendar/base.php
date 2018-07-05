<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<style>
.calendar{

}
.calendar .extra-days{
    color:lightgray;
}

.calendar .date-cells:hover{
    background-color:lightgray;
    cursor:pointer;
}

</style>

<div class="card" ng-controller="calendar_display" ng-init="init(null)">
    <div class="card-header">
        <h2>Calendar</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive container mx-auto" style="max-width:1200px">
            <div class="row mb-3 justify-content-between">
                <h3 class="col-5 text-left my-auto">{{formatCurrentDate()}}</h3>
                <div class="col my-auto btn-group">
                    <div class="btn-group">
                        <button class="btn btn-outline-secondary" type="button" ng-click="setCurrentDate(moment())">Today</button>
                        <a  class="dropdown-toggle btn btn-outline-secondary" id="navigateDate" role="button" data-toggle="dropdown" data-target="#" href="#">Navigate</a>
                        <ul class="dropdown-menu" role="menu">
                            <datetimepicker data-ng-model="navigateDate" data-on-set-time="setCurrentDate(newDate)" data-datetimepicker-config="{ dropdownSelector: '#navigateDate',minView:'month',startView:'month' }"/>
                        </ul>
                    </div>
                </div>
                <div class="col-2 btn-group justify-content-end">
                    <button ng-click="addMonth(-1)" class="btn btn-outline-dark my-auto" style="max-width:50px;max-height:50px"><i class="fas fa-angle-left fa-2x"></i></button>
                    <button ng-click="addMonth(1)" class="btn btn-outline-dark my-auto" style="max-width:50px;max-height:50px"><i class="fas fa-angle-right fa-2x"></i></button>
                </div>
            </div>
            <table class="table table-bordered calendar">
                <tr>
                    <th>Sunday</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Saturday</th>
                </tr>
                <tr ng-repeat="row in calendar">
                    <td class="date-cells" ng-repeat="date in row" style="height:110px;width:14%" ng-click="showModal(date)">
                        <span ng-class="{'extra-days':date.month()!==currentDate.month()}">{{date.date()}}</span>
                        <div>
                            <p class="font-italic" ng-repeat="event in date.events">{{event}}.</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventLabel">Add/Edit Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <p>Date: <span class="font-weight-bold">{{selectedDate.format('MMMM DD, YYYY')}}</span></p>
                        </div>
                        <div class="form-group">
                            <p>Events:</p>
                            <div class="form-group" ng-repeat="event in selectedDate.events track by $index">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">#{{$index+1}}</span>
                                    </div>
                                    <input type="text" class="form-control" ng-model="selectedDate.events[$index]"></input>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-dark" ng-click="addOrDeleteEvent(selectedDate.events,$index)"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-info" type="button" ng-click="addOrDeleteEvent(selectedDate.events)">Add Event</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
