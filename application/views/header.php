<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<html ng-app="app" lang="en">
<head ng-controller="initializer" ng-init="init('<?=base_url()?>','<?= $this->security->get_csrf_token_name()?>','<?= $this->security->get_csrf_hash()?>')">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <title>Department of Information and Communications Technology</title>
	<link rel="icon" href="<?= base_url()?>/favicon.png" type="image/gif">


	<link href="<?= base_url() ?>css/fontawesome-all.min.css" type="text/css" rel="stylesheet">
	<link href="<?= base_url() ?>css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<link href="<?= base_url() ?>css/datetimepicker/datetimepicker.css" type="text/css" rel="stylesheet">
	<link href="<?= base_url() ?>css/style.css" type="text/css" rel="stylesheet">
	<link href="<?= base_url() ?>css/print.css" type="text/css" rel="stylesheet">

	<script src="<?= base_url().'js/jquery.min.js' ?>"></script>
	<script src="<?= base_url().'js/moment.min.js' ?>"></script>
	<script src="<?= base_url().'js/moment-timezone.min.js' ?>"></script>
	<script src="<?= base_url().'js/popper.min.js' ?>"></script>
	<script src="<?= base_url().'js/bootstrap.min.js' ?>"></script>
    <script src="<?= base_url().'js/angular.min.js' ?>"></script>
	<script src="<?= base_url().'js/chart.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-chart.min.js' ?>"></script>
	<script src="<?= base_url().'js/datetimepicker/datetimepicker.js' ?>"></script>
	<script src="<?= base_url().'js/datetimepicker/datetimepicker.templates.js' ?>"></script>
	<script src="<?= base_url().'js/datetimepicker/dateTimeInput.js' ?>"></script>
	<script src="<?= base_url().'js/ng-table-to-csv.min.js' ?>"></script>

    <script src="<?= base_url().'js/app.js'?>"></script>
	<script src="<?= base_url().'js/app_employee.js'?>"></script>
	<script src="<?= base_url().'js/app_calendar.js'?>"></script>

	<script>
		console.log("%c STOP! ","color: red; background-color: black; font-size: 100px; font-weight: bold");
		console.log("%cThis feature is meant for developers' use only.","font-size: 30px");
		console.log("%cAnything entered here might cause errors in the system.","font-size:20px")
	</script>
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav" style="background-color:black">
		<a class="navbar-brand" href="<?=base_url()?>">
			<span><img src="<?=base_url()?>/favicon.png" width="25" height="25" alt="DICT Logo" /></span>
			DICT Leave Credit Computation
		</a>
		<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive" >
			<ul class="navbar-nav navbar-sidenav" id="exampleAccordion" >
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Home">
					<a class="nav-link" href="<?=base_url()?>">
						<i class="fas fa-fw fa-home"></i>
						<span class="nav-link-text">Home</span>
					</a>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Components">
					<a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseComponents" data-parent="#exampleAccordion">
						<i class="fas fa-fw fa-user-tie"></i>
						<span class="nav-link-text">Employees</span>
					</a>
					<ul class="sidenav-second-level collapse" id="collapseComponents">
						<li>
							<a href="<?= base_url()?>employee">View List</a>
						</li>
						<li>
							<a href="<?= base_url()?>employee/add">Add Employee</a>
						</li>
					</ul>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
					<a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseExamplePages" data-parent="#exampleAccordion">
						<i class="fas fa-fw fa-calendar-alt"></i>
						<span class="nav-link-text">Calendar</span>
					</a>
					<ul class="sidenav-second-level collapse" id="collapseExamplePages">
						<li>
							<a href="<?= base_url().'calendar' ?>">View Calendar</a>
						</li>
						<li>
							<a href="<?= base_url().'calendar/manageHolidays' ?>">Manage Holidays</a>
						</li>
						<li>
							<a href="<?= base_url().'calendar/suspendWork' ?>">Suspended Work</a>
						</li>
						<li>
							<a href="<?= base_url().'calendar/manageCollisions' ?>">Manage Collisions</a>
						</li>
					</ul>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Admin Settings" ng-if="<?=$this->ion_auth->is_admin()?1:0?>==true">
					<a class="nav-link"  href="<?=base_url()?>admin">
						<i class="fas fw-fw fa-user-cog"></i>
						<span class="nav-link-text">Admin</span>
					</a>
				</li>
				<li ng-controller="login" class="nav-item" data-toggle="tooltip" data-placement="right" title="Logout" ng-if="<?=$this->ion_auth->logged_in()?1:0?>==true" ng-click="logout()">
					<a class="nav-link"  href="#">
						<i class="fas fa-fw fa-sign-out-alt"></i>
						<span class="nav-link-text">Logout</span>
					</a>
				</li>
			</ul>
			<ul class="navbar-nav sidenav-toggler">
				<li class="nav-item">
					<a class="nav-link text-center" id="sidenavToggler">
						<i class="fa fa-fw fa-angle-left"></i>
					</a>
				</li>
			</ul>

		</div>
	</nav>
	<div class="content-wrapper p-5" ng-cloak>
