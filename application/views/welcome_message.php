<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div class="card">
	<div class="card-body p-5">
		<div style="text-align:center"><img class="mx-auto" src="<?=base_url()?>dict_home.png"/></div>
		<div class="row m-5 justify-content-center">
			<div class="col-sm-3 text-center">
				<a href="<?= base_url()?>employee" style="text-decoration:none; color:#2abe91;">
					<button class="btn btn-outline-success p-4 text-center icon-buttons" style="border-radius:10px;border-width:5px;">
						<span class="fas fa-user-tie fa-10x mb-3"></span></br>
						<h3>Employees</h3>
					</button>
				</a>
			</div>
			<div class="col-sm-3">
				<div class="mx-auto text-center">
					<a href="<?= base_url()?>calendar" style="text-decoration:none; color:#23969d;">
						<button class="btn btn-outline-primary p-4 text-center icon-buttons" style="border-radius:10px;border-width:5px;">
							<span class="far fa-calendar-alt fa-10x mb-3" ></span></br>
							<h3>Calendar</h3>
						</button>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
