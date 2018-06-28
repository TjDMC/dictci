<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>


<div class="card">
	<div class="card-body p-5">
		<div style="text-align:center"><img class="mx-auto" src="<?=base_url()?>dict_home.png"/></div>
		<div class="row m-5 justify-content-center">
			<div class="col-sm-3">
				<div class="myLargeIcon">
					<a href="<?= base_url()?>employee" style="text-decoration:none; color:#2abe91;">
						<div style="text-align:center">
							<span class="fas fa-user-tie fa-10x mb-3"></span></br>
							<h3>Employees</h3>
						</div>
					</a>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="myLargeIcon">
					<a href="<?= base_url()?>calendar" style="text-decoration:none; color:#23969d;">
						<div style="text-align:center">
							<span class="far fa-calendar-alt fa-10x mb-3" ></span></br>
							<h3 class="">Calendar</h3>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
