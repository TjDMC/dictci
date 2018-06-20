<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_display" ng-init='init(<?=$employee ?>)'>
    <p>{{employee.emp_no}}</p>
    <p>{{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}</p>
    <p>Sick Leave Balance: {{employee.sick_leave_bal}}</p>
    <p>Vacation Leave Balance: {{employee.vac_leave_bal}}</p>
</div>
