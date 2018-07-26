<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div ng-controller="init_db" class="card">
    <div class="card-header">
        <h2>Initialize Database</h2>
    </div>
    <div class="card-body">
        <h4 class="alert alert-danger">!WARNING! This page is for development only. Use at your own risk.</h4>

        <h4 class="alert alert-info">Set Employee Database Table:</h4>
        <div class="">
            <div class="w-50 float-left p-0 card">
                <div class="card-header">
                    <h4>Use internal employees table</h4>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary" ng-click="initialize()"> Initialize Database </button>
                    <button class="btn btn-primary" ng-click="populate()"> Populate Database (Use Carefully) </button>
                </div>
            </div>
            <div class="w-50 float-right p-0 card">
                <div class="card-header">
                    <h4>Use external</h4>
                </div>
                <div class="card-body">
                    <form ng-submit="initialize">
                        <div class="form-group">
                            <label>Employee Table Name:</label>
                            <input class="form-control" type="text" required/>
                        </div>
                        <div class="form-group">
                            <label>'Employee No.'' Column Name:</label>
                            <input class="form-control" type="text" required/>
                        </div>
                        <div class="form-group">
                            <label>'Employee First Name 'Column Name:</label>
                            <input class="form-control" type="text" required/>
                        </div>
                        <div class="form-group">
                            <label>'Employee Middle Name' Column Name:</label>
                            <input class="form-control" type="text" required/>
                        </div>
                        <div class="form-group">
                            <label>'Employee Surname' Column Name:</label>
                            <input class="form-control" type="text" required/>
                        </div>
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
