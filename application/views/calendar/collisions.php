<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="card" ng-controller="calendar_collisions" ng-init='init(<?=$collisions?>)'>
    <div class="card-header">
        <h3>Manage Collisions</h3>
    </div>
    <div class="card-body">
        <div ng-if="collisions.length==0">
            <span class="font-italic">No collisions found.</span>
        </div>
        <div ng-if="collisions.length>0" ng-repeat="event in collisions">
            <div class="alert alert-dark">
                <p class="font-weight-bold">#{{$index+1}}</p>
                <p><span class="font-weight-bold">Employee:</span> {{event.employee.last_name}}, {{event.employee.first_name}} {{event.employee.middle_name}}</p>
                <p><span class="font-weight-bold">Event:</span> {{moment(event.date).format('MMMM DD, YYYY')}}. {{event.title}}. <span class="font-italic">{{event.description}}</span></p>
                <p><span class="font-weight-bold">Collided Leave:</span> {{event.leave.info.type}} <span class="font-italic">{{event.leave.info.remarks}}</span></p>
            </div>
            <div>
                Leave: {{event.range_id}},
                Date_ranges:
                <div ng-repeat="date_range in event.leave.date_ranges">
                    {{date_range.start_date}} {{date_range.end_date}}
                </div>
            </div>
            <div>
                <button type="button" ng-click="openLeaveModal(event.event_id,event.leave)">BOOM</button>
            </div>
            <div ng-controller="employee_display" ng-init="init(event.employee,[])">
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
                                <?=$this->load->view('employee/leave_records',array('employee'=>'event.employee','leaves'=>'[]','submit_button'=>'Save Changes And Mark As Resolved'),true);?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" ng-click="promptResolve($index)" class="btn btn-danger">Just Mark As Resolved</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
