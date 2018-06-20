<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_nav" ng-init='init(<?=$employees?>)'>
    <h1>Employees</h1>
    <div>
        <a href="<?=base_url()."employee/leaveapplication"?>">Application For Leave</a> |
        <a href="<?=base_url()."employee/add"?>">Add Employee</a>
    </div>
    <div>
        <p>Search: <input type="text"/></p>
        <ul>
            <li ng-repeat="employee in employees">
                <a href="<?=base_url()."employee/display/"?>{{employee.emp_no}}">{{employee["emp_no"]}} {{employee["last_name"]}}, {{employee["first_name"]}} {{employee["middle_name"]}}</a>
            </li>
        </ul>
    </div>
</div>
