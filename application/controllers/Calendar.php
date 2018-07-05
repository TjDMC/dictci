<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Calendar extends MY_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->model("calendar_model");
		//$this->load->helper('form');
    }

    public function body()
    {
        //$this->load->view("calendar/index.php", array());
        $events = $this->calendar_model->getEvents();
        $this->load->view("calendar/base.php",array(
            'events'=>json_encode($events,JSON_HEX_APOS|JSON_HEX_QUOT|JSON_NUMERIC_CHECK)
        ));
    }

    public function actionEvents($action=null){
        $input = $this->input->post('data');
        if($action==null || $input==null)
            redirect(site_url("calendar"));
        $input = parse_custom_post($input);
        switch($action){
            case 'add':
                $res=$this->calendar_model->addEvent($input);
                custom_response(true,'Success',array(
                    'id'=>$res
                ));
                break;
            case 'edit':
                $res=$this->calendar_model->editEvent($input);
                break;
            case 'delete':
                $res=$this->calendar_model->deleteEvent($input);
                break;
        }

        if($res!=null){
            custom_response(false,$res);
        }
        custom_response(true,'Success');
    }


    /*public function get_events()
    {
        // Our Stand and End Dates
        $start = $this->input->get("start");
        $end = $this->input->get("end");

        $startdt = new DateTime('now'); // setup a local datetime
        $startdt->setTimestamp($start); // Set the date based on timestamp
        $format = $startdt->format('Y-m-d');

        $enddt = new DateTime('now'); // setup a local datetime
        $enddt->setTimestamp($end); // Set the date based on timestamp
        $format2 = $enddt->format('Y-m-d');

        $events = $this->calendar_model->get_events($format, $format2);

        $data_events = array();

        foreach($events->result() as $r) {
			$end = date('Y-m-d H:i',strtotime($r->end.'+12 hour'));
			$start = date('Y-m-d H:i',strtotime($r->start.'+12 hour'));
            $data_events[] = array(
                "id" => $r->ID,
                "title" => $r->title,
                "description" => $r->description,
                "end" => $end,
                "start" => $start
            );
        }

        echo json_encode(array("events" => $data_events));
        exit();
    }*/

    /*public function add_event()
    {
        $name = $this->input->post("name");
        $desc = $this->input->post("description");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");

        if(!empty($start_date)) {
            $sd = DateTime::createFromFormat("Y/m/d", $start_date);
            $start_date = $sd->format('Y-m-d');
            $start_date_timestamp = $sd->getTimestamp();
        } else {
            $start_date = date("Y-m-d", time());
            $start_date_timestamp = time();
        }

        if(!empty($end_date)) {
            $ed = DateTime::createFromFormat("Y/m/d", $end_date);
            $end_date = $ed->format('Y-m-d');
            $end_date_timestamp = $ed->getTimestamp();
        } else {
            $end_date = date("Y-m-d", time());
            $end_date_timestamp = time();
        }

        $this->calendar_model->add_event(array(
            "title" => $name,
            "description" => $desc,
            "start" => $start_date,
            "end" => $end_date
            )
        );

        redirect(site_url("calendar"));
    }

    public function edit_event()
    {
        $eventid = intval($this->input->post("eventid"));
        $event = $this->calendar_model->get_event($eventid);
        if($event->num_rows() == 0) {
            echo"Invalid Event";
            exit();
        }

        $event->row();

        $name = $this->input->post("name");
        $desc = $this->input->post("description");
        $start_date = $this->input->post("start_date");
        $end_date = $this->input->post("end_date");
        $delete = intval($this->input->post("delete"));

        if(!$delete) {

            if(!empty($start_date)) {
                $sd = DateTime::createFromFormat("Y/m/d", $start_date);
                $start_date = $sd->format('Y-m-d');
                $start_date_timestamp = $sd->getTimestamp();
            } else {
                $start_date = date("Y-m-d", time());
                $start_date_timestamp = time();
            }

            if(!empty($end_date)) {
                $ed = DateTime::createFromFormat("Y/m/d", $end_date);
                $end_date = $ed->format('Y-m-d');
                $end_date_timestamp = $ed->getTimestamp();
            } else {
                $end_date = date("Y-m-d", time());
                $end_date_timestamp = time();
            }

            $this->calendar_model->update_event($eventid, array(
                "title" => $name,
                "description" => $desc,
                "start" => $start_date,
                "end" => $end_date,
                )
            );

        } else {
            $this->calendar_model->delete_event($eventid);
        }

        redirect(site_url("calendar"));
    }*/

}

?>
