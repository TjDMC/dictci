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

		$input = $this->input->post('data');
		$data = parse_custom_post($input);
		$res = null;
		if(isset($data['is_external']) && !$data['is_external']){
			$res = $this->employee_leaves_model->setEmployeeTableMeta();
		}else{
			$res = $this->employee_leaves_model->setEmployeeTableMeta($data);
		}
		if($res!=null){
			custom_response(false,$res);
		}
		$this->calendar_model->createTable();
		custom_response(true,'Success');
	}
}
