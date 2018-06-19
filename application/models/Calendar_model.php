<?php

class Calendar_Model extends CI_Model
{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		if(!$this->db->table_exists('dict_leaves_calendar_events')){
			$this->createRecords();
		}
	}
	
	public function createRecords(){
		$query = "CREATE TABLE `".DB_PREFIX."calendar_events` (
 `ID` int(11) NOT NULL,
 `title` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
 `start` datetime NOT NULL,
 `end` datetime NOT NULL,
 `description` varchar(1000) COLLATE utf8_unicode_ci NOT NULL
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$this->db->query($query);
	}
	
	public function get_events($start, $end)
	{
		return $this->db->where("start >=", $start)->where("end <=", $end)->get("".DB_PREFIX."calendar_events");
	}

	public function add_event($data)
	{
		$this->db->insert("".DB_PREFIX."calendar_events", $data);
	}

	public function get_event($id)
	{
		return $this->db->where("ID", $id)->get("".DB_PREFIX."calendar_events");
	}

	public function update_event($id, $data)
	{
		$this->db->where("ID", $id)->update("".DB_PREFIX."calendar_events", $data);
	}

	public function delete_event($id)
	{
		$this->db->where("ID", $id)->delete("".DB_PREFIX."calendar_events");
	}

}

?>