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
	}
}
