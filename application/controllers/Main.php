<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

	public function body()
	{
		if($this->ion_auth->logged_in()){
			$this->load->view('welcome_message');
		}else{
			$this->load->view('login');
		}
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
