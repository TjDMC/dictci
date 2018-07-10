<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div>
	<div style="float:center">
		<p>RECORD OF LEAVES OF ABSENCE</p>
		<p>(UNDER R.A. 2266)</p>
	</div>
	<div>
		<div style="background-color:#000000;width:auto;float:left">
			<p>NAME:&nbsp&nbsp<u>asdf</u><p>
		</div>
		<div style="background-color:#cccccc;width:auto;float:right">
			<p>Date Entered Service:&nbsp&nbsp<u>sdfdf</u></p>
		</div>
	</div>
	<div style="float:center">
	<table class="table table-bordered" style="width:auto;table-layout:fixed">
		<col width="50px">
		<col width="50px">
		<col width="100px">
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
		<col width="100px">
		<tr>
			<th colspan="2" rowspan="2" style="padding-top:35px;padding-left:20px">Leave Earned</th>
			<th rowspan="3" style="padding-top:35px;padding-left:15px">WHEN TAKEN</th>
			<th colspan="2" rowspan="2" style="padding-top:35px;padding-left:15px">LEAVES TAKEN</th>
			<th colspan="3" style="padding-left:20px">UNDERTIME</th>
			<th rowspan="2" colspan="3" style="padding-top:35px;padding-left:30px">W/O PAY</th>
			<th colspan="2" rowspan="2" style="padding-top:35px;padding-left:30px">BALANCE</th>
			<th rowspan="3" style="padding-top:50px;padding-left:35px">REMARK</th>
		</tr>
		<tr>
			<th colspan="3" style="padding-left:50px">VAC</th>
		</tr>
		<tr>
			<th>VAC</th>
			<th>SICK</th>
			<th>VAC</th>
			<th>SICK</th>
			<th style="padding-left:8px"><font size="1">HOUR</font></th>
			<th style="padding-left:8px"><font size="1">MIN</font></th>
			<th style="padding-left:8px"><font size="1">TOTAL</font></th>
			<th style="padding-left:8px"><font size="1">HOUR</font></th>
			<th style="padding-left:8px"><font size="1">MIN</font></th>
			<th style="padding-left:8px"><font size="1">TOTAL</font></th>
			<th>VAC</th>
			<th>SICK</th>
		</tr>
		<tr ng-repeat="i in [1,2,3,4,5,6,7,8,9]">
			<td><font size="2">value{{i}}</font></td>
			<td><font size="2">value{{i}}</font></td>
			<td><font size="2">value{{i}}</font></td>
			<td><font size="2">value{{i}}</font></td>
			<td><font size="2">value{{i}}</font></td>
			<td style="padding-left:5px"><font size="2">value{{i}}</font></td>
			<td style="padding-left:5px"><font size="2">value{{i}}</font></td>
			<td style="padding-left:5px"><font size="2">value{{i}}</font></td>
			<td style="padding-left:5px"><font size="2">value{{i}}</font></td>
			<td style="padding-left:5px"><font size="2">value{{i}}</font></td>
			<td style="padding-left:5px"><font size="2">value{{i}}</font></td>
			<td><font size="2">value{{i}}</font></td>
			<td><font size="2">value{{i}}</font></td>
			<td><font size="2">value{{i}}</font></td>
		</tr>
	</table>
	</div>
</div>