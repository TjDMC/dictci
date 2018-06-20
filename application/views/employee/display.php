<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_display" ng-init='init(<?=$employee ?>,<?=$leaves?>)'>
    <p>Employee No: {{employee.emp_no}}</p>
    <p>Name: {{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}</p>
    <p>Sick Leave Balance: {{employee.sick_leave_bal}}</p>
    <p>Vacation Leave Balance: {{employee.vac_leave_bal}}</p>
    <p>
        <a href="<?=base_url()."employee/leaveapplication/{{employee.emp_no}}"?>">Application For Leave</a>
    </p>
    <p>
        <button onclick="alert('No')">Delete Employee Record</button>
    </p>
    <div>
        <h3>Leave History</h3>

    </div>
</div>
