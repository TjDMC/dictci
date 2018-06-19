<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="login" ng-init="test()">
    <form>
        <p>
            Username: <input ng-model="username" type="text" />
        </p>
        <p>
            Password: <input ng-model="password" type="password"/>
        </p>
        <div>
            <input type="submit" ng-click="login()"/>
        </div>
    </form>
</div>
