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

<div ng-controller="employee_nav" ng-init='init(<?=$employees?>)' class="card">
    <div class="card-header">
        <h1>Employees</h1>
    </div>
    <div class="card-body p-2 p-lg-5">
        <div class="form-group" ng-if="<?=isset($editable)?$editable:'true'?>">
            <a href="<?=base_url()."employee/add"?>" class="btn btn-primary">Add Employee</a>
        </div>
        <div ng-init='employeeArray =employeesToArray(<?=$employees?>)'>
    		<div class="row justify-content-between">
    			<div class="input-group input-group-lg col-12 col-md-5 col-lg-4 mb-2" style="max-height:50px">
    				<div class="input-group-prepend">
    					<span class="input-group-text">Search</span>
    				</div>
    				<input type="text" class="form-control" ng-model="searchTerm">
    			</div>
    			<div class="col row align-items-center justify-content-center container">
                    <span class="col text-lg-right text-center" style="min-width:219px">Showing {{(page-1)*limit+1}} to {{(page-1)*limit+getDisplayNumber()}} of {{filteredEmployees.length}} results</span>

                    <span class="col justify-content-center text-center" style="min-width:205px">
                        <span class="nav-button" ng-class="{disabled:(page<=1)}" ng-click="page=1"><i class="fas fa-angle-double-left fa-fw"></i></span>
                        <span class="nav-button" ng-class="{disabled:(page<=1)}"ng-click="page=page-1<1?1:page-1"><i class="fas fa-angle-left fa-fw"></i></span>
    					<select class="custom-select" style="max-width:75px;min-width:75px" name="lim col" ng-model="page">
    						<option ng-repeat="number in numberToArray(filteredEmployees.length/limit) track by $index" ng-value="$index+1">{{$index+1}}</option>
                            <option ng-if="filteredEmployees.length==0" ng-value="1">1</option>
    					</select>
    					<span class="nav-button" ng-class="{disabled:(page>=getMaxPage())}" ng-click="page=page+1>getMaxPage()?(getMaxPage()==0?1:getMaxPage()):page+1"><i class="fas fa-angle-right fa-fw"></i></span>
                        <span class="nav-button" ng-class="{disabled:(page>=getMaxPage())}" ng-click="page=getMaxPage()"><i class="fas fa-angle-double-right fa-fw"></i></span>
                    </span>

                    <span class="col align-items-center text-xl-left text-center" style="min-width:232px">
                        <span class="mr-1">Results Per Page:</span>
                        <select class="custom-select" ng-model="limit" style="max-width:75px;min-width:75px">
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
</div>
