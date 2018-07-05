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
		)
	);

	public function createTable(){
		$this->dbforge->add_field("id int primary key auto_increment");
		$this->dbforge->add_field("title varchar(50)");
		$this->dbforge->add_field("date datetime not null");
		$this->dbforge->add_field("description varchar(300) not null");
		$this->dbforge->add_field("is_suspension boolean not null default false");
		$this->dbforge->create_table(DB_PREFIX.'calendar_events');
	}

	public function getEvents(){
		return $this->db->get(DB_PREFIX.'calendar_events')->result_array();
	}

	public function addEvent($eventData){
		$checker = $this->checkFields($this->eventFields,$eventData);
		$this->db->insert(DB_PREFIX.'calendar_events',$checker);
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

	/*public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		$this->createRecords();
	}

	public function createRecords(){
		$query = "CREATE TABLE IF NOT EXISTS `dict_leaves_calendar_events` (
			 `ID` Integer PRIMARY KEY AUTO_INCREMENT,
			 `title` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
			 `start` date NOT NULL,
			 `end` date NOT NULL,
			 `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL
			 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$this->db->query($query);
	}

	public function get_events($start, $end)
    {
        return $this->db
            ->where("start >=", $start)
            ->where("end <=", $end)
            ->get("dict_leaves_calendar_events");
    }

	public function get_all_events(){
		return $this->db->get("dict_leaves_calendar_events")->result_array();
	}

    public function add_event($data)
    {
        $this->db->insert("dict_leaves_calendar_events", $data);
    }

    public function get_event($id)
    {
        return $this->db->where("ID", $id)->get("dict_leaves_calendar_events");
    }

    public function update_event($id, $data)
    {
        $this->db->where("ID", $id)->update("dict_leaves_calendar_events", $data);
    }

    public function delete_event($id)
    {
        $this->db->where("ID", $id)->delete("dict_leaves_calendar_events");
    }*/

}

?>
