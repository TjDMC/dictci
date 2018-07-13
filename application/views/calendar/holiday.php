<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="event_display" ng-init='init(<?=isset($events)?$events:''?>)'>
	<div class="card-body">
		<h1>Holiday list</h1>
		<div class="form-group">
            <button title="Edit Holidays" ng-click="showAddOrEditModal()" class="btn btn-primary">Add Holidays</button>
        </div>
		<div class="table-responsive">
			<table class="table">
				<tr><th>Event</th><th>Date</th><th></th></tr>
				<tr ng-repeat="event in events" ng-init=''>
					<td>{{event.title}}</td>
					<td>{{event.date.split(" ")[0]}}</td>
					<td>
						<button class="btn btn-light" style="background-color:transparent;border:0px" title="Edit Leave" ng-click="differentModal(event)">
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
                    <h5 class="modal-title" id="eventLabel">Add/Edit event for {{modalDate.format('MMMM DD, YYYY')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form ng-submit="actionEvent(modalEvent.hasOwnProperty('id')?'edit':'add')">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" id="isSuspension" type="checkbox" ng-true-value=1 ng-false-value=0 ng-model="modalEvent.is_suspension"></input>
                                <label for="isSuspension">Suspension</label>
                            </div>
                        </div>
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
                            <label>Description</label>
                            <textarea class="form-control" ng-model="modalEvent.description"></textarea>
                        </div>
                        <button ng-disabled="busy" class="btn btn-info" type="submit">Submit</button>
                        <button ng-disabled="busy" ng-if="modalEvent.hasOwnProperty('id')" class="btn btn-danger" type="button" ng-click="actionEvent('delete')">Delete Event</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>