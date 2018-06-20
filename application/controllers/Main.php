<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

	//Override
	public function checkLogin(){
		//In case login and logout functions are called, checklogin would have empty logic
	}

	//Override
	public function index()
	{
		parent::checkLogin();

		$this->html(function(){
			$this->load->view('welcome_message');
		});
	}

	public function login(){
		$data = parse_custom_post( $this->input->post('data'));

		if(!isset($data['username'])||!isset($data['password'])){
			custom_response(false,'Please fill-in the required fields');
		}

		if(!$this->ion_auth->login($data['username'],$data['password'])){
			custom_response(false,'Invalid Login');
		}

		custom_response(true,$data['username']);
	}

	public function logout(){
		$this->ion_auth->logout();
	}
}
