<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Model extends MY_Model{

    public function __construct(){
        parent::__construct();
    }

    public function createTable(){
        if(!$this->db->table_exists(DB_PREFIX."employee")){
            $this->dbforge->add_field("id char(7) not null unique");
            $this->dbforge->add_field("last_name varchar(20) not null");
            $this->dbforge->add_field("first_name varchar(20) not null");
            $this->dbforge->add_field("middle_name varchar(20) not null");
            $this->dbforge->add_field("position varchar(50) not null");
            $this->dbforge->add_field("salary decimal(8,2) not null");
            $this->dbforge->add_field("vac_leave_bal decimal(6,2) not null default 0");
            $this->dbforge->add_field("sick_leave_bal decimal(6,2) not null default 0");
            $this->dbforge->add_field("primary key (id)");
            $this->dbforge->create_table(DB_PREFIX."employee");
        }

        if(!$this->db->table_exists(DB_PREFIX."leaves")){
            $this->dbforge->add_field("id int unsigned not null auto_increment unique");
            $this->dbforge->add_field("type varchar(20) not null");
            $this->dbforge->add_field("emp_id char(7) not null");
            $this->dbforge->add_field("from_date datetime not null");
            $this->dbforge->add_field("to_date datetime not null");
            $this->dbforge->add_field("primary key (id)");
            $this->dbforge->create_table(DB_PREFIX."leaves");
        }
    }
}
