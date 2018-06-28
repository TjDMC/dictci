<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div class="card">
	<div class="card-body">
		<h1 style="font-weight:none">Department of Information and Technology</h1>
		<div class="row" style="padding: 5%; padding-top: 7%; padding-bottom: 7%">
			<div class="col-sm-6" style="text-align: center">
				<div class="myLargeIcon">
					<a href="<?= base_url()?>employee" style="text-decoration:none; color:#2abe91;">
						<div class="thumbnail">
							<span class="fas fa-user-tie fa-10x"></span></br>
							Employee
						</div>
					</a>
				</div>
			</div>
			<div class="col-sm-6" style="text-align: center">
				<div class="myLargeIcon">
					<a href="<?= base_url()?>calendar" style="text-decoration:none; color:#23969d;">
						<div class="thumbnail">
							<span class="far fa-calendar-alt fa-10x" ></span></br>
							Calendar
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
