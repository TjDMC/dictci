<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<html ng-app="app" lang="en">
<head ng-controller="initializer" ng-init="init('<?=base_url()?>','<?= $this->security->get_csrf_token_name()?>','<?= $this->security->get_csrf_hash()?>')">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	
    <title>DICT</title>
	
	
	<link href="<?= base_url() ?>/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<link href="<?= base_url() ?>/css/style.css" type="text/css" rel="stylesheet">
	
	<!--<link href="<?= base_url() ?>/css/example.css" type="text/css" rel="stylesheet">-->
    <script src="<?= base_url().'js/angular.min.js' ?>"></script>

    <script src="<?= base_url().'js/app.js'?>"></script>
</head>

<body>
<div id="wrapper">
	<!-- Header -->
	<header>
		<div class="row">
			<div class="col-md-5 col-12">
				(logo)
				DICT
			</div>
		
			<div class="col-md-7 col-12" style="text-align: right; font-size: 25px;">
				Leave Credit</br>
				Computation
			</div>
		<div>
	</header>
	
	<nav class="nav">
		<div>
			<div class="row button-grp">
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#employee" aria-expanded="false">Working</button>
			</div>
		</div>
	</nav>
	
	<div id="employee" class="collapse">
		Sad Life
	</div>