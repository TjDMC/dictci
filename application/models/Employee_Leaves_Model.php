<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee_Leaves_Model extends MY_Model{

    private $employeeTableMetaFields = array(
        array(
            "field_name"=>"table_name",
            "field_title"=>"Employee Table Name",
            "required"=>true
        ),
        array(
            "field_name"=>"emp_no",
            "field_title"=>"Employee Number Column Name",
            "required"=>true
        ),
        array(
            "field_name"=>"surname",
            "field_title"=>"Surname Column Name",
            "required"=>true
        ),
        array(
            "field_name"=>"first_name",
            "field_title"=>"First Name Column Name",
            "required"=>true
        ),
        array(
            "field_name"=>"middle_name",
            "field_title"=>"Middle Name Column Name",
            "required"=>true
        ),
        array(
            "field_name"=>"first_day_employ",
            "field_title"=>"Employement Date Column Name",
            "required"=>true
        ),
        array(
            "field_name"=>"is_external",
            "field_title"=>"Is External",
            "required"=>true
        )
    );

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
            "required"=>false
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
			"field_name"=>"credits",
            "field_title"=>"Credits",
            "required"=>true
		)
	);

    public function __construct(){
        parent::__construct();
        $this->createEmployeeTableMetaTable();
    }

    private function createEmployeeTableMetaTable(){
        if(!$this->db->table_exists(DB_PREFIX."employee_table_meta")){
            $this->dbforge->add_field("table_name varchar(100) not null");
            $this->dbforge->add_field("emp_no varchar(100) not null");
            $this->dbforge->add_field("surname varchar(100) not null");
            $this->dbforge->add_field("first_name varchar(100) not null");
            $this->dbforge->add_field("middle_name varchar(100) not null");
            $this->dbforge->add_field("first_day_employ varchar(100) not null");
            $this->dbforge->add_field("is_external boolean not null");
            $this->dbforge->create_table(DB_PREFIX."employee_table_meta",true);
        }
    }

    public function createTable(){
        $m = $this->getEmployeeTableMeta();
        if(!$this->db->table_exists(DB_PREFIX."employee")){
            $this->dbforge->add_field("flag int not null default 0");
            $this->dbforge->add_field("emp_no char(7) not null unique");
            $this->dbforge->add_field("last_name varchar(20) not null");
            $this->dbforge->add_field("first_name varchar(20) not null");
            $this->dbforge->add_field("middle_name varchar(20)");
            $this->dbforge->add_field("first_day_employ date not null");
            $this->dbforge->add_field("position varchar(50)");
            $this->dbforge->add_field("salary decimal(8,2)");
            $this->dbforge->add_field("vac_leave_bal decimal(6,3) not null default 0");
            $this->dbforge->add_field("sick_leave_bal decimal(6,3) not null default 0");
            $this->dbforge->add_field("primary key (emp_no)");
            $this->dbforge->create_table(DB_PREFIX."employee",true);
        }
        if(!$this->db->table_exists(DB_PREFIX.'employee_leaves')){
            $this->dbforge->add_field('emp_no tinytext not null');
            $this->dbforge->add_field("vac_leave_bal decimal(6,3) not null default 0");
            $this->dbforge->add_field("sick_leave_bal decimal(6,3) not null default 0");
            $this->dbforge->add_field("first_day_compute date not null");
            $this->dbforge->add_field("highest_salary decimal(6,3) not null default 0");
            $this->dbforge->add_field("foreign key (emp_no) references ".$m['table_name']."(".$m['emp_no'].") on update cascade on delete cascade");
        }

        if(!$this->db->table_exists(DB_PREFIX."leaves")){
            $this->dbforge->add_field("flag int not null default 0");
            $this->dbforge->add_field("leave_id int unsigned not null auto_increment unique");
            $this->dbforge->add_field("type varchar(50) not null");
            $this->dbforge->add_field("is_without_pay boolean not null default false");
            $this->dbforge->add_field("date_added date not null");
            $this->dbforge->add_field("date_last_edited date not null");
            $this->dbforge->add_field("emp_no tinytext not null");
            $this->dbforge->add_field("remarks varchar(50)");
            $this->dbforge->add_field("primary key (leave_id)");
            $this->dbforge->add_field("foreign key (emp_no) references ".DB_PREFIX."employee(emp_no) on update cascade on delete cascade");
            $this->dbforge->create_table(DB_PREFIX."leaves",true);
        }

		if(!$this->db->table_exists(DB_PREFIX."leave_date_range")){
            $this->dbforge->add_field("leave_id int unsigned not null");
            $this->dbforge->add_field("range_id int unsigned not null auto_increment unique"); //only used for calendar events
            $this->dbforge->add_field("start_date date not null");
            $this->dbforge->add_field("end_date date not null");
            $this->dbforge->add_field("credits float(7,3) not null default 0");
            $this->dbforge->add_field("primary key (range_id)");
            $this->dbforge->add_field("foreign key (leave_id) references ".DB_PREFIX."leaves(leave_id) on update cascade on delete cascade");
            $this->dbforge->create_table(DB_PREFIX."leave_date_range",true);
        }
    }

    public function setEmployeeTableMeta($data=null){
        if($data==null){
            $data = array(
                'table_name'=>DB_PREFIX.'employee',
                'first_name'=>'first_name',
                'surname'=>'last_name',
                'middle_name'=>'middle_name',
                'first_day_employ'=>'first_day_employ',
                'emp_no'=>'emp_no',
                'is_external'=>false
            );
        }
        $checker = $this->checkFields($this->employeeTableMetaFields,$data);
        if(!is_array($checker)){
            return $checker;
        }
        if(count($this->db->get(DB_PREFIX.'employee_table_meta')->result_array())>0){
            $this->db->update(DB_PREFIX.'employee_table_meta',$checker);
        }else{
            $this->db->insert(DB_PREFIX.'employee_table_meta',$checker);
        }
        if(!$checker['is_external']){ //create default table
            if(!$this->db->table_exists($checker['table_name'])){
                $this->dbforge->add_field($checker['emp_no']." tinytext not null unique");
                $this->dbforge->add_field($checker['surname']." tinytext not null");
                $this->dbforge->add_field($checker['first_name']."tinytext not null");
                $this->dbforge->add_field($checker['middle_name']."tinytext");
                $this->dbforge->add_field($checker['first_day_employ']." date not null");
                $this->dbforge->add_field("primary key (".$checker['emp_no'].")");
                $this->dbforge->create_table($checker['table_name'],true);
            }
        }
    }

    public function getEmployeeTableMeta(){
        return $this->db->get(DB_PREFIX.'employee_table_meta')->result_array()[0];
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
        //get leave id
        $id = $this->db->insert_id();
		$this->db->insert_batch(DB_PREFIX."leave_date_range",$dateRangeChecker);

        //Check for holiday collisions
        $this->db->where('leave_id',$id);
        $res = $this->db->get(DB_PREFIX.'leave_date_range')->result_array();
        foreach($res as $dateRange){
            $this->checkForEventCollisions($dateRange['range_id']);
        }

        return $this->getLeave($id);
	}

    public function checkForEventCollisions($dateRangeID){
		$this->db->select('range_id,event_id');
		$this->db->from(DB_PREFIX.'leave_date_range');
		$this->db->where('range_id',$dateRangeID);
        $this->db->where(DB_PREFIX.'leaves.type !=','Monetization'); //exclude monetization
        $this->db->where(DB_PREFIX.'leaves.type !=','Special Monetization');
        $this->db->join(DB_PREFIX.'leaves',DB_PREFIX.'leaves.leave_id = '.DB_PREFIX.'leave_date_range.leave_id');
		$this->db->join(DB_PREFIX.'calendar_events',DB_PREFIX.'calendar_events.date >= '.DB_PREFIX.'leave_date_range.start_date and '.DB_PREFIX.'calendar_events.date <= '.DB_PREFIX.'leave_date_range.end_date');
		$res = $this->db->get()->result_array();

        $insertData = array();
		foreach($res as $r){
			$this->db->where('range_id',$r['range_id']);
			$this->db->where('event_id',$r['event_id']);
			$this->db->delete(DB_PREFIX.'calendar_collisions');
			array_push($insertData,array(
				'range_id'=>$r['range_id'],
				'event_id'=>$r['event_id']
			));
		}
        if(count($insertData)>0)
			$this->db->insert_batch(DB_PREFIX.'calendar_collisions',$insertData);
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
            /* Counting holiday conflicts
                *note:  holidays happening in the same date count as 1 holiday conflict.
            */

            /*Method 1. Single messy query*/
            $subquery = 'select count(distinct '.DB_PREFIX.'calendar_events.date) from '.DB_PREFIX.'calendar_collisions
                join '.DB_PREFIX.'calendar_events on '.DB_PREFIX.'calendar_events.event_id = '.DB_PREFIX.'calendar_collisions.event_id
                where '.DB_PREFIX.'calendar_collisions.range_id='.DB_PREFIX.'leave_date_range.range_id and
                '.DB_PREFIX.'calendar_events.is_suspension = false';
            $this->db->select(DB_PREFIX.'leave_date_range.*,('.$subquery.') as holiday_conflicts');
            $this->db->from(DB_PREFIX.'leave_date_range');
			$this->db->where("leave_id",$leave['leave_id']);
			array_push($leaves,array(
				'info'=>$leave,
				'date_ranges'=>$this->db->order_by("start_date","desc")->get()->result_array()
			));

            /*Method 2. Multiple queries
            $this->db->where('leave_id',$leave['leave_id']);
            $dateRanges = $this->db->get(DB_PREFIX.'leave_date_range')->result_array();

            $outDateRanges = array();
            foreach($dateRanges as $dateRange){  //Make sure holidays have unique dates
                $this->db->select(DB_PREFIX.'calendar_collisions.*,'.DB_PREFIX.'calendar_events.date');
                $this->db->from(DB_PREFIX.'calendar_collisions');
                $this->db->join(DB_PREFIX.'calendar_events',DB_PREFIX.'calendar_events.event_id = '.DB_PREFIX.'calendar_collisions.event_id');
                $this->db->where('range_id',$dateRange['range_id']);
                $this->db->where('is_suspension',false);
                $events = $this->db->get()->result_array();
                $holidays = 0;
                $prevDates = array();
                foreach($events as $event){ //each event's date is added to an array. Holiday conflicts is incremented if the array doesn't contain the current event's date
                    if(array_search($event['date'],$prevDates)===false){
                        array_push($prevDates,$event['date']);
                        $holidays++;
                    }
                }
                $dateRange['holiday_conflicts'] = $holidays;
                array_push($outDateRanges,$dateRange);
            }

            array_push($leaves,array(
                'info'=>$leave,
                'date_ranges'=>$outDateRanges
            ));*/
		}
        return $leaves;
    }

    public function getLeave($leaveID){
        $this->db->where('leave_id',$leaveID);
        $res = $this->db->get(DB_PREFIX.'leaves')->result_array();
        if(count($res)<1){
            return 'No such leave';
        }

        $subquery = 'select count(distinct '.DB_PREFIX.'calendar_events.date) from '.DB_PREFIX.'calendar_collisions
            join '.DB_PREFIX.'calendar_events on '.DB_PREFIX.'calendar_events.event_id = '.DB_PREFIX.'calendar_collisions.event_id
            where '.DB_PREFIX.'calendar_collisions.range_id='.DB_PREFIX.'leave_date_range.range_id and
            '.DB_PREFIX.'calendar_events.is_suspension = false';
        $this->db->select(DB_PREFIX.'leave_date_range.*,('.$subquery.') as holiday_conflicts');
        $this->db->from(DB_PREFIX.'leave_date_range');
        $this->db->where("leave_id",$leaveID);
        //log_message('debug',print_r($this->db->get_compiled_select(),true));
        $leave = array(
            'info'=>$res[0],
            'date_ranges'=>$this->db->order_by("start_date","desc")->get()->result_array()
        );
        /*$this->db->where('leave_id',$res[0]['leave_id']);
        $dateRanges = $this->db->get(DB_PREFIX.'leave_date_range')->result_array();

        $outDateRanges = array();
        foreach($dateRanges as $dateRange){ //Make sure holiday conflicts have unique dates
            $this->db->select(DB_PREFIX.'calendar_collisions.*,'.DB_PREFIX.'calendar_events.date');
            $this->db->from(DB_PREFIX.'calendar_collisions');
            $this->db->join(DB_PREFIX.'calendar_events',DB_PREFIX.'calendar_events.event_id = '.DB_PREFIX.'calendar_collisions.event_id');
            $this->db->where('range_id',$dateRange['range_id']);
            $this->db->where('is_suspension',false);
            $events = $this->db->get()->result_array();
            $holidays = 0;
            $prevDates = array();
            foreach($events as $event){
                if(array_search($event['date'],$prevDates)===false){
                    array_push($prevDates,$event['date']);
                    $holidays++;
                }
            }
            $dateRange['holiday_conflicts'] = $holidays;
            array_push($outDateRanges,$dateRange);
        }

        $leave = array(
            'info'=>$res[0],
            'date_ranges'=>$outDateRanges
        );*/

        return $leave;
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
        //get leave id
        $id = $this->db->insert_id();
        $this->db->insert_batch(DB_PREFIX.'leave_date_range',$dateRangeChecker);

        //Check for holiday collisions
        $this->db->where('leave_id',$id);
        $res = $this->db->get(DB_PREFIX.'leave_date_range')->result_array();
        foreach($res as $dateRange){
            $this->checkForEventCollisions($dateRange['range_id']);
        }

        return $this->getLeave($id);

    }

    public function deleteLeave($leaveID){
        $this->db->where('leave_id',$leaveID);
        $this->db->delete(DB_PREFIX.'leaves');
    }
}
