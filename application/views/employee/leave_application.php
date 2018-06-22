<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="leave_application" ng-init='init(<?=$employees?><?=isset($employee)?','.$employee:''?>)'>
    <div>
        <h1>Application for Leave</h1>
        <form ng-submit="submit()">
			<div class="form-group autocomplete">
                <label>Employee No: </label>
                <input id="empNo" class="form-control" type="text" ng-model="employee.emp_no" ng-blur="fillName()" pattern="[0-9]{7}" maxlength="7" required>
            </div>
            <div class="form-group autocomplete">
                <label>Employee Name: </label>
                <input id="empName" class="form-control" type="text" ng-model="employee.name" required>
            </div>

            <div>
                <label>Date Ranges: </label>
                <div>
                    <div class="container row">
                        <button class="btn btn-light form-group" type="button"><i class="fas fa-times"></i></button>
                        <div class="dropdown form-group col-sm" style="max-width:400px">
                            <a id="startdate" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">From</i></span>
                                    </div>
                                    <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="leave.start_date">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <datetimepicker  data-ng-model="leave.start_date" data-datetimepicker-config="{ dropdownSelector:'#startdate',minView:'day' }" data-on-set-time="startDateSet()"></datetimepicker>
                            </ul>
                        </div>

                        <div class="dropdown form-group col-sm"  ng-if="isStartDateSet" style="max-width:400px">
                            <a id="enddate" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">To</i></span>
                                    </div>
                                    <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="leave.end_date">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <datetimepicker data-before-render="endDateRender($view,$dates)" data-ng-model="leave.end_date" data-datetimepicker-config="{ dropdownSelector:'#enddate', minView:'day' }" data-on-time-set="endDateSet()"></datetimepicker>
                            </ul>
                        </div>
                        <div class="form-group col-" ng-if="isStartDateSet" style="max-width:200px">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Hours</i></span>
                                </div>
                                <input class="form-control" min="0" step="1" type="number">
                            </div>
                        </div>
                        <div class="form-group col-" ng-if="isStartDateSet" style="max-width:200px;margin-left:10px">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Minutes</i></span>
                                </div>
                                <input class="form-control" min="0" step="1" type="number">
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-secondary form-group" type="button"><span>Add Range</span></button>
                </div>

            </div>

            <div>
                <label>Days: {{computeDays()}}</label>
            </div>

			<div class="form-group">
				<label>Remarks: </label>
                <input class="form-control" type="textarea" ng-model="leave.remarks"/>
			</div>

            <div class="form-group">
                <label>Leave Type:</label>
                <div style="margin-left:25px">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="vacation" ng-model="leave.type" value="Vacation"/>
                        <label class="form-check-label" for="vacation">Vacation</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="sick" ng-model="leave.type" value="Sick"/>
                        <label class="form-check-label" for="sick">Sick</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="maternity" ng-model="leave.type" value="Maternity"/>
                        <label class="form-check-label" for="maternity">Maternity</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="paternity" ng-model="leave.type" value="Paternity"/>
                        <label class="form-check-label" for="paternity">Paternity</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="others" ng-model="leave.type" value="Others"/>
                        <label class="form-check-label" for="others">Others</label>
                    </div>

                    <div class="form-group" ng-if="leave.type=='Others'">
        				<label>Specify: </label>
                        <input class="form-control" type="text" ng-model="leave.type_others"/>
        			</div>
                </div>
            </div>
            <p>
				<button type="submit" class="btn btn-primary"/>Submit</button>
            </p>
        </form>
		<a hidden>
			{{autocomplete()}}
		</a>
    </div>
</div>
