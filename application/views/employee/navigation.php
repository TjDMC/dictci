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
		<div class="row" style="width:100%">
			<div class="input-group input-group-lg col-sm-7" style="max-width:500px;margin-bottom:25px">
				<div class="input-group-prepend">
					<span class="input-group-text" id="inputGroup-sizing-lg">Search</span>
				</div>
				<input type="text" class="form-control" ng-model="searchTerm">
			</div>
			<div class="col-sm-5">
				<div style="float:right">
					Showing {{begin+1}} to {{begin+res2.length}} of {{res.length}}
					&nbsp;&nbsp;&nbsp;&nbsp;
					<span id="left" style="font-size: +35px" data-ng-click="prev()"><i class="fas fa-angle-left"></i></span>
					<select name="lim" ng-model="limit" ng-change="reBegin()">
						<option ng-value="1">1</option>
						<option ng-value="2">2</option>
						<option ng-value="3">3</option>
					</select>
					<span id="right" style="font-size: +35px" data-ng-click="next()"><i class="fas fa-angle-right"></i></span>
				</div>
			</div>
		</div>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" ng-repeat='employee in res = (employeeArray | filter:searchTerm) | limitTo:limit:begin as res2'  href="<?=base_url()."employee/display/"?>{{employee.emp_no}}">
                {{employee.string}}
            </a>
        </div>
    </div>
</div>
