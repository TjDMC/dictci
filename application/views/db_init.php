<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->database();
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
                    <button class="btn btn-primary" ng-click="initialize({is_external:false})"> Initialize Database </button>
                </div>
            </div>
            <div class="w-50 float-right p-0 card">
                <div class="card-header">
                    <h4>Use external</h4>
                </div>
                <div class="card-body">
                    <form ng-submit="initialize(meta)">
                        <div class="form-group">
                            <label>Employee Table Name (must be <?=$this->db->dbcollat?> (collation)):</label>
                            <input ng-model="meta.table_name" class="form-control" type="text" required/>
                        </div>
                        <div class="form-group">
                            <label>'Employee No.'' Column Name (must be 'char(7)'):</label>
                            <input ng-model="meta.emp_no" class="form-control" type="text" required/>
                        </div>
                        <div class="form-group">
                            <label>'Employee First Name 'Column Name:</label>
                            <input ng-model="meta.first_name" class="form-control" type="text" required/>
                        </div>
                        <div class="form-group">
                            <label>'Employee Middle Name' Column Name:</label>
                            <input ng-model="meta.middle_name" class="form-control" type="text" required/>
                        </div>
                        <div class="form-group">
                            <label>'Employee Surname' Column Name:</label>
                            <input ng-model="meta.surname" class="form-control" type="text" required/>
                        </div>
                        <div class="form-group">
                            <label>'Employee Employment Date' Column Name:</label>
                            <input ng-model="meta.first_day_employ" class="form-control" type="text" required/>
                        </div>
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
