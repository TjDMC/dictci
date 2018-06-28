<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<style>
.nav-button{
    color:black;
}
.nav-button:hover{
    color:gray;
}
.nav-button.disabled{
    color:lightgray;
}
</style>

<div ng-controller="employee_nav" ng-init='init(<?=$employees?>)'>
    <h1>Employees</h1>
    <div class="form-group">
        <a href="<?=base_url()."employee/leaveapplication"?>" role="button" class="btn btn-primary">Application For Leave</a>
        <a href="<?=base_url()."employee/add"?>" class="btn btn-primary">Add Employee</a>
    </div>
    <div ng-init='employeeArray =employeesToArray(<?=$employees?>)'>
		<div class="row justify-content-between" style="width:100%">
			<div class="input-group input-group-lg col-12 col-md-5 col-lg-4 mb-2">
				<div class="input-group-prepend">
					<span class="input-group-text">Search</span>
				</div>
				<input type="text" class="form-control" ng-model="searchTerm">
			</div>
			<div class="col row align-items-center justify-content-center">
                <span class="col-2 col-md-5 col-lg-3">Showing {{getDisplayNumber()}} of {{filteredEmployees.length}} results</span>

                <span class="col-5 col-md-7 col-lg-5 col-xl-4 row justify-content-center align-items-center">
                    <span class="col-1 nav-button" ng-class="{disabled:(page<=1)}" ng-click="page=1"><i class="fas fa-angle-double-left "></i></span>
                    <span class="col-1 nav-button" ng-class="{disabled:(page<=1)}"ng-click="page=page-1<1?1:page-1"><i class="fas fa-angle-left "></i></span>
					<select class="col-3 custom-select" style="max-width:75px;min-width:75px" name="lim col" ng-model="page">
						<option ng-repeat="number in numberToArray(filteredEmployees.length/limit) track by $index" ng-value="$index+1">{{$index+1}}</option>
                        <option ng-if="filteredEmployees.length==0" ng-value="1">1</option>
					</select>
					<span class="col-1 nav-button" ng-class="{disabled:(page>=getMaxPage())}" ng-click="page=page+1>getMaxPage()?(getMaxPage()==0?1:getMaxPage()):page+1"><i class="fas fa-angle-right "></i></span>
                    <span class="col-1 nav-button" ng-class="{disabled:(page>=getMaxPage())}" ng-click="page=getMaxPage()"><i class="fas fa-angle-double-right "></i></span>
                </span>

                <span class="col-5 col-md-12 col-lg-4 col-xl-5 row align-items-center justify-content-end">
                    <span class="mr-3">Results Per Page:</span>
                    <select class="custom-select" ng-model="limit" style="max-width:75px;min-width:75px">
                        <option ng-value="1">1</option>
                        <option ng-value="5">5</option>
                        <option ng-value="10">10</option>
                        <option ng-value="25">25</option>
                        <option ng-value="50">50</option>
                        <option ng-value="100">100</option>
                    </select>
                </span>
			</div>
		</div>
        <div class="list-group">
            <a class="list-group-item list-group-item-action" ng-repeat='employee in (filteredEmployees = (employeeArray | filter:searchTerm)) | limitTo:limit:getBegin() '  href="<?=base_url()."employee/display/"?>{{employee.emp_no}}">
                {{employee.string}}
            </a>
        </div>
    </div>
</div>
