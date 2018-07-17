<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="card" ng-controller="calendar_collisions" ng-init='init(<?=$collisions?>)'>
    <div class="card-header">
        <h3>Manage Collisions</h3>
    </div>
    <div class="card-body">
        <div ng-repeat="event in collisions">
            <div>
                Event: {{event.date}} {{event.title}} {{event.description}}
            </div>
            <div>
                Leave: {{event.leave.info.type}},
                Date_ranges:
                <div ng-repeat="date_range in event.leave.date_ranges">
                    {{date_range.start_date}} {{date_range.end_date}}
                </div>
            </div>

        </div>
    </div>
</div>
