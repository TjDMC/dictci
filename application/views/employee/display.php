<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_display" ng-init='init(<?=$employee ?>,<?=$leaves?>)' class="card">
    <div class="card-header">
        <h1>Employee Information</h1>
    </div>
    <div class="card-body">
        <p>Employee No: {{employee.emp_no}}</p>
        <p>Name: {{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}</p>
        <p>First Day: {{employee.first_day}}</p>

        <div class="dropdown form-group" style="max-width:400px">
            <label>Leave Credits Balance: {{computeBal()}}</label>
            <a id="vacBalDate" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">As of: </span>
                    </div>
                    <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="bal_date">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                    </div>
                </div>
            </a>
            <ul class="dropdown-menu">
                <datetimepicker  data-ng-model="bal_date" data-datetimepicker-config="{ dropdownSelector:'#vacBalDate',startView:'month',minView:'month' }" data-on-set-time="formatBalDate()"></datetimepicker>
            </ul>
        </div>


        <div class="form-group">
            <a href="<?=base_url()."employee/leaveapplication/{{employee.emp_no}}"?>" class="btn btn-primary">Application For Leave</a>
            <button onclick="alert('No')" class="btn btn-primary">Delete Employee Record</button>
        </div>

        <div>
            <h3>Leave History</h3>
			<div class="form-group" style="overflow-x:auto">
				<div class="btn-group btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-outline-info">
						<input type="checkbox" name="filt" ng-checked="filter.every" ng-model="All" ng-init="All=true" ng-click="reFilter('Every')"></input>All
					</label>
					<label class="btn btn-outline-info" ng-class="{active:filter.vacation}">
						<input type="checkbox" name="filt" ng-checked="filter.vacation" ng-click="reFilter('Vacation')"></input>Vacation
					</label>
					<label class="btn btn-outline-info" ng-class="{active:filter.sick}">
						<input type="checkbox" name="filt" ng-checked="filter.sick" ng-click="reFilter('Sick')"></input>Sick
					</label>
					<label class="btn btn-outline-info" ng-class="{active:filter.maternity}">
						<input type="checkbox" name="filt" ng-checked="filter.maternity" ng-click="reFilter('Maternity')"></input>Maternity
					</label>
					<label class="btn btn-outline-info" ng-class="{active:filter.paternity}">
						<input type="checkbox" name="filt" ng-checked="filter.paternity" ng-click="reFilter('Paternity')"></input>Paternity
					</label>
					<label class="btn btn-outline-info" ng-class="{active:filter.others}">
						<input type="checkbox" name="filt" ng-checked="filter.others" ng-click="reFilter('Others')"></input>Others
					</label>
				</div>
			</div>
            <div class="table-responsive">
    			<table class="table" >
    				<tr>
    					<th>Leave Type</th>
    					<th>Remarks</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Hours</th>
                        <th>Minutes</th>
                        <th>Deducted Credits</th>
                    </tr>
    				<tbody ng-repeat="leave in leaves" ng-init="leave.info.show=true" ng-show="( leave.info.type=='Vacation'&& filter.vacation ? true:false ) || ( leave.info.type=='Sick'&& filter.sick ? true:false ) || ( leave.info.type=='Maternity'&& filter.maternity ? true:false ) || ( leave.info.type=='Paternity'&& filter.paternity ? true:false ) || ( leave.info.type!='Vacation' && leave.info.type!='Sick' && leave.info.type!='Maternity' && leave.info.type!='Paternity' && filter.others ? true:false )">
    					<tr style="background-color:lightgray" >
                            <td>
    							{{leave.info.type}}
    						</td>
    						<td colspan=5>
    							{{leave.info.remarks}}
    						</td>
                            <td style="text-align:right">
                                <!--<button data-toggle="modal" data-target="#editLeaveModal" class="btn btn-light" style="background-color:transparent;border:0px" title="Edit Leave" ng-click="modalOpened = true">
                                    <i class="fas fa-edit"></i>
                                </button>-->
    							<button class="btn btn-light" style="background-color:transparent;border:0px" title="Edit Leave" ng-click="openModal($index)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button ng-click="leave.info.show=!leave.info.show" class="btn btn-light" style="background-color:transparent;border:0px" title="{{leave.info.show?'Collapse':'Expand'}}">
                                    <i class="far {{leave.info.show?'fa-minus-square':'fa-plus-square'}}"></i>
                                </button>
                            </td>
    					</tr>
    					<tr ng-show="leave.info.show" ng-repeat="date_range in leave.date_ranges">
    						<td></td>
    						<td></td>
    						<td>{{date_range.start_date}}</td>
    						<td>{{date_range.end_date}}</td>
    						<td>{{date_range.hours}}</td>
    						<td>{{date_range.minutes}}</td>
    						<td>{{getDeductedCredits(leave.info.type,date_range)}}</td>
    					</tr>
    				</tbody>

    			</table>
            </div>
        </div>
    </div>
	<div class="modal fade" id="editLeaveModal" tabindex="-1" role="dialog" aria-labelledby="leaveModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="min-width:800px;max-width:1000px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leaveModelLabel">Edit Leave</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?= $this->load->view("employee/leave_application",array(
                        'isModal'=>true
                    ),true);?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
