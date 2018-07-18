<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="card" ng-controller="calendar_collisions" ng-init='init(<?=$collisions?>)'>
    <div class="card-header">
        <h3>Manage Collisions</h3>
    </div>
    <div class="card-body">
        <div class="alert alert-primary">
            <span>Manage leaves that collided with work suspensions.</span>
        </div>
        <div ng-if="!collisions.events">
            <span class="font-italic">No collisions found.</span>
        </div>
        <div ng-if="collisions.events" ng-repeat="emp_leave in collisions.leaves">
            <div class="alert alert-dark">
                <p class="font-weight-bold">#{{$index+1}}</p>
                <p><span class="font-weight-bold">Employee:</span> {{emp_leave.employee.emp_no}}. {{emp_leave.employee.last_name}}, {{emp_leave.employee.first_name}} {{emp_leave.employee.middle_name}}</p>
                <p><span class="font-weight-bold">Leave Info:</span> {{emp_leave.leave.info.type}}. <span class="font-italic">{{emp_leave.leave.info.remarks}}</span></p>
                <p><span class="font-weight-bold">Suspensions Collided With:</span></p>
                <p class="ml-4" ng-repeat="event in emp_leave.leave.collision_events">- {{event.title}}. {{moment(event.date).format('MMMM DD, YYYY')}}. <span class="font-italic">{{event.description}}</span></p>
                <div class="text-right">
                    <button class="btn btn-primary" type="button" ng-click="openLeaveModal(emp_leave.leave)">Manage Collision</button>
                </div>
            </div>
            <div>

            </div>
            <div ng-controller="employee_display" ng-init="init(emp_leave.employee,[])">
                <div class="modal fade" id="addOrEditLeaveModal" tabindex="-1" role="dialog" aria-labelledby="addOrEditLeaveLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" style="max-width:1000px" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addOrEditLeaveLabel">Edit Leave</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?=$this->load->view('employee/leave_records',array('employee'=>'emp_leave.employee','leaves'=>'[]','submit_button'=>'Save Changes And Mark As Resolved'),true);?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" ng-click="promptResolve(emp_leave.leave.info.leave_id)" class="btn btn-danger">Just Mark As Resolved</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
