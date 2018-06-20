<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>


<div id="container">
	<div class="row" style="padding-top: 7%">
		<div class="col-sm-6">
			<div>
				<a href="<?= base_url()?>employee" style="text-decoration:none; color:mediumaquamarine;">
					<div class="thumbnail" style="text-align:center; font-size:50px;">
						<span class="fas fa-user-tie" style="font-size:100px"></span></br>
						Employee
					</div>
				</a>
			</div>
		</div>
		<div class="col-sm-6">
			<div>
				<a href="<?= base_url()?>calendar" style="text-decoration:none; color:red;">
					<div class="thumbnail" style="text-align:center; font-size:50px;">
						<span class="far fa-calendar-alt" style="font-size:100px"></span></br>
						Calendar
					</div>
				</a>
			</div>
		</div>
	</div>
</div>
