<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<style>

fieldset.icon-buttons {
	border-radius:10px;
	border-width:5px;
}

.icon-buttons legend.icon-buttons-primary{
	border:3px solid #007bff;
	border-radius:5px;
	background-color:white;
	color:#007bff
}

.icon-buttons legend.icon-buttons-success{
	border:3px solid #28a745;
	border-radius:5px;
	background-color:white;
	color:#28a745;
}

</style>

<div class="card">
	<div class="card-body p-5">
		<div style="text-align:center"><img class="mx-auto" src="<?=base_url()?>dict_home.png"/></div>
		<div class="row m-5 justify-content-center">
			<div class="col-md-3 col-sm-8 col-12 text-center" style="min-width:300px">
				<a href="<?= base_url()?>employee">
					<fieldset class="btn btn-outline-success p-4 text-center icon-buttons">
						<legend class="icon-buttons-success text-center">Employees</legend>
						<span class="fas fa-user-tie fa-10x mb-3"></span></br>
					</fieldset>
				</a>
			</div>

			<div class="col-md-3 col-sm-8 col-12 text-center" style="min-width:300px">
				<a href="<?= base_url()?>calendar">
					<fieldset class="btn btn-outline-primary p-4 text-center icon-buttons">
						<legend class="icon-buttons-primary text-center">Calendar</legend>
						<span class="far fa-calendar-alt fa-10x mb-3" ></span></br>
					</fieldset>
				</a>
			</div>
		</div>
	</div>
</div>
