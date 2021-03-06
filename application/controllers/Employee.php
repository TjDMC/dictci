<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model("employee_leaves_model");
		$this->load->model("calendar_model");
    }

    public function body(){
        $this->load->view("employee/navigation",array(
            "employees"=>json_encode($this->employee_leaves_model->getEmployees(),JSON_HEX_APOS|JSON_HEX_QUOT),
            "editable"=>$this->canEditEmployees() ? 'true':'false'
        ));
    }

    public function display($employeeNo=null){
        if($employeeNo == null){
            $this->index();
            return;
        }
        $employee = $this->employee_leaves_model->getEmployee($employeeNo);
        if($employee==null){
            show_404();
        }
        $leaves = $this->employee_leaves_model->getLeaves($employeeNo);
		$events = $this->calendar_model->getEvents();
        $editable = $this->canEditEmployees();

        $this->html(
            function() use ($employee,$leaves,$events,$editable){
                $this->load->view("employee/display",array(
                    "employee"=>json_encode($employee,JSON_HEX_APOS|JSON_HEX_QUOT),
                    "leaves"=>json_encode($leaves,JSON_HEX_APOS|JSON_HEX_QUOT|JSON_NUMERIC_CHECK),
                    "events"=>json_encode($events,JSON_HEX_APOS|JSON_HEX_QUOT|JSON_NUMERIC_CHECK),
                    "editable"=>$editable ? 'true':'false'
                ));
            }
        );
    }

    public function add(){
        if(!$this->canEditEmployees()) return;
        $input = $this->input->post('data');
        if($input === null){
            $this->html(
                function(){
                    $this->load->view("employee/add");
                }
            );
        }else{
            $data = parse_custom_post($input);
            $response = $this->employee_leaves_model->addEmployee($data);
            if($response!==null){
                custom_response(false,$response);
            }else{
                custom_response(true,"Successfully added employee");
            }
        }

    }

    public function edit(){
        $input = $this->input->post('data');
        if($input === null)
            redirect(site_url("employee"));
        $data = parse_custom_post($input);
        $response = $this->employee_leaves_model->editEmployee($data,$this->canEditEmployees());
        if($response!==null){
            custom_response(false,$response);
        }else{
            custom_response(true,"Successfully edited employee info.");
        }
    }

    public function delete(){
        if(!$this->canEditEmployees()) return;
        $input = $this->input->post('data');
        if($input === null)
            redirect(site_url("employee"));
        $data = parse_custom_post($input);
        if(!isset($data['emp_no']) || !isset($data['password'])){
            custom_response(false,'Incomplete input.');
        }
        if(!$this->ion_auth->hash_password_db($this->ion_auth->user()->row()->id, $data['password'])){
            custom_response(false,'Incorrect Password');
        }
        $this->employee_leaves_model->deleteEmployee($data['emp_no']);
        custom_response(true,'Success.');
    }

    public function leaveRecords($employeeNo=null){
        $input = $this->input->post('data');
        if($input === null){
            $employee = null;
            if($employeeNo===null){
                redirect('/employee');
            }else{
                $employee = $this->employee_leaves_model->getEmployee($employeeNo);
            }
			//	Calendar Events
			$events = $this->calendar_model->getEvents();
            $this->html(
                function() use ($employee,$events){
                    $this->load->view('employee/leave_records',array(
                        "employee"=>json_encode($employee,JSON_HEX_APOS|JSON_HEX_QUOT),
						"events"=>json_encode($events,JSON_HEX_APOS|JSON_HEX_QUOT|JSON_NUMERIC_CHECK)
                    ));
                }
            );
        }else{
            $data = parse_custom_post($input);
            if(isset($data['info']["type"]) && $data['info']["type"] == "Others"){ //convert type_others to type
                if(!isset($data['info']['type_others'])){
                    custom_response(false,"Please specify the leave type.");
                }
                $invalidSpecialTypes = array('Special Leave','Forced Leave','Undertime','Vacation','Sick');
                if(in_array($data['info']["type_others"],$invalidSpecialTypes)){
                    custom_response(false,"Invalid leave type.");
                }
                $data['info']["type"] = $data['info']['type_others'];
            }
            if(isset($data['action'])&&$data['action']=='edit'){
                $response = $this->employee_leaves_model->editLeave($data);
            }else{
                $response = $this->employee_leaves_model->addLeaves($data);
            }
            if(!is_array($response)){ //if response is not an array, it is a string containing an error msg.
                custom_response(false, $response);
            }else{
                custom_response(true,"Success",array('leave'=>$response));
            }
        }

    }

    public function deleteLeave(){
        $data = parse_custom_post($this->input->post('data'));
        $this->employee_leaves_model->deleteLeave($data);
        custom_response(true,'Leave record deleted');
    }

    public function canEditEmployees(){
        return $this->employee_leaves_model->getEmployeeTableMeta()['is_external'] ? $this->config->item('employee_editable_if_external') : true;
    }

}
