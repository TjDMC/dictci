<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_nav" ng-init='init(<?=$employees?>)'>
    <h1>Employees</h1>
    <div class="form-group">
        <a href="<?=base_url()."employee/leaveapplication"?>" role="button" class="btn btn-primary">Application For Leave</a>
        <a href="<?=base_url()."employee/add"?>" class="btn btn-primary">Add Employee</a>
    </div>
    <div ng-init='employeeArray =employeesToArray(<?=$employees?>)'>
        <div class="input-group input-group-lg" style="max-width:500px;margin-bottom:25px">
            <div class="input-group-addon">
                <span class="input-group-text" id="inputGroup-sizing-lg">Search</span>
            </div>
            <input type="text" class="form-control" ng-model="searchTerm">
        </div>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" ng-repeat='employee in employeeArray | filter:searchTerm'  href="<?=base_url()."employee/display/"?>{{employee.emp_no}}">
                {{employee.string}}
            </a>
        </div>
    </div>
</div>