<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<html ng-app="app" lang="en">
<head ng-controller="initializer" ng-init="init('<?=base_url()?>','<?= $this->security->get_csrf_token_name()?>','<?= $this->security->get_csrf_hash()?>')">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <title>&#124;  DICT</title>
	<link rel="icon" href="<?= base_url()?>/favicon.png" type="image/gif">


	<link href="<?= base_url() ?>/css/fontawesome-all.min.css" type="text/css" rel="stylesheet">
	<link href="<?= base_url() ?>/bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<link href="<?= base_url() ?>/css/datetimepicker/datetimepicker.css" type="text/css" rel="stylesheet">
	<link href="<?= base_url() ?>/css/style.css" type="text/css" rel="stylesheet">

	<script src="<?= base_url().'js/jquery.min.js' ?>"></script>
	<script src="<?= base_url().'js/moment.min.js' ?>"></script>
	<script src="<?= base_url().'js/moment-timezone.min.js' ?>"></script>
	<script src="<?= base_url().'js/popper.min.js' ?>"></script>
	<script src="<?= base_url().'bootstrap/js/bootstrap.min.js' ?>"></script>
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
<div id="wrapper">
	<div id="content">
	<!-- Header -->
	<header>
		<div class="row">
			<div class="col-sm-4 col-12">
				<a href="<?= base_url() ?>"><img src="<?=base_url()?>/dict_logo.png" alt="DICT Logo" style="max-width:55%;width: auto;height: auto;" /></a>
			</div>

			<div class="col-sm-8 col-12" style="text-align: right; font-size:calc(12px + 1.25vw);">
				Leave Credit Computation</br>
				<button ng-hide="<?=$this->ion_auth->logged_in()?1:0?>==false" href="#menu-toggle" class="btn btn-default" id="menu-toggle"><span class="fas fa-ellipsis-h"></span></button>
			</div>
		</div>
	</header>
	<!-- /#header -->
	<div id="container">
