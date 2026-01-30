<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';


class Buildings extends REST_Controller {


	function __construct()
	{
		parent::__construct();
		$this->load->model('edificios_model');
		$this->load->model('Event_model');

		header('Access-Control-Allow-Origin: *');
		header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Content-Length, Bearer, Accept, Access-Control-Request-Method");
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

    	$method = $_SERVER['REQUEST_METHOD'];
       if(!$this->verify_post()) {
            die();
        }
	}


	/**
	* @get : http://{{URL}}/api/Buildings/getBuildingData
	*
	* @param int buildingId
	* 
	* @return json array();
	*  
	*/ 

	public function getBuildingData_get(){
	//	$this->verifySecurityToken();
		$buildingId = $this->input->get('buildingId');
		$buildingData = $this->db->get_where('edificios', array('id' => $buildingId))->result_array();
		foreach($buildingData as $keys => $value) {
			if( $keys == 'images') {
				$img = base_url('upload/expensas/'.$buildingData[$keys]['imagen']);
				//$image = to_base64($img);
				$buildingData[$keys]['imagen'] = $img; 
			}
		}	
		   
		if ($buildingData)
		{
		    $this->response($buildingData, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
		    $this->response([
		        'status' => FALSE,
		        'message' => 'No building data were found'
		    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}
	}
	
	/**
	* @get : http://{{URL}}/api/Buildings/getBuildingEvents
	*
	* @param int buildingId
	* @param int unidadId
	* @param date initDate
	* @param date endDate
	* 
	* @return json array();
	*  
	*/ 
	public function getBuildingEvents_get()
	{
		$buildingId = $this->input->get('buildingId');
		$unidadId = $this->input->get('unidadId');

		$initDate = $this->input->get('initDate');
		$endDate = $this->input->get('endDate');

		$this->Event_model->set_building($buildingId);
		$this->Event_model->set_unity($unidadId);

		$data = $this->Event_model->get_nearby_events($initDate,$endDate);
		
		if ($data)
		{
		    $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
		}
		else
		{
		    $this->response([
		        'status' => FALSE,
		        'message' => 'No Events were found'
		    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
		}
	}

	public function verify_post()
	{  
	    $headers = $this->input->request_headers(); 
	    if (isset($headers['Bearer'])) {
	        $decodedToken = $this->authorization_token->validateToken($headers['Bearer']);
	        if ($decodedToken) {
	            return true;
	        }
	    }
	    else {
	        return FALSE;
	    }
	      
	}	
}
