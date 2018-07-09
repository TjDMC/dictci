<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div>
	<div style="align:center">
		<p>RECORD OF LEAVES OF ABSENCE</p>
		<p>(UNDER R.A. 2266)</p>
	</div>
	<div>
		<div style="position:relative;align:left">
			<p>NAME:&nbsp&nbsp<u>asdf</u><p>
		</div>
		<div style="position:relative;align:right">
			<p>Date Entered Service:&nbsp&nbsp<u>sdfdf</u></p>
		</div>
	</div>
	<table class="table table-bordered">
		<tr>
			<th colspan="2" rowspan="2">Leave Earned</th>
			<th rowspan="3">WHEN TAKEN</th>
			<th colspan="2" rowspan="2">LEAVES TAKEN</th>
			<th colspan="3">UNDERTIME</th>
			<th rowspan="2" colspan="3">W/O PAY</th>
			<th colspan="2" rowspan="2">BALANCE</th>
			<th rowspan="3">REMARK</th>
		</tr>
		<tr>
			<th colspan="3">VAC</th>
		</tr>
		<tr>
			<th>VAC</th>
			<th>SICK</th>
			<th>VAC</th>
			<th>SICK</th>
			<th>HOUR</th>
			<th>MIN</th>
			<th>TOTAL</th>
			<th>HOUR</th>
			<th>MIN</th>
			<th>TOTAL</th>
			<th>VAC</th>
			<th>SICK</th>
		</tr>
		<tr ng-repeat="i in [1,2,3,4,5,6,7,8,9]">
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
			<td>value{{i}}</td>
		</tr>
	</table>
</div>