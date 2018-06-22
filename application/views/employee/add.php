<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_add">
    <div>
        <h1>Add Employee</h1>
    </div>
    <form>
        <p>
            Employee No: <input type="text" ng-model="employee.emp_no"/>
        </p>
        <p>
            Last Name: <input type="text" ng-model="employee.last_name"/>
        </p>
        <p>
            Fist Name: <input type="text" ng-model="employee.first_name"/>
        </p>
        <p>
            Middle Name: <input type="text" ng-model="employee.middle_name"/>
        </p>
        <p>
            First Day: <input type="text" ng-model="employee.first_day"/>
        </p>
        <p>
            Salary: <input type="text" ng-model="employee.salary"/>
        </p>
        <p>
            Position: <input type="text" ng-model="employee.position"/>
        </p>
        <p>
            Vacation Leave Balance: <input type="text" ng-model="employee.vac_leave_bal"/>
        </p>
        <p>
            Sick Leave Balance: <input type="text" ng-model="employee.sick_leave_bal"/>
        </p>
        <p>
            <input type="Submit" ng-click="add()"/>
        </p>
    </form>
</div>
