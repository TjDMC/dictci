<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div ng-controller="employee_display" ng-init='init(<?=$employee ?>,<?=$leaves?>)'>
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
	<table class="table table-bordered table-sm" style="text-align:center;width:100%">
	<colgroup>
		<col width="7%">
		<col width="7%">
		<col width="17%">
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
		<col width="17%">
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
		<tbody ng-repeat="leave in leaves">
		<tr ng-repeat="range in leave.date_ranges">
			<td style="vertical-align:middle"><font size="2"></font></td>
			<td style="vertical-align:middle"><font size="2"></font></td>
			<td style="vertical-align:middle"><font size="2">{{range.start_date}}-{{range.end_date}}</font></td>
			<td style="vertical-align:middle"><font size="2"></font></td>
			<td style="vertical-align:middle"><font size="2"></font></td>
			<td style="vertical-align:middle"><font size="2">{{range.hours}}</font></td>
			<td style="vertical-align:middle"><font size="2">{{range.minutes}}</font></td>
			<td style="vertical-align:middle"><font size="2">{{Math.round((range.hours*60 + range.minutes)/60)}}</font></td>
			<td style="vertical-align:middle"><font size="2"></font></td>
			<td style="vertical-align:middle"><font size="2"></font></td>
			<td style="vertical-align:middle"><font size="2"></font></td>
			<td style="vertical-align:middle"><font size="2"></font></td>
			<td style="vertical-align:middle"><font size="2"></font></td>
			<td style="vertical-align:middle"><font size="2">{{leave.info.remarks}}</font></td>
		</tr>
		</tbody>
	</table>
</div>