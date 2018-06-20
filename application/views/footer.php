<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

	


		<footer>
			Sample footer
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
				<div id="employee" class="collapse">
					<a href="<?= base_url()?>employee/add">&nbsp;&nbsp;Add New</a></br>
					<a href="<?= base_url()?>employee/leaveapplication">&nbsp;&nbsp;Application for Leave</a></br>
					<a href="<?= base_url()?>employee">&nbsp;&nbsp;View List</a></br>
					<a href="#">&nbsp;&nbsp;Resignation</a>
				</div>
			</li>
			<li>
				<a data-toggle="collapse" data-target="#calendar" aria-expanded="false">Calendar</a>
				<div id="calendar" class="collapse">
					<a href="#">&nbsp;&nbsp;Add Holiday</a></br>
					<a href="<?= base_url().'calendar' ?>">&nbsp;&nbsp;View Calendar</a></br>
					<a href="#">&nbsp;&nbsp;Suspended Work</a>
				</div>
			</li>
			<li ng-controller="login">
				<a ng-if="<?=$this->ion_auth->logged_in()?1:0?>==true" ng-click="logout()">Logout</a>
			</li>
		</ul>
	</div>
	<!-- /#sidebar -->
	
</div>

<!-- Bootstrap Scripts -->
<script src="<?= base_url() ?>/js/jquery.min.js"></script>
<script src="<?= base_url() ?>bootstrap/js/bootstrap.min.js"></script>

<script>
	$("#menu-toggle").click(function(e) {
		e.preventDefault();
		$("#wrapper").toggleClass("active");
	});
</script>

</body>
</html>
