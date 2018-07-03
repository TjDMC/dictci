<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="init_db" class="card">
    <div class="card-header">
        <h2>Initialize Database</h2>
    </div>
    <div class="card-body">
        <button class="btn btn-primary" ng-click="initialize()"> Initialize Database </button>
		<button class="btn btn-primary" ng-click="populate()"> Populate Database </button>
    </div>
</div>
