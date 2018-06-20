<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DB extends MY_Controller {

	//Override
	public function body()
	{
		echo '<p><a href="'.base_url().'/db/initDB"> Initialize Database </a></p>';
		echo '<p><a href="'.base_url().'/db/populate"> Populate Database </a></p>';
	}

	public function initDB(){
		if(!$this->ion_auth->is_admin()){
			show_error("You don't have permission to access this page.",403);
			return;
		}
		$this->load->model("employee_model");
		$this->employee_model->createTable();
	}

	public function populate(){
		if(!$this->ion_auth->is_admin()){
			show_error("You don't have permission to access this page.",403);
			return;
		}
		$this->load->model("employee_model");
		$this->employee_model->populate();
	}
}
