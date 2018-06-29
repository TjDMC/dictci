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
            $this->index();
            return;
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

            if(isset($data['info']["type"]) && $data['info']["type"] == "Others"){
                if(!isset($data['info']['type_others'])){
                    custom_response(false,"Please specify the leave type.");
                }
                $invalidSpecialTypes = array('Maternity','Paternity','Vacation','Sick');
                if(in_array($data['info']["type_others"],$invalidSpecialTypes)){
                    custom_response(false,"Invalid leave type.");
                }
                $data['info']["type"] = $data['info']['type_others'];
            }
            if(isset($data['action'])&&$data['action']=='edit'){
                $response = $this->employee_model->editLeave($data);
            }else{
                $response = $this->employee_model->addLeaves($data);
            }

            if($response !== null){
                custom_response(false, $response);
            }else{
                custom_response(true,"Successfully ".(isset($data['action'])&&$data['action']=='edit' ? 'edited':'added' )." leave.");
            }
        }

    }

    public function deleteLeave(){
        $data = parse_custom_post($this->input->post('data'));
        $this->employee_model->deleteLeave($data);
        custom_response(true,'Leave record deleted');
    }

}
