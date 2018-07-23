<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="event_display" ng-init='init(<?=isset($events)?$events:''?>,false)' class="card">
	<div class="card-header">
		<h1>Holiday list</h1>
	</div>
	<div class="card-body">
		<div class="form-group">
            <button title="Edit Holidays" ng-click="showModal(null)" class="btn btn-primary">Add Holidays</button>
        </div>
		<div class="table-responsive">
			<table class="table">
				<tr><th>Event</th><th>Date</th><th></th></tr>
				<tr ng-repeat="event in events" ng-init=''>
					<td>{{event.title}}</td>
					<td>{{event.date.format('MMMM DD, YYYY')}}</td>
					<td>
						<button class="btn btn-light" style="background-color:transparent;border:0px" title="Edit Leave" ng-click="showModal(event)">
							<i class="fas fa-edit"></i>
						</button>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="modal fade" id="addOrEditEventModal" tabindex="-1" role="dialog" aria-labelledby="addOrEditEventLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventLabel">Add/Edit Holiday Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form ng-submit="actionEvent(modalEvent.hasOwnProperty('event_id')?'edit':'add')">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" id="isRecurring" type="checkbox" ng-true-value=1 ng-false-value=0 ng-model="modalEvent.is_recurring"></input>
                                <label for="isRecurring">Recurring</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Title</label>
                            <input class="form-control" type="text" ng-model="modalEvent.title" required></input>
                        </div>
						<div class="form-group">
							<label>Date</label>
							<a id="eventdate" style="text-decoration:none" data-toggle="dropdown" data-target="dropdown" href="#">
                                <div class="input-group">
                                    <input data-date-time-input="{{dateFormat}}" class="form-control" type="text" data-ng-model="modalEvent.date" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu">
                                <datetimepicker data-on-set-time="modalEvent.date = moment(modalEvent.date)" data-ng-model="modalEvent.date" data-datetimepicker-config="{ dropdownSelector:'#eventdate',minView:'day' }"></datetimepicker>
                            </ul>
						</div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" ng-model="modalEvent.description"></textarea>
                        </div>
                        <button ng-disabled="busy" class="btn btn-info" type="submit">Submit</button>
                        <button ng-disabled="busy" ng-if="modalEvent.hasOwnProperty('event_id')" class="btn btn-danger" type="button" ng-click="actionEvent('delete')">Delete Event</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
