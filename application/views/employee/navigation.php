<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_nav" ng-init='init(<?=$employees?>)'>
    <h1>Employees</h1>
    <div>
        <a href="<?=base_url()."employee/leaveapplication"?>">Application For Leave</a> &#124;
        <a href="<?=base_url()."employee/add"?>">Add Employee</a>
    </div>
    <div ng-init='employeeArray =employeesToArray(<?=$employees?>)'>
        <p>Search: <input type="text" ng-model="searchTerm"/></p>
        <ul>
            <li ng-repeat='employee in employeeArray' ng-if="employee.string.toLowerCase().includes(searchTerm.toLowerCase()) || !searchTerm">
                <a  href="<?=base_url()."employee/display/"?>{{employee.emp_no}}">{{employee.string}}</a>
            </li>
        </ul>
    </div>
</div>
