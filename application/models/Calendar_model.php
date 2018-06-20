<?php

class Calendar_Model extends CI_Model
{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		$this->createRecords();
	}
	
	public function createRecords(){
		$query = "CREATE TABLE IF NOT EXISTS `dict_leaves_calendar_events` (
			 `ID` Integer PRIMARY KEY AUTO_INCREMENT,
			 `title` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
			 `start` datetime NOT NULL,
			 `end` datetime NOT NULL,
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
    }

}

?>