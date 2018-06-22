<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends MY_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model("employee_model");
    }

    public function body(){
        $this->load->view("employee/navigation",array(
            "employees"=>json_encode($this->employee_model->getEmployees(),JSON_HEX_APOS|JSON_HEX_QUOT)
        ));
    }

    public function display($employeeNo=null){
        if($employeeNo == null){
            show_404();
        }
        $employee = $this->employee_model->getEmployee($employeeNo);
        if($employee==null){
            show_404();
        }
        $leaves = $this->employee_model->getLeaves($employeeNo);

        $this->html(
            function() use ($employee,$leaves){
                $this->load->view("employee/display",array(
                    "employee"=>json_encode($employee,JSON_HEX_APOS|JSON_HEX_QUOT),
                    "leaves"=>json_encode($leaves,JSON_HEX_APOS|JSON_HEX_QUOT)
                ));
            }
        );
    }

    public function add(){
        $input = $this->input->post('data');
        if($input === null){
            $this->html(
                function(){
                    $this->load->view("employee/add");
                }
            );
        }else{
            $data = parse_custom_post($input);
            $response = $this->employee_model->addEmployee($data);
            if($response!==null){
                custom_response(false,$response);
            }else{
                custom_response(true,"Successfully added employee");
            }
        }

    }

    public function delete(){
    }

    public function leaveApplication($employeeNo=null){
        $input = $this->input->post('data');
        if($input === null){
            $employee = null;
            if($employeeNo!==null){
                $employee = $this->employee_model->getEmployee($employeeNo);
            }
            $employees = $this->employee_model->getEmployees();

            $this->html(
                function() use ($employees,$employee){
                    $this->load->view('employee/leave_application',array(
                        "employees"=>json_encode($employees,JSON_HEX_APOS|JSON_HEX_QUOT),
                        "employee"=>json_encode($employee,JSON_HEX_APOS|JSON_HEX_QUOT)
                    ));
                }
            );
        }else{
            $data = parse_custom_post($input);
            /*$requiredFields = array("emp_no","start_date","end_date","type");
            $insertData = array();
            foreach($requiredFields as $field){
                if(isset($data[$field])){
                    $insertData[$field] = $data[$field];
                }else{
                    log_message('debug',"DEBUGGG ".$field);
                    custom_response(false,"Please fill-in the required field ".$field);
                }
            }
            if($insertData["type"] == "Others"){
                if(!isset($data['type_others'])){
                    custom_response(false,"Please fill-in the required field Specify");
                }
                $invalidSpecialTypes = array('Maternity','Maternity','Vacation','Sick');
                if(in_array($data["type_others"],$invalidSpecialTypes)){
                    custom_response(false,"Invalid leave type.");
                }
                $insertData["type"] = $data['type_others'];
            }

            $response = $this->employee_model->addLeave($insertData);*/

            if(isset($data["type"]) && $data["type"] == "Others"){
                if(!isset($data['type_others'])){
                    custom_response(false,"Please specify the leave type.");
                }
                $invalidSpecialTypes = array('Maternity','Maternity','Vacation','Sick');
                if(in_array($data["type_others"],$invalidSpecialTypes)){
                    custom_response(false,"Invalid leave type.");
                }
                $data["type"] = $data['type_others'];
            }
            $response = $this->employee_model->addLeave($data);
            if($response !== null){
                custom_response(false, $response);
            }else{
                custom_response(true,"Successfully added leave.");
            }
        }

    }

}
