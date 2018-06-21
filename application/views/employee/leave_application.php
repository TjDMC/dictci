<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="leave_application" ng-init='init(<?=$employees?><?=isset($employee)?','.$employee:''?>)'>
    <div>
        <h1>Application for Leave</h1>
        <form style="max-width:600px;margin-left:25px">
            <div class="form-group">
                <label>Employee No: </label>
                <input class="form-control" type="text" ng-model="employee.emp_no" pattern="[0-9]{7}" min="7">
            </div>
            <div class="form-group">
                <label>Employee Name: </label>
                <input class="form-control" type="text" ng-model="employee.name">
            </div>
            <div class="dropdown form-group" >
                <label>Start Date:</label>
                <a id="startdate" data-toggle="dropdown" data-target="dropdown" href="#">
                    <div class="input-group">
                        <input data-date-time-input="MMMM DD, YYYY - hh:mm a" class="form-control" type="text" data-ng-model="leave.start_date">
                        <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <datetimepicker  data-ng-model="leave.start_date" data-datetimepicker-config="{ dropdownSelector:'#startdate',minuteStep:1 }" data-on-set-time="startDateSet()"></datetimepicker>
                </ul>
            </div>

            <div class="dropdown form-group"  ng-if="leave.start_date!=''" >
                <label>End Date:</label>
                <a id="enddate" data-toggle="dropdown" data-target="dropdown" href="#">
                    <div class="input-group">
                        <input data-date-time-input="MMMM DD, YYYY - hh:mm a" class="form-control" type="text" data-ng-model="leave.end_date">
                        <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <datetimepicker data-before-render="endDateRender($view,$dates)" data-ng-model="leave.end_date" data-datetimepicker-config="{ dropdownSelector:'#enddate', minuteStep:1 }"></datetimepicker>
                </ul>
            </div>

			<div class="form-group">
				<label>Remarks: </label>
                <input class="form-control" type="textarea" ng-model="leave.remarks"/>
			</div>

            <div class="form-group">
                <label>Leave Type:</label>
                <div style="margin-left:25px">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" ng-model="leave.type" value="vacation"/>
                        <label class="form-check-label">Vacation</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" ng-model="leave.type" value="sick"/>
                        <label class="form-check-label">Sick</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" ng-model="leave.type" value="maternity"/>
                        <label class="form-check-label">Maternity</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" ng-model="leave.type" value="paternity"/>
                        <label class="form-check-label">Paternity</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" ng-model="leave.type" value="others"/>
                        <label class="form-check-label">Others</label>
                    </div>

                    <div class="form-group" ng-if="leave.type=='others'">
        				<label>Specify: </label>
                        <input class="form-control" type="text" ng-model="leave.type_others"/>
        			</div>
                </div>
            </div>
            <p>
				<button type="submit" class="btn btn-primary" ng-click="submit()"/>Submit</button>
            </p>
        </form>
    </div>
</div>
