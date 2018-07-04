<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<style>
.calendar{

}
.calendar .extra-days{
    color:lightgray;
}

</style>

<div class="card" ng-controller="calendar_display" ng-init="init()">
    <div class="card-header">
        <h2>Calendar</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive mx-auto" style="max-width:1200px">
            <div class="row mb-3 justify-content-between container">
                <h3 class="col-5 text-left">{{formatCurrentDate()}}</h3>
                <div class="row col-2 btn-group justify-content-end">
                    <button ng-click="addMonth(-1)" class="col-1 btn btn-outline-dark my-auto" style="max-width:50px;max-height:50px"><i class="fas fa-angle-left fa-2x"></i></button>
                    <button ng-click="addMonth(1)" class="col-1 btn btn-outline-dark my-auto" style="max-width:50px;max-height:50px"><i class="fas fa-angle-right fa-2x"></i></button>
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
                    <td ng-repeat="date in row" style="height:110px;width:14%">
                        <span ng-class="{'extra-days':date.month()!==currentDate.month()}">{{date.date()}}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
