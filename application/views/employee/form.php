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
			<p style="font-size:18px"><b>NAME:</b>&nbsp;&nbsp;<u>&nbsp;&nbsp;{{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}&nbsp;&nbsp;</u><p>
		</div>
		<div style="width:auto;float:right">
			<p style="font-size:18px"><b>Date Entered Service:</b>&nbsp;&nbsp;<u>&nbsp;&nbsp;{{employee.first_day}}&nbsp;&nbsp;</u></p>
		</div>
	</div>
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
			<th style="vertical-align:middle"><font size="2">HOUR</font></th>
			<th style="vertical-align:middle"><font size="2">MIN</font></th>
			<th style="vertical-align:middle"><font size="2">TOTAL</font></th>
			<th style="vertical-align:middle"><font size="2">HOUR</font></th>
			<th style="vertical-align:middle"><font size="2">MIN</font></th>
			<th style="vertical-align:middle"><font size="2">TOTAL</font></th>
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