<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<div ng-controller="admin">
    <div class="card">
        <div class="card-header">
            <h2>Admin Settings</h2>
        </div>
        <div class="card-body p-4">
            <h5>Change login credentials</h5>
            <div class="row justify-content-start">
                <form class="col-md-3 col-xs-12" ng-submit="confirmPassword('username')">
                    <div class="form-group">
                        <label>New Username:</label>
                        <input ng-model="username" class="form-control" type="text" required>
                    </div>
                    <button ng-disabled="busy" class="btn btn-primary btn-block" type="submit">Change Username</button>
                </form>
                <form class="col-md-3 col-xs-12" ng-submit="confirmPassword('password')">
                    <div class="form-group">
                        <label>New Password:</label>
                        <input ng-model="newPassword1" class="form-control" type="password" required>
                    </div>
                    <div class="form-group">
                        <label>Enter Password Again:</label>
                        <input ng-model="newPassword2" class="form-control" type="password" required>
                    </div>
                    <button ng-disabled="busy" class="btn btn-primary btn-block" type="submit">Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmPasswordModal" tabindex="-1" role="dialog" aria-labelledby="confirmPasswordLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmPasswordLabel">Enter Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form ng-submit="submit()">
                        <div class="form-group">
                            <label>Please confirm by entering your old password:</label>
                            <input ng-model="password" class="form-control" type="password" required>
                        </div>
                        <button ng-disabled="busy" class="btn btn-primary btn-block" type="submit">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
