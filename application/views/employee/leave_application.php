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
?><!DOCTYPE html>

<script>
 $('body') .on('show.bs.dropdown', '.table-responsive', function () { $(this).css("overflow", "visible"); }) .on('hide.bs.dropdown', '.table-responsive', function () { $(this).css("overflow", "auto"); });
</script>

<div ng-controller="leave_application" ng-init='init(<?=$employees?>,<?=$employee?>)'>
    <div>
        <?php if (!$isModal) :?>
            <h1>Application for Leave</h1>
        <?php endif ?>
        <form ng-submit="submit()" autocomplete="off">
            <?php if(!$isModal) :?>
    			<div class="form-group autocomplete" >
                    <label>Employee No: </label>
                    <input id="empNo" class="form-control" type="text" ng-model="employee.emp_no" ng-blur="fillName()" pattern="[0-9]{7}" maxlength="7" required>
                </div>
                <div class="form-group autocomplete">
                    <label>Employee Name: </label>
                    <input id="empName" class="form-control" type="text" ng-model="employee.name" required>
                </div>
            <?php endif ?>
			<div class="form-group">
                <p>Leave Type:</p>
				<div class="btn-group btn-group-toggle" data-toggle="buttons">
				    <label class="btn btn-outline-info">
					    <input type="radio" name="type" value="Vacation" ng-model="leave.info.type" autocomplete="off" required> Vacation
				    </label>
					<label class="btn btn-outline-info">
					    <input type="radio" name="type" value="Sick" ng-model="leave.info.type" autocomplete="off" required> Sick
				    </label>
					<label class="btn btn-outline-info">
					    <input type="radio" name="type" value="Maternity" ng-model="leave.info.type" autocomplete="off" required> Maternity
				    </label>
					<label class="btn btn-outline-info">
					    <input type="radio" name="type" value="Paternity" ng-model="leave.info.type" autocomplete="off" required> Paternity
				    </label>
					<label class="btn btn-outline-info">
					    <input type="radio" name="type" value="Others" ng-model="leave.info.type" autocomplete="off" required> Others
				    </label>
				</div>
				<div class="form-group" ng-if="leave.info.type=='Others'" style="max-width:400px">
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text">
								Specify:
							</span>
						</div>
						<input class="form-control" type="text" ng-model="leave.info.type_others" ng-required="leave.info.type=='Others'"/>
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
                        <td style="width:40px"><button class="btn btn-light" type="button" ng-click="rangeAction(1,$index)"><i class="fas fa-times"></i></button></td>
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
                                <datetimepicker data-before-render="endDateRender($view,$dates,$index)" data-ng-model="date_range.end_date" data-datetimepicker-config="{ dropdownSelector:'#enddate'+$index, minView:'day' }" data-on-set-time="endDateSet($index)"></datetimepicker>
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
				<button class="btn btn-secondary form-group" type="button" ng-click="rangeAction(0)"><span>Add Range</span></button>
            </div>

			<div class="form-group">
				<label>Remarks: </label>
                <input class="form-control" type="textarea" ng-model="leave.info.remarks"/>
			</div>
            <?php if(!$isModal):?>
            <p>
				<button type="submit" class="btn btn-primary"/>Submit</button>
            </p>
            <?php endif?>
        </form>
        <?php if(!$isModal):?>
    		<a hidden>
    			{{autocomplete()}}
    		</a>
        <?php endif ?>
    </div>
</div>
