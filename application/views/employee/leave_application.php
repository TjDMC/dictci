<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!isset($isModal)){
    $isModal = false;
}
if(!isset($employees)){
    $employees = '""';
}
if(!isset($employee)){
    $employee= '""';
}
if(!isset($events)){
    $events= '""';
}
?><!DOCTYPE html>

<script>
 $('body') .on('show.bs.dropdown', '.table-responsive', function () { $(this).css("overflow", "visible"); }) .on('hide.bs.dropdown', '.table-responsive', function () { $(this).css("overflow", "auto"); });
</script>

<style>
#search-results {
	position: absolute;
    max-width: 500px;
    max-height: 200px;
    border: 1px solid #dedede;
    border-radius: 3px;
    box-sizing: border-box;
    overflow-y: auto;
	top:100%;
	left:15px;
	right:15px;
	z-index: 99;
}

.search-result {
    background: white;
    padding: 10px;
}

.search-result:nth-child(even) {
    background: #fafafa;
}

.search-result.selected{
    background: gray;
}

.search-result:hover{
    cursor:pointer;
}
</style>

<div ng-controller="leave_application" ng-init='init(<?=$employees?>,<?=$employee?>,<?=$events?>)' <?=!$isModal?'class="card"':''?>>

    <?php if (!$isModal) :?>
        <div class="card-header">
            <h2>Application for Leave</h2>
        </div>
    <?php endif ?>

    <div <?=!$isModal?'class="card-body p-4"':''?>>
        <form ng-submit="submit(<?=$isModal?'true':'false'?>)" autocomplete="off">
            <?php if(!$isModal) :?>

                <div class="form-group row justify-content-start" >
                    <div class="col-12 col-sm-4 col-lg-3 col-xl-2">
                        <label>Employee No:</label>
                        <input  class="form-control" ng-keydown="onKeyDown($event,'emp_no')" type="text" ng-model="employee.emp_no" ng-blur="searchFocusEmpNo=false" ng-focus="searchFocusEmpNo=true" maxlength="7" required>
                        <div id="search-results" ng-show="searchFocusEmpNo">
                            <div class="search-result" ng-class="{selected:focusedEmployeeIndex===$index}" ng-mouseover="onMouseOver($index)" ng-repeat="employee in employees | employeeSearch:'emp_no':employee.emp_no" ng-mousedown="setEmployee(employee.emp_no)">
                                <span>{{employee.emp_no}} - {{employee.name}}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-7 col-lg-5 col-xl-3">
                        <label>Employee Name: </label>
                        <input  class="form-control" ng-keydown="onKeyDown($event,'name')" type="text" ng-model="employee.name" ng-blur="searchFocusName=false" ng-focus="searchFocusName=true" required>
                        <div id="search-results" ng-show="searchFocusName">
                            <div class="search-result" ng-class="{selected:focusedEmployeeIndex===$index}" ng-mouseover="onMouseOver($index)" ng-repeat="employee in employees | employeeSearch:'name':employee.name" ng-mousedown="setEmployee(employee.emp_no)">
                                <span>{{employee.emp_no}} - {{employee.name}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>
			<div class="form-group" style="overflow-x: auto">
                <p>Leave Type:</p>
    			<div class="btn-group btn-group-toggle" data-toggle="buttons">
    			    <label id="leaveTypeVacation" class="btn btn-outline-info" >
    				    <input type="radio" name="type" ng-value="'Vacation'" ng-model="leave.info.type" autocomplete="off" required> Vacation
    			    </label>
    				<label id="leaveTypeSick" class="btn btn-outline-info" >
    				    <input type="radio" name="type" ng-value="'Sick'" ng-model="leave.info.type" autocomplete="off" required> Sick
    			    </label>
    				<label id="leaveTypeMaternity" class="btn btn-outline-info" >
    				    <input type="radio" name="type" ng-value="'Maternity'" ng-model="leave.info.type" autocomplete="off" required> Maternity
    			    </label>
    				<label id="leaveTypePaternity" class="btn btn-outline-info" >
    				    <input type="radio" name="type" ng-value="'Paternity'" ng-model="leave.info.type" autocomplete="off" required> Paternity
    			    </label>
    				<label id="leaveTypeOthers" class="btn btn-outline-info">
    				    <input type="radio" name="type" ng-value="'Others'" ng-model="leave.info.type" autocomplete="off" required> Others
    			    </label>
    			</div>
    			<div class="form-group" ng-if="leave.info.type=='Others'" style="max-width:700px">
    				<div class="input-group mt-2">
    					<div class="input-group-prepend">
    						<span class="input-group-text">
    							Specify:
    						</span>
    					</div>
    					<input class="form-control" type="text" ng-model="leave.info.type_others" ng-required="leave.info.type=='Others'"/>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" ng-click="leave.info.type_others='Forced Leave'">Forced Leave</button>
                            <button type="button" class="btn btn-outline-secondary" ng-click="leave.info.type_others='Special Leave'">Special Leave</button>
                        </div>
    				</div>
    			</div>
            </div>
            <div class="table-responsive">
                <label>Date Ranges: </label>
                <table class="table table-bordered">
    				<tr>
    					<th></th>
    					<th>From</th>
    					<th>To</th>
    					<th>Hours</th>
    					<th>Minutes</th>
    					<th>Credits Equivalent</th>
    				</tr>
                    <tr ng-repeat="date_range in leave.date_ranges track by $index">
                        <td style="width:40px"><button class="btn btn-light" type="button" ng-click="addOrDeleteRange(1,$index)"><i class="fas fa-times"></i></button></td>
                        <td><div class="dropdown" style="min-width:200px;max-width:400px">
                            <a id="startdate{{$index}}" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                <div class="input-group">
                                    <input data-date-time-input="{{dateFormat}}" class="form-control" type="text" data-ng-model="date_range.start_date" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <datetimepicker  data-ng-model="date_range.start_date" data-datetimepicker-config="{ dropdownSelector:'#startdate'+$index,minView:'day' }" data-on-set-time="startDateSet($index)"></datetimepicker>
                            </ul>
                        </div></td>

                        <td><div class="dropdown" style="min-width:200px;max-width:400px">
                            <a id="enddate{{$index}}" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                <div class="input-group">
                                    <input data-date-time-input="{{dateFormat}}" class="form-control" type="text" data-ng-model="date_range.end_date" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <datetimepicker data-before-render="endDateRender($view,$dates,$index)" data-ng-model="date_range.end_date" data-datetimepicker-config="{ dropdownSelector:'#enddate'+$index, minView:'day',renderOn:'startDateSet' }" data-on-set-time="endDateSet($index)"></datetimepicker>
                            </ul>
                        </div></td>
                        <td style="max-width:200px">
                            <div class="input-group">
                                <input class="form-control" min="0" step="1" type="number" ng-model="date_range.hours" ng-disabled="!date_range.start_date" required>
                            </div>
                        </td>
                        <td style="max-width:200px;margin-left:10px">
                            <div class="input-group">
                                <input class="form-control" min="0" step="1" type="number" ng-model="date_range.minutes" ng-disabled="!date_range.start_date" required>
                            </div>
                        </td>
    					<td>
    						<span>{{date_range.hours/8+date_range.minutes/(60*8) | number:3}}</span>
    					</td>
                    </tr>
                    <tr>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td>Total Credits: {{getTotalCredits()|number:3}}</td>
    				</tr>
                </table>
    			<button class="btn btn-secondary form-group" type="button" ng-click="addOrDeleteRange(0)"><span>Add Range</span></button>
            </div>

    		<div class="form-group">
    			<label>Remarks: </label>
                <input class="form-control" type="textarea" ng-model="leave.info.remarks"/>
    		</div>
            <p>
    			<button type="submit" class="btn btn-primary">Submit</button>
                <?php if ($isModal) :?>
                    <button type="button" ng-click="deleteLeave(leave.info.leave_id)" class="btn btn-danger">Delete Leave</button>
                <?php endif?>
            </p>
        </form>
    </div>
</div>
