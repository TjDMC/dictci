<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="leave_application" ng-init='init(<?=$employees?><?=isset($employee)?','.$employee:''?>)'>
    <div>
        <h1>Application for Leave</h1>
        <form>
            <p>
                Employee No: <input type="text" ng-model="employee.emp_no">
            </p>
            <p>
                Last Name: <input type="text" ng-model="employee.last_name">
            </p>
            <p>
                First Name: <input type="text" ng-model="employee.first_name">
            </p>
            <p>
                Middle Name: <input type="text" ng-model="employee.middle_name">
            </p>
        </form>
    </div>
</div>
