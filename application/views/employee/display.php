<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_display" ng-init='init(<?=$employee ?>,<?=$leaves?>)'>
    <div>
        <h1>Employee Information</h1>
    </div>
    <p>Employee No: {{employee.emp_no}}</p>
    <p>Name: {{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}</p>
    <p>Employed On: {{employee.first_day}}</p>

    <div class="dropdown form-group" style="max-width:400px">
        <label>Vacation Leave Balance: {{computeBal(0)}}</label>
        <a id="vacBalDate" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">As of: </span>
                </div>
                <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="vac_bal_date">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                </div>
            </div>
        </a>
        <ul class="dropdown-menu">
            <datetimepicker  data-ng-model="vac_bal_date" data-datetimepicker-config="{ dropdownSelector:'#vacBalDate',startView:'month',minView:'month' }" data-on-set-time="formatDate(0)"></datetimepicker>
        </ul>
    </div>

    <div class="dropdown form-group" style="max-width:400px">
        <label>Sick Leave Balance: {{computeBal(1)}}</label>
        <a id="sickBalDate" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">As of: </span>
                </div>
                <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="sick_bal_date">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                </div>
            </div>
        </a>
        <ul class="dropdown-menu">
            <datetimepicker  data-ng-model="sick_bal_date" data-datetimepicker-config="{ dropdownSelector:'#sickBalDate',startView:'month',minView:'month' }" data-on-set-time="formatDate(1)"></datetimepicker>
        </ul>
    </div>

    <div class="form-group">
        <a href="<?=base_url()."employee/leaveapplication/{{employee.emp_no}}"?>" class="btn btn-primary">Application For Leave</a>
        <button onclick="alert('No')" class="btn btn-primary">Delete Employee Record</button>
    </div>

    <div>
        <h3>Leave History</h3>
        <div class="table-responsive">
            <table class="table table-hover">
                <tr>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Type</th>
                    <th>Remarks</th>
                    <th>Days</th>
                    <th>Credits Equivalent</th>
                    <th>Deducted Credits (Vacation : Sick)</th>
                </tr>
                <tr ng-repeat="leave in leaves">
                    <td>{{leave.start_date}}</td>
                    <td>{{leave.end_date}}</td>
                    <td>{{leave.type}}</td>
                    <td>{{leave.remarks}}</td>
                    <td>{{leave.time}}</td>
                    <td>{{leave.credits}}</td>
                    <td ng-bind-html="leave.deducted"></td>
                </tr>
            </table>
        </div>
    </div>
</div>
