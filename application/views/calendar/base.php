<?php
defined('BASEPATH') OR exit('No direct script access allowed');

?>

<div class="card" ng-controller="calendar_display" ng-init="init()">
    <div class="card-header">
        <h2>Calendar</h2>
    </div>
    <div class="card-body p-5">
        <div class="row mb-3 justify-content-between">
            <button class="col-1 btn btn-secondary text-left" style="max-width:50px"><i class="fas fa-angle-left fa-2x"></i></button>
            <button class="col-5 btn btn-light text-center my-auto">
                <h3>Date</h3>
            </button>
            <button class="col-1 btn btn-secondary text-right" style="max-width:50px"><i class="fas fa-angle-right fa-2x"></i></button>
        </div>
        <div>
            <table class="table table-bordered">
                <tr ng-repeat="row in calendar">
                    <td ng-repeat="date in row">
                        {{date.date()}}
                        <div style="min-height:75px">

                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
