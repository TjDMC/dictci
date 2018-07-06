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
            "field_name"=>"info",
            "field_title"=>"Leave Info",
            "required"=>true
        ),
        array(
            "field_name"=>"date_ranges",
            "field_title"=>"Employee Number",
            "required"=>true
        )
    );

    private $leaveInfoFields = array(
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
            "field_name"=>"remarks",
            "field_title"=>"Remarks",
            "required"=>false
        ),
        array(
            "field_name"=>"is_without_pay",
            "field_title"=>"Without Pay?",
            "required"=>false
        )
    );

	private $leaveDateRangeFields = array(
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
			"field_name"=>"hours",
            "field_title"=>"Hours",
            "required"=>true
		),
		array(
			"field_name"=>"minutes",
            "field_title"=>"Minutes",
            "required"=>true
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
            $this->dbforge->add_field("vac_leave_bal decimal(6,3) not null default 0");
            $this->dbforge->add_field("sick_leave_bal decimal(6,3) not null default 0");
            $this->dbforge->add_field("primary key (emp_no)");
            $this->dbforge->create_table(DB_PREFIX."employee",true);
        }

        if(!$this->db->table_exists(DB_PREFIX."leaves")){
            $this->dbforge->add_field("flag int not null default 0");
            $this->dbforge->add_field("leave_id int unsigned not null auto_increment unique");
            $this->dbforge->add_field("type varchar(50) not null");
            $this->dbforge->add_field("is_without_pay boolean not null default false");
            $this->dbforge->add_field("date_added date not null");
            $this->dbforge->add_field("date_last_edited date not null");
            $this->dbforge->add_field("emp_no char(7) not null");
            $this->dbforge->add_field("remarks varchar(50)");
            $this->dbforge->add_field("primary key (leave_id)");
            $this->dbforge->add_field("foreign key (emp_no) references ".DB_PREFIX."employee(emp_no) on update cascade on delete cascade");
            $this->dbforge->create_table(DB_PREFIX."leaves",true);
        }

		if(!$this->db->table_exists(DB_PREFIX."leave_date_range")){
            $this->dbforge->add_field("leave_id int unsigned not null");
            $this->dbforge->add_field("start_date date not null");
            $this->dbforge->add_field("end_date date not null");
            $this->dbforge->add_field("hours int not null default 0");
            $this->dbforge->add_field("minutes int not null default 0");
            $this->dbforge->add_field("foreign key (leave_id) references ".DB_PREFIX."leaves(leave_id) on update cascade on delete cascade");
            $this->dbforge->create_table(DB_PREFIX."leave_date_range",true);
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

	public function addLeaves($leaveData){
		//Validate leave data
		$leaveChecker = $this->checkFields($this->leaveFields,$leaveData);
		if(!is_array($leaveChecker)){
			return $leaveChecker;
		}
		$leaveInfoChecker = $this->checkFields($this->leaveInfoFields,$leaveChecker['info']);
		if(!is_array($leaveInfoChecker)){
			return $leaveInfoChecker;
		}

		//Check if employee exists
		$this->db->where("emp_no",$leaveInfoChecker["emp_no"]);
        $res = $this->db->get(DB_PREFIX."employee")->result_array();
        if(count($res)<1){
            return "Employee does not exist.";
        }

		//Get next auto_increment
		$nextID = $this->db->query("SHOW TABLE STATUS LIKE '".DB_PREFIX."leaves'")->result_array()[0]["Auto_increment"];
        //INCOMPLETE
		//Validate date ranges
		$dateRangeChecker = array();
		foreach($leaveChecker['date_ranges'] as $dateRange){
			$checker = $this->checkFields($this->leaveDateRangeFields,$dateRange);
			if(!is_array($checker)){
				return $checker;
			}

            if(strpos(strtolower($leaveInfoChecker['type']),'monetization')===false && strpos(strtolower($leaveInfoChecker['type']),'undertime')===false){
                //Check for date conflicts
                $this->db->select("*");
                $this->db->from(DB_PREFIX.'leaves');
                $this->db->where("emp_no",$leaveInfoChecker['emp_no']);
                $this->db->not_like(DB_PREFIX.'leaves.type','Monetization'); //don't include monetization leaves
                $this->db->not_like(DB_PREFIX.'leaves.type','Undertime'); //don't include undertime leaves
                $this->db->where("start_date <=",$checker['end_date']);
                $this->db->where("end_date >=",$checker['start_date']);
                $this->db->join(DB_PREFIX.'leave_date_range',DB_PREFIX.'leaves.leave_id = '.DB_PREFIX.'leave_date_range.leave_id');
                $res = $this->db->get()->result_array();
                if(count($res)>0){
                    return "Conflicting dates found: ".print_r($res,true);
                }
            }

            $checker['leave_id']=$nextID;
			array_push($dateRangeChecker,$checker);
		}

		//Inserting leaves to tables
        $leaveInfoChecker['date_added']=date('Y-m-d');
        $leaveInfoChecker['date_last_edited']=date('Y-m-d');
		$this->db->insert(DB_PREFIX."leaves",$leaveInfoChecker);
		$this->db->insert_batch(DB_PREFIX."leave_date_range",$dateRangeChecker);
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
		$leaves = array();
        $this->db->where("emp_no",$employeeNo);
        $res = $this->db->get(DB_PREFIX."leaves")->result_array();

		foreach($res as $leave){
			$this->db->where("leave_id",$leave['leave_id']);
			array_push($leaves,array(
				'info'=>$leave,
				'date_ranges'=>$this->db->get(DB_PREFIX."leave_date_range")->result_array()
			));
		}
        return $leaves;
    }

    public function editLeave($leaveData){
        //Validate leave data
        $leaveChecker = $this->checkFields($this->leaveFields,$leaveData);
        if(!is_array($leaveChecker)){
            return $leaveChecker;
        }

        //Validate leave info data
        $leaveInfoFields = $this->leaveInfoFields;
        $leaveInfoFields['leave_id']=array(
            'field_name'=>'leave_id',
            'field_title'=>'Leave ID',
            'required'=>true
        );
        $leaveInfoFields['date_added']=array(
            'field_name'=>'date_added',
            'field_title'=>'Date Added',
            'required'=>true
        );
        $leaveInfoChecker = $this->checkFields($leaveInfoFields,$leaveChecker['info']);
        if(!is_array($leaveInfoChecker)){
            return $leaveInfoChecker;
        }

        //Validate date ranges data
        $dateRangeChecker = array();
        foreach($leaveChecker['date_ranges'] as $dateRange){
            //Validate date range
            $checker = $this->checkFields($this->leaveDateRangeFields,$dateRange);
            if(!is_array($checker)){
                return $checker;
            }

            if(strpos(strtolower($leaveInfoChecker['type']),'monetization')===false && strpos(strtolower($leaveInfoChecker['type']),'undertime')===false){
                //Check for conflicting dates
                $this->db->select("*");
                $this->db->from(DB_PREFIX.'leaves');
                $this->db->where("emp_no",$leaveInfoChecker['emp_no']);
                $this->db->where(DB_PREFIX."leaves.leave_id !=",intval($leaveInfoChecker['leave_id']));
                $this->db->not_like(DB_PREFIX.'leaves.type','Monetization'); //don't include monetization leaves
                $this->db->not_like(DB_PREFIX.'leaves.type','Undertime'); //don't include undertime leaves
                $this->db->where("start_date <=",$checker['end_date']);
                $this->db->where("end_date >=",$checker['start_date']);
                $this->db->join(DB_PREFIX.'leave_date_range',DB_PREFIX.'leaves.leave_id = '.DB_PREFIX.'leave_date_range.leave_id');
                $res = $this->db->get()->result_array();
                if(count($res)>0){
                    return "Conflicting dates found: ".print_r($res,true);
                }
            }

            $checker['leave_id']=intval($leaveInfoChecker['leave_id']);
            array_push($dateRangeChecker,$checker);
        }

        //Delete previous records
        $this->db->where('leave_id',$leaveInfoChecker['leave_id']);
        $this->db->delete(DB_PREFIX.'leaves');

        //Insert new records
        $leaveInfoChecker['date_last_edited']=date('Y-m-d');
        $this->db->insert(DB_PREFIX.'leaves',$leaveInfoChecker);
        $this->db->insert_batch(DB_PREFIX.'leave_date_range',$dateRangeChecker);
    }

    public function deleteLeave($leaveID){
        $this->db->where('leave_id',$leaveID);
        $this->db->delete(DB_PREFIX.'leaves');
    }
}
