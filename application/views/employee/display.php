<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_display" ng-init='init(<?=$employee ?>,<?=$leaves?>)'>
    <div>
        <h1>Employee Information</h1>
    </div>
    <p>Employee No: {{employee.emp_no}}</p>
    <p>Name: {{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}</p>
    <p>Sick Leave Balance: {{employee.sick_leave_bal}}</p>
    <p>Vacation Leave Balance: {{employee.vac_leave_bal}}</p>
    <p>
        <a href="<?=base_url()."employee/leaveapplication/{{employee.emp_no}}"?>">Application For Leave</a>
    </p>
    <p>
        <button onclick="alert('No')">Delete Employee Record</button>
    </p>
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
                    <th>Deducted Credits</th>
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
