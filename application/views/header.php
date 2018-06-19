<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<html ng-app="app">
<head ng-controller="initializer" ng-init="init('<?=base_url()?>','<?= $this->security->get_csrf_token_name()?>','<?= $this->security->get_csrf_hash()?>')">
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
			<div class="col-md-5">
				(logo)
				DICT
			</div>
		
			<div class="col-md-7" style="text-align: right; font-size: 25px;">
				Leave Credit</br>
				Computation
			</div>
		<div>
	</header>
