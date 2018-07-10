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

	public function manageHolidays(){
		$this->html(
			function(){
				$this->load->view('calendar/holiday');
			}
		);
	}

	public function suspendWork(){
        $events = $this->calendar_model->getEvents();
		$this->html(
			function() use ($events){
				$this->load->view('calendar/suspend',array(
					'events'=>json_encode($events,JSON_HEX_APOS|JSON_HEX_QUOT|JSON_NUMERIC_CHECK)
				));
			}
		);
	}

}

?>
