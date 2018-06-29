<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>

<div class="card mx-auto my-auto h-100" ng-controller="login" style="max-width:25em">
    <div class="card-header" >
        <h3>Login</h3>
    </div>
    <div class="card-body container">
        <form ng-submit="login()" class="max-auto">
            <div class="form-group" >
                <label>Username:</label>
                <div class="input-group" >

                    <input class="form-control" type="text" ng-model="username" required>
                </div>
            </div>
            <div class="form-group" >
                <label>Password:</label>
                <div class="input-group" ng-init="passType='password'">
                    <input id="pass" class="form-control" type="{{passType}}" ng-model="password" required>
    				<div class="input-group-append" ng-mousedown="passType='text'" ng-mouseleave="passType='password'" ng-mouseup="passType='password'">
                        <span class="input-group-text"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
            </div>
            <div>
                <button class="btn btn-primary btn-block" type="submit">Login</button>
            </div>
        </form>
    </div>
	<script>
	var pageTitle = document.getElementsByTagName("title");
	pageTitle[0].innerHTML = "Login";
	</script>
</div>
