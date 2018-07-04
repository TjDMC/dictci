<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div class="card">
	<div class="card-header">
		<h1>Calendar</h1>
	</div>
	<div class="card-body p-5 mx-auto" style="max-width:1200px">
		<div>
			
			<div>
				<input id="indate" type="text" data-date-time-input="YYYY/MM/DD">
				<button id="nav">Navigate</button>
			</div>
			<div id="calendar">

			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Add Calendar Event</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fas fa-times"></i></span></button>
			</div>
			<div class="modal-body">
				<?php echo form_open(site_url("calendar/add_event"), array("class" => "form-horizontal")) ?>
				<div class="form-group">
					<label for="p-in" class="col-md-4 label-heading">Event Name</label>
					<div class="col-md-8 ui-front">
						<input type="text" class="form-control" name="name" value="">
					</div>
				</div>
				<div class="form-group">
					<label for="p-in" class="col-md-4 label-heading">Description</label>
					<div class="col-md-8 ui-front">
						<input type="text" class="form-control" name="description">
					</div>
				</div>
				<!--<div class="form-group">
					<p>
						<div class="dropdown dropdown-start-parent">
							<label for="p-in" class="col-md-4 label-heading">Start Date</label>
							<a id="dropdownStart" data-toggle="dropdown" data-target=".dropdown-start-parent" href="#">
								<div class="col-md-8"><input data-date-time-input="YYYY/MM/DD hh:mm" type="text" data-ng-model="dataRangeStart" name="start_date" class="form-control"></div>
							</a>
							<ul class="dropdown-menu">
								<datetimepicker  data-ng-model="dateRangeStart" data-datetimepicker-config="{ dropdownSelector:'#dropdownStart',minuteStep:1 }" data-on-set-time="startDateOnSetTime()" data-before-render="startDateBeforeRender($dates)"></datetimepicker>
							</ul>
						</div>
					</p>
				</div>
				<div class="form-group">
					<p>
						<div class="dropdown dropdown-end-parent">
							<label for="p-in" class="col-md-4 label-heading">End Date</label>
							<a id="dropdownEnd" data-toggle="dropdown" data-target=".dropdown-end-parent" href="#">
								<div class="col-md-8"><input data-date-time-input="YYYY/MM/DD hh:mm" type="text" data-ng-model="dateRangeEnd" name="end_date" class="form-control"></div>
							</a>
							<ul class="dropdown-menu">
								<datetimepicker  data-ng-model="dateRangeEnd" data-datetimepicker-config="{ dropdownSelector:'#dropdownEnd',minuteStep:1 }" data-on-set-time="endDateOnSetTime()" data-before-render="endDateBeforeRender($view, $dates, $leftDate, $upDate, $rightDate)"></datetimepicker>
							</ul>
						</div>
					</p>
				</div>-->
				<div class="dropdown form-group dropdown-start-parent">
					<label for="p-in" class="col-md-4 label-heading">Start Date</label>
					<a id="dropdownStartAdd" style="text-decoration:none" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent" href="#">
						<div class="input-group date">
							<input type="text" class="form-control" data-date-time-input="YYYY/MM/DD" data-ng-model="dateRangeStartAdd" name="start_date" id="fill_start_date" autocomplete="off">
							<div class="input-group-append">
		                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
		                    </div>
						</div>
					</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<datetimepicker data-ng-model="dateRangeStartAdd"
										data-datetimepicker-config="{ dropdownSelector: '#dropdownStartAdd', minView:'day', renderOn: 'end-date-changed' }"
										data-on-set-time="startDateOnSetTime()"
										data-before-render="startDateBeforeRender($dates)"></datetimepicker>
					</ul>
				</div>
				<div class="dropdown form-group dropdown-end-parent">
					<label for="p-in" class="col-md-4 label-heading">End Date</label>
					<a id="dropdownEndAdd" style="text-decoration:none" role="button" data-toggle="dropdown" data-target=".dropdown-end-parent"
					   href="#">
						<div class="input-group date">
							<input type="text" class="form-control" data-date-time-input="YYYY/MM/DD" data-ng-model="dateRangeEndAdd" name="end_date" autocomplete="off">
							<div class="input-group-append">
		                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
		                    </div>
						</div>
					</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<datetimepicker data-ng-model="dateRangeEndAdd"
										data-datetimepicker-config="{ dropdownSelector: '#dropdownEndAdd', minView:'day', renderOn: 'start-date-changed' }"
										data-on-set-time="endDateOnSetTime()"
										data-before-render="endDateBeforeRender($view, $dates, $leftDate, $upDate, $rightDate)"></datetimepicker>
					</ul>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-primary" style="background-color:#209270" value="Add Event">
				<?php echo form_close() ?>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel">Update Calendar Event</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<?php echo form_open(site_url("calendar/edit_event"), array("class" => "form-horizontal")) ?>
				<div class="form-group">
					<label for="p-in" class="col-md-4 label-heading">Event Name</label>
					<div class="col-md-8 ui-front">
						<input type="text" class="form-control" name="name" value="" id="name">
					</div>
				</div>
				<div class="form-group">
					<label for="p-in" class="col-md-4 label-heading">Description</label>
					<div class="col-md-8 ui-front">
						<input type="text" class="form-control" name="description" id="description">
					</div>
				</div>
				<!--<div class="form-group">
					<label for="p-in" class="col-md-4 label-heading">Start Date</label>
					<div class="col-md-8">
						<input type="text" class="form-control" name="start_date" id="start_date">
					</div>
				</div>
				<div class="form-group">
					<label for="p-in" class="col-md-4 label-heading">End Date</label>
					<div class="col-md-8">
						<input type="text" class="form-control" name="end_date" id="end_date">
					</div>
				</div>-->
				<div class="dropdown form-group dropdown-start-parent1">
					<label for="p-in" class="col-md-4 label-heading">Start Date</label>
					<a id="dropdownStartEdit" style="text-decoration:none" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent1" href="#">
						<div class="input-group date">
							<input type="text" class="form-control" data-date-time-input="YYYY/MM/DD" data-ng-model="dateRangeStartEd" name="start_date" id="start_date" autocomplete="off">
							<div class="input-group-append">
		                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
		                    </div>
						</div>
					</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<datetimepicker data-ng-model="dateRangeStartEd"
										data-datetimepicker-config="{ dropdownSelector: '#dropdownStartEdit', minView:'day', renderOn: 'end-date-changed' }"
										data-on-set-time="startDateOnSetTime()"
										data-before-render="startDateBeforeRender($dates)"></datetimepicker>
					</ul>
				</div>
				<div class="dropdown form-group dropdown-end-parent1">
					<label for="p-in" class="col-md-4 label-heading">End Date</label>
					<a id="dropdownEndEdit" style="text-decoration:none" role="button" data-toggle="dropdown" data-target=".dropdown-end-parent1" href="#">
						<div class="input-group date">
							<input type="text" class="form-control" data-date-time-input="YYYY/MM/DD" data-ng-model="dateRangeEndEd" name="end_date" id="end_date" autocomplete="off">
							<div class="input-group-append">
		                        <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
		                    </div>
						</div>
					</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<datetimepicker data-ng-model="dateRangeEndEd"
										data-datetimepicker-config="{ dropdownSelector: '#dropdownEndEdit', minView:'day', renderOn: 'start-date-changed' }"
										data-on-set-time="endDateOnSetTime()"
										data-before-render="endDateBeforeRender($view, $dates, $leftDate, $upDate, $rightDate)"></datetimepicker>
					</ul>
				</div>
				<div class="form-group">
					<label for="p-in" class="col-md-4 label-heading">Delete Event</label>
					<div class="col-md-8">
						<input type="checkbox" name="delete" value="1">
					</div>
				</div>
				<input type="hidden" name="eventid" id="event_id" value="0" />
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-primary" style="background-color:#209270" value="Update Event">
				<?php echo form_close() ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

	var date_last_clicked = null;
	
	$('#calendar').fullCalendar({
		header: {
			left: 'title',
			center: 'custView prevYear,nextYear',
			right: 'today prev,next'
		},
		buttonText: {
			prevYear: 'PrevYear',
			nextYear: 'NextYear'
		},
		eventSources: [
		    {
			events: function(start, end, timezone, callback) {
				$.ajax({
					url: '<?php echo base_url() ?>calendar/get_events',
					dataType: 'json',
					data: {
						// our hypothetical feed requires UNIX timestamps
						start: start.unix(),
						end: end.unix()
					},
					success: function(msg) {
						var events = msg.events;
						callback(events);
					}
				});
			  }
			},
		],
		eventColor: '#209270',
		eventTextColor: '#f6f6f6',
		displayEventTime: false,
		eventClick: function(event, jsEvent, view) {
			$('#name').val(event.title);
			$('#description').val(event.description);
			$('#start_date').val(moment(event.start).format('YYYY/MM/DD'));
			if(event.end) {
				$('#end_date').val(moment(event.end).format('YYYY/MM/DD'));
			} else {
				$('#end_date').val(moment(event.start).format('YYYY/MM/DD'));
			}
			$('#event_id').val(event.id);
			$('#editModal').modal();
		},
		eventRender: function (event, element, view) {
			element.find('.fc-title').append(': '+event.description);
		},
		dayClick: function(date, allDay, jsEvent, view) {
			$(this).css('background-color', '#acecd9');
			$('#addModal').modal();
			$('#fill_start_date').val(date.format('YYYY/MM/DD'));
			$(date_last_clicked).css('background-color', '#ffffff');
			date_last_clicked = this;
		},
	});
	$('#nav').click(function(){
		$('#calendar').fullCalendar('gotoDate',$('#indate').val());
		//alert($('#indate').val());
	});
	
});
</script>