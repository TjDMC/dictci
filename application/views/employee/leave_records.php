<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

<div ng-controller="employee_leave_records" ng-init='init(<?=$employee?>,<?=$events?>)'>
    <form ng-submit="submit(leave.info.leave_id?'edit':'add')" autocomplete="off">
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
                        <button type="button" class="btn btn-outline-secondary" ng-click="leave.info.type_others='Undertime'">Undertime</button>
                    </div>
				</div>
			</div>
        </div>
        <div class="form-group">
            <div class="form-check">
                <input class="form-check-input" ng-true-value=1 ng-false-value=0 type="checkbox" id="withoutPay" ng-model="leave.info.is_without_pay"></input>
                <label class="form-check-label" for="withoutPay">Without Pay</label>
            </div>
        </div>
        <div class="table-responsive">
            <label>Date Ranges: </label>

            <table class="table table-bordered">
				<tr>
					<th></th>
					<th>From</th>
					<th>To</th>
                    <th>Credits</th>
					<th>Hours</th>
					<th>Minutes</th>
                    <th>Holiday Conflicts</th>
				</tr>
                <tr ng-repeat="date_range in leave.date_ranges track by $index">
                    <td style="width:40px"><button class="btn btn-light" type="button" ng-click="addOrDeleteRange(1,$index)"><i class="fas fa-times"></i></button></td>
                    <td>
                        <div class="dropdown" style="min-width:200px;max-width:400px">
                            <a id="startdate{{$index}}" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                <div class="input-group">
                                    <input data-date-time-input="{{dateFormat}}" class="form-control" type="text" data-ng-model="date_range.start_date" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <datetimepicker data-before-render="startDateRender($view,$dates,$index)" data-ng-model="date_range.start_date" data-datetimepicker-config="{ dropdownSelector:'#startdate'+$index,minView:'day' }" data-on-set-time="startDateSet($index)"></datetimepicker>
                            </ul>
                        </div>
                    </td>

                    <td>
                        <div class="dropdown" style="min-width:200px;max-width:400px">
                            <a id="enddate{{$index}}" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                <div class="input-group">
                                    <input data-date-time-input="{{dateFormat}}" class="form-control" type="text" data-ng-model="date_range.end_date" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <datetimepicker data-before-render="startDateRender($view,$dates,$index);endDateRender($view,$dates,$index)" data-ng-model="date_range.end_date" data-datetimepicker-config="{ dropdownSelector:'#enddate'+$index, minView:'day',renderOn:'startDateSet' }" data-on-set-time="endDateSet($index)"></datetimepicker>
                            </ul>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <input class="form-control" min="0" step="0.001" type="number" ng-change="updateCredits(date_range,'credits')" ng-model="date_range.credits" ng-disabled="!date_range.start_date" required>
                        </div>
					</td>
                    <td style="max-width:200px">
                        <div class="input-group">
                            <input class="form-control" min="0" step="1" type="number" ng-change="updateCredits(date_range,'time')" ng-model="date_range.hours" ng-disabled="!date_range.start_date" required>
                        </div>
                    </td>
                    <td style="max-width:200px;margin-left:10px">
                        <div class="input-group">
                            <input class="form-control" min="0" step="1" type="number" ng-change="updateCredits(date_range,'time')" ng-model="date_range.minutes" ng-disabled="!date_range.start_date" required>
                        </div>
                    </td>
                    <td class="align-middle">
                        {{date_range.holiday_conflicts}}
                    </td>
                </tr>
                <tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td colspan="2">Total Credits: {{getTotalCredits()|number:3}}</td>
				</tr>
            </table>
			<button class="btn btn-secondary form-group" type="button" ng-click="addOrDeleteRange(0)"><span>Add Range</span></button>
        </div>

		<div class="form-group">
			<label>Remarks: </label>
            <input class="form-control" type="textarea" ng-model="leave.info.remarks"/>
		</div>
        <p>
			<button ng-disabled="busy" type="submit" class="btn btn-primary">Submit</button>
            <button type="button" ng-if="leave.info.leave_id" ng-click="deleteLeave(leave.info.leave_id)" class="btn btn-danger">Delete Leave</button>
        </p>
    </form>
</div>
