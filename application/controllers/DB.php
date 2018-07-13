<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DB extends MY_Controller {

	//Override
	public function body()
	{
		$this->load->view('db_init');
	}

	public function initDB(){
		if(!$this->ion_auth->is_admin()){
			show_error("You don't have permission to access this page.",403);
			return;
		}
		$this->load->model("employee_leaves_model");
		$this->load->model("calendar_model");
		$this->employee_leaves_model->createTable();
		$this->calendar_model->createTable();
	}

	public function populate(){
		if(!$this->ion_auth->is_admin()){
			show_error("You don't have permission to access this page.",403);
			return;
		}
		$this->load->model("employee_leaves_model");
		$this->employee_leaves_model->populate();
	}
}
