<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_display" ng-init='init(<?=$employee ?>,<?=$leaves?>)' class="card">
    <div class="card-header">
        <h1>Employee Information</h1>
    </div>
    <div class="card-body">
        <p><span class="font-weight-bold">Employee No:</span> {{employee.emp_no}}</p>
        <p><span class="font-weight-bold">Name:</span> {{employee.last_name}}, {{employee.first_name}} {{employee.middle_name}}</p>
        <p><span class="font-weight-bold">First Day:</span> {{employee.first_day}}</p>
        <p><span class="font-weight-bold">Initial Credits:</span> Vacation {{employee.vac_leave_bal}} | Sick {{employee.sick_leave_bal}} </p>

        <div class="dropdown form-group" style="max-width:400px">
            <button class="btn btn-success" data-toggle="modal" data-target="#computeBalModal" ng-click="longComputation(this,'balance',getBalance)">Leave Credits Balance</button>
            <button class="btn btn-success" data-toggle="modal" data-target="#leaveCreditStatisticsModal" ng-click="$broadcast('openStatisticsModal')" type="button">Leave Credits Statistics</button>
        </div>

        <div class="form-group">
            <button title="Edit Leave" ng-click="openLeaveModal()" class="btn btn-primary">Add Leave Record</button>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#monetizeLeaveModal">Monetize Leave</button>
            <button type="button" ng-click="setTerminalDate()" class="btn btn-primary" data-toggle="modal" data-target="#terminalModal">Terminal Leave Benefits</button>
			<a href="<?= base_url().'employee/form/'?>{{employee.emp_no}}">
				<button type="button" class="btn btn-primary">Print Information</button>
			</a>
        </div>

        <div>
            <h3>Leave History</h3>
			<div class="form-group row">
				<div class="col btn-group btn-group-toggle">
					<label class="btn btn-outline-info" ng-class="{active:filter.type.every}" ng-click="reFilter('every')">All</label>
                    <label ng-init="initFilters()" ng-repeat="type_filter in type_filters" class="btn btn-outline-info text-capitalize" ng-class="{active:filter.type[type_filter]}" ng-click="reFilter(type_filter)">{{type_filter}}</label>
				</div>
                <div class="text-right">
                    <div class="col btn-group">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info">
                                <input type="checkbox" ng-true-value=false ng-false-value=true ng-model="filter.date.enabled" autocomplete="off"/> All Time
                            </label>
                        </div>
                        <div ng-if="filter.date.enabled" class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-outline-info" ng-class="{active:filter.date.precision == 'year'}" ng-click="(filter.date.precision='year')+changeDateFilter()">
                                <input type="radio" name="leave_date_filter_precision" autocomplete="off" ng-checked="filter.date.precision=='year'"> Year
                            </label>
                            <label class="btn btn-outline-info" ng-class="{active:filter.date.precision == 'month'}" ng-click="(filter.date.precision='month')+changeDateFilter()">
                                <input type="radio" name="leave_date_filter_precision" autocomplete="off" ng-checked="filter.date.precision=='month'"> Month
                            </label>
                            <label class="btn btn-outline-info" ng-class="{active:filter.date.precision == 'day'}" ng-click="(filter.date.precision='day')+changeDateFilter()">
                                <input type="radio" name="leave_date_filter_precision autocomplete="off" ng-checked="filter.date.precision=='day'"> Day
                            </label>
                        </div>
                        <div ng-if="filter.date.enabled" class="btn-group">
                            <a  class="dropdown-toggle btn btn-outline-info" id="leaveFilterDate" role="button" data-toggle="dropdown" data-target="#" href="#">{{filter.date.date.format(filter.date.format)}}</a>
                            <ul class="dropdown-menu" role="menu">
                                <datetimepicker data-ng-model="filter.date.date" data-on-set-time="changeDateFilter()" data-datetimepicker-config="{ dropdownSelector: '#leaveFilterDate',minView:filter.date.precision,startView:filter.date.precision,configureOn:'configDateFilter'}"/>
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
    							<button class="btn btn-light" style="background-color:transparent;border:0px" title="Edit Leave" ng-click="openLeaveModal($index)">
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
    						<td>{{date_range.start_date}}</td>
    						<td>{{date_range.end_date}}</td>
    						<td>{{leave.info.type.toLowerCase().includes('monetization') ? '':date_range.hours}}</td>
    						<td>{{leave.info.type.toLowerCase().includes('monetization') ? '':date_range.minutes}}</td>
    						<td>{{leave.info.is_without_pay ? '(WOP)':getDeductedCredits(leave.info.type,date_range)}}</td>
    					</tr>
    				</tbody>

    			</table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="computationsModal" tabindex="-1" role="dialog" aria-labelledby="computationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:1000px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="computationsModalLabel">Computations</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <p>Vacation</p>
                        <table class="table table-bordered">
                            <tr>
                                <th>Amount</th>
                                <th>Remarks</th>
                                <th>Date</th>
                            </tr>
                            <tr ng-repeat="com in computationsCopy.vacation">
                                <td>{{com.amount/1000}}</td>
                                <td>{{com.remarks}}</td>
                                <td>{{com.date}}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <p>Sick</p>
                        <table class="table table-bordered">
                            <tr>
                                <th>Amount</th>
                                <th>Remarks</th>
                                <th>Date</th>
                            </tr>
                            <tr ng-repeat="com in computationsCopy.sick">
                                <td>{{com.amount/1000}}</td>
                                <td>{{com.remarks}}</td>
                                <td>{{com.date}}</td>
                            </tr>
                        </table>
                    </div>
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
                            <datetimepicker data-before-render="startDateBeforeRender($dates,employee.first_day)" data-ng-model="bal_date" data-datetimepicker-config="{ dropdownSelector:'#vacBalDate',startView:'month',minView:'month' }" data-on-set-time="balDateSet()"></datetimepicker>
                        </ul>
                    </div>
                    <div class="text-center">
                        <div class="btn btn-group">
                            <button class="btn btn-outline-primary" ng-disabled="computations.year_filter.isSameOrBefore(moment(employee.first_day,dateFormat),'year')" ng-click="computations.year_filter.add(-1,'year')  + longComputation(this.computations,'table',getComputationsTable,[computations.year_filter.year()])" type="button"><i class="fas fa-angle-left"></i></button>
                            <div class="btn-group">
                                <a  class="btn btn-outline-primary dropdown-toggle" id="computationsDate" role="button" data-toggle="dropdown" data-target="#" href="#">{{computations.year_filter.year()}}</a>
                                <ul class="dropdown-menu" role="menu">
                                    <datetimepicker data-ng-model="computations.year_filter" data-datetimepicker-config="{ dropdownSelector: '#computationsDate',minView:'year',startView:'year' }" data-before-render="computationsDateRender($view,$dates)"
                                        data-on-set-time="(computations.year_filter = moment(computations.year_filter))  + longComputation(this.computations,'table',getComputationsTable,[computations.year_filter.year()])"/>
                                </ul>
                            </div>
                            <button class="btn btn-outline-primary" ng-disabled="computations.year_filter.isSameOrAfter(bal_date,'year')" ng-click="computations.year_filter.add(1,'year') + longComputation(this.computations,'table',getComputationsTable,[computations.year_filter.year()])" type="button"><i class="fas fa-angle-right"></i></button>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-left">
                                <tr>
                                    <th>Date</th>
                                    <th>Credit Type</th>
                                    <th>Amount Added/Deducted</th>
                                    <th>Balance</th>
                                    <th>Remarks</th>
                                </tr>
                                <tbody ng-repeat="(monthName,month) in computations.table">
                                    <tr class="bg-light">
                                        <td colspan="5" >{{monthName}}</td>
                                    </tr>
                                    <tr ng-repeat="factor in month">
                                        <td>{{factor.date.format('MMMM DD, YYYY')}}</td>
                                        <td>{{factor.type}}</td>
                                        <td>{{(factor.amount/1000).toFixed(3)}}</td>
                                        <td>{{(factor.balance/1000).toFixed(3)}}</td>
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
                        <button class="btn btn-outline-primary" ng-disabled="year.year() <= moment(employee.first_day,dateFormat).year()"  ng-click="addYear(-1)" type="button"><i class="fas fa-angle-left"></i></button>
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

	<div class="modal fade" id="addOrEditLeaveModal" tabindex="-1" role="dialog" aria-labelledby="addOrEditLeaveLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:1000px" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOrEditLeaveLabel">Add/Edit Leave</h5>
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
                                <datetimepicker data-before-render="startDateRender($view,$dates,$index)" data-ng-model="monetize.date" data-datetimepicker-config="{ dropdownSelector:'#monetizationDate',minView:'day' }"></datetimepicker>
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
                            <td>{{employee.first_day}}</td>
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
                                        <datetimepicker data-before-render="startDateRender($view,$dates,$index)" data-on-set-time="setTerminalDate(newDate)" data-ng-model="terminal_date" data-datetimepicker-config="{ dropdownSelector:'#terminationDate',minView:'day'}"></datetimepicker>
                                    </ul>
                                </div>
                            </td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </table>

                    <table class="table">
                        <tr>
                            <th>Credits Earned (Vac:Sick)</th>
                            <th>Credits Enjoyed (Vac:Sick)</th>
                        </tr>
                        <tr>
                            <td>0:0</td>
                            <td>0:0</td>
                        </tr>
                    </table>

                    <div>
                        computations</br>
						{{terBenefit}}</br>
						{{terBenefit2}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>
