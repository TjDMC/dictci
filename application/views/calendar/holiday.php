<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="calendar_display" ng-init='init(<?=isset($events)?$events:''?>)'>
	<h1>Holiday list</h1>
	<div ng-repeat="event in date.events">
		<p>Event: {{event.title}}, Date: {{event.date}}</p>
	</div>
</div>