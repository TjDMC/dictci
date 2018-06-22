<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_add">
    <div>
        <h1>Add Employee</h1>
    </div>
    <form ng-submit="add()">
        <div class="form-group">
            <label>Employee No: </label>
            <input class="form-control" type="text" ng-model="employee.emp_no" pattern="[0-9]{7}" required>
        </div>
        <div class="form-group">
            <label>Last Name: </label>
            <input class="form-control" type="text" ng-model="employee.last_name" required>
        </div>
        <div class="form-group">
            <label>First Name: </label>
            <input class="form-control" type="text" ng-model="employee.first_name" required>
        </div>
        <div class="form-group">
            <label>Middle Name: </label>
            <input class="form-control" type="text" ng-model="employee.middle_name" required>
        </div>
        <div class="dropdown form-group" style="max-width:400px">
            <label>First Day:</label>
            <a id="firstday" data-toggle="dropdown" data-target="dropdown" href="#">
                <div class="input-group">
                    <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="employee.first_day">
                    <span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
                </div>
            </a>
            <ul class="dropdown-menu">
                <datetimepicker  data-ng-model="employee.first_day" data-datetimepicker-config="{ dropdownSelector:'#firstday',minView:'day' }"></datetimepicker>
            </ul>
        </div>
        <div class="form-group">
            <label>Salary: </label>
            <input class="form-control" type="text" ng-model="employee.salary">
        </div>
        <div class="form-group">
            <label>Position: </label>
            <input class="form-control" type="text" ng-model="employee.position">
        </div>
        <div class="form-group" ng-init="employee.vac_leave_bal=0">
            <label>Vacation Leave Balance: </label>
            <input class="form-control" type="number" ng-model="employee.vac_leave_bal" required>
        </div>
        <div class="form-group" ng-init="employee.sick_leave_bal=0">
            <label>Sick Leave Balance: </label>
            <input class="form-control" type="number" ng-model="employee.sick_leave_bal" required>
        </div>
        <p>
            <button class="btn btn-primary" type="Submit">Add</button>
        </p>
    </form>
</div>
