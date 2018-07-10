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

<div class="card" ng-controller="calendar_display" ng-init='init(<?=isset($events)?$events:''?>)'>
    <div class="card-header">
        <h2>Calendar</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive container mx-auto" style="max-width:1200px">
            <div class="row mb-3 justify-content-between pt-1">
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
                <tr style="background-color:#0099ff;color:white">
                    <th class="text-center p-0">Sun</th>
                    <th class="text-center p-0">Mon</th>
                    <th class="text-center p-0">Tue</th>
                    <th class="text-center p-0">Wed</th>
                    <th class="text-center p-0">Thu</th>
                    <th class="text-center p-0">Fri</th>
                    <th class="text-center p-0">Sat</th>
                </tr>
                <tr ng-repeat="row in calendar">
                    <td class="date-cells p-2" ng-repeat="date in row" style="height:120px;width:14%;{{(moment().format('YYYY-MM-DD') == date.format('YYYY-MM-DD')) ? 'background-color:#cccccc':''}} " ng-click="showModal(date)">
                        <p class="text-right" ng-class="{'extra-days':date.month()!==currentDate.month()}" >{{date.date()}}</p>
                        <div ng-repeat="event in date.events" style="background-color:#000000;color:#ffffff;border-radius:5px;padding-left:5px">
                            <p class="font-italic" >{{event.title}}.</p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventLabel">Events for {{modalDate.format('MMMM DD, YYYY')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class='table-responsive'>
                        <table class="table">
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Suspension</th>
                                <th></th>
                            </tr>
                            <tr ng-repeat="event in modalDate.events">
                                <td>{{event.title}}</td>
                                <td>{{event.description}}</td>
                                <td>{{event.is_suspension?'true':'false'}}</td>
                                <td><button class="btn btn-info" type="button" ng-click="showAddOrEditModal(modalDate,$index)">Edit</button></td>
                            </tr>
                        </table>
                        <p ng-if="modalDate.events.length===0" class="font-italic">
                            No events here.
                        </p>
                    </div>
                    <button class="btn btn-info" type="button" ng-click="showAddOrEditModal(modalDate)">Add Event</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addOrEditEventModal" tabindex="-1" role="dialog" aria-labelledby="addOrEditEventLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventLabel">Add/Edit event for {{modalDate.format('MMMM DD, YYYY')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form ng-submit="actionEvent(modalEvent.hasOwnProperty('id')?'edit':'add')">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" id="isSuspension" type="checkbox" ng-true-value=1 ng-false-value=0 ng-model="modalEvent.is_suspension"></input>
                                <label for="isSuspension">Suspension</label>
                            </div class="form-check">
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input class="form-control" type="text" ng-model="modalEvent.title" required></input>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" ng-model="modalEvent.description"></textarea>
                        </div>
                        <button ng-disabled="busy" class="btn btn-info" type="submit">Submit</button>
                        <button ng-disabled="busy" ng-if="modalEvent.hasOwnProperty('id')" class="btn btn-danger" type="button" ng-click="actionEvent('delete')">Delete Event</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
