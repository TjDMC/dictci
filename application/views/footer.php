<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

	
		</div>

		<footer>
			<div style="float:right; padding:5px">
				All Rights Reserved. © 2018 YEEAA BOIII!!!
			</div>
		</footer>
	</div>
	
	<!-- SideBar -->
	<div id="sidebar-wrapper">
		<ul class="sidebar-nav">
			<li class="sidebar-brand">
				<a href="<?= base_url() ?>">DICT</a>
			</li>
			<li>
				<a href="<?= base_url() ?>">Home</a>
			</li>
			<li>
				<a data-toggle="collapse" data-target="#employee" aria-expanded="false">Employee</a>
				<div id="employee" class="collapse smaller">
					<a href="<?= base_url()?>employee/add">&nbsp;&nbsp;&nbsp;&nbsp;Add New</a></br>
					<a href="<?= base_url()?>employee/leaveapplication">&nbsp;&nbsp;&nbsp;&nbsp;Application for Leave</a></br>
					<a href="<?= base_url()?>employee">&nbsp;&nbsp;&nbsp;&nbsp;View List</a></br>
					<a href="#">&nbsp;&nbsp;&nbsp;&nbsp;Resignation</a>
				</div>
			</li>
			<li>
				<a data-toggle="collapse" data-target="#choice_calendar" aria-expanded="false">Calendar</a>
				<div id="choice_calendar" class="collapse smaller">
					<a href="#">&nbsp;&nbsp;&nbsp;&nbsp;Add Holiday</a></br>
					<a href="<?= base_url().'calendar' ?>">&nbsp;&nbsp;&nbsp;&nbsp;View Calendar</a></br>
					<a href="#">&nbsp;&nbsp;&nbsp;&nbsp;Suspended Work</a>
				</div>
			</li>
			<li ng-controller="login">
				<a ng-if="<?=$this->ion_auth->logged_in()?1:0?>==true" ng-click="logout()">Logout</a>
			</li>
		</ul>
	</div>
	<!-- /#sidebar -->
	
</div>

<script>
	$("#menu-toggle").click(function(e) {
		e.preventDefault();
		$("#wrapper").toggleClass("active");
	});
</script>

</body>
</html>
