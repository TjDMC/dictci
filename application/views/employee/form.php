<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div ng-controller="employee_display" ng-init='init(<?=$employee ?>,<?=$leaves?>)'>
	<div style="text-align:center">
		<p>RECORD OF LEAVES OF ABSENCE</p>
		<p>(UNDER R.A. 2266)</p>
	</div>
	<div style="background-color:#000000">
		<div style="width:auto;float:left">
			<p>NAME:&nbsp;&nbsp;<span>{{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}</span><p>
		</div>
		<div style="width:auto;float:right">
			<p>Date Entered Service:&nbsp;&nbsp;<span>{{employee.first_day}}</span></p>
		</div>
	</div>
	<div style="clear:center">
	<table class="table table-bordered" style="text-align:center;width:100%;table-layout:abs">
		<col width="50px">
		<col width="50px">
		<col width="130px">
		<col width="50px">
		<col width="50px">
		<col width="33px">
		<col width="33px">
		<col width="33px">
		<col width="33px">
		<col width="33px">
		<col width="33px">
		<col width="50px">
		<col width="50px">
		<col width="130px">
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
			<th style="vertical-align:middle"><font size="1">HOUR</font></th>
			<th style="vertical-align:middle"><font size="1">MIN</font></th>
			<th style="vertical-align:middle"><font size="1">TOTAL</font></th>
			<th style="vertical-align:middle"><font size="1">HOUR</font></th>
			<th style="vertical-align:middle"><font size="1">MIN</font></th>
			<th style="vertical-align:middle"><font size="1">TOTAL</font></th>
			<th style="vertical-align:middle">VAC</th>
			<th style="vertical-align:middle">SICK</th>
		</tr>
		<tr ng-repeat="i in [1,2,3,4,5,6,7,8,9]">
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
			<td style="vertical-align:middle"><font size="2">value{{i}}</font></td>
		</tr>
	</table>
	</div>
</div>