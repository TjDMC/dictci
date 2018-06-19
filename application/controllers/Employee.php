<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends MY_Controller{

    public $sampleEmployees = array(
        array(
            "ID"=>1,
            "Name"=>"Peter"
        ),
        array(
            "ID"=>2,
            "Name"=>"Paul"
        ),
        array(
            "ID"=>3,
            "Name"=>"Israel"
        ),
    );

    public function body(){
        $this->load->view("employee/list",array(
            "employees"=>json_encode($this->sampleEmployees,JSON_HEX_QUOT|JSON_HEX_APOS)
        ));
    }

    public function display($employeeID){
        $class = $this;
        $this->html(
            function() use ($employeeID){
                $this->load->view("employee/display",array("employeeID"=>$employeeID));
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

    public function delete($employeeID){
    }

}
