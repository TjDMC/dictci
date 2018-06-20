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

        $this->html(
            function() use ($employee){
                $this->load->view("employee/display",array(
                    "employee"=>json_encode($employee,JSON_HEX_APOS|JSON_HEX_QUOT)
                ));
            }
        );
    }

    public function add(){
        $this->html(
            function(){
                $this->load->view("employee/add");
            }
        );
    }

    public function delete(){
    }

    public function leaveApplication($employeeNo=null){
        $this->html(
            function() use ($employeeNo){
                $this->load->view('employee/leave_application',array(
                    "employees"=>json_encode($this->employee_model->getEmployees(),JSON_HEX_APOS|JSON_HEX_QUOT),
                    "employee_no"=>$employeeNo
                ));
            }
        );
    }

}
