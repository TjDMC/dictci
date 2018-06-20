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
                Employee Name: <input type="text" ng-model="employee.name">
            </p>
            <p>
                Start Date: <input type="datetime-local" ng-model="leave.start_date" ng-init="debug()" ng-change="debug()"/>
            </p>
            <p>
                End Date: <input type="datetime-local" ng-model="leave.end_date"/>
            </p>
			<p>
				Remark: <input type="textarea" ng-model="leave.remark"/>
			</p>
            <div>
                Leave Type:
                <p><input type="radio" ng-model="leave.type" value="vacation"/>Vacation</p>
                <p><input type="radio" ng-model="leave.type" value="sick"/>Sick</p>
                <p><input type="radio" ng-model="leave.type" value="maternity"/>Maternity</p>
                <p><input type="radio" ng-model="leave.type" value="paternity"/>Paternity</p>
                <p><input type="radio" ng-model="leave.type" value="others"/>Others</p>
                <p ng-if="leave.type=='others'">Specify: <input  type="text" ng-model="leave.type_others"/></p>
            </div>
            <p>
                <input type="submit" ng-click="submit()"/>
            </p>
        </form>
    </div>
</div>
