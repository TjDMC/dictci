<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
		
<div class="row">
<div class="col-md-12">

<button id="addHoliday" class="btn btn-success" data-toggle="modal" data-target="#addModal">Add Holiday</button>

<h1>Calendar</h1>

<div id="calendar">

</div>

</div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Add Calendar Event</h4>
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
					<a class="dropdown-toggle" id="dropdownStart" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent"
					   href="#">
						<div class="input-group date">
							<input type="text" class="form-control" data-date-time-input="YYYY/MM/DD hh:mm" data-ng-model="dateRangeStart" name="start_date">
							<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						</div>
					</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<datetimepicker data-ng-model="dateRangeStart"
										data-datetimepicker-config="{ dropdownSelector: '#dropdownStart', renderOn: 'end-date-changed' }"
										data-on-set-time="startDateOnSetTime()"
										data-before-render="startDateBeforeRender($dates)"></datetimepicker>
					</ul>
				</div>
				<div class="dropdown form-group dropdown-end-parent">
					<label for="p-in" class="col-md-4 label-heading">End Date</label>
					<a class="dropdown-toggle" id="dropdownEnd" role="button" data-toggle="dropdown" data-target=".dropdown-end-parent"
					   href="#">
						<div class="input-group date">
							<input type="text" class="form-control" data-date-time-input="YYYY/MM/DD hh:mm" data-ng-model="dateRangeEnd" name="end_date">
							<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						</div>
					</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<datetimepicker data-ng-model="dateRangeEnd"
										data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'start-date-changed' }"
										data-on-set-time="endDateOnSetTime()"
										data-before-render="endDateBeforeRender($view, $dates, $leftDate, $upDate, $rightDate)"></datetimepicker>
					</ul>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-primary" value="Add Event">
				<?php echo form_close() ?>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Update Calendar Event</h4>
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
				<div class="dropdown form-group dropdown-start-parent">
					<label for="p-in" class="col-md-4 label-heading">Start Date</label>
					<a class="dropdown-toggle" id="dropdownStart" role="button" data-toggle="dropdown" data-target=".dropdown-start-parent"
					   href="#">
						<div class="input-group date">
							<input type="text" class="form-control" data-date-time-input="YYYY/MM/DD hh:mm" data-ng-model="dateRangeStart" name="start_date" id="start_date">
							<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						</div>
					</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<datetimepicker data-ng-model="dateRangeStart"
										data-datetimepicker-config="{ dropdownSelector: '#dropdownStart', renderOn: 'end-date-changed' }"
										data-on-set-time="startDateOnSetTime()"
										data-before-render="startDateBeforeRender($dates)"></datetimepicker>
					</ul>
				</div>
				<div class="dropdown form-group dropdown-end-parent">
					<label for="p-in" class="col-md-4 label-heading">End Date</label>
					<a class="dropdown-toggle" id="dropdownEnd" role="button" data-toggle="dropdown" data-target=".dropdown-end-parent"
					   href="#">
						<div class="input-group date">
							<input type="text" class="form-control" data-date-time-input="YYYY/MM/DD hh:mm" data-ng-model="dateRangeEnd" name="end_date" id="end_date">
							<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
						</div>
					</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<datetimepicker data-ng-model="dateRangeEnd"
										data-datetimepicker-config="{ dropdownSelector: '#dropdownEnd', renderOn: 'start-date-changed' }"
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
				<input type="submit" class="btn btn-primary" value="Update Event">
				<?php echo form_close() ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {

	var date_last_clicked = null;

	$('#calendar').fullCalendar({
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
	   eventClick: function(event, jsEvent, view) {
		  $('#name').val(event.title);
		  $('#description').val(event.description);
		  $('#start_date').val(moment(event.start).format('YYYY/MM/DD HH:mm'));
		  if(event.end) {
			$('#end_date').val(moment(event.end).format('YYYY/MM/DD HH:mm'));
		  } else {
			$('#end_date').val(moment(event.start).format('YYYY/MM/DD HH:mm'));
		  }
		  $('#event_id').val(event.id);
		  $('#editModal').modal();
	   },
	});
});
</script>