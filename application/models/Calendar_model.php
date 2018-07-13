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

	public function createTable(){
		$this->dbforge->add_field("id int unsigned not null primary key auto_increment");
		$this->dbforge->add_field("title varchar(50)");
		$this->dbforge->add_field("date datetime not null");
		$this->dbforge->add_field("description varchar(300) not null");
		$this->dbforge->add_field("is_suspension boolean not null default false");
		$this->dbforge->add_field("is_recurring boolean not null default false");
		$this->dbforge->create_table(DB_PREFIX.'calendar_events',true);

		$this->dbforge->add_field('range_id int unsigned not null');
		$this->dbforge->add_field('event_id int unsigned not null');
		$this->dbforge->add_field('deducted_credits float(4,3) not null default 0');
		$this->dbforge->add_field("foreign key (range_id) references ".DB_PREFIX."leave_date_range(range_id) on update cascade on delete cascade");
		$this->dbforge->add_field("foreign key (event_id) references ".DB_PREFIX."calendar_events(id) on update cascade on delete cascade");
		$this->dbforge->create_table(DB_PREFIX.'calendar_collisions',true);
	}

	public function checkForCollisions(){

	}

	public function getEvents(){
		return $this->db->get(DB_PREFIX.'calendar_events')->result_array();
	}

	public function addEvent($eventData){
		//get next id
		$nextID = $this->db->query("SHOW TABLE STATUS LIKE '".DB_PREFIX."calendar_events'")->result_array()[0]["Auto_increment"];

		$checker = $this->checkFields($this->eventFields,$eventData);
		$this->db->insert(DB_PREFIX.'calendar_events',$checker);

		return $nextID;
	}

	public function editEvent($eventData){
		$eventFields = $this->eventFields;
		array_push($eventFields,array(
			'field_name'=>"id",
			'field_title'=>'ID',
			'required'=>true
		));

		$checker = $this->checkFields($eventFields,$eventData);

		$this->db->where('id',$checker['id'])->update(DB_PREFIX.'calendar_events',$checker);
	}

	public function deleteEvent($eventID){
		if(is_array($eventID))
			return 'Invalid ID';
		$this->db->where('id',$eventID)->delete(DB_PREFIX.'calendar_events');
	}

}

?>
