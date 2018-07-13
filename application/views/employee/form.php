<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<style>
@media print{
	.none-printable{
		display: none;
	}
}
</style>
<div ng-controller="employee_display as dynhtml" ng-init='init(<?=$employee ?>,<?=$leaves?>)'>
	<div class="none-printable"><br/><br/>
		<div><p><font size="4"><b>Select printing range</b></font></p></div>
		<table>
			<tr>
				<td><p><font size="3"><b>From</b></font></p>
					<div class="dropdown">
						<a id="startDateRange" data-toggle="dropdown" data-target="dropdown" href="#">
							<div class="input-group">
								<input data-date-time-input="MMMM YYYY" class="form-control" type="text" data-ng-model="range_start_date" required>
								<div class="input-group-append">
									<span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
								</div>
							</div>
						</a>
						<ul class="dropdown-menu">
							<datetimepicker
								data-ng-model="range_start_date"
								data-datetimepicker-config="{ dropdownSelector:'#startDateRange', startView:'month', minView:'month', renderOn: 'end-date-changed' }"
								data-on-set-time="startDateOnSetTime()"
								data-before-render="startDateBeforeRender($dates,employee.first_day,leaves)"></datetimepicker>
						</ul>
					</div>
				</td>
				<td><p><font size="3"><b>To</b></font></p>
					<div class="dropdown">
						<a id="endDateRange" data-toggle="dropdown" data-target="dropdown" href="#">
							<div class="input-group">
								<input data-date-time-input="MMMM YYYY" class="form-control" type="text" data-ng-model="range_end_date" required>
								<div class="input-group-append">
									<span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
								</div>
							</div>
						</a>
						<ul class="dropdown-menu">
							<datetimepicker
								data-ng-model="range_end_date"
								data-datetimepicker-config="{ dropdownSelector:'#endDateRange', startView:'month', minView: 'month', renderOn: 'start-date-changed' }"
								data-on-set-time="endDateOnSetTime()"
								data-before-render="endDateBeforeRender($view,$dates,employee.first_day)"></datetimepicker>
						</ul>
					</div>
				</td>
				<td><p>&nbsp;</p>
					<button class="btn btn-success" ng-click="printAll(employee.first_day,leaves[0].date_ranges[leaves[0].date_ranges.length-1].end_date)">All Time</button>
				</td>
			</tr>
		</table><br/><br/>
	</div>
	<div style="text-align:center">
		<p style="font-size:25px"><b>RECORD OF LEAVES OF ABSENCE</b></p>
		<p><b>(UNDER R.A. 2266)</b></p>
	</div>
	<div style="background-color:#000000">
		<div style="width:auto;float:left">
			<p style="font-size:18px"><b>NAME:</b>&nbsp;&nbsp;<span>&nbsp;&nbsp;{{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}&nbsp;&nbsp;</span><p>
		</div>
		<div style="width:auto;float:right">
			<p style="font-size:18px"><b>Date Entered Service:</b>&nbsp;&nbsp;<span>&nbsp;&nbsp;{{employee.first_day}}&nbsp;&nbsp;</span></p>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table table-bordered table-sm" style="text-align:center;width:100%">
		<colgroup>
			<col width="7%">
			<col width="7%">
			<col width="22%">
			<col width="7%">
			<col width="7%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="4%">
			<col width="7%">
			<col width="7%">
			<col width="12%">
		</colgroup>
			<tr>
				<th colspan="2" rowspan="2" style="vertical-align:middle">Leave Earned</th>
				<th rowspan="3" style="vertical-align:middle">WHEN TAKEN</th>
				<th colspan="2" rowspan="2" style="vertical-align:middle">LEAVES TAKEN</th>
				<th colspan="3" style="vertical-align:middle">UNDERTIME</th>
				<th rowspan="2" colspan="3" style="vertical-align:middle">W/O PAY</th>
				<th colspan="2" rowspan="2" style="vertical-align:middle">BALANCE</th>
				<th rowspan="3" style="vertical-align:middle">REMARK</th>
			</tr>
			<tr>
				<th colspan="3">VAC</th>
			</tr>
			<tr>
				<th style="vertical-align:middle">VAC</th>
				<th style="vertical-align:middle">SICK</th>
				<th style="vertical-align:middle">VAC</th>
				<th style="vertical-align:middle">SICK</th>
				<th style="vertical-align:middle"><font size="2">HOUR</font></th>
				<th style="vertical-align:middle"><font size="2">MIN</font></th>
				<th style="vertical-align:middle"><font size="2">TOTAL</font></th>
				<th style="vertical-align:middle"><font size="2">HOUR</font></th>
				<th style="vertical-align:middle"><font size="2">MIN</font></th>
				<th style="vertical-align:middle"><font size="2">TOTAL</font></th>
				<th style="vertical-align:middle">VAC</th>
				<th style="vertical-align:middle">SICK</th>
			</tr>
			<tbody ng-repeat="leave in leaveFiltered">
				<tr ng-repeat="range in leave.date_range">
					<td style="vertical-align:middle"><font size="2">{{leave.vac_bal}}</font></td>
					<td style="vertical-align:middle"><font size="2">{{leave.sick_bal}}</font></td>
					<td style="vertical-align:middle"><font size="2">{{shortDate(range.start) +' - '+ shortDate(range.end)}}</font></td>
					<td style="vertical-align:middle"><font size="2">{{leave.type=='Vacation' ? range.credits : ''}}</font></td>
					<td style="vertical-align:middle"><font size="2">{{leave.type=='Sick' ? range.credits : ''}}</font></td>
					<td style="vertical-align:middle"><font size="2">{{!leave.is_wop ? range.hours : '' | numNullRounder}}</font></td>
					<td style="vertical-align:middle"><font size="2">{{!leave.is_wop ? range.minutes : '' | numNullRounder}}</font></td>
					<td style="vertical-align:middle"><font size="2">{{!leave.is_wop ? ((range.hours*60 + range.minutes)/60) : '' | numNullRounder:2}}</font></td>
					<td style="vertical-align:middle"><font size="2">{{leave.is_wop ? range.hours : '' | numNullRounder}}</font></td>
					<td style="vertical-align:middle"><font size="2">{{leave.is_wop ? range.minutes : '' | numNullRounder}}</font></td>
					<td style="vertical-align:middle"><font size="2">{{leave.is_wop ? ((range.hours*60 + range.minutes)/60) : '' | numNullRounder:2}}</font></td>
					<td style="vertical-align:middle"><font size="2"></font></td>
					<td style="vertical-align:middle"><font size="2"></font></td>
					<td style="vertical-align:middle"><font size="2">{{leave.remarks}}</font></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
