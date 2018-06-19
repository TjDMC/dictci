<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="login" ng-init="test()">
    <form>
        <p>
            Username: <input type="text" />
        </p>
        <p>
            Password: <input type="password"/>
        </p>
        <div>
            <input type="submit"/>
        </div>
    </form>
</div>
