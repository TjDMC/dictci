<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Model extends MY_Model{

    public function createTable(){
        if(!$this->db->table_exists(DB_PREFIX."employee")){
            $this->dbforge->add_field("emp_no char(7) not null unique");
            $this->dbforge->add_field("last_name varchar(20) not null");
            $this->dbforge->add_field("first_name varchar(20) not null");
            $this->dbforge->add_field("middle_name varchar(20) not null");
            $this->dbforge->add_field("position varchar(50) not null");
            $this->dbforge->add_field("salary decimal(8,2) not null");
            $this->dbforge->add_field("vac_leave_bal decimal(6,2) not null default 0");
            $this->dbforge->add_field("sick_leave_bal decimal(6,2) not null default 0");
            $this->dbforge->add_field("primary key (emp_no)");
            $this->dbforge->create_table(DB_PREFIX."employee");
        }

        if(!$this->db->table_exists(DB_PREFIX."leaves")){
            $this->dbforge->add_field("id int unsigned not null auto_increment unique");
            $this->dbforge->add_field("type varchar(20) not null");
            $this->dbforge->add_field("emp_no char(7) not null");
            $this->dbforge->add_field("from_date datetime not null");
            $this->dbforge->add_field("to_date datetime not null");
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
                    "position"=>"idk",
                    "salary"=>123
                ),
                array(
                    "emp_no"=>"0000002",
                    "last_name"=>"Ramos",
                    "first_name"=>"Peter",
                    "middle_name"=>"Peter",
                    "position"=>"idk",
                    "salary"=>1234
                ),
                array(
                    "emp_no"=>"0000003",
                    "last_name"=>"Aquino",
                    "first_name"=>"Israel",
                    "middle_name"=>"Israel",
                    "position"=>"idk",
                    "salary"=>1234
                )
            )
        );
    }

    public function addEmployee($employee){
        $this->db->where("emp_no",$employee["emp_no"]);
        $res = $this->db->get(DB_PREFIX."employee")->result_array();

        if(count($res)>0){
            return "Employee Number already exists";
        }

        $this->db->insert(DB_PREFIX."employee",$employee);
        return null;
    }

    public function addLeave($employeeNo,$leaveData){
        
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
        $res = $this->db->get(DB_PREFIX."employee")->result_array();
        return $res;
    }
}
