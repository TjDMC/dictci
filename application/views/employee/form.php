<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<style>
@media print{
	.none-printable{
		display: none;
	}
	.card{
		border:none;
		margin:0;
		padding:0;
	}
	.card-body{
		border:none;
		margin:0;
		padding:0;
	}
}
</style>
<div class="card" ng-controller="employee_display as dynhtml" ng-init='init(<?=$employee ?>,<?=$leaves?>)'>
	<div class="card-header none-printable">
		<h3>Print Records</h3>
	</div>
	<div class="card-body">
		<div class="none-printable alert alert-info">
			<h5 class="font-weight-bold mb-3">Select printing range</h5>
			<table class="table">
				<tr>
					<th>From</th>
					<th>To</th>
					<th></th>
				</tr>
				<tr>
					<td>
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
					<td>
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
					<td>
						<button class="btn btn-success" ng-click="printAll(employee.first_day,leaves[0].date_ranges[leaves[0].date_ranges.length-1].end_date)">All Time</button>
					</td>
				</tr>
			</table>
			<div class="text-center">
				<button class="btn btn-primary" onclick="window.print()">Print</button>
				<a class="btn btn-primary" style="color:white" ng-click="form.generate()" ng-href="{{form.link()}}" download="{{employee.last_name}}_{{moment().format('YYYY-MM-DD')}}_leaverecords.csv">Export As CSV</a>
			</div>
		</div>
		<div>
			<div style="text-align:center">
				<h2 class="font-weight-bold">RECORD OF LEAVES OF ABSENCE</h2>
				<p class="font-weight-bold">(UNDER R.A. 2266)</p>
			</div>
			<div>
				<div style="width:auto;float:left">
					<p style="font-size:18px"><span class="font-weight-bold">NAME:</span>&nbsp;&nbsp;<span>&nbsp;&nbsp;{{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}&nbsp;&nbsp;</span><p>
				</div>
				<div style="width:auto;float:right">
					<p style="font-size:18px"><span class="font-weight-bold">Date Entered Service:</span>&nbsp;&nbsp;<span>&nbsp;&nbsp;{{employee.first_day}}&nbsp;&nbsp;</span></p>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table table-bordered table-sm" export-csv="form" style="text-align:center;width:100%">
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
						<th colspan="2" rowspan="2" class="align-middle">Leave Earned</th>
						<th rowspan="3" class="align-middle">WHEN TAKEN</th>
						<th colspan="2" rowspan="2" class="align-middle">LEAVES TAKEN</th>
						<th colspan="3" class="align-middle">UNDERTIME</th>
						<th rowspan="2" colspan="3" class="align-middle">W/O PAY</th>
						<th colspan="2" rowspan="2" class="align-middle">BALANCE</th>
						<th rowspan="3" class="align-middle">REMARK</th>
					</tr>
					<tr>
						<th colspan="3">VAC</th>
					</tr>
					<tr>
						<th class="align-middle">VAC</th>
						<th class="align-middle">SICK</th>
						<th class="align-middle">VAC</th>
						<th class="align-middle">SICK</th>
						<th class="align-middle"><h6 class="mb-0"><small class="font-weight-bold">HOUR</small></h6>
						<th class="align-middle"><h6 class="mb-0"><small class="font-weight-bold">MIN</small></h6>
						<th class="align-middle"><h6 class="mb-0"><small class="font-weight-bold">TOTAL</small></h6>
						<th class="align-middle"><h6 class="mb-0"><small class="font-weight-bold">HOUR</small></h6>
						<th class="align-middle"><h6 class="mb-0"><small class="font-weight-bold">MIN</small></h6>
						<th class="align-middle"><h6 class="mb-0"><small class="font-weight-bold">TOTAL</small></h6>
						<th class="align-middle">VAC</th>
						<th class="align-middle">SICK</th>
					</tr>
					<tbody ng-repeat="leave in leaveFiltered">
						<tr ng-repeat="range in leave.date_range | filter:dateRangeFilter">
							<td class="align-middle"><h6 class="mb-0"><small>{{leave.vac_bal}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{leave.sick_bal}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{shortDate(range.start) +' - '+ shortDate(range.end)}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{leave.type=='Vacation' ? range.credits : ''}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{leave.type=='Sick' ? range.credits : ''}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{!leave.is_wop ? range.hours : '' | numNullRounder}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{!leave.is_wop ? range.minutes : '' | numNullRounder}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{!leave.is_wop ? ((range.hours*60 + range.minutes)/60) : '' | numNullRounder:2}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{leave.is_wop ? range.hours : '' | numNullRounder}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{leave.is_wop ? range.minutes : '' | numNullRounder}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{leave.is_wop ? ((range.hours*60 + range.minutes)/60) : '' | numNullRounder:2}}</small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small></small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small></small></h6></td>
							<td class="align-middle"><h6 class="mb-0"><small>{{leave.remarks}}</small></h6></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
