<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="leave_application" ng-init='init(<?=$employees?><?=isset($employee)?','.$employee:''?>)'>
    <div>
        <h1>Application for Leave</h1>
        <form>
            <p>
                Employee No: <input id="empNo" type="text" ng-model="employee.emp_no" pattern="[0-9]{7}" min="7">
            </p>
            <p>
                Employee Name: <input id="empName" type="text" ng-model="employee.name">
            </p>
            <p>
                <div>
                    Start Date:
                    <a id="startdate" data-toggle="dropdown" href="#">
                        <span>
                            <input data-date-time-input="MMMM DD, YYYY - hh:mm a" style="width:25%;" type="text" data-ng-model="leave.start_date">
                            <span><i class="far fa-calendar-alt"></i></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <datetimepicker  data-ng-model="leave.start_date" data-datetimepicker-config="{ dropdownSelector:'#startdate',minuteStep:1 }" data-on-set-time="startDateSet()"></datetimepicker>
                    </ul>
                </div>
            </p>

            <p>
                <div ng-if="leave.start_date!=''">
                    End Date:
                    <a id="enddate" data-toggle="dropdown" data-target=".enddate" href="#">
                        <span>
                            <input data-date-time-input="MMMM DD, YYYY - hh:mm a" style="width:25%" type="text" data-ng-model="leave.end_date">
                            <span><i class="far fa-calendar-alt"></i></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <datetimepicker data-before-render="endDateRender($view,$dates)" data-ng-model="leave.end_date" data-datetimepicker-config="{ minuteStep:1 }"></datetimepicker>
                    </ul>
                </div>
            </p>

			<p>
				Remarks: <input type="textarea" ng-model="leave.remarks"/>
			</p>
            <div>
                Leave Type:
                <p><input type="radio" ng-model="leave.type" value="vacation"/>Vacation</p>
                <p><input type="radio" ng-model="leave.type" value="sick"/>Sick</p>
                <p><input type="radio" ng-model="leave.type" value="maternity"/>Maternity</p>
                <p><input type="radio" ng-model="leave.type" value="paternity"/>Paternity</p>
                <p><input type="radio" ng-model="leave.type" value="others"/>Others</p>
                <p ng-if="leave.type=='others'">Specify: <input  type="text" ng-model="leave.type_others"/></p>
            </div>
            <p>
				<input type="submit" ng-click="submit()"/>
            </p>
        </form>
    </div>
</div>
