<?php

class Calendar_Model extends MY_Model
{

	private $eventFields = array(
		array(
			'field_name'=>"title",
			'field_title'=>'Title',
			'required'=>true
		),
		array(
			'field_name'=>"description",
			'field_title'=>'Description',
			'required'=>false
		),
		array(
			'field_name'=>"date",
			'field_title'=>'Date',
			'required'=>true,
			'field_type'=>'datetime'
		),
		array(
			'field_name'=>'is_suspension',
			'field_title'=>'Suspension',
			'required'=>false
		),
		array(
			'field_name'=>'is_recurring',
			'field_title'=>'Recurring',
			'required'=>false
		)
	);

	private $resolutionFields = array(
		array(
			'field_name'=>"leave_id",
			'field_title'=>'Leave ID',
			'required'=>true
		)
	);

	public function createTable(){
		$this->dbforge->add_field("event_id int unsigned not null primary key auto_increment");
		$this->dbforge->add_field("title varchar(50)");
		$this->dbforge->add_field("date date not null");
		$this->dbforge->add_field("description varchar(300) not null");
		$this->dbforge->add_field("is_suspension boolean not null default false");
		$this->dbforge->add_field("is_recurring boolean not null default false");
		$this->dbforge->create_table(DB_PREFIX.'calendar_events',true);

		$this->dbforge->add_field('range_id int unsigned not null');
		$this->dbforge->add_field('event_id int unsigned not null');
		$this->dbforge->add_field('is_resolved boolean not null default false'); //Used for suspension only
		$this->dbforge->add_field("foreign key (range_id) references ".DB_PREFIX."leave_date_range(range_id) on update cascade on delete cascade");
		$this->dbforge->add_field("foreign key (event_id) references ".DB_PREFIX."calendar_events(event_id) on update cascade on delete cascade");
		$this->dbforge->create_table(DB_PREFIX.'calendar_collisions',true);
	}

	public function checkForDateCollisions($eventID){
		$this->db->select('*');
		$this->db->from(DB_PREFIX.'calendar_events');
		$this->db->where('event_id',$eventID);
		$this->db->join(DB_PREFIX.'leave_date_range',DB_PREFIX.'leave_date_range.start_date <= '.DB_PREFIX.'calendar_events.date and '.DB_PREFIX.'leave_date_range.end_date >= '.DB_PREFIX.'calendar_events.date');
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

	public function getEvents($suspension=null){
		if(null!==$suspension)
			return $this->db->order_by("date","asc")->where('is_suspension',$suspension)->get(DB_PREFIX.'calendar_events')->result_array();
		return $this->db->get(DB_PREFIX.'calendar_events')->result_array();
	}

	public function addEvent($eventData){
		//get next id
		$nextID = $this->db->query("SHOW TABLE STATUS LIKE '".DB_PREFIX."calendar_events'")->result_array()[0]["Auto_increment"];

		$checker = $this->checkFields($this->eventFields,$eventData);
		if(!is_array($checker)){
			return null;
		}
		$this->db->insert(DB_PREFIX.'calendar_events',$checker);
		$this->checkForDateCollisions($nextID);
		return $nextID;
	}

	public function editEvent($eventData){
		$eventFields = $this->eventFields;
		array_push($eventFields,array(
			'field_name'=>"event_id",
			'field_title'=>'ID',
			'required'=>true
		));

		$checker = $this->checkFields($eventFields,$eventData);
		if(!is_array($checker)){
			return $checker;
		}

		$this->db->where('event_id',$checker['event_id'])->update(DB_PREFIX.'calendar_events',$checker);
		$this->checkForDateCollisions($checker['event_id']);
	}

	public function deleteEvent($eventID){
		if(is_array($eventID))
			return 'Invalid ID';
		$this->db->where('event_id',$eventID)->delete(DB_PREFIX.'calendar_events');
	}

	public function getCollisions(){
		$this->load->model('employee_leaves_model');
		$this->db->select('*');
		$this->db->from(DB_PREFIX.'calendar_collisions');
		$this->db->join(DB_PREFIX.'calendar_events',DB_PREFIX.'calendar_collisions.event_id = '.DB_PREFIX.'calendar_events.event_id');
		$this->db->where('is_suspension',true);
		$this->db->where('is_resolved',false);
		$events = $this->db->get()->result_array();
		$output = array();
		if(count($events)<1){
			return $output;
		}

		//get distinct leave ids
		$this->db->select('distinct l.leave_id',false);
		$this->db->from(DB_PREFIX.'leaves as l');
		$this->db->join(DB_PREFIX.'leave_date_range as ldr','l.leave_id = ldr.leave_id');
		foreach($events as $event){
			$this->db->or_where('ldr.range_id',$event['range_id']);
		}
		$leaveIDsRaw = $this->db->get()->result_array();
		$leaveIDs = array();
		foreach($leaveIDsRaw as $leaveIDRaw){
			array_push($leaveIDs,$leaveIDRaw['leave_id']);
		}
		//get associated employees
		$output['leaves']=array();
		foreach($leaveIDs as $leaveID){
			$this->db->select(DB_PREFIX.'employee.*');
			$this->db->from(DB_PREFIX.'leaves');
			$this->db->join(DB_PREFIX.'employee',DB_PREFIX.'leaves.emp_no = '.DB_PREFIX.'employee.emp_no');
			$this->db->where('leave_id',$leaveID);
			$employeeInfo = $this->db->get()->result_array()[0];
			$leave = $this->employee_leaves_model->getLeave($leaveID);
			array_push($output['leaves'],array(
				'leave'=>$leave,
				'employee'=>$employeeInfo
			));
		}

		$output['events']=$events;
		return $output;
	}

	public function resolveCollision($data){
		$checker = $this->checkFields($this->resolutionFields,$data);
		if(!is_array($checker)){
			return $checker;
		}

		//get all range_id
		$this->db->select('range_id');
		$this->db->where('leave_id',$data['leave_id']);
		$dateRanges = $this->db->get(DB_PREFIX.'leave_date_range')->result_array();
		if(count($dateRanges)<1){
			return;
		}

		//set as resolved
		$this->db->set('is_resolved',true);
		foreach($dateRanges as $dateRange){
			$this->db->or_where('range_id',$dateRange['range_id']);
		}
		$this->db->update(DB_PREFIX.'calendar_collisions');

	}

}

?>
