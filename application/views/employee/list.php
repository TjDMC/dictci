<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-init='employees=<?=$employees?>'>
    <table>
        <tr ng-repeat="employee in employees">
            <td>{{employee.ID}}</td>
            <td>{{employee.Name}}</td>
        </tr>
    </table>
</div>
