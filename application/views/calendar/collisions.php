<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="card" ng-controller="calendar_collisions" ng-init='init('.<?=$collisions?>.')'>
    <div class="card-header">
        <h3>Manage Collisions</h3>
    </div>
    <div class="card-body">
        {{collisions}}
    </div>
</div>
