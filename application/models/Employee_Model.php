<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Model extends MY_Model{

    private $employeeFields = array(
        array(
            "field_name"=>"emp_no",
            "field_title"=>"Employee Number",
            "required"=>true
        ),
        array(
            "field_name"=>"last_name",
            "field_title"=>"Last Name",
            "required"=>true
        ),
        array(
            "field_name"=>"first_name",
            "field_title"=>"First Name",
            "required"=>true
        ),
        array(
            "field_name"=>"middle_name",
            "field_title"=>"Middle Name",
            "required"=>true
        ),
        array(
            "field_name"=>"first_day",
            "field_title"=>"First Day",
            "required"=>true
        ),
        array(
            "field_name"=>"position",
            "field_title"=>"Position",
            "required"=>false
        ),
        array(
            "field_name"=>"salary",
            "field_title"=>"Salary",
            "required"=>false
        ),
        array(
            "field_name"=>"vac_leave_bal",
            "field_title"=>"Vacation Leave Balance",
            "required"=>false
        ),
        array(
            "field_name"=>"sick_leave_bal",
            "field_title"=>"Sick Leave Balance",
            "required"=>false
        )
    );

    private $leaveFields = array(
        array(
            "field_name"=>"id",
            "field_title"=>"Leave ID",
            "required"=>false
        ),
        array(
            "field_name"=>"type",
            "field_title"=>"Leave Type",
            "required"=>true
        ),
        array(
            "field_name"=>"emp_no",
            "field_title"=>"Employee Number",
            "required"=>true
        ),
        array(
            "field_name"=>"start_date",
            "field_title"=>"Start Date",
            "required"=>true
        ),
        array(
            "field_name"=>"end_date",
            "field_title"=>"End Date",
            "required"=>true
        ),
        array(
            "field_name"=>"remarks",
            "field_title"=>"Remarks",
            "required"=>false
        )
    );

    public function createTable(){
        if(!$this->db->table_exists(DB_PREFIX."employee")){
            $this->dbforge->add_field("flag int not null default 0");
            $this->dbforge->add_field("emp_no char(7) not null unique");
            $this->dbforge->add_field("last_name varchar(20) not null");
            $this->dbforge->add_field("first_name varchar(20) not null");
            $this->dbforge->add_field("middle_name varchar(20) not null");
            $this->dbforge->add_field("first_day date not null");
            $this->dbforge->add_field("position varchar(50)");
            $this->dbforge->add_field("salary decimal(8,2)");
            $this->dbforge->add_field("vac_leave_bal decimal(6,2) not null default 0");
            $this->dbforge->add_field("sick_leave_bal decimal(6,2) not null default 0");
            $this->dbforge->add_field("primary key (emp_no)");
            $this->dbforge->create_table(DB_PREFIX."employee");
        }

        if(!$this->db->table_exists(DB_PREFIX."leaves")){
            $this->dbforge->add_field("flag int not null default 0");
            $this->dbforge->add_field("id int unsigned not null auto_increment unique");
            $this->dbforge->add_field("type varchar(20) not null");
            $this->dbforge->add_field("emp_no char(7) not null");
            $this->dbforge->add_field("start_date date not null");
            $this->dbforge->add_field("end_date date not null");
            $this->dbforge->add_field("remarks varchar(50)");
            $this->dbforge->add_field("primary key (id)");
            $this->dbforge->add_field("foreign key (emp_no) references ".DB_PREFIX."employee(emp_no) on update cascade on delete cascade");
            $this->dbforge->create_table(DB_PREFIX."leaves");
        }
    }

    public function populate(){
        $this->db->insert_batch(DB_PREFIX."employee",
            array(
                array(
                    "emp_no"=>"0000001",
                    "last_name"=>"Naraval",
                    "first_name"=>"Paul",
                    "middle_name"=>"Paul",
                    "first_day"=>"2018/06/19",
                    "position"=>"idk",
                    "salary"=>123
                ),
                array(
                    "emp_no"=>"0000002",
                    "last_name"=>"Ramos",
                    "first_name"=>"Peter",
                    "middle_name"=>"Peter",
                    "first_day"=>"2018/04/19",
                    "position"=>"idk",
                    "salary"=>1234
                ),
                array(
                    "emp_no"=>"0000003",
                    "last_name"=>"Aquino",
                    "first_name"=>"Israel",
                    "middle_name"=>"Israel",
                    "first_day"=>"2018/05/19",
                    "position"=>"idk",
                    "salary"=>1234
                )
            )
        );
    }

    public function addEmployee($employeeData){
        $checker = $this->checkFields($this->employeeFields,$employeeData);
        if(!is_array($checker)){
            return $checker;
        }

        $this->db->where("emp_no",$employeeData["emp_no"]);
        $res = $this->db->get(DB_PREFIX."employee")->result_array();

        if(count($res)>0){
            return "Employee Number already exists";
        }

        $this->db->insert(DB_PREFIX."employee",$employeeData);
        return null;
    }

    public function addLeave($leaveData){
        $checker = $this->checkFields($this->leaveFields,$leaveData);
        if(!is_array($checker)){
            return $checker;
        }

        $this->db->where("emp_no",$leaveData["emp_no"]);
        $res = $this->db->get(DB_PREFIX."employee")->result_array();

        if(count($res)<1){
            return "Employee does not exist.";
        }

        $this->db->insert(DB_PREFIX."leaves",$leaveData);

        //TODO check if leave overlaps with other leaves

        return null;
    }

    protected function checkFields($checker,$checkee){
        $result = array();
        foreach($checker as $field){
            if($field['required']){
                if(!isset($checkee[$field['field_name']])){
                    return "Please fill-in the required field ".$field['field_title'];
                }else{
                    $result[$field['field_name']]=$checkee[$field['field_name']];
                }
            }else{
                if(isset($checkee[$field['field_name']])){
                    $result[$field['field_name']]=$checkee[$field['field_name']];
                }
            }
        }
        return $result;
    }

    public function getEmployee($employeeNo){
        $this->db->where("emp_no",$employeeNo);
        $res = $this->db->get(DB_PREFIX."employee")->result_array();

        if(count($res)!=1){
            return null;
        }

        return $res[0];
    }

    public function getEmployees(){
        $res = $this->db->get(DB_PREFIX."employee")->result_array();
        return $res;
    }

    public function getLeaves($employeeNo){
        $this->db->where("emp_no",$employeeNo);
        $res = $this->db->get(DB_PREFIX."leaves")->result_array();
        return $res;
    }
}
