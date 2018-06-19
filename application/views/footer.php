<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

</div>

<div ng-controller="login" style="background-color:black;box-shadow: 0 0 8px #D0D0D0;">
	<br/><br/><br/><br/><br/>Sample footer
	<button ng-if="<?=$this->ion_auth->logged_in()?1:0?>==true" ng-click="logout()">LOGOUT</button>
</div>

<!-- Bootstrap Scripts -->
<script src="<?= base_url() ?>/js/jquery.min.js"></script>
<script src="<?= base_url() ?>/js/bootstrap.bundle.min.js"></script>

</body>
</html>
