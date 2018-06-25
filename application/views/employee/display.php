<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_display" ng-init='init(<?=$employee ?>,<?=$leaves?>)'>
    <div>
        <h1>Employee Information</h1>
    </div>
    <p>Employee No: {{employee.emp_no}}</p>
    <p>Name: {{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}</p>
    <p>First Day: {{employee.first_day}}</p>

    <div class="dropdown form-group" style="max-width:400px">
        <label>Leave Credits Balance: {{computeBal()}}</label>
        <a id="vacBalDate" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">As of: </span>
                </div>
                <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="bal_date">
                <div class="input-group-append">
                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                </div>
            </div>
        </a>
        <ul class="dropdown-menu">
            <datetimepicker  data-ng-model="bal_date" data-datetimepicker-config="{ dropdownSelector:'#vacBalDate',startView:'month',minView:'month' }" data-on-set-time="formatDate()"></datetimepicker>
        </ul>
    </div>


    <div class="form-group">
        <a href="<?=base_url()."employee/leaveapplication/{{employee.emp_no}}"?>" class="btn btn-primary">Application For Leave</a>
        <button onclick="alert('No')" class="btn btn-primary">Delete Employee Record</button>
    </div>

    <div>
        <h3>Leave History</h3>
        <div class="table-responsive">
			<table class="table" >
				<tr>
					<th>Leave Type</th>
					<th>Remarks</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Hours</th>
                    <th>Minutes</th>
                    <th>Deducted Credits</th>
                </tr>
				<tbody ng-repeat="leave in leaves" ng-init="leave.info.show=true">
					<tr ng-click="leave.info.show=!leave.info.show" style="background-color:lightgray" >
						<td >
							{{leave.info.type}}
						</td>
						<td colspan=6>
							{{leave.info.remarks}}
						</td>
					</tr>
					<tr ng-show="leave.info.show" ng-repeat="date_range in leave.date_ranges">
						<td></td>
						<td></td>
						<td>{{date_range.start_date}}</td>
						<td>{{date_range.end_date}}</td>
						<td>{{date_range.hours}}</td>
						<td>{{date_range.minutes}}</td>
						<td>{{getDeductedCredits(leave.info.type,date_range)}}</td>
					</tr>
				</tbody>

			</table>
        </div>
    </div>
</div>
