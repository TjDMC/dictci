<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_display" ng-init='init(<?=$employee ?>,<?=$leaves?>)' class="card">
    <div class="card-header">
        <h1>Employee Information</h1>
    </div>
    <div class="card-body">
        <div class="alert alert-dark">
            <p><span class="font-weight-bold">Employee No:</span> {{employee.emp_no}}</p>
            <p><span class="font-weight-bold">Name:</span> {{employee.surname}}, {{employee.first_name}} {{employee.middle_name}}</p>
            <p><span class="font-weight-bold">Employement Date:</span> {{employee.first_day_employ.format('MMMM DD, YYYY')}}</p>
            <p><span class="font-weight-bold">Initial Credits:</span> Vacation {{employee.vac_leave_bal}} | Sick {{employee.sick_leave_bal}} </p>
            <p><span class="font-weight-bold">Computation Date:</span> {{employee.first_day_compute.format('MMMM DD, YYYY')}}</p>
            <div class="text-right">
                <button class="btn btn-primary" ng-click="showEditEmployeeModal()">Edit Employee Info</button>
            </div>
        </div>

        <div class="form-group text-center">
            <button class="btn btn-success" data-toggle="modal" data-target="#computeBalModal" ng-click="longComputation(this,'balance',getBalance)">Leave Credits Balance</button>
            <button class="btn btn-success" data-toggle="modal" data-target="#leaveCreditStatisticsModal" ng-click="$broadcast('openStatisticsModal')" type="button">Leave Credits Statistics</button>
            <button class="btn btn-success" data-toggle="modal" data-target="#recordOfLeavesModal" ng-click="initRecordOfLeaves()" type="button">Print Record of Leaves</button>
        </div>

        <div class="form-group text-center">
            <button title="Edit Leave" ng-click="edit=false;$broadcast('openLeaveModal')" class="btn btn-primary">Add Leave Record</button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#monetizeLeaveModal">Monetize Leave</button>
            <button type="button" ng-click="setTerminalDate()" class="btn btn-primary" data-toggle="modal" data-target="#terminalModal">Terminal Leave Benefits</button>
        </div>

        <div>
            <h3>Leave History</h3>
			<div class="form-group row">
				<div class="col-12 col-md-6 btn-group btn-group-toggle table-responsive">
					<label class="btn btn-outline-info" ng-class="{active:filter.type.every}" ng-click="reFilter('every')">All</label>
                    <label ng-init="initFilters()" ng-repeat="type_filter in type_filters" class="btn btn-outline-info text-capitalize" ng-class="{active:filter.type[type_filter]}" ng-click="reFilter(type_filter)">{{type_filter}}</label>
				</div>
                <div class="col-12 col-md-6 text-right table-responsive">
                    <div class="btn-group">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info">
                                <input type="checkbox" ng-true-value=false ng-false-value=true ng-model="filter.date.enabled" autocomplete="off"/> All Time
                            </label>
                        </div>
                        <div ng-if="filter.date.enabled" class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info" ng-class="{active:filter.date.precision == 'year'}" ng-click="filter.date.precision='year';changeDateFilter()">
                                <input type="radio" name="leave_date_filter_precision" autocomplete="off" ng-checked="filter.date.precision=='year'"> Year
                            </label>
                            <label class="btn btn-outline-info" ng-class="{active:filter.date.precision == 'month'}" ng-click="filter.date.precision='month';changeDateFilter()">
                                <input type="radio" name="leave_date_filter_precision" autocomplete="off" ng-checked="filter.date.precision=='month'"> Month
                            </label>
                            <label class="btn btn-outline-info" ng-class="{active:filter.date.precision == 'day'}" ng-click="filter.date.precision='day';changeDateFilter()">
                                <input type="radio" name="leave_date_filter_precision" autocomplete="off" ng-checked="filter.date.precision=='day'"> Day
                            </label>
                        </div>
                        <div ng-if="filter.date.enabled" class="btn-group">
                            <a  class="dropdown-toggle btn btn-outline-info" id="leaveFilterDate" role="button" data-toggle="dropdown" data-target="#" href="#">{{filter.date.date.format(filter.date.format)}}</a>
                            <ul class="dropdown-menu" role="menu">
                                <datetimepicker data-before-render="startDateRender($view,$dates,$index)" data-ng-model="filter.date.date" data-on-set-time="changeDateFilter()" data-datetimepicker-config="{ dropdownSelector: '#leaveFilterDate',minView:filter.date.precision,startView:filter.date.precision,configureOn:'configDateFilter'}"/>
                            </ul>
                        </div>
                    </div>
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
    				<tbody ng-repeat="leave in leaves" ng-show="(filter.date.enabled?leave.show:true) && filterLeave(leave)">
    					<tr style="background-color:lightgray" >
                            <td>
    							{{leave.info.type}}
    						</td>
    						<td colspan=5>
    							{{leave.info.remarks}}
    						</td>
                            <td style="text-align:right">
    							<button class="btn btn-light" style="background-color:transparent;border:0px" title="Edit Leave"  ng-click="$parent.edit=true; $parent.$broadcast('openLeaveModal',leaves[$index])"> <!--$parent is required because of scoping issues with ng-repeat -->
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button ng-init="leave.expand=true" ng-click="leave.expand=!leave.expand" class="btn btn-light" style="background-color:transparent;border:0px" title="{{leave.expand?'Collapse':'Expand'}}">
                                    <i class="far {{leave.expand?'fa-minus-square':'fa-plus-square'}}"></i>
                                </button>
                            </td>
    					</tr>
    					<tr ng-show="leave.expand && (filter.date.enabled?date_range.show:true)" ng-repeat="date_range in leave.date_ranges">
    						<td></td>
    						<td></td>
    						<td>{{date_range.start_date.format('MMMM DD, YYYY')}}</td>
    						<td>{{date_range.end_date.format('MMMM DD, YYYY')}}</td>
    						<td>{{leave.info.type.toLowerCase().includes('monetization') ? '':date_range.hours}}</td>
    						<td>{{leave.info.type.toLowerCase().includes('monetization') ? '':date_range.minutes}}</td>
    						<td>{{leave.info.is_without_pay ? '(WOP)':getDeductedCredits(leave.info.type,date_range).toFixed(3)}}</td>
    					</tr>
    				</tbody>

    			</table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editEmployeeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Employee Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form ng-submit="editEmployee()">
                        <div class="form-group">
                            <label>Last Name: </label>
                            <input class="form-control" type="text" ng-model="clone_employee.surname" required>
                        </div>
                        <div class="form-group">
                            <label>First Name: </label>
                            <input class="form-control" type="text" ng-model="clone_employee.first_name" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name: </label>
                            <input class="form-control" type="text" ng-model="clone_employee.middle_name">
                        </div>
                        <div class="dropdown form-group" ng-init="clone_employee.first_day_employ = moment(clone_employee.first_day_employ)">
                            <label>Employment Date:</label>
                            <a id="firstdayemploy" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                <div class="input-group">
                                    <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="clone_employee.first_day_employ">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <datetimepicker data-on-set-time="clone_employee.first_day_employ = moment(clone_employee.first_day_employ)" data-ng-model="clone_employee.first_day_employ" data-datetimepicker-config="{ dropdownSelector:'#firstdayemploy',minView:'day' }"></datetimepicker>
                            </ul>
                        </div>
                        <div class="form-group">
                            <label>Initial Vacation Leave Credit Balance: </label>
                            <input class="form-control" type="number"  min=0 step="0.001" ng-model="clone_employee.vac_leave_bal">
                        </div>
                        <div class="form-group">
                            <label>Initial Sick Leave Credit Balance: </label>
                            <input class="form-control" type="number"  min=0 step="0.001" ng-model="clone_employee.sick_leave_bal">
                        </div>
                        <div class="form-group">
                            <label>Highest Salary: </label>
                            <input class="form-control" type="number"  min=0 step="0.001" ng-model="clone_employee.highest_salary">
                        </div>
                        <div class="dropdown form-group" ng-init="clone_employee.first_day_compute = moment(clone_employee.first_day_compute)">
                            <label>Computation Date:</label>
                            <a id="firstdaycompute" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                <div class="input-group">
                                    <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="clone_employee.first_day_compute">
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <datetimepicker data-on-set-time="clone_employee.first_day_compute = moment(clone_employee.first_day_compute)"  data-ng-model="clone_employee.first_day_compute" data-datetimepicker-config="{ dropdownSelector:'#firstdaycompute',minView:'day' }"></datetimepicker>
                            </ul>
                        </div>
                        <div class="text-center">
                            <button ng-disabled="busy" class="btn btn-primary" type="Submit">Save Changes</button>
                            <button ng-disabled="busy" class="btn btn-danger" data-toggle="modal" type="button" data-target="#confirmDeleteModal">Delete Employee Records</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Warning</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form ng-submit="deleteEmployee()">
                        <div class="form-group">
                            <label>Enter Password:</label>
                            <input class="form-control" ng-model="password" type="text" required/>
                        </div>
                        <button class="btn btn-block btn-danger">Confirm</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="computeBalModal" tabindex="-1" role="dialog" aria-labelledby="computeBalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:1000px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="computeBalModalLabel">Credits</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="dropdown form-group"  style="max-width:500px">
                        <p>Leave Credits Balance: {{balance}}</p>
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
                            <datetimepicker data-before-render="startDateRender($view,$dates)" data-ng-model="bal_date" data-datetimepicker-config="{ dropdownSelector:'#vacBalDate',startView:'month',minView:'month' }" data-on-set-time="balDateSet()"></datetimepicker>
                        </ul>
                    </div>
                    <div class="text-center">
                        <div class="btn btn-group">
                            <button class="btn btn-outline-primary" ng-disabled="computations.year_filter.isSameOrBefore(employee.first_day_compute,'year')" ng-click="computations.year_filter.add(-1,'year'); longComputation(this.computations,'table',getComputationsTable,[computations.year_filter.year()])" type="button"><i class="fas fa-angle-left"></i></button>
                            <div class="btn-group">
                                <a  class="btn btn-outline-primary dropdown-toggle" id="computationsDate" role="button" data-toggle="dropdown" data-target="#" href="#">{{computations.year_filter.year()}}</a>
                                <ul class="dropdown-menu" role="menu">
                                    <datetimepicker data-ng-model="computations.year_filter" data-datetimepicker-config="{ dropdownSelector: '#computationsDate',minView:'year',startView:'year' }" data-before-render="computationsDateRender($view,$dates)"
                                        data-on-set-time="computations.year_filter = moment(computations.year_filter);longComputation(this.computations,'table',getComputationsTable,[computations.year_filter.year()])"/>
                                </ul>
                            </div>
                            <button class="btn btn-outline-primary" ng-disabled="computations.year_filter.isSameOrAfter(bal_date,'year')" ng-click="computations.year_filter.add(1,'year'); longComputation(this.computations,'table',getComputationsTable,[computations.year_filter.year()])" type="button"><i class="fas fa-angle-right"></i></button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-left">
                                <tr>
                                    <th rowspan="2" class="align-middle text-center">Date</th>
                                    <th rowspan="2" class="align-middle text-center">Credit Type</th>
                                    <th colspan="2" class="align-middle text-center">Amount Added/Deducted</th>
                                    <th colspan="2" class="align-middle text-center">Balance</th>
                                    <th rowspan="2" class="align-middle text-center">Remarks</th>
                                </tr>
                                <tr>
                                    <th class="align-middle text-center">Vac</th>
                                    <th class="align-middle text-center">Sick</th>
                                    <th class="align-middle text-center">Vac</th>
                                    <th class="align-middle text-center">Sick</th>
                                </tr>
                                <tbody ng-repeat="(monthName,month) in computations.table">
                                    <tr class="bg-light">
                                        <td colspan="7" >{{monthName}}</td>
                                    </tr>
                                    <tr ng-repeat="factor in month">
                                        <td>{{factor.date.format('MMMM DD, YYYY')}}</td>
                                        <td>{{factor.type}}</td>
                                        <td>{{(factor.amount.v/1000).toFixed(3)}}</td>
                                        <td>{{(factor.amount.s/1000).toFixed(3)}}</td>
                                        <td>{{(factor.balance.v/1000).toFixed(3)}}</td>
                                        <td>{{(factor.balance.s/1000).toFixed(3)}}</td>
                                        <td>{{factor.remarks}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div ng-controller="employee_statistics" class="modal fade" id="leaveCreditStatisticsModal" tabindex="-1" role="dialog" aria-labelledby="leaveCreditStatisticsLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="leaveCreditStatisticsLabel">Leave Credits Statistics</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="btn btn-group mx-auto">
                        <button class="btn btn-outline-primary" ng-disabled="year.year() <= employee.first_day_compute.year()"  ng-click="addYear(-1)" type="button"><i class="fas fa-angle-left"></i></button>
                        <div class="btn-group">
                            <a  class="btn btn-outline-primary dropdown-toggle" id="statisticsDate" role="button" data-toggle="dropdown" data-target="#" href="#">{{year.year()}}</a>
                            <ul class="dropdown-menu" role="menu">
                                <datetimepicker data-ng-model="year" data-datetimepicker-config="{ dropdownSelector: '#statisticsDate',minView:'year',startView:'year' }" data-before-render="statisticsDateRender($view,$dates)"
                                    data-on-set-time="setYear()"/>
                            </ul>
                        </div>
                        <button class="btn btn-outline-primary" ng-disabled="year.year() >= moment().year()" ng-click="addYear(1)" type="button"><i class="fas fa-angle-right"></i></button>
                    </div>
                    <div>
                        <canvas class="chart chart-line" chart-data="statistics.data"
                            chart-labels="statistics.labels" chart-options="statistics.options" chart-series="statistics.series" chart-colors="statistics.colors">
                        </canvas>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="recordOfLeavesModal" tabindex="-1" role="dialog" aria-labelledby="recordOfLeavesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:1100px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recordOfLeavesModalLabel">Record Of Leaves</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
					<div class="alert alert-info">
						<h5 class="font-weight-bold mb-3">Select display range</h5>
                        <div class="table-responsive">
    						<table class="table">
    							<tr>
    								<th>From</th>
    								<th>To</th>
    								<th></th>
    							</tr>
    							<tr>
    								<td style="min-width:200px;">
    									<div class="dropdown">
    										<a id="startDateRange" data-toggle="dropdown" style="text-decoration:none" data-target="dropdown" href="#">
    											<div class="input-group">
    												<input data-date-time-input="MMMM YYYY" class="form-control" type="text" data-ng-model="rol.start_date" required>
    												<div class="input-group-append">
    													<span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
    												</div>
    											</div>
    										</a>
    										<ul class="dropdown-menu">
    											<datetimepicker
    												data-ng-model="rol.start_date"
    												data-datetimepicker-config="{ dropdownSelector:'#startDateRange', startView:'month', minView:'month',renderOn:'rol-end-date-set'}"
    												data-on-set-time="rolStartDateSet()"
    												data-before-render="rolStartDateRender($view,$dates)"></datetimepicker>
    										</ul>
    									</div>
    								</td>
    								<td style="min-width:200px;">
    									<div class="dropdown">
    										<a id="endDateRange" data-toggle="dropdown" style="text-decoration:none" data-target="dropdown" href="#">
    											<div class="input-group">
    												<input data-date-time-input="MMMM YYYY" class="form-control" type="text" data-ng-model="rol.end_date" required>
    												<div class="input-group-append">
    													<span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
    												</div>
    											</div>
    										</a>
    										<ul class="dropdown-menu">
    											<datetimepicker
    												data-ng-model="rol.end_date"
    												data-datetimepicker-config="{ dropdownSelector:'#endDateRange', startView:'month', minView: 'month',renderOn:'rol-start-date-set'}"
    												data-on-set-time="rolEndDateSet()"
    												data-before-render="rolEndDateRender($view,$dates)"></datetimepicker>
    										</ul>
    									</div>
    								</td>
    								<td style="min-width:50px;">
    									<button class="btn btn-success" ng-click="setAllTime()">All Time</button>
    								</td>
    							</tr>
    						</table>
                        </div>
						<div class="text-center">
							<button class="btn btn-primary" ng-click="printROLTable()">Print</button>
                            <a href="#" class="btn btn-primary" id="rol-export" style="color:white" ng-click="exportROLTable();">Export as XLS</a>
						</div>
					</div>
                    <div class="mt-3 table-responsive" id="rolTable">
                        <div style="text-align:center">
            				<h4 style="font-weight:bold;margin:0;">RECORD OF LEAVES OF ABSENCE</h4>
            				<p style="font-weight:bold;margin:0;font-size:0.8em;">(UNDER R.A. 2266)</p>
            			</div>
            			<div>
            				<div style="width:auto;float:left">
            					<p><span style="font-weight:bold">NAME:</span>&nbsp;&nbsp;<span style="text-decoration:underline">&nbsp;&nbsp;{{employee.first_name}} {{employee.middle_name ? employee.middle_name.charAt(0)+".":''}} {{employee.surname}}&nbsp;&nbsp;</span></p>
            				</div>
            				<div style="width:auto;float:right">
            					<p><span style="font-weight:bold">Date Entered Service:</span>&nbsp;&nbsp;<span style="text-decoration:underline">&nbsp;&nbsp;{{employee.first_day_employ.format('MMMM DD, YYYY')}}&nbsp;&nbsp;</span></p>
            				</div>
            			</div>
                        <style>
                            #rol-table colgroup{
                                border:2px solid black;
                            }
                            #rol-table th{
                                border:1px solid gray;
                            }
                            #rol-table td{
                                border:1px solid gray;
                            }
                            #rol-table .eoma{
                                border-bottom:2px solid black;
                                border-top:2px solid black;
                                font-weight:bold;
                            }
                        </style>
                        <table id="rol-table" style="border-collapse:collapse;margin:0;padding:0;width:100%;">
        					<colgroup>
        						<col width="5%">
        						<col width="5%">
                            </colgroup>
                            <colgroup>
        						<col width="22%">
                            </colgroup>
                            <colgroup>
        						<col width="6%">
        						<col width="6%">
                            </colgroup>
                            <colgroup>
        						<col width="4%">
        						<col width="4%">
        						<col width="4%">
                            </colgroup>
                            <colgroup>
        						<col width="4%">
        						<col width="4%">
        						<col width="4%">
                            </colgroup>
                            <colgroup>
        						<col width="6%">
        						<col width="6%">
                            </colgroup>
                            <colgroup>
        						<col width="20%">
        					</colgroup>
        					<tr style="text-align:center">
        						<th colspan="2" style="font-weight:bold;font-size:0.7em;">Leaves Earned</th>
        						<th rowspan="3" style="font-weight:bold;">WHEN TAKEN</th>
        						<th colspan="2" style="font-size:0.7em;font-weight:bold;">LEAVES TAKEN</th>
        						<th colspan="3" style="font-size:0.75em;font-weight:bold;">UNDERTIME</th>
        						<th rowspan="2" colspan="3" style="font-weight:bold;">W/O PAY</th>
        						<th colspan="2" style="font-weight:bold;">BALANCE</th>
        						<th rowspan="3" style="font-weight:bold;">REMARK</th>
        					</tr>
        					<tr style="text-align:center;font-size:0.8em;">
                                <th rowspan="2" style="font-weight:bold;">VAC</th>
        						<th rowspan="2" style="font-weight:bold;">SICK</th>
                                <th rowspan="2" style="font-weight:bold;">VAC</th>
        						<th rowspan="2" style="font-weight:bold;">SICK</th>
        						<th colspan="3" style="font-weight:bold;">VAC</th>
        						<th rowspan="2" style="font-weight:bold;">VAC</th>
        						<th rowspan="2" style="font-weight:bold;">SICK</th>
        					</tr>
        					<tr style="font-size:0.6em;text-align:center;">
        						<th style="font-weight:bold;">HOUR</th>
        						<th style="font-weight:bold;">MIN</th>
        						<th style="font-weight:bold;">TOTAL</th>
        						<th style="font-weight:bold;">HOUR</th>
        						<th style="font-weight:bold;">MIN</th>
        						<th style="font-weight:bold;">TOTAL</th>
        					</tr>
                            <tbody ng-repeat="month in rol.factors" style="font-size:0.75em;" >
                                <tr ng-repeat="factor in month track by $index" style="text-align:right;" ng-class="{eoma:factor.eoma}">
                                    <td>{{factor.leaves_earned.v == '0.000' ? '':factor.leaves_earned.v}}</td>
                                    <td>{{factor.leaves_earned.s == '0.000' ? '':factor.leaves_earned.s}}</td>
                                    <td style="text-align:left;">{{factor.when_taken}}</td>
                                    <td>{{factor.leaves_taken.v == '0.000' ? '':factor.leaves_taken.v}}</td>
                                    <td>{{factor.leaves_taken.s == '0.000' ? '':factor.leaves_taken.s}}</td>
                                    <td>{{factor.undertime.hour == 0 ? '':factor.undertime.hour}}</td>
                                    <td>{{factor.undertime.min == 0 ? '':factor.undertime.min}}</td>
                                    <td>{{factor.undertime.total == '0.000' ? '':factor.undertime.total}}</td>
                                    <td>{{factor.without_pay.hour == 0 ? '':factor.without_pay.hour}}</td>
                                    <td>{{factor.without_pay.min == 0 ? '':factor.without_pay.min}}</td>
                                    <td>{{factor.without_pay.total == '0.000' ? '':factor.without_pay.total}}</td>
                                    <td>{{factor.balance.v}}</td>
                                    <td>{{factor.balance.s}}</td>
                                    <td style="text-align:left;">{{factor.remarks}}</td>
                                </tr>
                            </tbody>
        				</table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

	<div class="modal fade" id="addOrEditLeaveModal" tabindex="-1" role="dialog" aria-labelledby="addOrEditLeaveLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:1000px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOrEditLeaveLabel">{{edit?'Edit':'Add'}} Leave</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?= $this->load->view("employee/leave_records",array(),true);?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="monetizeLeaveModal" tabindex="-1" role="dialog" aria-labelledby="monetizeLeaveLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="monetizeLeaveLabel">Monetize Leave</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form ng-submit="submitMonetization()">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" ng-true-value=1 ng-false-value=0 id="specialMonetization" ng-model="monetize.special">
                            <label class="form-check-label" for="specialMonetization">Special</label>
                        </div>
                        <div class="dropdown form-group">
                            <label>Monetization Date:</label>
                            <a id="monetizationDate" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                <div class="input-group">
                                    <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="monetize.date" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <datetimepicker data-on-set-time="monetize.date = moment(monetize.date)" data-before-render="startDateRender($view,$dates)" data-ng-model="monetize.date" data-datetimepicker-config="{ dropdownSelector:'#monetizationDate',minView:'day' }"></datetimepicker>
                            </ul>
                        </div>
                        <div class="form-group">
                            <label>Credits:</label>
                            <div class="input-group">
                                <input class="form-control" ng-model="monetize.credits" type="number" min="0" required></input>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Remarks:</label>
                            <div class="input-group">
                                <input class="form-control" ng-model="monetize.remarks" type="text"></input>
                            </div>
                        </div>
                        <button ng-disabled="busy" type="submit" class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="terminalModal" tabindex="-1" role="dialog" aria-labelledby="terminalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="terminalLabel">Terminal Leave Benefit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <tr>
                            <th>First Day</th>
                            <th>Termination Date</th>
                            <th>Total Days</th>
                            <th>Leave Without Pay</th>
                        </tr>
                        <tr>
                            <td>{{employee.first_day_employ.format('MMMM DD, YYYY')}}</td>
                            <td>
                                <div class=" dropdown">
                                    <a id="terminationDate" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                        <div class="input-group">
                                            <input data-date-time-input="MMMM DD, YYYY" class="form-control" type="text" data-ng-model="terminal_date">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <datetimepicker data-before-render="startDateRender($view,$dates)" data-on-set-time="setTerminalDate(newDate)" data-ng-model="terminal_date" data-datetimepicker-config="{ dropdownSelector:'#terminationDate',minView:'day'}"></datetimepicker>
                                    </ul>
                                </div>
                            </td>
                            <td>Years: {{totalDays.years}}</br>Months: {{totalDays.months}}</br>Days: {{totalDays.days}}</td>
                            <td>{{lwop[0]}}</td>
                        </tr>
                    </table>

                    <table class="table">
                        <tr>
                            <th>Credits Earned (Vac:Sick)</th>
                            <th>Credits Enjoyed (Vac:Sick)</th>
                            <th>Highest Salary</th>
                        </tr>
                        <tr>
                            <td>{{cEarned.v}} : {{cEarned.s}}</td>
                            <td>{{cEnjoyed.v}} : {{cEnjoyed.s}}</td>
                            <td>{{employee.highest_salary}}</td>
                        </tr>
                    </table>

                    <div class=" alert alert-dark">
                        <p class="font-weight-bold">Computations:</p>
						<p class="font-weight-bold text-center">{{terBenefit.computation}}</p>
						<p class="font-weight-bold text-center">{{terBenefit2.computation}}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
