<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="employee_list" ng-init='init(<?=$employees?>)'>
    <table>
        <tr ng-repeat="employee in employees">
            <td>{{employee.ID}}</td>
            <td>{{employee.Name}}</td>
        </tr>
    </table>
</div>
