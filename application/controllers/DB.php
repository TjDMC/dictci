<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DB extends MY_Controller {

	public function body()
	{
		if($this->ion_auth->logged_in()){
			$this->load->view('welcome_message');
		}else{
			$this->load->view('login');
		}
	}

	public function initDB(){
		$this->load->model("employee_model");
		$this->employee_model->createTable();
	}
}
