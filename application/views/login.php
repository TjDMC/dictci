<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="login">
    <div>
        <h2>LOGIN</h2>
    </div>
    <form ng-submit="login()">
        <div class="form-group" >
            <label>Username: </label>
            <input class="form-control" type="text" ng-model="username" required>
        </div>
        <div class="form-group" >
            <label>Password: </label>
            <input class="form-control" type="password" ng-model="password" required>
        </div>
        <div>
            <button class="btn btn-primary" type="submit">Login</button>
        </div>
    </form>
</div>
