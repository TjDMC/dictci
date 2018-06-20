<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<html ng-app="app" lang="en">
<head ng-controller="initializer" ng-init="init('<?=base_url()?>','<?= $this->security->get_csrf_token_name()?>','<?= $this->security->get_csrf_hash()?>')">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <title>DICT</title>
	<link rel="icon" href="<?= base_url()?>/favicon.png" type="image/gif">


	<link href="<?= base_url() ?>bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<link href="<?= base_url() ?>/css/style.css" type="text/css" rel="stylesheet">
	
	
    <script src="<?= base_url().'js/angular.min.js' ?>"></script>

    <script src="<?= base_url().'js/app.js'?>"></script>
	<script src="<?= base_url().'js/app_employee.js'?>"></script>
</head>

<body>
<div id="wrapper">
	<div id="content">
	<!-- Header -->
	<header>
		<div class="row">
			<div class="col-md-5 col-12">
				<a><img src="<?=base_url()?>/dict_logo.png" alt="DICT Logo" height="140px" /></a>
			</div>
		
			<div class="col-md-7 col-12" style="text-align: right; font-size: 25px;">
				Leave Credit Computation</br>
				<a ng-hide="<?=$this->ion_auth->logged_in()?1:0?>==false" href="#menu-toggle" class="btn btn-default" id="menu-toggle">Menu</a>
			</div>
		</div>
	</header>
	<!-- /#header -->