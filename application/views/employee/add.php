<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_add" class="card">
    <div class="card-header">
        <h1>Add Employee</h1>
    </div>
    <div class="card-body p-md-4 p-2">
        <form ng-submit="add()">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label>Employee No: </label>
                        <input class="form-control" type="text" ng-model="employee.emp_no" pattern="[0-9]{7}" maxlength="7" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name: </label>
                        <input class="form-control" type="text" ng-model="employee.surname" required>
                    </div>
                    <div class="form-group">
                        <label>First Name: </label>
                        <input class="form-control" type="text" ng-model="employee.first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Middle Name: </label>
                        <input class="form-control" type="text" ng-model="employee.middle_name">
                    </div>
                    <div class="dropdown form-group" style="max-width:400px" ng-init="employee.first_day_employ = moment(employee.first_day_employ)">
                        <label>Employment Date:</label>
                        <a id="firstdayemploy" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                            <div class="input-group">
                                <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="employee.first_day_employ">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu">
                            <datetimepicker data-on-set-time="employee.first_day_employ = moment(employee.first_day_employ)" data-ng-model="employee.first_day_employ" data-datetimepicker-config="{ dropdownSelector:'#firstdayemploy',minView:'day' }"></datetimepicker>
                        </ul>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Initial Vacation Leave Credit Balance: </label>
                        <input class="form-control" type="number"  min=0 step="0.001" ng-model="employee.vac_leave_bal">
                    </div>
                    <div class="form-group">
                        <label>Initial Sick Leave Credit Balance: </label>
                        <input class="form-control" type="number" min=0 step="0.001" ng-model="employee.sick_leave_bal">
                    </div>
                    <div class="form-group">
                        <label>Highest Salary: </label>
                        <input class="form-control" type="number" min=0 step="0.01" ng-model="employee.highest_salary">
                    </div>
                    <div class="dropdown form-group" style="max-width:400px" ng-init="employee.first_day_compute = moment(employee.first_day_compute)">
                        <label>Computation Date:</label>
                        <a id="firstdaycompute" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                            <div class="input-group">
                                <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="employee.first_day_compute">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu">
                            <datetimepicker data-on-set-time="employee.first_day_compute = moment(employee.first_day_compute)"  data-ng-model="employee.first_day_compute" data-datetimepicker-config="{ dropdownSelector:'#firstdaycompute',minView:'day' }"></datetimepicker>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button ng-disabled="busy" class="btn btn-primary" type="Submit">Add Employee</button>
            </div>
        </form>
    </div>
</div>
