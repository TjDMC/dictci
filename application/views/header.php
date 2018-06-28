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

	<script src="<?= base_url().'js/jquery.min.js' ?>"></script>
	<script src="<?= base_url().'js/moment.min.js' ?>"></script>
	<script src="<?= base_url().'js/moment-timezone.min.js' ?>"></script>
	<script src="<?= base_url().'js/popper.min.js' ?>"></script>
	<script src="<?= base_url().'js/bootstrap.min.js' ?>"></script>
    <script src="<?= base_url().'js/angular.min.js' ?>"></script>
	<script src="<?= base_url().'js/angular-sanitize.min.js' ?>"></script>

	<script src="<?= base_url().'js/datetimepicker/datetimepicker.js' ?>"></script>
	<script src="<?= base_url().'js/datetimepicker/datetimepicker.templates.js' ?>"></script>
	<script src="<?= base_url().'js/datetimepicker/dateTimeInput.js' ?>"></script>

    <script src="<?= base_url().'js/app.js'?>"></script>
	<script src="<?= base_url().'js/app_employee.js'?>"></script>

	<link rel="stylesheet" href="<?php echo base_url() ?>scripts/fullcalendar/fullcalendar.min.css" />
	<script src="<?php echo base_url() ?>scripts/fullcalendar/fullcalendar.min.js"></script>
	<script src="<?php echo base_url() ?>scripts/fullcalendar/gcal.js"></script>
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
		<a class="navbar-brand" href="index.html">DICT Leave Credit Computation</a>
		<button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarResponsive">
			<ul class="navbar-nav navbar-sidenav" id="exampleAccordion">
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Home">
					<a class="nav-link" href="#">
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
							<a href="navbar.html">Navbar</a>
						</li>
						<li>
							<a href="cards.html">Cards</a>
						</li>
					</ul>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
					<a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseExamplePages" data-parent="#exampleAccordion">
						<i class="fas fa-fw fa-calendar-alt"></i>
						<span class="nav-link-text">Example Pages</span>
					</a>
					<ul class="sidenav-second-level collapse" id="collapseExamplePages">
						<li>
							<a href="login.html">Login Page</a>
						</li>
						<li>
							<a href="register.html">Registration Page</a>
						</li>
						<li>
							<a href="forgot-password.html">Forgot Password Page</a>
						</li>
						<li>
							<a href="blank.html">Blank Page</a>
						</li>
					</ul>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Menu Levels">
					<a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseMulti" data-parent="#exampleAccordion">
						<i class="fa fa-fw fa-sitemap"></i>
						<span class="nav-link-text">Menu Levels</span>
					</a>
					<ul class="sidenav-second-level collapse" id="collapseMulti">
						<li>
							<a href="#">Second Level Item</a>
						</li>
						<li>
							<a href="#">Second Level Item</a>
						</li>
						<li>
							<a href="#">Second Level Item</a>
						</li>
					</ul>
				</li>
				<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Link">
					<a class="nav-link" href="#">
						<i class="fa fa-fw fa-link"></i>
						<span class="nav-link-text">Link</span>
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
			<ul class="navbar-nav ml-auto">
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle mr-lg-2" id="messagesDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fa fa-fw fa-envelope"></i>
					<span class="d-lg-none">Messages
						<span class="badge badge-pill badge-primary">12 New</span>
					</span>
					<span class="indicator text-primary d-none d-lg-block">
						<i class="fa fa-fw fa-circle"></i>
					</span>
					</a>
					<div class="dropdown-menu" aria-labelledby="messagesDropdown">
						<h6 class="dropdown-header">New Messages:</h6>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#">
							<strong>David Miller</strong>
							<span class="small float-right text-muted">11:21 AM</span>
							<div class="dropdown-message small">Hey there! This new version of SB Admin is pretty awesome! These messages clip off when they reach the end of the box so they don't overflow over to the sides!</div>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#">
							<strong>Jane Smith</strong>
							<span class="small float-right text-muted">11:21 AM</span>
							<div class="dropdown-message small">I was wondering if you could meet for an appointment at 3:00 instead of 4:00. Thanks!</div>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#">
							<strong>John Doe</strong>
							<span class="small float-right text-muted">11:21 AM</span>
							<div class="dropdown-message small">I've sent the final files over to you for review. When you're able to sign off of them let me know and we can discuss distribution.</div>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item small" href="#">View all messages</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle mr-lg-2" id="alertsDropdown" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-fw fa-bell"></i>
						<span class="d-lg-none">Alerts
							<span class="badge badge-pill badge-warning">6 New</span>
						</span>
						<span class="indicator text-warning d-none d-lg-block">
							<i class="fa fa-fw fa-circle"></i>
						</span>
					</a>
					<div class="dropdown-menu" aria-labelledby="alertsDropdown">
						<h6 class="dropdown-header">New Alerts:</h6>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#">
							<span class="text-success">
								<strong>
								<i class="fa fa-long-arrow-up fa-fw"></i>Status Update</strong>
							</span>
							<span class="small float-right text-muted">11:21 AM</span>
							<div class="dropdown-message small">This is an automated server response message. All systems are online.</div>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#">
							<span class="text-danger">
								<strong>
								<i class="fa fa-long-arrow-down fa-fw"></i>Status Update</strong>
							</span>
							<span class="small float-right text-muted">11:21 AM</span>
							<div class="dropdown-message small">This is an automated server response message. All systems are online.</div>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#">
							<span class="text-success">
								<strong>
								<i class="fa fa-long-arrow-up fa-fw"></i>Status Update</strong>
							</span>
							<span class="small float-right text-muted">11:21 AM</span>
							<div class="dropdown-message small">This is an automated server response message. All systems are online.</div>
						</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item small" href="#">View all alerts</a>
					</div>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-toggle="modal" data-target="#exampleModal">
					<i class="fa fa-fw fa-sign-out"></i>Logout</a>
				</li>
			</ul>
		</div>
	</nav>
