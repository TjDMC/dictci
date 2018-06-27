<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div ng-controller="login">
    <div>
        <h2>LOGIN</h2>
    </div>
    <form ng-submit="login()" style="max-width:400px">
        <div class="form-group" >
            <div class="input-group" >
                <div class="input-group-prepend">
                    <span class="input-group-text">Username</span>
                </div>
                <input class="form-control" type="text" ng-model="username" required>
            </div>
        </div>
        <div class="form-group" >
            <div class="input-group" >
                <div class="input-group-prepend">
                    <span class="input-group-text">Password&nbsp</span>
                </div>
                <input id="pass" class="form-control" type="password" ng-model="password" required>&nbsp;
				<div style="font-size:25px" ng-mousedown="mouseDown()" ng-mouseup="mouseUp()"><i class="fas fa-eye"></i></div>
            </div>
        </div>
        <div>
            <button class="btn btn-primary" type="submit">Login</button>
        </div>
    </form>
</div>
